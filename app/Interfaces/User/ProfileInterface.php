<?php

namespace App\Interfaces\User;

interface ProfileInterface
{
    public function getUser();
    public function update(array $data);
    public function getNotifications();

    public function markAsRead($notificationId);
    public function markAllAsRead();
}