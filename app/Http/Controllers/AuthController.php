<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
        ],200);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'logged out']);
    }

    public function registration(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|string|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-1]/|regex:/[@$!%*?&.]/',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if($user){
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User successfully created'
            ], 201);

        }else{
            return response()->json([
                'success' => false
            ], 409);
        }
    }
}
