<?php

namespace App\Http\Controllers;

use Error;
use Illuminate\Http\Request;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Auth;
use App\Helper\Ui\FlashMessageGenerator;
use Spatie\Permission\Models\Permission;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (!Auth::user()->can('module')) {
            return redirect(route('home'));
        }

        if ($request->ajax()) {
            return ModuleService::getModuleDatatable();
        }

        return view('dashboard.module.module_index');
    }

    public function actionMenuIndex(Request $request, $menuId)
    {

        if (!Auth::user()->can('action menu')) {
            return redirect(route('home'));
        }

        if ($request->ajax()) {

            return ModuleService::getActionModuleDatatable($menuId);
        }

        return view('dashboard.module.action_module_index', compact('menuId'));
    }


    public function create()
    {
        if (!Auth::user()->can('create module')) {
            return redirect(route('home'));
        }

        $data = (object)[
            'permissions' => Permission::select(['id', 'name'])->where('action_menu', 0)->orderBy('order_serial')->get(),
        ];

        return view('dashboard.module.create_module', compact('data'));
    }

    public function actionMenuCreate($menuId)
    {
        if (!Auth::user()->can('action menu module')) {
            return redirect(route('home'));
        }

        $data = (object)[
            'menuId' => $menuId,
        ];

        return view('dashboard.module.create_action_module', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if (!Auth::user()->can('create module')) {
            return redirect(route('home'));
        }

        // validate
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        try{
            ModuleService::storeModule($request);
            FlashMessageGenerator::generate('primary', 'Module Successfully Added');
        }catch(Exception $e){
            FlashMessageGenerator::generate('danger', $e->getMessage());
        }

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('edit module')) {
            return redirect(route('home'));
        }

        $data = (object) [
            'permission' => Permission::findOrFail($id),
            'permissions' => Permission::select(['id', 'name'])->where('action_menu', 0)->orderBy('order_serial')->get(),
        ];

        return view('dashboard.module.edit_module', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (!Auth::user()->can('edit module')) {
            return redirect(route('home'));
        }

        // validate
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'order_no' => 'required|numeric'
        ]);

        try{
            ModuleService::updateModule($request, $id);
            FlashMessageGenerator::generate('primary', 'Module Successfully Added');
        }catch(Exception $e){
            FlashMessageGenerator::generate('danger', $e->getMessage());
        }

        return redirect(route('module.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('delete module')) {
            return redirect(route('home'));
        }

        Permission::findOrFail($id)->delete();
        return back();
    }
}
