<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Branch;
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
        $brandId = $request->query('brand_id');
        $branchId = $request->query('branch_id');

        $query = $this->baseQuery($q, $status, $rating, $from, $to, $brandId, $branchId);

        $reviews = $query
            ->withCount(['answers', 'photos'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $brands = Brand::query()->orderBy('name_en')->get(['id', 'name_en', 'name_ar']);
        $branches = Branch::query()
            ->when($brandId, fn ($qq) => $qq->where('brand_id', $brandId))
            ->orderBy('name')
            ->get(['id', 'name', 'name_en', 'name_ar', 'brand_id']);

        return view('admin.reviews.index', compact(
            'reviews',
            'q',
            'status',
            'rating',
            'from',
            'to',
            'brandId',
            'branchId',
            'brands',
            'branches'
        ));
    }

    public function show(Review $review)
    {
        $review->load([
            'user',
            'place',
            'branch.brand',
            'photos',
            'answers.criteria',
            'answers.choice',
            'answers.photos',
            'hiddenByAdmin:id,name,email',
            'repliedByAdmin:id,name,email',
            'featuredByAdmin:id,name,email',
        ]);

        return view('admin.reviews.show', compact('review'));
    }

    public function toggleHide(Request $request, Review $review)
    {
        $isHidden = (bool) ($review->is_hidden ?? false);

        if ($isHidden) {
            $review->is_hidden = false;
            $review->hidden_reason = null;
            $review->hidden_at = null;
            $review->hidden_by_admin_id = null;
        } else {
            $data = $request->validate([
                'reason' => ['required', 'string', 'max:1000'],
            ]);
            $reason = trim((string) ($data['reason'] ?? ''));

            $review->is_hidden = true;
            $review->hidden_reason = $reason;
            $review->hidden_at = Carbon::now();
            $review->hidden_by_admin_id = auth('admin_web')->id();
        }
        $review->save();

        return back()->with('success', $isHidden ? 'Review unhidden.' : 'Review hidden.');
    }

    public function toggleFeatured(Review $review)
    {
        $isFeatured = (bool) ($review->is_featured ?? false);
        $review->is_featured = ! $isFeatured;
        $review->featured_at = $review->is_featured ? Carbon::now() : null;
        $review->featured_by_admin_id = $review->is_featured ? auth('admin_web')->id() : null;
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
        $review->replied_by_admin_id = auth('admin_web')->id();
        $review->save();

        return back()->with('success', 'Reply sent.');
    }

    public function exportCsv(Request $request)
    {
        [$reviews, $meta] = $this->exportData($request);
        $lang = (string) $request->query('lang', app()->getLocale());
        $isRtl = $lang === 'ar';

        $fileName = 'reviews-' . $meta['range'] . '-' . Carbon::now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($reviews, $meta, $isRtl) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Reviews Export']);
            fputcsv($out, ['Range', $meta['range_label']]);
            fputcsv($out, []);
            fputcsv($out, [
                'ID',
                'User',
                'Rating',
                'Comment',
                'Hidden',
                'Featured',
                'Replied',
                'Branch',
                'Place',
                'Created At',
            ]);
            foreach ($reviews as $r) {
                $placeName = $this->placeName($r, $isRtl);
                $branchName = $isRtl
                    ? ($r->branch?->name_ar ?? $r->branch?->name_en ?? $r->branch?->name ?? '-')
                    : ($r->branch?->name_en ?? $r->branch?->name_ar ?? $r->branch?->name ?? '-');

                fputcsv($out, [
                    $r->id,
                    $r->user?->name ?? '-',
                    $r->overall_rating ?? '-',
                    $r->comment ?? '-',
                    $r->is_hidden ? 'yes' : 'no',
                    $r->is_featured ? 'yes' : 'no',
                    $r->replied_at ? 'yes' : 'no',
                    $branchName,
                    $placeName,
                    $r->created_at ? '="' . $r->created_at->format('Y-m-d H:i:s') . '"' : '-',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        [$reviews, $meta] = $this->exportData($request);
        $lang = (string) $request->query('lang', app()->getLocale());
        $isRtl = $lang === 'ar';

        $html = view('admin.reviews.pdf', [
            'reviews' => $reviews,
            'rangeLabel' => $meta['range_label'],
            'lang' => $lang,
            'isRtl' => $isRtl,
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
        $brandId = $request->query('brand_id');
        $branchId = $request->query('branch_id');

        $query = $this->baseQuery($q, $status, $rating, $from, $to, $brandId, $branchId);

        $reviews = $query->orderBy('created_at', 'desc')->limit(500)->get();

        $range = ($from && $to) ? ($from . '_' . $to) : 'all';
        $rangeLabel = ($from && $to) ? ($from . ' -> ' . $to) : 'All time';

        return [$reviews, ['range' => $range, 'range_label' => $rangeLabel]];
    }

    private function baseQuery(?string $q, ?string $status, $rating, ?string $from, ?string $to, $brandId = null, $branchId = null)
    {
        $query = Review::query()->with(['user:id,name,phone', 'branch:id,name,name_en,name_ar,brand_id', 'branch.brand:id,name_en,name_ar', 'place']);

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

        if ($branchId !== null && $branchId !== '') {
            $query->where('branch_id', (int) $branchId);
        }

        if ($brandId !== null && $brandId !== '') {
            $query->whereHas('branch', function ($qq) use ($brandId) {
                $qq->where('brand_id', (int) $brandId);
            });
        }

        if ($from && $to) {
            $start = Carbon::parse($from)->startOfDay();
            $end = Carbon::parse($to)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query;
    }

    private function placeName(Review $r, bool $isRtl = false): string
    {
        if ($isRtl) {
            return $r->place?->name_ar
                ?? $r->place?->title_ar
                ?? $r->place?->display_name
                ?? $r->place?->name
                ?? $r->place?->name_en
                ?? $r->place?->title_en
                ?? '-';
        }

        return $r->place?->display_name
            ?? $r->place?->name
            ?? $r->place?->name_en
            ?? $r->place?->title_en
            ?? $r->place?->name_ar
            ?? $r->place?->title_ar
            ?? '-';
    }
}
