<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $data = $notification->data;
        $url  = $data['url'] ?? route('home');
        return redirect($url);
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Todas as notificações marcadas como lidas.');
    }
}
