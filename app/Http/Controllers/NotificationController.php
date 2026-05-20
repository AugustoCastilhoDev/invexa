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

    public function unread()
    {
        $user  = auth()->user();
        $items = $user->unreadNotifications->take(10)->map(function ($n) {
            return [
                'id'      => $n->id,
                'type'    => $n->data['type']    ?? 'info',
                'title'   => $n->data['title']   ?? 'Notificação',
                'message' => $n->data['message'] ?? '',
                'url'     => $n->data['url']     ?? null,
                'icon'    => $n->data['icon']    ?? 'bi-bell',
                'time'    => $n->created_at->diffForHumans(),
            ];
        });
        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'items' => $items,
        ]);
    }

    public function markRead(string $id)
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['ok' => true]);
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
