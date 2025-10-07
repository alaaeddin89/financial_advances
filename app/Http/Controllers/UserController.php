<?php

namespace App\Http\Controllers;

use App\Models\CurrantSurvey;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Result;
use App\Models\User;
use App\Models\Group;
use App\Models\User_Group;
use http\Encoding\Stream\Enbrotli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Helper\Helper;
use Redirect;

class UserController extends Controller
{
    public function index(Request  $request)
    {
        //if(!Helper::checkPermission(4))
           // return view('404');
        if(!Helper::checkPermission(13))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");
            
        if ($request->ajax()) {
            if(auth()->user()->role=='admin')
                $data = User::latest()->get();
            else
                //$data = User::whereIn('role',['accountant','hr','employee'])->latest()->get();
                $data = [];
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '<a href="' . route('users.edit', $data) . '" class="edit btn btn-icon   btn-light-primary  me-2 mb-2 py-3"><i class="fa fa-pen"></i></a>
                                    <a href="javascript:void(0)" data-id="' . $data->id . '"   class="delete btn btn-icon   btn-light-danger  me-2 mb-2 py-3"><i class="fa fa-trash"></i></a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return  view('users.index');
    }



    public  function create(){

        if(!Helper::checkPermission(14))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $user =new User();
        $gruop=Group::where('b_enabled',1)->get();
        $arr=array();

        return view("users.create",compact('user','gruop','arr'));
    }

    public  function  store(Request $request){

        if(!Helper::checkPermission(14))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $validator = Validator::make($request->all(), [
            'name'=>"required|unique:users,name",

          //  'email'=>"required|unique:users,email",
            'role'=>'required|in:admin,accountant,cashier',
            'password'=>'required|min:4'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $group=$request->my_multi_select1;
        if(!is_array($group) || sizeof($group)==0)
            if(auth()->user()->s_role=='admin')
                return response()->json(['success' => false, 'message' => 'اختر مجموعة صلاحيات']);
            else 
                return response()->json(['success' => false, 'message' => 'اختر مجموعة صلاحيات']); 
        
     //   dd($request->full_name);
        //$data=$request->except(['password']);
        $data['name']= $request->name;
        $data['full_name']=$request->full_name;
        $data['email']=$request->email;
        $data['role']=$request->role;
        $data['password']=Hash::make($request->password);
        $user=User::create($data);

        foreach($group as $row){
            $userGroup=new User_Group();
            $userGroup->user_id=$user->id;
            $userGroup->group_id=$row;
            $userGroup->save();
        } 

        if ($user){
            return response()->json(['success' => true, 'message' => "تمت العملية بنجاح"]);
        }
            return response()->json(['success' => false, 'message' => " لم تتم العملية"]);


    }


    public function  show($id){


    }





    public  function edit(User $user){

        if(!Helper::checkPermission(15))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $gruop=Group::where('b_enabled',1)->get();
        $recGroup=User_Group::where('user_id',$user->id)->get();
        $arr=array();
        foreach($recGroup as $row)
            array_push($arr,$row->group_id);
        return view("users.edit",compact('user','gruop','arr'));
    }

    public  function  update(Request $request,User $user){
        if(!Helper::checkPermission(15))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $validator = Validator::make($request->all(), [
            'name'=>"required|unique:users,name,".$user->id,
            'role'=>'required|in:admin,accountant,cashier',

        ]);


        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $group=$request->my_multi_select1;
        if(!is_array($group) || sizeof($group)==0)
            if(auth()->user()->s_role=='admin')
                return response()->json(['success' => false, 'message' => 'اختر مجموعة صلاحيات']);
            else 
                return response()->json(['success' => false, 'message' => 'اختر مجموعة صلاحيات']); 
        

        $data=$request->except(['password']);

        if ($request->password!==null) {
            $data['password'] = Hash::make($request->password);
        }


        $user->update($data);

        User_Group::where('user_id',$user->id)->delete();
        foreach($group as $row){
            $userGroup=new User_Group();
            $userGroup->user_id=$user->id;
            $userGroup->group_id=$row;
            $userGroup->save();
        } 


        

        if ($user){
            return response()->json(['success' => true, 'message' => "تمت العملية بنجاح"]);
        }
        return response()->json(['success' => false, 'message' => " لم تتم العملية"]);


    }
    public function  destroy(User $user){

        if(!Helper::checkPermission(16))
            return response()->json(['status' => 'error', 'message'=>"لايوجد صلاحيات للوصول الى النموذج المطلوب"]);


        $user->delete();
        User_Group::where('user_id',$user->id)->delete();
        return response()->json(['status' => 'success']);
    }




    public function editUserProfile(){
        
        $userdata = User::findorFail(auth()->user()->id);
        return view("users.profile",compact('userdata'));
    }

    public function updateUserProfile(Request $request){

        $userdata = User::findorFail(auth()->user()->id);

        if($request->newPass!==$request->cnewPass){
            return  redirect()->route("myProfile")->with("error"," كلمة المرور وتاكيدها غير متطابقاتان");
        }

        if ($request->newPass!==null) {
            $data['password'] = Hash::make($request->newPass);
        }


        $userdata->update($data);

        if($userdata){
            return  redirect()->route("myProfile")->with("success","تم الحفظ بنجاح");


        }
        return  redirect()->route("myProfile")->with("error","لم يتم الحفظ بنجاح");

    }

   
}
