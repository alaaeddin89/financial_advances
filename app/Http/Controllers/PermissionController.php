<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tool;
use App\Models\Group;
use App\Models\tool_group;
use App\Models\user_group;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Redirect;


class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function checkPermission($screen=0){
        $navTitle='لوحة التحكم';
        $groups=User_Group::select('group_id')->where('user_id', auth()->user()->id)->get();
        
        $arr=array();
        foreach($groups as $row)
            array_push($arr,$row->group_id);
        if(sizeof($arr)==0)
            return false;
        $tool=DB::select('SELECT * FROM `tool_groups` WHERE `tool_id` = '.$screen.' AND `group_id` IN ('.implode(',',$arr).')');
       // dd($tool);
        $arr1=array();
        foreach($tool as $row)
            array_push($arr1,$row->tool_id);
        if(sizeof($arr1)==0)
            return false;
        return true;

    }
 /*   public function index()
    {
        $group=Group::all();
        $navTitle='نظام الصلاحيات';
        return view('permission.panel',compact('navTitle'));
    } */

    public function group()
    { 
        if(!$this->checkPermission(2))
            return redirect('/');
        $navTitle='إدارة المجموعات'; 
        $group=Group::where('b_enabled',1)->get();
        return view('permission.panel',compact('group','navTitle'));
    }

    public function pergroup($id=0)
    {  
        if(!$this->checkPermission(12))
            return redirect('/');
       
        $s_role=array();
        $s_role[1]='admin'; 
        $s_role[2]='accountant'; 
        $s_role[3]='cashier'; 

      
        
        $group=Group::find($id);
        $navTitle='إدارة المجموعات' . '-' . $group->name; 
        $tool=Tool::where('i_type','1')->get();
       // $user=User::where('role',$s_role[$id])->get();
        $user=User::all();
        $selectedTool=Tool_Group::select('tool_id')->where('tool_groups.group_id', $id)->get();
        $selectedUser=User_Group::select('user_id')->where('user_groups.group_id', $id)->get();
        $arr=array();
        foreach($selectedTool as $row)
            array_push($arr,$row->tool_id);
        $arr1=array();
        foreach($selectedUser as $row)
            array_push($arr1,$row->user_id);
        foreach($tool as $row)
            $row->sub=Tool::where('i_parent_id',$row->id)->orderby('i_order','asc')->get();     
        return view('permission.pergroup',compact('group','tool','arr','user','arr1','navTitle'));
    }
    
    public function doPergroup(Request $request)
    {
        
        if(!$this->checkPermission(12))
            return redirect('/');
        $group_id=$request->id;
        $screen=$request->my_multi_select1;
        $users=$request->my_multi_select2; 
        Tool_Group::where('group_id',$group_id)->delete();
        User_Group::where('group_id',$group_id)->delete();
        foreach($screen as $row){
            $toolGroup=new Tool_Group();
            $toolGroup->group_id=$group_id;
            $toolGroup->tool_id= $row;
            $toolGroup->save();
        }
        foreach($users as $row){
            $userGroup=new User_Group();
            $userGroup->group_id=$group_id;
            $userGroup->user_id= $row;
            $userGroup->save();
        }
        return redirect('/group'); ;
    }

    public function doGroup(Request $request)
    {
        if(!$this->checkPermission(2))
            return redirect('/');
        $group=Group::find($request->id);
        if($group){
            $group->name=$request->name;
            $group->b_enabled=1;
            $group->update();
        }
        else{
            $group=new Group();
            $group->name=$request->name;
            $group->b_enabled=1;
            $group->save();
        } 
        return Redirect::back() ;
    }
    public function delGroup (Request $request)
    {
        if(!$this->checkPermission(2))
            return redirect('/');
            
        ToolGroup::where('group_id',$request->id)->delete();
        UserGroup::where('group_id',$request->id)->delete();
        $group=Group::find($request->id);  
            $group->b_enabled=0; 
            $group->update();
        return Redirect::back() ;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
