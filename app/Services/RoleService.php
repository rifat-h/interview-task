<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class RoleService
{

    public static function getRoleDatatable()
    {
        $roles = Role::query();

        if (!Auth::user()->hasRole(['Software Admin'])) {
            $roles = $roles->where('id', '!=', 1);
        }

        return DataTables::eloquent($roles)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $btn = "";

                if (Auth::user()->can('edit role')) {
                    $btn = $btn . '<a href="' . route('role.edit', $row->id) . '" class="edit btn btn-primary btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i>
                        Edit</a> &nbsp;';
                }

                if (Auth::user()->can('delete role')) {
                    $btn = $btn . "<button type='button' onclick='deleteRow(" . $row->id . ")' class='edit btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i>Delete</button>";
                }

                return $btn;
            })
            ->toJson();
    }


    public static function store(Request $request)
    {

        // save role
        $role = Role::create([
            'name' => $request->name
        ]);

        // assign role permission
        $role->syncPermissions($request->permissions);
    }
}
