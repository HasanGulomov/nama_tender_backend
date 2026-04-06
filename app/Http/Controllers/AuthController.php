<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Muvaffaqiyatli ro‘yxatdan o‘tdingiz',
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'user'    => $user
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);


        $user = User::where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email, yoki parol xato!'], 401);
        }

        return response()->json([
            'message' => 'Xush kelibsiz!',
            'token'   => $user->createToken('auth_token')->plainTextToken,
            'user'    => $user
        ]);
    }


    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'username' => 'string|max:255',
            'email'    => 'string|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->username = $request->username ?? $user->username;
        $user->email = $request->email ?? $user->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Ma’lumotlar muvaffaqiyatli yangilandi',
            'user'    => $user
        ]);
    }


    public function delete(Request $request)
    {
        $user = $request->user();


        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Akkaunt o‘chirildi'
        ], 200);
    }
}
