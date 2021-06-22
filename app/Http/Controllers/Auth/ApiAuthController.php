<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Player;
use App\Models\PlayerAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiAuthController extends Controller
{
    //
    public function register (Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => 'required', 'alpha_dash', 'string', 'max:255' ,'unique:users',
            'password' => 'required' , 'alpha_dash', 'string', 'min:6' , 'confirmed',
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
            'username' => 'required' , 'alpha_dash', 'string', 'max:255',
            'password' => 'required', 'alpha_dash', 'string', 'min:6',
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

    public function playerRegister(Request $request){

        $data = request()->validate([
            'game_id' => ['required','exists:games,id'],  
            'player_name' => ['required', 'alpha_dash', 'unique:players,player_name,NULL,id,games_id,'.$request['game_id']],
            'password' => ['required', 'alpha_dash', 'string', 'min:8']       
        ]);

        return player::create([
            "player_name" => $data["player_name"],
            'password' => Hash::make($data['password']),
            "games_id" => $data["game_id"],
        ]);

    }

    public function playerLogin (Request $request) {

        $validator = Validator::make($request->all(), [
            'game_id' => ['required','exists:games,id'],
            'username' => ['required',
            Rule::exists('players','player_name')->where(function ($query) {
                return $query->where('games_id', request('game_id'));
            }),],
            'password' => ['required', 'alpha_dash', 'string', 'min:8']
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $user = PlayerAuth::where([
            
            ['player_name', $request->username],
            ['games_id', $request->game_id]

        ])->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                $userdata = [
                    'id' => $user['id'],
                    'username' => $user['player_name'],
                    'games_id' => $user['games_id']
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

    public function playerlogout (Request $request) {

        $user = $request->user();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        $response = ['message' => 'You have been successfully logged out!'];

        return response($response, 200);

    }

}
