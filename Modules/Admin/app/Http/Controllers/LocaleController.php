<?php

namespace Modules\Admin\app\Http\Controllers;

use Modules\Admin\app\Services\LocaleService;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __construct(protected LocaleService $localeService)
    {
    }

    /**
     * Switch locale.
     */
    public function switch(string $locale): RedirectResponse
    {
        $this->localeService->switchLocale($locale);

        return redirect()->back();
    }
}
