<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        // Get the first settings record or create default
        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create([
                'app_name' => '',
                'phone_number' => '',
                'whatsapp_number' => '',
                'address' => '',
                'alternative_address' => null,
                'email' => '',
                'logo' => 'default-logo.png',
                'favicon' => 'default-logo.png',
                'facebook_link' => null,
                'instagram_link' => null,
                'youtube_link' => null,
                'x_id' => null,
                'google_map_link' => null,
                'working_hours' => null,
                'trade_license' => null,
            ]);
        }

        // Get the first user (or you can get the authenticated user)
        $user = User::first();

        return view('admin.settings.index', compact('settings', 'user'));
    }

    public function update(Request $request, $id)
    {
        $settings = Setting::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'address' => 'required|string',
            'alternative_address' => 'nullable|string',
            'email' => 'required|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:1024',
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'x_id' => 'nullable|string|max:255',
            'google_map_link' => 'nullable|string|max:5120',
            'working_hours' => 'nullable|string',
            'trade_license' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data for update
        $data = $request->except(['logo', 'favicon', '_token', '_method']);

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            if ($settings->logo && $settings->logo != 'default-logo.png') {
                $oldLogoPath = public_path('uploads/settings/' . $settings->logo);
                if (File::exists($oldLogoPath)) {
                    File::delete($oldLogoPath);
                }
            }

            if (!File::exists(public_path('uploads/settings'))) {
                File::makeDirectory(public_path('uploads/settings'), 0777, true);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/settings'), $logoName);
            $data['logo'] = $logoName;
        }

        // Handle Favicon Upload
        if ($request->hasFile('favicon')) {
            if ($settings->favicon && $settings->favicon != 'default-favicon.ico') {
                $oldFaviconPath = public_path('uploads/settings/' . $settings->favicon);
                if (File::exists($oldFaviconPath)) {
                    File::delete($oldFaviconPath);
                }
            }

            if (!File::exists(public_path('uploads/settings'))) {
                File::makeDirectory(public_path('uploads/settings'), 0777, true);
            }

            $favicon = $request->file('favicon');
            $faviconName = time() . '_favicon.' . $favicon->getClientOriginalExtension();
            $favicon->move(public_path('uploads/settings'), $faviconName);
            $data['favicon'] = $faviconName;
        }

        $settings->update($data);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    // New method to update user settings
    public function updateUser(Request $request)
    {
        $user = User::first(); // or use auth()->user() for authenticated user

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'current_password' => 'nullable|string|min:8',
            'new_password' => 'nullable|string|min:8|confirmed',
            'new_password_confirmation' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update name
        $user->name = $request->user_name;

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('admin.settings.index')
            ->with('success', 'User settings updated successfully!');
    }

    public function reset($id)
    {
        $settings = Setting::findOrFail($id);

        // Delete custom images
        if ($settings->logo && $settings->logo != 'default-logo.png') {
            $logoPath = public_path('uploads/settings/' . $settings->logo);
            if (File::exists($logoPath)) {
                File::delete($logoPath);
            }
        }

        if ($settings->favicon && $settings->favicon != 'default-favicon.ico') {
            $faviconPath = public_path('uploads/settings/' . $settings->favicon);
            if (File::exists($faviconPath)) {
                File::delete($faviconPath);
            }
        }

        // Reset to default values
        $settings->update([
            'app_name' => 'Your App Name',
            'phone_number' => '',
            'whatsapp_number' => '',
            'address' => '',
            'alternative_address' => null,
            'email' => '',
            'logo' => 'default-logo.png',
            'favicon' => 'default-favicon.ico',
            'facebook_link' => null,
            'instagram_link' => null,
            'youtube_link' => null,
            'x_id' => null,
            'google_map_link' => null,
            'working_hours' => null,
            'trade_license' => null,
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings reset to default successfully!');
    }
}