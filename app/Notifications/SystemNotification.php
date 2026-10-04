<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    public function __construct(public string $title, public string $body, public ?string $url = null) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return config('starter.notifications.enabled') ? ['database'] : [];
    }

    /** @return array<string, string|null> */
    public function toArray(object $notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url];
    }
}
