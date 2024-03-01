<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function show(string $id)
    {
        return User::findOrFail($id);
    }
    public function index()
    {
        $users = User::join('departments', 'users.department_id', '=', 'departments.id')
            ->join('users_status', 'users.status_id', '=', 'users_status.id')
            ->select(
                'users.*',
                'departments.name as department',
                'users_status.name as status'
            )
            ->get();
        return response()->json($users);
    }
    public function create()
    {
        $users_status = DB::table("users_status")->get();
        $departments = DB::table("departments")->get();

        return response()->json([
            "users_status" => $users_status,
            "departments" => $departments
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            "status_id" => "required",
            "username" => "required|unique:users,username",
            "name" => "required",
            "email" => "required|email",
            "department_id" => "required",
            "password" => "required|confirmed"
        ],[
            "status_id.required" => "Nhập tình trạng",
            "username.required" => "Nhập tên tài khoản",
            "username.unique" => "Tên tài khoản đã tồn tại",
            "name.required" => "Nhập họ và tên",
            "email.required" => "Nhập email",
            "email.email" => "Email không hợp lệ",
            "department_id.required" => "Nhập phòng ban",
            "password.required" => "Nhập mật khẩu",
            "password.confirmed" => "Mật khẩu và xác nhận mật khẩu không khớp"
        ]);

        $user = $request->except(["password", "password_confirmation"]);
        $user["password"] = Hash::make($request["password"]);
        User::create($user);

        //Eloquent ORM
//        $flight = User::create([
//            "status_id" => $request["status_id"],
//            "username" => $request["username"],
//            "name" => $request["name"],
//            "email" => $request["email"],
//            "department_id" => $request["department_id"],
//            "password" => Hash::make($request["password"])
//        ]);
    }
    public function edit($id)
    {
        $users = User::find($id);

        $users_status = DB::table("users_status")->get();
        $departments = DB::table("departments")->get();
        return response()->json([
            "users" => $users,
            "users_status" => $users_status,
            "departments" => $departments
        ]);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "status_id" => "required",
            "username" => "required|unique:users,username,".$id,//Lưu ý
            "name" => "required",
            "email" => "required|email",
            "department_id" => "required"
        ],[
            "status_id.required" => "Nhập tình trạng",
            "username.required" => "Nhập tên tài khoản",
            "username.unique" => "Tên tài khoản đã tồn tại",
            "name.required" => "Nhập họ và tên",
            "email.required" => "Nhập email",
            "email.email" => "Email không hợp lệ",
            "department_id.required" => "Nhập phòng ban"
        ]);

        User::find($id)->update(
            [
                "status_id" => $request["status_id"],
                "username" => $request["username"],
                "name" => $request["name"],
                "email" => $request["email"],
                "department_id" => $request["department_id"],
            ]
        );

        if($request["change_password"] == true){
            $validated = $request->validate([
                "password" => "required|confirmed"
            ],[
                "password.required" => "Nhập mật khẩu",
                "password.confirmed" => "Mật khẩu và xác nhận mật khẩu không khớp"
            ]);

            User::find($id)->update(
                [
                    "password" => Hash::make($request["password"]),
                    "change_password_at" => NOW()
                ]
            );
        }
    }
    public function delete($id)
    {
        $users = User::findOrFail($id);
        if($users)
            $users->delete();
        else
            return response()->json('Not found!', 404 );
        return response()->json(null);
    }
}
