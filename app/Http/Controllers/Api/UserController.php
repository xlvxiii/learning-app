<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    /**
     * create user
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {
        try {
            // validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'avatar' => 'required',
                    'type' => 'required',
                    'open_id' => 'required',
                    'name' => 'required',
                    'email' => 'required',
                    // 'password' => 'required|min:6'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ]);
            }

            // validated memiliki semua field data user
            $validated = $validateUser->validated();

            $map = [];
            $map['type'] = $validated['type'];
            $map['open_id'] = $validated['open_id'];

            $user = User::where($map)->first();

            // user sudah terautentikasi atau belum
            if (empty($user->id)) {
                // jika user belum pernah ada di database
                $validated['token'] = md5(uniqid() . rand(10000, 99999));
                $validated['created_at'] = Carbon::now();

                // encrypt password
                // $validated['password'] = Hash::make($validated['password']);

                $userID = User::insertGetId($validated);

                $userInfo = User::where('id', '=', $userID)->first();

                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;

                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userID)->update(['access_token' => $accessToken]);
                return response()->json([
                    'code' => 200,
                    'msg' => 'User created successfully',
                    'data' => $userInfo,
                ], 200);
            }

            // jika user pernah login
            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated['open_id'])->update(['access_token' => $accessToken]);
            return response()->json([
                'code' => 200,
                'msg' => 'User logged in successfully',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Login user
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & password does not match with our record',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
