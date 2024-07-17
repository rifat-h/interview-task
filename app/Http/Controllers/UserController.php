<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Facades\Session;
use App\Helper\Ui\FlashMessageGenerator;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\PermissionChecker\PermissionChecker;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {

        if (!Auth::user()->can('user')) {
            return redirect(route('home'));
        }

        if ($request->ajax()) {
            return User::userDatatable();
        }

        return view('dashboard.user.user_index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (!Auth::user()->can('create user')) {
            return redirect(route('home'));
        }

        $data = (object)[
            'form' => User::createForm(),
        ];

        return view('dashboard.user.create_user', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {

        try {
            UserService::store($request);
            FlashMessageGenerator::generate('primary', 'User Successfully Added');
        } catch (Exception $e) {
            FlashMessageGenerator::generate('danger', $e->getMessage());
        }

        return redirect(route('user.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

        $data = (object)[
            'user' => $user,
        ];

        return view('dashboard.user.profile', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

        if (!Auth::user()->can('edit user')) {
            return redirect(route('home'));
        }

        $data = (object)[
            'user' => $user,
            'form' => $user->EditForm(),
        ];

        return view('dashboard.user.edit_user', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate((new UserUpdateRequest())->rules($id));

        // update user
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // sync role
        $user->syncRoles($request->roles);

        FlashMessageGenerator::generate('primary', 'User Successfully Updated');

        return redirect(route('user.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        if (!Auth::user()->can('delete user')) {
            return redirect(route('home'));
        }

        $user->delete();
        FlashMessageGenerator::generate('primary', 'User Deleted Successfully');
        return back();
    }

    public function toggleStatus(User $user)
    {

        if (!Auth::user()->can('status user')) {
            return redirect(route('home'));
        }

        $user->toggleStatus();

        return true;
    }

    public function ChangePassView(User $user)
    {
        if (!Auth::user()->can('change_password user')) {
            return redirect(route('home'));
        }

        return view('dashboard.user.password_change', compact('user'));
    }

    public function ChangePass(Request $request, User $user)
    {
        if (!Auth::user()->can('change_password user')) {
            return redirect(route('home'));
        }

        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        // check old password
        if (Hash::check($request->old_password, $user->password)) {
            // update new
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        FlashMessageGenerator::generate('primary', 'Password Updated Successfully');

        return redirect(route('home'));
    }

    public function ChangeOwnPassView()
    {
        $user = Auth::user();
        return view('dashboard.user.password_change', compact('user'));
    }
}
