<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share Settings with all views
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $setting = \App\Models\Setting::firstOrCreate([], [
                'company_name' => 'New Citra Indonesia',
                'company_address' => 'Jl. Rogojembangan Barat 1 No.31',
                'company_phone' => '081225096633, 082133326959, 085866228323',
                'company_email' => 'info@newcitra.co.id',
                'invoice_font' => "'Helvetica Neue', Helvetica, Arial, sans-serif"
            ]);

            // Auto-update if still contains old address or old phone number
            if (str_contains($setting->company_address, 'Kedungmundu') || $setting->company_phone === '085866228323') {
                $setting->update([
                    'company_address' => 'Jl. Rogojembangan Barat 1 No.31',
                    'company_phone' => '081225096633, 082133326959, 085866228323',
                ]);
            }

            \Illuminate\Support\Facades\View::share('settings', $setting);
        }
    }
}
