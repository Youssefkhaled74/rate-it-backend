<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AreasController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');
        $cityId = (int) $request->get('city_id');
        $selectedCity = $cityId > 0 ? City::find($cityId) : null;
        $cities = City::orderBy('name_en')->get();

        $base = Area::query()
            ->with('city')
            ->when($cityId > 0, fn ($qq) => $qq->where('city_id', $cityId))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name_en', 'like', "%{$q}%")
                   ->orWhere('name_ar', 'like', "%{$q}%")
                   ->orWhereHas('city', function ($c) use ($q) {
                       $c->where('name_en', 'like', "%{$q}%")
                         ->orWhere('name_ar', 'like', "%{$q}%");
                   });
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

        return view('admin.lookups.areas.index', compact(
            'items',
            'q',
            'status',
            'total',
            'active',
            'inactive',
            'cityId',
            'selectedCity',
            'cities'
        ));
    }

    public function create()
    {
        $cities = City::orderBy('name_en')->get();
        return view('admin.lookups.areas.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city_id' => ['required','exists:cities,id'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', true);

        Area::create($data);
        return redirect()->route('admin.lookups.areas.index')->with('success', 'Area created.');
    }

    public function edit(Area $area)
    {
        $cities = City::orderBy('name_en')->get();
        return view('admin.lookups.areas.edit', compact('area','cities'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'city_id' => ['required','exists:cities,id'],
            'name_en' => ['required','string','max:255'],
            'name_ar' => ['nullable','string','max:255'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) $request->boolean('is_active', false);

        $area->update($data);
        return redirect()->route('admin.lookups.areas.index')->with('success', 'Area updated.');
    }

    public function toggle(Area $area)
    {
        $area->is_active = ! $area->is_active;
        $area->save();
        return back()->with('success', 'Area status updated.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:areas,id'],
        ])['ids'];

        Area::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' areas deleted.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['city_id', 'city_name_en', 'name_en', 'name_ar', 'is_active'], null, 'A1');
        $sheet->fromArray(['4', 'Cairo', 'Nasr City', 'مدينة نصر', '1'], null, 'A2');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'areas_template.xlsx', [
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
        $cityNameToId = City::query()->pluck('id', 'name_en')->toArray();

        $imported = 0;
        $skipped = 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $nameEn = $this->cell($row, $headerMap, 'name_en');
            if ($nameEn === null) {
                $skipped++;
                continue;
            }

            $cityId = $this->resolveCityId($row, $headerMap, $cityNameToId);
            if ($cityId === null) {
                $skipped++;
                continue;
            }

            Area::updateOrCreate(
                ['city_id' => $cityId, 'name_en' => $nameEn],
                [
                    'name_ar' => $this->cell($row, $headerMap, 'name_ar'),
                    'is_active' => $this->toBool($this->cell($row, $headerMap, 'is_active'), true),
                ]
            );
            $imported++;
        }

        return back()->with('success', "Areas import done. Imported: {$imported}, Skipped: {$skipped}");
    }

    private function resolveCityId(array $row, array $map, array $cityNameToId): ?int
    {
        $cityIdRaw = $this->cell($row, $map, 'city_id');
        if ($cityIdRaw !== null && ctype_digit($cityIdRaw)) {
            $id = (int) $cityIdRaw;
            if (City::where('id', $id)->exists()) {
                return $id;
            }
        }

        $cityName = $this->cell($row, $map, 'city_name_en');
        if ($cityName !== null && isset($cityNameToId[$cityName])) {
            return (int) $cityNameToId[$cityName];
        }

        return null;
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
