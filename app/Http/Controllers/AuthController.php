<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    //
    /**
     * تسجيل الدخول واسترجاع التوكن
     */
    public function login(Request $request)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // التحقق من وجود المستخدم
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401); // Unauthorized
        }

        // إنشاء التوكن
        $token = $user->createToken('YourAppName')->plainTextToken;

        // إعادة التوكن مع رسالة نجاح
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }



    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $otp = rand(100000, 999999);
        $expiration = now()->addMinutes(4);

        // Store OTP and user data in cache consistently
        cache()->put('otp_' . $request->email, $otp, $expiration);
        Cache()->put('user_data_' . $request->email, [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => $request->password, // Will be hashed later
        ], $expiration);

        // If using Redis, store the same data there too
        // Redis::setex('user_data_' . $request->email, 240, json_encode([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'phone_number' => $request->phone_number,
        //     'password' => $request->password,
        // ]));

        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email.',
            'email' => $request->email,
            'expires_in' => 240 // seconds
        ]);
    }



    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $cachedOtp = Cache()->get('otp_' . $request->email);
        $userData = Cache()->get('user_data_' . $request->email);

        // Fallback to Redis if cache doesn't have data
        if (!$userData) {
            $redisData = Redis::get('user_data_' . $request->email);
            $userData = $redisData ? json_decode($redisData, true) : null;
        }

        if (!$cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not found. Please request a new one.',
            ], 410);
        }

        if ((int) $request->otp !== (int) $cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 400);
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User already registered with this email.',
            ], 409);
        }

        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'Registration data expired. Please start again.',
            ], 410);
        }


        // Create user with hashed password
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone_number' => $userData['phone_number'],
            'password' => bcrypt($userData['password']),
            'role' => 'user'
        ]);

        // Clean up cached data
        Cache()->forget('otp_' . $request->email);
        Cache()->forget('user_data_' . $request->email);
        // Redis::del('user_data_' . $request->email);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
            ],
            'token' => $token,
        ]);
    }
}