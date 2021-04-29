<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    //
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create([
            'username' => $request['username'],
            'password' => $request['password'],
            'is_admin' => '1',
            'remember_token' => $request['remember_token'],
        ]);
        return response($user, 200);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('username', $request->username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                $userdata = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                ];
                $response = ['token' => $token];
                return response(array_merge($userdata,$response), 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout (Request $request) {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

}
