<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\FlareClient\Api;

use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|max:255|min:8',
            'device_name' => 'required',
        ]);


        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make( $request->password),
            'device_name'=>$request->device_name,
        ]);

        return $user->createToken($request->device_name)->plainTextToken;

    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|max:255|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->email)->plainTextToken;

    }
    public function logout(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user){
            $user->tokens()->delete();
        }
        return response()->noContent();
    }
}
