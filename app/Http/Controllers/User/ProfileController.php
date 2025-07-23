<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Interfaces\User\ProfileInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{


    public function __construct(protected ProfileInterface $profileInterface) {}

    public function showProfile()
    {
        $user = $this->profileInterface->getUser();

        if (!$user) {
            return response()->json(['message' => 'User Not Found'], 404);
        }
        return [
            'profile' => new UserResource($user)
        ];
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $data = $request->only([
            'name',
            'email',
            'password',
            'phone_number',
        ]);

        $this->profileInterface->update($data);

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function getNotifications()
    {
        $notifications = $this->profileInterface->getNotifications();

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