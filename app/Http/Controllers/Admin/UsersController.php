<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\Admin\UsersService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\User\Lookups\Models\Gender;
use App\Modules\User\Lookups\Models\Nationality;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UsersController extends Controller
{
    protected UsersService $service;

    public function __construct(UsersService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // dd([
        //     'default_guard_user' => auth()->check() ? get_class(auth()->user()) : null,
        //     'default_guard_id' => auth()->id(),
        //     'admin_web_user' => auth('admin_web')->check() ? get_class(auth('admin_web')->user()) : null,
        //     'admin_web_id' => auth('admin_web')->id(),
        //     'admin_role' => auth('admin_web')->user()->role ?? null,
        // ]);

        $this->authorize('viewAny', User::class);
        $filters = $request->only(['q']);
        $users = $this->service->listUsers($filters, 15);

        $totalUsers = User::count();
        $newUsers7 = User::where('created_at', '>=', now()->subDays(7))->count();
        $hasBlocked = Schema::hasColumn('users', 'is_blocked');
        $activeUsers = $hasBlocked ? User::where('is_blocked', false)->count() : $totalUsers;
        $inactiveUsers = $hasBlocked ? User::where('is_blocked', true)->count() : 0;

        $withReviews = User::has('reviews')->count();
        $withoutReviews = max(0, $totalUsers - $withReviews);

        $genderCounts = User::select('gender_id', DB::raw('count(*) as total'))
            ->groupBy('gender_id')
            ->orderByDesc('total')
            ->get();
        $genderIds = $genderCounts->pluck('gender_id')->filter()->unique()->values();
        $genderMap = Gender::whereIn('id', $genderIds)->get()->keyBy('id');
        $genderStats = $genderCounts->map(function ($row) use ($genderMap) {
            $name = $row->gender_id && isset($genderMap[$row->gender_id])
                ? $genderMap[$row->gender_id]->name
                : __('admin.unspecified');
            return ['name' => $name, 'total' => (int) $row->total];
        })->take(5);

        $nationCounts = User::select('nationality_id', DB::raw('count(*) as total'))
            ->groupBy('nationality_id')
            ->orderByDesc('total')
            ->get();
        $nationIds = $nationCounts->pluck('nationality_id')->filter()->unique()->values();
        $nationMap = Nationality::whereIn('id', $nationIds)->get()->keyBy('id');
        $nationalityStats = $nationCounts->map(function ($row) use ($nationMap) {
            $name = $row->nationality_id && isset($nationMap[$row->nationality_id])
                ? $nationMap[$row->nationality_id]->name
                : __('admin.unspecified');
            return ['name' => $name, 'total' => (int) $row->total];
        })->take(5);

        $stats = [
            'total' => $totalUsers,
            'new_7' => $newUsers7,
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
            'with_reviews' => $withReviews,
            'without_reviews' => $withoutReviews,
        ];

        return view('admin.users.index', compact('users','filters','stats','genderStats','nationalityStats'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        $data = $this->service->getUserProfile($user);
        return view('admin.users.show', $data);
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $filters = $request->only(['q']);
        $rows = $this->service->exportUsers($filters);

        $headers = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Gender',
            'Nationality',
            'City',
            'Area',
            'Reviews',
            'Created At',
            'Status',
        ];

        if (class_exists(Spreadsheet::class)) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->fromArray($headers, null, 'A1');
            $sheet->getStyle('A1:K1')->getFont()->setBold(true);
            $sheet->freezePane('A2');

            $rowNum = 2;
            foreach ($rows as $row) {
                $colNum = 1;
                foreach ($row as $key => $value) {
                    if ($key === 'phone') {
                        $sheet->setCellValueExplicitByColumnAndRow(
                            $colNum,
                            $rowNum,
                            (string) $value,
                            DataType::TYPE_STRING
                        );
                    } else {
                        $sheet->setCellValueByColumnAndRow($colNum, $rowNum, $value);
                    }
                    $colNum++;
                }
                $rowNum++;
            }

            $lastRow = max(1, count($rows) + 1);
            $sheet->setAutoFilter("A1:K{$lastRow}");

            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $tmp = tempnam(sys_get_temp_dir(), 'users_') . '.xlsx';
            $writer->save($tmp);

            $fileName = 'users-export-' . now()->format('Ymd_His') . '.xlsx';
            return response()->download($tmp, $fileName)->deleteFileAfterSend(true);
        }

        // Fallback to CSV if Spreadsheet isn't available
        $fileName = 'users-export-' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($rows as $row) {
            fputcsv($handle, array_values($row));
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }
}
