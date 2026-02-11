<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Lookups\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NationalitiesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Nationality::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhere('country_code', 'like', "%{$q}%");
            });

        $total = (clone $base)->count();
        $active = (clone $base)->where('is_active', true)->count();
        $inactive = (clone $base)->where('is_active', false)->count();

        $items = (clone $base)
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.lookups.nationalities.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        return view('admin.lookups.nationalities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_code' => ['nullable','string','max:5'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'flag_style' => ['nullable','string','max:50'],
            'flag_size' => ['nullable','integer','min:16','max:256'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Nationality::create($data);
        return redirect()->route('admin.lookups.nationalities.index')->with('success', 'Nationality created.');
    }

    public function edit(Nationality $nationality)
    {
        return view('admin.lookups.nationalities.edit', compact('nationality'));
    }

    public function update(Request $request, Nationality $nationality)
    {
        $data = $request->validate([
            'country_code' => ['nullable','string','max:5'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'flag_style' => ['nullable','string','max:50'],
            'flag_size' => ['nullable','integer','min:16','max:256'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $nationality->update($data);
        return redirect()->route('admin.lookups.nationalities.index')->with('success', 'Nationality updated.');
    }

    public function toggle(Nationality $nationality)
    {
        $nationality->is_active = ! $nationality->is_active;
        $nationality->save();
        return back()->with('success', 'Nationality status updated.');
    }

    public function destroy(Nationality $nationality)
    {
        $nationality->delete();
        return back()->with('success', 'Nationality deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:nationalities,id'],
        ])['ids'];

        Nationality::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' nationalities deleted.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['country_code', 'name_en', 'name_ar', 'is_active'], null, 'A1');
        $sheet->fromArray(['EG', 'Egyptian', 'مصري', '1'], null, 'A2');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'nationalities_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt'],
        ]);

        $rows = $this->readRows($request->file('file')->getRealPath());
        if (count($rows) <= 1) {
            return back()->with('error', 'Empty file.');
        }

        $headerMap = $this->headerMap($rows[0]);
        $imported = 0;
        $skipped = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $nameEn = $this->cell($row, $headerMap, 'name_en');
            if ($nameEn === null) {
                $skipped++;
                continue;
            }

            $countryCode = $this->cell($row, $headerMap, 'country_code');
            Nationality::updateOrCreate(
                ['name_en' => $nameEn],
                [
                    'country_code' => $countryCode ? strtoupper($countryCode) : null,
                    'name_ar' => $this->cell($row, $headerMap, 'name_ar'),
                    'is_active' => $this->toBool($this->cell($row, $headerMap, 'is_active'), true),
                ]
            );
            $imported++;
        }

        return back()->with('success', "Nationalities import done. Imported: {$imported}, Skipped: {$skipped}");
    }

    private function readRows(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
    }

    private function headerMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $idx => $value) {
            $key = Str::of((string) $value)->trim()->lower()->toString();
            if ($key !== '') {
                $map[$key] = $idx;
            }
        }
        return $map;
    }

    private function cell(array $row, array $map, string $key): ?string
    {
        if (!isset($map[$key])) {
            return null;
        }
        $value = isset($row[$map[$key]]) ? trim((string) $row[$map[$key]]) : '';
        return $value === '' ? null : $value;
    }

    private function toBool(?string $value, bool $default): bool
    {
        if ($value === null) {
            return $default;
        }
        $v = strtolower(trim($value));
        return in_array($v, ['1', 'true', 'yes', 'y', 'active', 'on'], true);
    }
}
