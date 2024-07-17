<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use App\Helper\FormBuilder\FormBuilder;
use Illuminate\Notifications\Notifiable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function toggleStatus()
    {
        if ($this->status) {
            $this->status = False;
        } else {
            $this->status = True;
        }

        $this->save();
    }

    public static function createForm(){

        $roleOptions = Role::all()->map(function ($role) {
            return ['value' => $role->name, 'label' => $role->name];
        });

        $formInputs = [
            [
                'col' => 4,
                'type' => 'text',
                'label' => 'Name',
                'name' => 'name',
                'id' => 'name',
                'placeholder' => 'Enter Name',
                'value' => '',
                'required' => true,
            ],
            [
                'col' => 4,
                'type' => 'email',
                'label' => 'Email',
                'name' => 'email',
                'id' => 'email',
                'placeholder' => 'Enter Email',
                'value' => '',
                'required' => true,
            ],
            [
                'col' => 4,
                'type' => 'select',
                'label' => 'Roles',
                'id' => 'roles',
                'name' => 'roles[]',
                'value' => '',
                'options' => $roleOptions,
                'required' => true,
                'show_blank' => false,
                'smart' => false,
            ],
            [
                'col' => 4,
                'label' => 'Password',
                'name' => 'password',
                'id' => 'password',
                'value' => '',
                'type' => 'password',
                'placeholder' => 'Enter Password',
            ],
            [
                'col' => 4,
                'label' => 'Password',
                'name' => 'password_confirmation',
                'id' => 'password_confirmation',
                'value' => '',
                'type' => 'password',
                'placeholder' => 'Enter Password Again',
            ],
        ];

        return FormBuilder::build($formInputs);
    }

    public function EditForm(){

        $roleOptions = Role::all()->map(function ($role) {
            return ['value' => $role->name, 'label' => $role->name];
        });

        $formInputs = [
            [
                'col' => 4,
                'type' => 'text',
                'label' => 'Name',
                'name' => 'name',
                'id' => 'name',
                'placeholder' => 'Enter Name',
                'value' => $this->name,
                'required' => true,
            ],
            [
                'col' => 4,
                'type' => 'email',
                'label' => 'Email',
                'name' => 'email',
                'id' => 'email',
                'placeholder' => 'Enter Email',
                'value' => $this->email,
                'required' => true,
            ],
            [
                'col' => 4,
                'type' => 'select',
                'label' => 'Roles',
                'id' => 'roles',
                'name' => 'roles[]',
                'value' => $this->roles()->get()->pluck(['name'])->toArray()[0],
                'options' => $roleOptions,
                'required' => true,
                'show_blank' => false,
                'smart' => false,
            ]
        ];

        return FormBuilder::build($formInputs);
    }


    public static function userDatatable()
    {
        $users = User::query();

        return DataTables::eloquent($users)
            ->addIndexColumn()
            ->addColumn('roles', function ($row) {
                $roles = User::findOrFail($row->id)->roles()->get()->pluck(['name'])->toArray();
                $roles_string = implode(",", $roles);
                return $roles_string;
            })
            ->addColumn('status_ui', function ($row) {
                $status = $row->status;
                $checked = "";

                if ($status) {
                    $checked = "checked";
                }


                $slide_button = '<div class="toggle">
                  <label>
                    <input onclick="toggleStatus(' . $row->id . ')" type="checkbox" ' . $checked . '><span class="button-indecator"></span>
                  </label>
                </div>';

                if (Auth::user()->can('status user')) {
                    return $slide_button;
                }
                return "";
            })

            ->rawColumns(['status_ui', 'action'])
            ->addColumn('action', function ($row) {

                $btn = "";

                if (Auth::user()->can('view user')) {
                    $btn = $btn . '<a href="' . route('user.show', $row->id) . '" class="edit btn btn-info btn-sm m-1"><i class="fa fa-eye" aria-hidden="true"></i>
 View</a> &nbsp;';
                }

                if (Auth::user()->can('edit user')) {
                    $btn = $btn . '<a href="' . route('user.edit', $row->id) . '" class="edit btn btn-primary btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i>
 Edit</a> &nbsp;';
                }

                if (Auth::user()->can('change_password user')) {
                    $btn = $btn . '<a href="' . route('changepassword.view', $row->id) . '" class="edit btn btn-warning btn-sm"><i class="fa fa-lock" aria-hidden="true"></i>
 change_password</a> &nbsp;';
                }

                if (Auth::user()->can('delete user')) {
                    $btn = $btn . "<button type='button' onclick='deleteRow(" . $row->id . ")' class='edit btn btn-danger btn-sm'><i class='fa fa-trash' aria-hidden='true'></i>Delete</button>";
                }


                return $btn;
            })
            ->toJson();
    }

}
