<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class ModuleService
{

    public static function getModuleDatatable()
    {
        $module = Permission::where('action_menu', 0);

        return DataTables::eloquent($module)
            ->addIndexColumn()
            ->addColumn('parent_menu_name', function ($row) {
                $parentId = $row->parent_id;

                $parentName = '';
                if ($parentId) {

                    $permission = Permission::findOrFail($parentId);

                    $parentName = $permission->name;
                }

                return $parentName;
            })
            ->addColumn('action', function ($row) {

                $btn = "";

                if (Auth::user()->can('edit module')) {
                    $btn = $btn . '<a href="' . route('module.edit', $row->id) . '" class="edit btn btn-primary btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a> &nbsp;';
                }


                if (Auth::user()->can('action menu')) {
                    $btn = $btn . '<a href="' . route('actionmenu.index', $row->id) . '" class="mx-1 edit btn btn-info btn-sm"><i class="fa fa-lock" aria-hidden="true"></i>Action Menu</a> &nbsp;';
                }

                if (Auth::user()->can('delete module')) {
                    $btn = $btn . "<button type='button' onclick='deleteRow(" . $row->id . ")' class='edit btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i>Delete</button>";
                }


                return $btn;
            })
            ->toJson();
    }

    public static function getActionModuleDatatable($menuId)
    {
        $module = Permission::where('action_menu', 1)->where('parent_id', $menuId);

        return DataTables::eloquent($module)
            ->addIndexColumn()
            ->addColumn('parent_menu_name', function ($row) {
                $parentId = $row->parent_id;

                $parentName = '';
                if ($parentId) {

                    $permission = Permission::findOrFail($parentId);

                    $parentName = $permission->name;
                }

                return $parentName;
            })
            // ->addColumn('is_action_menu', function ($row) {

            //     return $row->action_menu == 1 ? true : false;
            // })
            ->addColumn('action', function ($row) {

                $btn = "";

                if (Auth::user()->can('edit module')) {
                    $btn = $btn . '<a href="' . route('module.edit', $row->id) . '" class="edit btn btn-primary btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</a> &nbsp;';
                }

                if (Auth::user()->can('delete module')) {
                    $btn = $btn . "<button type='button' onclick='deleteRow(" . $row->id . ")' class='edit btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i>Delete</button>";
                }

                return $btn;
            })
            ->toJson();
    }

    public static function storeModule(Request $request)
    {
        $orderNo = $request->order_no;

        // Generate order no if not provided
        if (is_null($orderNo)) {
            $query = Permission::orderBy('order_serial', 'desc')->select('order_serial');

            if (is_null($request->parent_id)) {
                $permission = $query->whereNull('parent_id')->first();
            } else {
                $permission = $query->where('parent_id', $request->parent_id)->first();
            }

            $orderNo = ($permission->order_serial ?? 0) + 1;
        }

        // parent name checkered a
        $name = $request->name;

        $action = $request->action_menu ? 1 : 0;

        // save
        Permission::create([
            'name' => $name,
            'parent_id' => $request->parent_id,
            'order_serial' => $orderNo,
            'action_menu' => $action,
            'url' => $request->url,
            'icon' => $request->icon,
            'icon_color' => $request->icon_color,
        ]);
    }

    public static function updateModule(Request $request, $id)
    {

        $orderNo = $request->order_no;
        $name = $request->name;
        $action = $request->action_menu ? 1 : 0;

        // save

        // Permission::where('id', $id)->first()->update([
        Permission::findOrFail($id)->update([
            'name' => $name,
            'parent_id' => $request->parent_id,
            'order_serial' => $orderNo,
            'action_menu' => $action,
            'url' => $request->url,
            'icon' => $request->icon,
            'icon_color' => $request->icon_color,
        ]);
    }
}
