<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth; // Correct import
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getProfile()
    {
        // $user = Auth::user(); 

        // Alternatively you could use:
        $user = auth('sanctum')->user(); // إذا كنت تستخدم Sanctum

        if (!$user) {
            return response()->json(['message' => 'User Not Found'], 404);
        }

        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'password' => $user->password,
            'created_at' => $user->created_at,
        ];

        return response()->json(['user' => $userData]);
    }


    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'Profile updated successfully']);
    }


    public function getNotifications()
    {
        $notifications = Auth::user()->notifications()->whereNull('read_at')->latest()->get();

        return response()->json($notifications);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            // فقط إذا كان الإشعار غير مقروء
            if (is_null($notification->read_at)) {
                $notification->markAsRead();
                return response()->json(['message' => 'تم تحديد الإشعار كمقروء']);
            }
            return response()->json(['message' => 'الإشعار مقروء بالفعل']);
        }

        return response()->json(['message' => 'الإشعار غير موجود']);
    }

    public function markAllAsRead()
    {
        $unreadNotifications = Auth::user()->unreadNotifications;

        if ($unreadNotifications->isEmpty()) {
            return response()->json(['message' => 'لا توجد إشعارات غير مقروءة']);
        }

        $unreadNotifications->markAsRead();
        return response()->json(['message' => 'تم تحديد كل الإشعارات كمقروءة']);
    }
}
