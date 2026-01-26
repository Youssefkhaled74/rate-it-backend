<?php

namespace Modules\Admin\app\Services;

class LocaleService
{
    /**
     * Get supported locales.
     */
    public function getSupportedLocales(): array
    {
        return ['ar', 'en'];
    }

    /**
     * Get locale display names.
     */
    public function getLocaleNames(): array
    {
        return [
            'ar' => 'العربية',
            'en' => 'English',
        ];
    }

    /**
     * Switch locale and persist in session.
     */
    public function switchLocale(string $locale): string
    {
        if (!in_array($locale, $this->getSupportedLocales())) {
            $locale = 'ar';
        }

        session(['admin_locale' => $locale]);
        app()->setLocale($locale);

        return $locale;
    }

    /**
     * Get current locale.
     */
    public function getCurrentLocale(): string
    {
        return session('admin_locale', 'ar');
    }

    /**
     * Get direction based on locale (RTL for Arabic, LTR for English).
     */
    public function getDirection(): string
    {
        $locale = $this->getCurrentLocale();
        return $locale === 'ar' ? 'rtl' : 'ltr';
    }

    /**
     * Get HTML dir attribute.
     */
    public function getDirAttribute(): string
    {
        return $this->getDirection() === 'rtl' ? 'rtl' : 'ltr';
    }
}
