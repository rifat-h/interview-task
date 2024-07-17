<?php

namespace App\Http\Controllers;

use Error;
use Exception;
use Throwable;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helper\Ui\FlashMessageGenerator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (!Auth::user()->can('role')) {
            return redirect(route('home'));
        }

        if ($request->ajax()) {
            return RoleService::getRoleDatatable();
        }

        return view('dashboard.role.role_index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('create role')) {
            return redirect(route('home'));
        }

        $data = (object) [
            'permissions' => Permission::orderBy('order_serial')->get(),
        ];

        return view('dashboard.role.create_role', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);

        try{
            RoleService::store($request);
            FlashMessageGenerator::generate('primary', 'Role Successfully Added');
        }catch(Exception $e){
            FlashMessageGenerator::generate('danger', $e->getMessage());
        }

        return redirect(route('role.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        if (!Auth::user()->can('edit role')) {
            return redirect(route('home'));
        }

        $data = (object) [
            'permissions' => Permission::orderBy('order_serial')->get(),
            'role' => $role,
            'role_permissions' => $role->permissions()->select(['id'])->get()->pluck(['id'])->toArray(),
        ];

        return view('dashboard.role.edit_role', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {

        if (!Auth::user()->can('edit role')) {
            return redirect(route('home'));
        }

        // validate
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required',
        ]);


        // save role
        $role->update([
            'name' => $request->name
        ]);

        // assign role permission
        $role->syncPermissions($request->permissions);


        FlashMessageGenerator::generate('primary', 'Role Updated Successfully');

        return redirect(route('role.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {

        if (!Auth::user()->can('delete role')) {
            return redirect(route('home'));
        }

        $role->delete();

        FlashMessageGenerator::generate('primary', 'Role Deleted Successfully');

        return back();
    }
}
