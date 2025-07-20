<?php



namespace App\Repositories\User;

use App\Interfaces\User\ProfileInterface;

class ProfileRepository implements ProfileInterface
{

    public function getUser()
    {
        /** @var \App\Models\User $user */
        return auth('sanctum')->user();
    }

    public function update(array $data)
    {
        $user = $this->getUser();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        /** @var \App\Models\User $user */
        $user->update($data);
        return $user;
    }

    public function getNotifications()
    {
        /** @var \App\Models\User $user */

        $user = $this->getUser();
        return $user->notifications()->whereNull('read_at')->latest()->get();
    }

    public function markAsRead($notificationId)
    {
        /** @var \App\Models\User $user */
        $user = $this->getUser();
        
        $user->unreadNotifications()
            ->where('id', $notificationId)
            ->first()?->markAsRead();
    }

    public function markAllAsRead()
    {
        $this->getUser()
            ->unreadNotifications
            ->markAsRead();
    }
}