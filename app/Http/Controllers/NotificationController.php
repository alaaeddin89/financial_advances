<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * تعليم إشعار واحد كمقروء.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // إرجاع رد JSON للـ AJAX
        return response()->json(['success' => true]);
    }

    /**
     * تعليم جميع الإشعارات غير المقروءة كمقروءة.
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        // إرجاع رد JSON للـ AJAX
        return response()->json(['success' => true]);
    }
}
