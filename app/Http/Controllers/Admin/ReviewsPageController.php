<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class ReviewsPageController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');
        $rating = $request->query('rating');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = $this->baseQuery($q, $status, $rating, $from, $to);

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'q', 'status', 'rating', 'from', 'to'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'place', 'branch', 'photos', 'answers']);

        return view('admin.reviews.show', compact('review'));
    }

    public function toggleHide(Request $request, Review $review)
    {
        $reason = trim((string) $request->input('reason', ''));
        $isHidden = (bool) ($review->is_hidden ?? false);

        if ($isHidden) {
            $review->is_hidden = false;
            $review->hidden_reason = null;
            $review->hidden_at = null;
        } else {
            $review->is_hidden = true;
            $review->hidden_reason = $reason !== '' ? $reason : null;
            $review->hidden_at = Carbon::now();
        }
        $review->save();

        return back()->with('success', $isHidden ? 'Review unhidden.' : 'Review hidden.');
    }

    public function toggleFeatured(Review $review)
    {
        $isFeatured = (bool) ($review->is_featured ?? false);
        $review->is_featured = ! $isFeatured;
        $review->featured_at = $review->is_featured ? Carbon::now() : null;
        $review->save();

        return back()->with('success', $review->is_featured ? 'Review featured.' : 'Review unfeatured.');
    }

    public function reply(Request $request, Review $review)
    {
        $data = $request->validate([
            'admin_reply_text' => ['required', 'string', 'max:2000'],
        ]);

        $review->admin_reply_text = $data['admin_reply_text'];
        $review->replied_at = Carbon::now();
        $review->save();

        return back()->with('success', 'Reply sent.');
    }

    public function exportCsv(Request $request)
    {
        [$reviews, $meta] = $this->exportData($request);

        $fileName = 'reviews-' . $meta['range'] . '-' . Carbon::now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($reviews, $meta) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reviews Export']);
            fputcsv($out, ['Range', $meta['range_label']]);
            fputcsv($out, []);
            fputcsv($out, ['ID', 'User', 'Rating', 'Comment', 'Hidden', 'Featured', 'Replied', 'Branch', 'Place', 'Created At']);
            foreach ($reviews as $r) {
                $placeName = $this->placeName($r);
                fputcsv($out, [
                    $r->id,
                    $r->user?->name ?? '-',
                    $r->overall_rating ?? '-',
                    $r->comment ?? '-',
                    (int) ($r->is_hidden ?? 0),
                    (int) ($r->is_featured ?? 0),
                    $r->replied_at ? 'yes' : 'no',
                    $r->branch?->name ?? '-',
                    $placeName,
                    $r->created_at?->toDateTimeString() ?? '-',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        [$reviews, $meta] = $this->exportData($request);

        $html = view('admin.reviews.pdf', [
            'reviews' => $reviews,
            'rangeLabel' => $meta['range_label'],
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'reviews-' . $meta['range'] . '-' . Carbon::now()->format('Y-m-d') . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function exportData(Request $request): array
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');
        $rating = $request->query('rating');
        $from = $request->query('from');
        $to = $request->query('to');

        $query = $this->baseQuery($q, $status, $rating, $from, $to);

        $reviews = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $range = ($from && $to) ? ($from . '_' . $to) : 'all';
        $rangeLabel = ($from && $to) ? ($from . ' → ' . $to) : 'All time';

        return [$reviews, ['range' => $range, 'range_label' => $rangeLabel]];
    }

    private function baseQuery(?string $q, ?string $status, $rating, ?string $from, ?string $to)
    {
        $query = Review::query()->with(['user:id,name,phone', 'branch:id,name', 'place']);

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('comment', 'like', "%{$q}%")
                   ->orWhereHas('user', function ($u) use ($q) {
                       $u->where('name', 'like', "%{$q}%")
                         ->orWhere('phone', 'like', "%{$q}%");
                   });
            });
        }

        if ($status === 'hidden') {
            $query->where('is_hidden', true);
        } elseif ($status === 'visible') {
            $query->where(function ($q2) {
                $q2->whereNull('is_hidden')->orWhere('is_hidden', false);
            });
        } elseif ($status === 'featured') {
            $query->where('is_featured', true);
        } elseif ($status === 'pending') {
            $query->whereNull('admin_reply_text')->whereNull('replied_at');
        }

        if ($rating !== null && $rating !== '') {
            $query->where('overall_rating', (int) $rating);
        }

        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }

    private function placeName(Review $r): string
    {
        return $r->place?->display_name
            ?? $r->place?->name
            ?? $r->place?->name_en
            ?? $r->place?->title_en
            ?? $r->place?->name_ar
            ?? $r->place?->title_ar
            ?? '-';
    }
}
