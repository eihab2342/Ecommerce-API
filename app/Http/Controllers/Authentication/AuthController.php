<?php

namespace App\Http\Controllers\Authentication;

use App\Helpers\RateLimitHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Resources\User\UserResource;
use App\Interfaces\Auth\UserRepoInterface;
use App\Jobs\Auth\RequestOtp;
use App\Models\User;
use App\Services\Auth\UserRegisteredService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{

    public function __construct(protected UserRegisteredService $userRegisteredService) {}


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = $this->userRegisteredService->findByEmail($credentials['email']);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ]);
    }
    public function requestOtp(SignupRequest $request)
    {
        Log::info('request start');
        $data = $request->validated();
        $key = 'otp:' . $data['email'];
        $rateLimitResponse = RateLimitHelper::checkAndHit($key, 3, 60);
        if ($rateLimitResponse) {
            return $rateLimitResponse;
        }

        $otp = rand(100000, 999999);
        $expiration = now()->addMinutes(4);

        // Store OTP and user data in cache
        Cache()->put('otp_' . $data['email'], $otp, $expiration);
        Cache()->put('user_data_' . $data['email'], [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => $data['password'], // Will be hashed later
        ], $expiration);

        dispatch(new RequestOtp($data['email'], $otp));
        Log::info('request start AND Otp sent');

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email.',
            'email' => $data['email'],
            'otp' => $otp,
            'expires_in' => 240, // seconds
        ]);
    }
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $email = $validated['email'];
        $otpInput = (int) $validated['otp'];

        $cachedOtp = Cache()->get('otp_' . $email);
        $cachedUserData = Cache()->get('user_data_' . $email);

        if (!$cachedOtp || !$cachedUserData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP or registration data expired. Please request a new one.',
            ], 410);
        }

        if ($otpInput !== (int) $cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 400);
        }

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User already registered with this email.',
            ], 409);
        }

        $user = $this->userRegisteredService->create($cachedUserData);

        Cache()->forget('otp_' . $email);
        Cache()->forget('user_data_' . $email);
        RateLimiter::clear('otp:' . $email);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}