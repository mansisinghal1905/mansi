<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Mail;
use DB;


class NotificationController extends Controller
{
    public function fetchNotifications()
    {
        // Fetch notifications from the database
        $notifications = Notification::where('user_id', auth()->id())
                                    ->where('id_read','0')
                                    ->latest()
                                    ->take(10)
                                    ->get();
        // dd($notifications);
        $notificationData = [];
        foreach ($notifications as $notification) {
            $getUser = User::where("id",$notification->user_id)->first();

            $notificationData[] = [
                'id' => $notification->id,
                'user_id' => $getUser->name,
                'title' => $notification->title,
                'message' => $notification->message,
                'time' => $notification->created_at->diffForHumans(),
            ];
        }
        return view('admin.notifications.list',compact('notificationData'));
    }

    public function getNotificationCount()
    {
        try{
        $unreadCount = Notification::where('id_read','0')->where('user_id', auth()->id())->count();
        // dd($unreadCount);
        return response()->json(['unreadCount' => $unreadCount,'status' => true]);
        }catch(Exception $e){
            // dd($e);
            return response()->json(['unreadCount' => 0,'status' => false]);

        }
       
        
    }

    public function markAsRead(Request $request)
    {
        $id = $request->id;
        $updateData = notification::where("user_id",auth()->id());
        
        if($id !=0){
        $updateData = $updateData->where("id",$id);
        }
        $updateData = $updateData->update(['id_read'=>1]);

        return response()->json(['success' => true]);
    }
    

    public function deleteNotification(Request $request)
    {
        $id = $request->id;
        $notification = Notification::find($id);
        if ($notification && $notification->user_id == auth()->id()) {
            $notification->delete();
        }

        return response()->json(['success' => true]);
    }

}