<?php

namespace App\Http\Controllers;

use Error;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Services\SiteSettingService;
use App\Helper\Ui\FlashMessageGenerator;
use App\Http\Requests\SiteSettingRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $data = (object)[];

        return view('dashboard.layouts.home', compact('data'));
    }

    public function siteSettingView()
    {

        $siteSetting = SiteSetting::findOrFail(1);

        $data = (object)[
            'settings' => $siteSetting,
            'form' => $siteSetting->EditForm(),
        ];

        return view('dashboard.SiteSetting', compact('data'));
    }

    public function siteSetting(SiteSettingRequest $request)
    {

        try {
            SiteSettingService::set($request);
            FlashMessageGenerator::generate('primary', 'Site Setting Updated');
        } catch (Error $e) {
            FlashMessageGenerator::generate('danger', $e->getMessage());
        }

        return back();
    }
}
