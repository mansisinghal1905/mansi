<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\User;
use App\Models\Notification;
use Auth;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    function __construct()
    {
       
        $this->middleware('permission:Chat-Management', ['only' => ['index','store','create','edit','destroy','update']]);
    }
    public function index(Request $request)
    {
        return view('admin.chat.index');

    }

    
    public function getuserlist(Request $request)
    {
        try{
            if(Auth::user()->role==1){
                $userlist = User::where('role', '<>', 1)->get();
            }else{
                $userlist = User::where('role', 1)->get();
            }
            $loggedInUserId = auth()->user()->id;

            $userDetails = [];

            foreach ($userlist as $key => $value) {
            
                $chatrec = Chat::where('sender_id', $value->id)
                                ->orderBy('id', "desc")
                                ->first();

                $value->unreadmessage = $chatrec ? $chatrec->message : '';
                $value->is_read = $chatrec->is_read ?? 0;

                $chatcount = Chat::where('is_read', '0')
                                ->where('sender_id', $value->id)
                                ->count();

                $value->chatcount = $chatcount != 0 ? $chatcount : '';
                $value->createdDate = $chatrec ? $chatrec->created_at->diffForHumans() : '';
                $value->chat_status = $value->chat_status;
                $userDetails[] = $value;
            }

            usort($userDetails, function ($a, $b) {
                return $b->chatcount <=> $a->chatcount;
            });

            usort($userDetails, function ($a, $b) {
                return strtotime($b->createdDate) <=> strtotime($a->createdDate);
            });

            $chats = Chat::with('user')->get();

            return view('admin.chat.userlist', compact('chats', 'userDetails'));
        }catch(Exception $e){
            return redirect()->back();
        }
    }

    // public function getuserlist(Request $request){
    //     $userlist = User::where('role','<>',1)->get();

    //      // ID of the logged-in user
    //     $loggedInUserId = auth()->user()->id;

    //     foreach($userlist as $key=>$value){
    //         $chatrec = Chat::where('sender_id',$value->id)->orderBy('id',"desc")->first();
    //         $value->unreadmessage =$chatrec ? $chatrec->message :'';
    //         $value->is_read =$chatrec->is_read ?? 0;

    //         $chatcount = Chat::where('is_read','0')->where('sender_id',$value->id)->count();
    //         $value->chatcount = $chatcount!=0 ? $chatcount :'' ;
    //         $value->createdDate =$chatrec ? $chatrec->created_at->diffForHumans() :'';
            
    //         $value->chat_status = $value->chat_status;

    //     }
    //     // dd($userlist);
    //     // Initialize an array to store last unread messages for each user
        

    //     $chats = Chat::with('user')->get();
    //     // dd($chats);
       
    //     return view('admin.chat.userlist', compact('chats','userlist'));
    // }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message = new Chat();
        $message->message = $request->input('message');
        $message->sender_id = auth()->user()->id;
        $message->receiver_id = $request->input('receiver_id');
        $message->save();

        // Save the notification
        $notification = new Notification();
        $notification->user_id = $message->receiver_id;  // The user receiving the message
        $notification->chat_id = $message->id;
        $notification->message = "You have received a new message from " . auth()->user()->name;
        $notification->title = 'Chat Message'; 
        $notification->save();

        return response()->json([
            'success' => true,
            'message' => $message->message,
            'sender_name' => auth()->user()->name,
            'sender_avatar' => auth()->user()->avatar ?? asset('public/assets/images/no-image-available.png'),
            'created_at' => $message->created_at->format('h:i A'),
        ]);
    }

    public function showMessage(Request $request) {
    
        
        $userId = $request->id; // ID of the user we want to chat with
        // dd($userId);
        if(isset($userId)){
            $loggedInUserId = auth()->user()->id;  // The ID of the logged-in user
    
            // Find the user by 'id' and get the 'name' and 'avatar'
            $userName = User::select('name', 'avatar','chat_status')->where('id', $userId)->first();
        //   dd(Auth::user()->role);
            if(Auth::user()->role==1 && $request->is_read== true)
            {
                
                Chat::where(function ($query) use ($loggedInUserId, $userId) {
                    $query->where('sender_id', $loggedInUserId)
                          ->orWhere('receiver_id', $loggedInUserId);
                })
                ->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                          ->orWhere('receiver_id', $userId);
                })
                ->update(['is_read'=>1]);
            }
            // Retrieve chat messages between the logged-in user and the other user
            $chatMessages = Chat::where(function ($query) use ($loggedInUserId, $userId) {
                $query->where('sender_id', $loggedInUserId)
                      ->orWhere('receiver_id', $loggedInUserId);
            })
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            
            ->get();
        }
       else{
        $userName = '';
        $chatMessages= [];
       }

        
        return view('admin.chat.show', compact('userName', 'chatMessages'));
    }

 
}
