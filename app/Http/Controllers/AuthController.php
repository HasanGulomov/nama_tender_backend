<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $request->validate([
            'login'    => 'required|string|unique:users,login',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'login'    => $request->login,
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        return response()->json([
            'message' => 'Muvaffaqiyatli ro‘yxatdan o‘tdingiz',
            'token'   => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

  
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('login', $request->login)->first();

        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login yoki parol xato'], 401);
        }

        return response()->json([
            'message' => 'Xush kelibsiz!',
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'user'    => $user
        ]);
    }
}