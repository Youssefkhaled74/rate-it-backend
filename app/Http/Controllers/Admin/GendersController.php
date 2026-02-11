<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Lookups\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GendersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Gender::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhere('code', 'like', "%{$q}%");
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

        return view('admin.lookups.genders.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        return view('admin.lookups.genders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Gender::create($data);
        return redirect()->route('admin.lookups.genders.index')->with('success', 'Gender created.');
    }

    public function edit(Gender $gender)
    {
        return view('admin.lookups.genders.edit', compact('gender'));
    }

    public function update(Request $request, Gender $gender)
    {
        $data = $request->validate([
            'code' => ['required','string','max:50'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $gender->update($data);
        return redirect()->route('admin.lookups.genders.index')->with('success', 'Gender updated.');
    }

    public function toggle(Gender $gender)
    {
        $gender->is_active = ! $gender->is_active;
        $gender->save();
        return back()->with('success', 'Gender status updated.');
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();
        return back()->with('success', 'Gender deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:genders,id'],
        ])['ids'];

        Gender::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' genders deleted.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['code', 'name_en', 'name_ar', 'is_active'], null, 'A1');
        $sheet->fromArray(['male', 'Male', 'ذكر', '1'], null, 'A2');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'genders_template.xlsx', [
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

            $code = $this->cell($row, $headerMap, 'code') ?? Str::slug($nameEn);
            if ($code === '') {
                $skipped++;
                continue;
            }

            Gender::updateOrCreate(
                ['code' => $code],
                [
                    'name_en' => $nameEn,
                    'name_ar' => $this->cell($row, $headerMap, 'name_ar'),
                    'is_active' => $this->toBool($this->cell($row, $headerMap, 'is_active'), true),
                ]
            );
            $imported++;
        }

        return back()->with('success', "Genders import done. Imported: {$imported}, Skipped: {$skipped}");
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
