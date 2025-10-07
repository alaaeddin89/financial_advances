<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Http\Helper\Helper;
use Redirect;

class BranchesController extends Controller
{
    

    public function index(Request $request){

        if(!Helper::checkPermission(31))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        if ($request->ajax()) {
            $data = Branch::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    
                        <a href="'.route("branches.edit",$data->id).'" class="btn btn-icon btn-light-primary me-2 mb-2 py-3" ><i class="fa fa-pen"></i></a>
                        <a href="javascript:void(0)" data-id="' . $data->id . '"   class="delete btn btn-icon   btn-light-danger  me-2 mb-2 py-3"><i class="fa fa-trash"></i></a>

                        ';

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("branches.index");


    }
    
    
    public function edit($id){
        if(!Helper::checkPermission(31))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $branch=Branch::findOrFail($id);
        return view("branches.edit",compact("branch"));
    }
    
    
    public function update(Request $request,$id){

        if(!Helper::checkPermission(31))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");
        
        $request->validate([
            'name_ar' => 'required|string|max:255',
        ], [
            'name.required' => 'حقل الاسم مطلوب.',
        ]);
        $branch=Branch::findOrFail($id);
        $branch->update($request->all());
        return redirect()->route("branches.index")->with("success","تم التحديث بنجاح");

    }


    public function create(){
        if(!Helper::checkPermission(31))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        return view("branches.create");
    }


    public function store(Request  $request){

        if(!Helper::checkPermission(31))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");

        $request->validate(["name_ar"=>"required"]);


        Branch::create($request->all());
        return redirect()->route("branches.index");

    }

    public function destroy(Branch $branch)
    {
        if(!Helper::checkPermission(31))
            return response()->json(['status' => 'error', 'message'=>"لايوجد صلاحيات للوصول الى النموذج المطلوب"]);


        $branch->delete();
        return response()->json(['status' => 'success' , 'message'=> " تم الحذف  بنجاح"]);

    }

}
