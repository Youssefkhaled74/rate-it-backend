<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $lang)
    {
        $lang = in_array($lang, ['en', 'ar'], true) ? $lang : 'en';
        if ($request->hasSession()) {
            $request->session()->put('lang', $lang);
        }

        return back()->withCookie(cookie('lang', $lang, 60 * 24 * 365));
    }
}
