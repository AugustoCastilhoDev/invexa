<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        auth()->user()->unreadNotifications->markAsRead();
        return view('notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    public function destroy(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back()->with('success', 'Notificação removida.');
    }
}
