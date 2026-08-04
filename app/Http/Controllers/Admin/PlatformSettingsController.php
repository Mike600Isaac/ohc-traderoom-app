<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;

class PlatformSettingsController extends Controller
{
    private const BOOLEAN_KEYS = [
        'registration_enabled', 'payments_enabled', 'renewals_enabled',
        'community_enabled', 'research_enabled', 'live_sessions_enabled',
    ];

    public function edit(Request $request)
    {
        AdminAccess::require($request->user(), 'settings.manage');
        $settings = PlatformSetting::all()->mapWithKeys(fn ($setting) => [$setting->key => $setting->value]);
        return view('admin.settings.edit', [
            'settings' => $settings,
            'paystackSecretConfigured' => filled(config('services.paystack.secret_key')),
        ]);
    }

    public function update(Request $request)
    {
        AdminAccess::require($request->user(), 'settings.manage');
        $data = $request->validate([
            'platform_name' => ['required', 'string', 'max:100'],
            'default_currency' => ['required', 'string', 'size:3'],
            'support_email' => ['required', 'email'],
            'registration_enabled' => ['nullable', 'boolean'],
            'payments_enabled' => ['nullable', 'boolean'],
            'renewals_enabled' => ['nullable', 'boolean'],
            'community_enabled' => ['nullable', 'boolean'],
            'research_enabled' => ['nullable', 'boolean'],
            'live_sessions_enabled' => ['nullable', 'boolean'],
        ]);

        foreach (self::BOOLEAN_KEYS as $key) $data[$key] = $request->boolean($key);

        foreach ($data as $key => $value) {
            PlatformSetting::updateOrCreate(['key' => $key], [
                'group' => in_array($key, ['default_currency', 'payments_enabled', 'renewals_enabled'], true) ? 'billing' : 'platform',
                'value' => $value,
                'updated_by' => $request->user()->id,
            ]);
        }

        AdminAudit::record($request, 'settings.updated', PlatformSetting::class, 'Updated platform and billing settings', $data);
        return back()->with('status', 'Platform settings updated. Environment secrets were not changed.');
    }
}
