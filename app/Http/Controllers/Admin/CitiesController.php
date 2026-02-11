<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CitiesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = City::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%");
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

        return view('admin.lookups.cities.index', compact('items','q','status','total','active','inactive'));
    }

    public function create()
    {
        return view('admin.lookups.cities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        City::create($data);
        return redirect()->route('admin.lookups.cities.index')->with('success', 'City created.');
    }

    public function edit(City $city)
    {
        return view('admin.lookups.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $city->update($data);
        return redirect()->route('admin.lookups.cities.index')->with('success', 'City updated.');
    }

    public function toggle(City $city)
    {
        $city->is_active = ! $city->is_active;
        $city->save();
        return back()->with('success', 'City status updated.');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return back()->with('success', 'City deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:cities,id'],
        ])['ids'];

        City::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' cities deleted.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['name_en', 'name_ar', 'is_active'], null, 'A1');
        $sheet->fromArray(['Cairo', 'القاهرة', '1'], null, 'A2');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'cities_template.xlsx', [
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

            City::updateOrCreate(
                ['name_en' => $nameEn],
                [
                    'name_ar' => $this->cell($row, $headerMap, 'name_ar'),
                    'is_active' => $this->toBool($this->cell($row, $headerMap, 'is_active'), true),
                ]
            );
            $imported++;
        }

        return back()->with('success', "Cities import done. Imported: {$imported}, Skipped: {$skipped}");
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
