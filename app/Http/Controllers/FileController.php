<?php

namespace App\Http\Controllers;

use App\Models\EmployeeCategory;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {

        if ($request->ajax()) {


            $data = File::query();

            if ($request->filled("file_name")){
                $data->where("file_name","like","%".$request->file_name."%")
                    ->orwhere("original_name","like","%".$request->file_name."%");
            }

            if ($request->filled("category_id")){
                $data->where("employee_category_id",$request->category_id);
            }
            if ($request->filled("emp_id")){
                $data->where("emp_id",$request->emp_id);
            }

            if ($request->filled("note")){
                $data->where("note","like","%".$request->note."%");
             
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {

                    $actionBtn = '
                    <a  href="'.asset('storage/uploads/' . $data->file_path).'" target="_blank"  class="btn btn-icon   btn-light-primary me-2 mb-2 py-3"><i class="fa fa-eye"></i></a>
                    <a download="'. $data->file_path.'" class="btn btn-icon   btn-light-dark  me-2 mb-2 py-3" href="'.asset('storage/uploads/' . $data->file_path).'"><li class="fa fa-download"></li></a>

                    <a href="javascript:void(0)" data-id="' . $data->id . '"   class="delete btn btn-icon   btn-light-danger  me-2 mb-2 py-3"><i class="fa fa-trash"></i></a>
              ';

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories=EmployeeCategory::all();

        return  view('files.index',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories=EmployeeCategory::all();
      return view("files.create",compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        $file_name_input = $request->input('file_name');
        $employeeCategory=EmployeeCategory::find($request->category_id);

        if($file){
            $path = storage_path('app/public/uploads/');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }


            $input_file = $file->getClientOriginalName();
            $file_name = pathinfo($input_file, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();


            $fileName = $file_name . "-" . time() . "." . $extension;


            $file->move($path, $fileName);

            DB::table("employee_category_attachments")->insert([
                'emp_id' => $request->emp_id,
                "employee_category_id"=>$request->category_id,
                "file_name"=>$file_name_input,
                "file_path"=>$fileName,
                "original_name"=>$input_file,
                "note"=>$request->note,

            ]);
        }
        return redirect()->route("files.index")->with("success","تم الحفظ بنجاح");
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
