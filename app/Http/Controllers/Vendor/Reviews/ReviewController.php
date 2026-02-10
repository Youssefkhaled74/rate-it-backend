<?php

namespace App\Http\Controllers\Vendor\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ReviewFilterRequest;
use App\Modules\Vendor\Reviews\Services\VendorReviewService;
use App\Modules\Vendor\Branches\Services\BranchService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReviewController extends Controller
{
    protected VendorReviewService $service;
    protected BranchService $branchService;

    public function __construct(VendorReviewService $service, BranchService $branchService)
    {
        $this->service = $service;
        $this->branchService = $branchService;
    }

    public function index(ReviewFilterRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch.place');

        $filters = $request->validated();
        $reviews = $this->service->list($vendor, $filters);
        $branches = $this->branchService->listBranches($vendor);

        return view('vendor.reviews.index', [
            'vendor' => $vendor,
            'reviews' => $reviews,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function show(int $id)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch.place');

        $review = $this->service->find($vendor, $id);
        if (! $review) {
            abort(404);
        }

        return view('vendor.reviews.show', [
            'vendor' => $vendor,
            'review' => $review,
        ]);
    }

    public function exportCsv(ReviewFilterRequest $request): StreamedResponse
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch.place');

        $filters = $request->validated();
        $filters['per_page'] = 10000; // export cap
        $filters['page'] = 1;

        $reviews = $this->service->list($vendor, $filters);

        $filename = 'vendor-reviews-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reviews) {
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'ID', 'Date', 'Rating', 'Review Score', 'Comment', 'User Name', 'User Phone', 'Branch', 'Place', 'Has Photos'
            ]);

            foreach ($reviews->items() as $review) {
                fputcsv($output, [
                    $review->id,
                    optional($review->created_at)->format('Y-m-d H:i'),
                    $review->overall_rating,
                    $review->review_score,
                    $review->comment,
                    $review->user?->nickname ?? $review->user?->name,
                    $review->user?->phone,
                    $review->branch?->name,
                    $review->place?->name,
                    $review->photos_count > 0 ? 'yes' : 'no',
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportXlsx(ReviewFilterRequest $request)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor) {
            abort(403);
        }
        $vendor->loadMissing('brand', 'branch.place');

        $filters = $request->validated();
        $filters['per_page'] = 10000;
        $filters['page'] = 1;

        $reviews = $this->service->list($vendor, $filters);

        $filename = 'vendor-reviews-' . now()->format('Ymd_His') . '.xlsx';

        $callback = function () use ($reviews) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Reviews');

            $headers = [
                'ID', 'Date', 'Rating', 'Review Score', 'Comment', 'User Name', 'User Phone', 'Branch', 'Place', 'Has Photos'
            ];
            $sheet->fromArray($headers, null, 'A1');

            $row = 2;
            foreach ($reviews->items() as $review) {
                $sheet->fromArray([
                    $review->id,
                    optional($review->created_at)->format('Y-m-d H:i'),
                    $review->overall_rating,
                    $review->review_score,
                    $review->comment,
                    $review->user?->nickname ?? $review->user?->name,
                    $review->user?->phone,
                    $review->branch?->name,
                    $review->place?->name,
                    $review->photos_count > 0 ? 'yes' : 'no',
                ], null, 'A' . $row);
                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

