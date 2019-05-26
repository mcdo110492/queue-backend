<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;

class UsersController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getAll($department_id){
        $get = User::where('department_id','=',$department_id)->get();
        return response()->json(['payload' => ['data' => $get]]);
    }
    
    public function getUsers()
    {
        $get = User::with('department')->get();

        return response()->json(['payload' => ['data' => $get]]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required|regex:/^\S*$/u|max:50|min:2|unique:users,username', //The regex rule check to ensure that there are no spaces in the username
            'password' => 'required',
            'name' => 'required|max:50|min:1',
            'role' => 'required|integer',
            'department_id' => 'required|integer'
        ]);

        $role = $request->input('role');

        if($role === 1)
        {
            return response()->json(['payload' => "Unauthorized Role Assignment"], 401);
        }

        $password = $request->input('password');
        $rawPassword = $password['password'];
        $passwordConfirm = $password['passwordConfirm'];

        if($rawPassword != $passwordConfirm){
            return response()->json(['payload' => "Password does not match"], 422);
        }

        $validatedData = [
            'username' => $request->input('username'),
            'password' => Hash::make($rawPassword),
            'name' => $request->input('name'),
            'role' => $request->input('role'),
            'department_id' => $request->input('department_id')
        ];

        $create = User::create($validatedData);

        $data = User::with('department')->findOrFail($create->id);

        return response()->json(['payload' => ['data' => $data]], 201);

    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => "required|regex:/^\S*$/u|max:50|min:2|unique:users,username,$id", //The regex rule check to ensure that there are no spaces in the username
            'name' => 'required|max:50|min:1',
            'role' => 'required|integer',
            'status' => 'required',
            'department_id' => 'required|integer'
        ]);

        $password = $request->input('password');
        $rawPassword = $password['password'];
        $passwordConfirm = $password['passwordConfirm'];

        if($rawPassword != null || $rawPassword != "") {

            if($rawPassword === $passwordConfirm){
                $validatedData = [
                    'username' => $request->input('username'),
                    'password' => Hash::make($rawPassword),
                    'name' => $request->input('name'),
                    'role' => $request->input('role'),
                    'status' => $request->input('status'),
                    'department_id' => $request->input('department_id')
                ];
                $user->update($validatedData);
            }
            else{
                return response()->json(['payload' => "Password does not match"], 422);
            }
            
        }
        else{
            $validatedData = [
                'username' => $request->input('username'),
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'status' => $request->input('status'),
                'department_id' => $request->input('department_id')
            ];
    
            $user->update($validatedData);
        }
       
        $data = User::with('department')->findOrFail($id);

        return response()->json([ 'payload' => [ 'data' => $data ]], 200);
    }

    public function checkUniqueValue(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $id = $request->input('id');

        $table = 'users';

        $validate = $this->validateUniqueValue($table, $field, $value, $id);

        return response()->json($validate, $validate['status']);
    }

    public function resetPassword(Request $request)
    {

        $request->validate([
            'username' => 'required'
        ]);

        $where = ['username' => $request->input('username')];

        $count = User::where($where)->count();

        if($count === 0)
        {
            return response()->json(['payload' => "User not found"], 404);
        }

        $rawNewPassword = str_random(7);
        $newPassword = Hash::make($rawNewPassword);
        

        User::where($where)->update(['password' => $newPassword]);

        return response()->json(['payload' => ['message' => 'Password reset successfully', 'newPassword' => $rawNewPassword]], 200);
    }
}