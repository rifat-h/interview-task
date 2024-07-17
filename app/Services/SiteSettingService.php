<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingService
{

    public static function set(Request $request)
    {
        $setting = SiteSetting::findOrFail(1);

        if ($request->has('site_logo')) {

            if ($request->old_image) {
                // delete old file
                Storage::delete($request->old_image);
            }

            // upload new file
            $file = $request->file('site_logo')->store('public/sitesetting');
            $file = explode('/', $file);
            $file = end($file);
        } else {
            $file = $request->old_image;
        }

        // save data
        $setting->update([
            'website_name' => $request->website_name,
            'website_logo' => $file,
        ]);
    }
}
