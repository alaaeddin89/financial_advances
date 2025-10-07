<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\FinancialAdvance;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Helper\Helper;
use Redirect;
use Carbon\Carbon;



class DashboardController extends Controller
{

  public function __construct(){
        //$this->middleware('auth');
  }


  public function index(){
    

    $employees = [] ; /*DB::table('employees')
    ->select(
      'category',
      DB::raw('count(id)  as category_count'),
      )
      ->where('deleted_at','=',null)        
      ->groupBy('category')
      ->get(); */
    
    $chart_employees_series=array();
    $chart_employees_label=array();

    foreach($employees as $emp){
      array_push($chart_employees_series,$emp->category_count);
      array_push($chart_employees_label,$emp->category);
    }

    $chart_employees_series = json_encode($chart_employees_series);
    $chart_employees_label = json_encode($chart_employees_label);
  
    //--------------------------------

    $currentYear = Carbon::now()->year;
    $startOfYear = Carbon::createFromDate($currentYear,1,1)->startOfMonth();
    $endOfYear = Carbon::createFromDate($currentYear,12,31)->endOfMonth();
   
    $salaries = [] ; /* DB::table('employees_salary')
    ->select(
      //DB::raw('DATE_FORMAT(created_at, "%Y-%m") as created_at'),
      'month',
      DB::raw('round(SUM(amount),2) as total_salary'), 
      )
      ->where('status','!=','وقف')
      ->whereBetween('created_at',[$startOfYear,$endOfYear])
      ->groupBy('month')
      ->orderBy('month')
      ->get(); */
    

    $chart_salaries_series=array();
    $chart_salaries_label=array();

    foreach($salaries as $sal){
      array_push($chart_salaries_series,$sal->total_salary);
      array_push($chart_salaries_label,$sal->month.'/'.$currentYear);
    }

    $chart_salaries_series = json_encode($chart_salaries_series);
    $chart_salaries_label = json_encode($chart_salaries_label);
    
            
    //---------------------------------


    $advances = FinancialAdvance::where('status', '=', 'Pending')
                ->where('user_id', auth()->id())
                ->latest()->get();

    $cashier = User::where('role', '=', 'cashier')->get();
                


    $permission=array();
    array_push($permission,Helper::checkPermission(84)); //near to retirments employees index [0]



    return view("dashboard",compact("cashier",'permission',
                                    'chart_employees_series','chart_employees_label',
                                    'chart_salaries_series','chart_salaries_label',
                                    'advances')) ;

  }
}
