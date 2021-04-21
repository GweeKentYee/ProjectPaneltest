<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterNewUserController extends Controller
{
    //

    public function registerPage(){

        return view('/RegisterNewUser');

    }

    public function register(){

        //Register new user 
        
        $data = request()->validate([
            'username' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
        'username' => $data['username'],
        // 'email' => $data['email'],
        'password' => Hash::make($data['password']),
        ]);

        return redirect('/home');
    }
}
