<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|min:4|unique:users',
                'email' => 'required|string|email|min:4|unique:users',
                'password' => 'required|string|min:3',
                'phone_number' => 'required|string|min:5',
                'address' => 'required|string|min:4',
                'image' => 'required|string|min:4',
                'status' => 'required|in:active,pending,inactive',
                'role' => 'required|integer|in:1,2,3'
            ];

            $data = $request->all();

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return ResponseFormatter::error(
                    null,
                    $validator->errors(),
                    400
                );
            } else {

                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'image' => $request->image,
                    'phone_number' => $request->phone_number,
                    'status' => $request->status,
                    'role' => $request->role,
                ]);

                $user = User::where('email', $request->email)->first();

                $configToken = env('TOKEN_REGISTER_USERS');

                $tokenResult = $user->createToken($configToken)->plainTextToken;

                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ], 'User register successfully', 201);
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication failed', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // return response()->json('test');
            $rules = [
                'email' => 'required|string|email|min:4',
                'password' => 'required'
            ];

            $data = $request->all();

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return ResponseFormatter::error(
                    null,
                    $validator->errors(),
                    400
                );
            } else {
                $credentials = request(['email', 'password']);
                if (!Auth::attempt($credentials)) {
                    return ResponseFormatter::error([
                        'message' => 'Unauthorized'
                    ], 'Authentication failed', 401);
                }

                $user = User::where('email', $request->email)->first();

                if (!Hash::check($request->password, $user->password, [])) {
                    throw new Exception('Invalid Credentials');
                }

                $configToken = env('TOKEN_REGISTER_USERS');

                $tokenResult = $user->createToken($configToken)->accessToken;

                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ], 'User login successfully');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication failed', 500);
        }
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();

        return ResponseFormatter::success([
            $user
        ], 'Token Revoked', 200);
    }
}
