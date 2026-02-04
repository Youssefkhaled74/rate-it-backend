<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Place;
use App\Models\City;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;

class BranchesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $base = Branch::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('address', 'like', "%{$q}%");
                })->orWhereHas('place', function ($p) use ($q) {
                    $p->where('name_en', 'like', "%{$q}%")
                      ->orWhere('name_ar', 'like', "%{$q}%")
                      ->orWhere('title_en', 'like', "%{$q}%")
                      ->orWhere('title_ar', 'like', "%{$q}%");
                });
            });

        $totalBranches = (clone $base)->count();
        $activeBranches = (clone $base)->where('is_active', 1)->count();
        $inactiveBranches = (clone $base)->where('is_active', 0)->count();

        $branches = (clone $base)
            ->with(['place.brand'])
            ->when($status === 'active', fn ($q) => $q->where('is_active', 1))
            ->when($status === 'inactive', fn ($q) => $q->where('is_active', 0))
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.branches.index', compact(
            'branches',
            'q',
            'status',
            'totalBranches',
            'activeBranches',
            'inactiveBranches'
        ));
    }

    public function create()
    {
        $places = Place::query()->orderBy('name_en')->get();
        $cities = City::query()->orderBy('name_en')->get();
        $areas = Area::query()->orderBy('name_en')->get();
        return view('admin.branches.create', compact('places','cities','areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id' => ['required', 'exists:places,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'address' => ['required', 'string', 'max:1000'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'review_cooldown_days' => ['nullable', 'integer', 'min:0'],
            'working_hours' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['working_hours'])) {
            $decoded = json_decode($data['working_hours'], true);
            $data['working_hours'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $data['working_hours'] = null;
        }

        $data['is_active'] = (bool) $request->boolean('is_active', true);
        $data['qr_code_value'] = (string) Str::uuid();
        $data['qr_generated_at'] = now();
        $data['brand_id'] = Place::query()->whereKey($data['place_id'])->value('brand_id');
        $data['logo'] = $this->saveImageToPublicAssets($request, 'logo', 'branches');
        $data['cover_image'] = $this->saveImageToPublicAssets($request, 'cover_image', 'branches/covers');

        Branch::create($data);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        $branch->load(['place.brand']);
        return view('admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $places = Place::query()->orderBy('name_en')->get();
        $cities = City::query()->orderBy('name_en')->get();
        $areas = Area::query()->orderBy('name_en')->get();
        return view('admin.branches.edit', compact('branch','places','cities','areas'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'place_id' => ['required', 'exists:places,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'address' => ['required', 'string', 'max:1000'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'city_id' => ['nullable','exists:cities,id'],
            'area_id' => ['nullable','exists:areas,id'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'review_cooldown_days' => ['nullable', 'integer', 'min:0'],
            'working_hours' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['working_hours'])) {
            $decoded = json_decode($data['working_hours'], true);
            $data['working_hours'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $data['working_hours'] = null;
        }

        $data['is_active'] = (bool) $request->boolean('is_active', false);
        $data['brand_id'] = Place::query()->whereKey($data['place_id'])->value('brand_id');

        $newLogo = $this->saveImageToPublicAssets($request, 'logo', 'branches');
        if ($newLogo) {
            $this->deletePublicAssetIfExists($branch->logo);
            $data['logo'] = $newLogo;
        } else {
            unset($data['logo']);
        }

        $newCover = $this->saveImageToPublicAssets($request, 'cover_image', 'branches/covers');
        if ($newCover) {
            $this->deletePublicAssetIfExists($branch->cover_image);
            $data['cover_image'] = $newCover;
        } else {
            unset($data['cover_image']);
        }

        $branch->update($data);

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function toggle(Branch $branch)
    {
        $branch->is_active = ! $branch->is_active;
        $branch->save();

        return back()->with('success', 'Branch status updated.');
    }

    public function destroy(Branch $branch)
    {
        $this->deletePublicAssetIfExists($branch->logo);
        $this->deletePublicAssetIfExists($branch->cover_image);
        $branch->delete();

        return back()->with('success', 'Branch deleted.');
    }

    public function qr(Branch $branch)
    {
        $png = $this->buildBranchQrPng($branch);
        return response($png, 200)->header('Content-Type', 'image/png');
    }

    public function qrPdf(Branch $branch)
    {
        $qrPng = $this->buildBranchQrPng($branch, 900);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrPng);

        $rateitLogoPath = public_path('assets/images/Vector.png');
        $rateitLogoBase64 = $this->fileToDataUri($rateitLogoPath);

        $html = view('admin.branches.qr-pdf', [
            'branch' => $branch,
            'qrBase64' => $qrBase64,
            'rateitLogoBase64' => $rateitLogoBase64,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'branch-qr-' . $branch->id . '.pdf';
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }

    protected function buildBranchQrPng(Branch $branch, int $size = 500): string
    {
        if (empty($branch->qr_code_value)) {
            $branch->qr_code_value = (string) Str::uuid();
            $branch->qr_generated_at = now();
            $branch->save();
        }

        $logoPath = $this->resolveBranchLogoPath($branch);
        $builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data((string) $branch->qr_code_value)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($size)
            ->margin(12)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->foregroundColor(new Color(25, 25, 25))
            ->backgroundColor(new Color(255, 255, 255));

        if ($logoPath) {
            $builder
                ->logoPath($logoPath)
                ->logoResizeToWidth((int) ($size * 0.22))
                ->logoResizeToHeight((int) ($size * 0.22))
                ->logoPunchoutBackground(true);
        }

        $result = $builder->build();

        return $result->getString();
    }

    protected function resolveBranchLogoPath(Branch $branch): ?string
    {
        $candidates = [
            $branch->logo,
            $branch->place?->logo,
            $branch->place?->brand?->logo,
        ];

        foreach ($candidates as $path) {
            if (empty($path)) continue;
            $publicPath = public_path($path);
            if (file_exists($publicPath)) return $publicPath;
            if (Storage::disk('public')->exists($path)) {
                return storage_path('app/public/' . ltrim($path, '/'));
            }
        }

        return null;
    }

    protected function fileToDataUri(?string $path): ?string
    {
        if (empty($path) || ! file_exists($path)) return null;
        $mime = mime_content_type($path) ?: 'image/png';
        $data = base64_encode(file_get_contents($path));
        return 'data:' . $mime . ';base64,' . $data;
    }

    /**
     * Save uploaded file into: public/assets/images/<folder>/
     * Returns path like: assets/images/branches/xxx.png
     */
    private function saveImageToPublicAssets(Request $request, string $field, string $folder): ?string
    {
        if (! $request->hasFile($field)) return null;

        $file = $request->file($field);
        if (! $file->isValid()) return null;

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $name = Str::uuid()->toString() . '.' . $ext;

        $dir = public_path("assets/images/{$folder}");
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $file->move($dir, $name);

        return "assets/images/{$folder}/{$name}";
    }

    private function deletePublicAssetIfExists(?string $path): void
    {
        if (! $path) return;

        $full = public_path($path);
        if (File::exists($full)) {
            @File::delete($full);
        }
    }
}


