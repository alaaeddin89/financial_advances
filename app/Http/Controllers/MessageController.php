<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Str;
use App\Http\Helper\Helper;
use Redirect;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Helper::checkPermission(47))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        if ($request->ajax()) {

            $query = Message::with('sender')->where('receiver_id', Auth::id())
          //  ->whereNull('parent_id') // Only show top-level messages
            ->latest();
                    
            $data =$query
            ->get();
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('message_status', function ($data) {
           
                    
                    if($data->status ==  1){
                        $status = '<span class="label label-inline label-light-success  font-weight-bold">مقروءة</span>';
                    }
                    else{
                        $status = '<span class="label label-inline label-light-warning   font-weight-bold">غير مقروءة</span>';
                    }
                
                    return $status;
                
                })
                ->addColumn('body_sub', function ($data) {
                    
                    return Str::limit($data->body, 50);
            
                })
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                        <a href="'.route("messages.show",$data->id).'" class="btn btn-icon btn-light-success me-2 mb-2 py-3">
                            <i class="fas fa-reply-all"></i>
                        </a>';
                    return $actionBtn;
                })

                
                ->rawColumns(['action','message_status','body_sub'])
                ->make(true);
             }
    

        return view('messages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!Helper::checkPermission(46))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");
        
        $users = User::where('id', '!=', Auth::id())->get(); // Get all users except current user
        return view('messages.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Helper::checkPermission(46))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,pdf,doc,docx|max:2048', // Adjust mimes and max size
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                Attachment::create([
                    'message_id' => $message->id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('messages.index')->with('success', 'تم الارسال بنجاح');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        if(!Helper::checkPermission(51))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        if ($message->receiver_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('messages.show', compact('message'));
    }

    public function reply(Request $request)
    {
        if(!Helper::checkPermission(51))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");
        
        $request->validate([
            'body' => 'required',
            'message_id' => 'required',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $message = Message::find($request->message_id);


        $reply = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $message->sender_id === Auth::id() ? $message->receiver_id : $message->sender_id, // Reply to the original sender
            'body' => $request->body,
            'parent_id' => $message->id,
        ]);

        $message->status = 1;
        $message->update();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                Attachment::create([
                    'message_id' => $reply->id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('messages.show', $message->id)->with('success', 'تم ارسال الرد بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
