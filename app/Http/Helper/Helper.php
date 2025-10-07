<?php
 
namespace App\Http\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 
use App\Models\User_group;
use App\Models\Employee;
use App\Models\EmployeeSaving;
use App\Models\AccountGeneralTeacher;
use App\Models\Account;
use App\Models\Activity;
use App\Models\ActivityDetail;
use App\Models\Currency;
use App\Models\Drag;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;




class Helper
{
    static function checkPermission($screen=0){
        $groups=User_group::select('group_id')->where('user_id', auth()->user()->id)->get();
        $arr=array();
        foreach($groups as $row)
            array_push($arr,$row->group_id);
        if(sizeof($arr)==0)
            return false;
        $tool=DB::select('SELECT * FROM `tool_groups` WHERE `tool_id` = '.$screen.' AND `group_id` IN ('.implode(',',$arr).')');
        $arr1=array();
        foreach($tool as $row)
            array_push($arr1,$row->tool_id);
        if(sizeof($arr1)==0)
            return false;
        return true;

    } 

    public function getUserInboxes(){
        return Message::where('receiver_id', auth()->user()->id)
        ->whereNull('parent_id') // Only show top-level messages
        ->latest()
        ->get(); 
    }

    static function timeAgo($timestamp) {
        $current_time = time();
        $timestamp = strtotime($timestamp);
        $time_diff = $current_time - $timestamp;
    
        if ($time_diff < 60) {
            return 'منذ '.$time_diff . ' ثواني';
        } elseif ($time_diff < 3600) {
            return 'منذ '.floor($time_diff / 60) . ' دقائق';
        } elseif ($time_diff < 86400) {
            return 'منذ '.floor($time_diff / 3600) . ' ساعات';
        } elseif ($time_diff < 2592000) {
            return 'منذ '.floor($time_diff / 86400) . ' أيام';
        } else {
            return 'منذ '.floor($time_diff / 2592000) . ' شهور';
        }
    }

    //accounting functions
    //ارجاع الحساب الذي سينفذ علية القيود حسب نوع الحساب
    static function get_full_account_no($account_type){

        $row=DB::select('SELECT * FROM `account_cheques` WHERE `account_type_cd` = '.$account_type);
        return $row[0];
    }
    // CREATING ACCOUNT AUTOMATICALLY
    static function add_new_account($teacher_no, $currency , $agent_no , $account_name ='' ){
        $account_name_v = '';
        $count_opend_account = 0;

        if( $account_name == '' ){
            $account_name_v = Employee::find($agent_no)->full_name;
        }else{
            $account_name_v = $account_name ;
        }

        $count_opend_account = Account::where('teacher_no', $teacher_no)
        ->where('currency_no', $currency)
        ->where('agent_no', $agent_no)
        ->count();

        if($count_opend_account >= 1)
            return -1;


        $accountType = AccountGeneralTeacher::where('teacher_no',$teacher_no)->first();

        $account = Account::create(
            [
                'teacher_no' => $teacher_no ,
                'currency_no' => $currency ,
                'agent_no'=>$agent_no ,
                'account_name'=>$account_name_v.' - '.$accountType->teacher_name,
                'account_type' => $accountType->account_type_id ,
                'teacher_id' =>$accountType->id,
            ]
        ); 

        return $account->id;
        
    }

    //دالة القيود الرئيسية - تخزن في الجدول الرئيسي
    // CLASS   1=MAKASSAH, 0=CASH
    public static function add_activity_master
    ($activity_id, $document_no, $class , $referance , $inverse, $screen_id, $user_id ){
        $err_no = 0;
        $err_message = '';
        $rante_no = 0;
        $app_status_v = 0; // هل النظام مفتوح ام مغلق 0 مفتوح
        
        $class=DB::table("activities_maintanence")->where("id",$activity_id)->first()->class;

        //منع تنفيذ القيد إذا كان النظام مغلق، إلا في حالة الإغلاق اليومي أو الشهري

        
        if($screen_id != 999){ // 999 شاشة اغلاق الارباح والخسائر 
            
            $constants = DB::table("general_constants_tb")
                ->whereIn("name", ["state_program "])
                ->pluck("value", "name");
            $app_status_v = $constants["state_program"] ;


            if($app_status_v == 1){
                $err_no = -100;
                $err_message = 'النظام في حالة إغلاق';
            }
        } 

        if($app_status_v == 0){

            $activity = Activity::create(
                [
                    'activity_id' => $activity_id ,
                    'document_no' => $document_no ,
                    'referance'=>$referance ,
                    'class' => $class ,
                    'inverse' =>$inverse,
                    'screen_id' =>$screen_id,
                    'user_id' =>$user_id,
                   
                ]
            ); 

            return json_encode(
                [
                    "activity_id"=>$activity->id,
                    "err_no" =>$err_no,
                    "err_message" =>$err_message
                ]
            );
            
        }
    }

    public static function add_activity_detail(
        $activity_master_id, $teacher_no, $currency_no, $agent_no,
        $from_to, $amount, $price, $amount_alue, $describe
    ){
        $account_type_v = 0;   
        // check input parameters
        if($activity_master_id > 0 and  $amount > 0 
            and in_array($from_to,[0,1]) and $teacher_no > 0 
            and  $currency_no > 0 and $agent_no > 0 
            and $price > 0 and $amount_alue > 0
        ){
            $account_type_v=DB::table("account_general_teachers")->where("teacher_no",$teacher_no)->first()->account_type_id;
            
            if($from_to == 0){
                if(in_array($account_type_v,[1,4,7]) ){
                    $account_type_v = 1;
                }else{
                    $account_type_v = -1;
                }
            }else{
                if(in_array($account_type_v,[1,4,7]) ){
                    $account_type_v = -1;
                }else{
                    $account_type_v = 1;
                }
            }
            $account = Account::where('teacher_no' , $teacher_no)
                ->where('currency_no',$currency_no)
                ->where('agent_no' ,$agent_no)
                ->first();

            ActivityDetail::create(
                [
                    'activity_id' => $activity_master_id ,
                    'account_id' => $account->id ,
                    'teacher_no'=>$teacher_no ,
                    'currency_no' => $currency_no ,
                    'agent_no' =>$agent_no,
                    'amount' =>$amount,
                    'amount_value' =>$amount_alue,
                    'price' =>$price,
                    'from_to' =>$from_to,
                    'bluse' =>$account_type_v,
                    'describe' =>$describe,
                    'current_amount' =>$account->all_amount + ($amount * $account_type_v),
                
                ]
            ); 
        
        }else{
            return false;
        }
        return true;
    }

    //---------------------------------------------
    //دالة عكس القيود في يوم آخر
    // يتم إنشاء قيد جديد ، مع تغيير منه ليصبح له والعكس صحيح
    // ويبقى كلا القيدين(لاجديد والقديم) ظاهران في كشف الحساب

    public static function reverse_activity_other_date(
        $activity_master_id, $description, $user_id
    ){
        $app_status_v = 0; // هل النظام مفتوح ام مغلق 0 مفتوح
        $inv_desc = 'عكس';
        $newActivityMasterId = 0;

        $activityMaster = Activity::where('id',$activity_master_id)
                            ->where('inverse',0)->first();
        $activityDetails = ActivityDetail::where('activity_id',$activity_master_id)->get();

        if(!empty($description) or $description != ''){
            $inv_desc = $description;
        }

        $constants = DB::table("general_constants_tb")
                ->whereIn("name", ["state_program "])
                ->pluck("value", "name");
        $app_status_v = $constants["state_program"] ;
        if($app_status_v == 1){
            return false;
        }

        if($app_status_v == 0){
            
            //inserting the old data as reversid transaction of the master record
            $activityMasterNew = Helper::add_activity_master($activityMaster->activity_id, $activityMaster->document_no, 
                    $activityMaster->class , $inv_desc .' '. $activityMaster->referance  , 
                    0, $activityMaster->screen_id, $user_id );
            $activityMasterNew= json_decode( $activityMasterNew);
          
            $newActivityMasterId = $activityMasterNew->activity_id;
            
            if($activityMasterNew->err_no < 0 )
            {
                return false;
            }


            foreach ($activityDetails as $activityRow) {
                $dt_from_to_v = $activityRow->from_to ;

                if($dt_from_to_v == 0){
                    $dt_from_to_v = 1;
                }else{
                    $dt_from_to_v = 0;
                }

                $activityDetailNew = Helper::add_activity_detail(
                    $newActivityMasterId, $activityRow->teacher_no, $activityRow->currency_no, 
                    $activityRow->agent_no,$dt_from_to_v, $activityRow->amount, $activityRow->price, 
                    $activityRow->amount_value, $inv_desc .' '.$activityRow->describe
                );
                if(!$activityDetailNew ){
                    log::error('Helper::add_activity_details' .$activityDetailNew);
                   return false;
                }
            } // foreache

        


        } //if app_status open
        
        return true;
    }

    // البروسيجر الرئيسي لإلغاء الحركات المالية وخاصة سندات الصرف والقبض
    //حيث يتم التعديل على  جدول السندات انة ملغي
    
    public static function reverse_activity($activity_master_id, $user_id){
        $err_no = 0;
        $err_message= '';
        $status = true;
        $activity_type_id ;
        $document_no ;

        $constants = DB::table("general_constants_tb")
            ->whereIn("name", ["state_program "])
            ->pluck("value", "name");
        $app_status_v = $constants["state_program"] ;
        if($app_status_v == 1){
            $err_no = -1;
            $err_message = 'النظام في حالة إغلاق';
            $status = false;
        }

        $activity = Activity::where('id',$activity_master_id)
            ->where('inverse', 0)
            ->whereIn('activity_id',[16,1,2,3,4,6,7,8,9]) // انواع القيود التي يمكن الغاءها من هذا البروسيجر
            ->first();
        if(!$activity){
            $err_no = -2;
            $err_message = 'رقم القيد غير موجود او تم إلغائة';
            $status = false;
        }

        if(in_array($activity->activity_id,[2,3])){ // سند صرف او سند قبض
            
            $reverseActivity =Helper::reverse_activity_other_date($activity_master_id, '' , auth()->user()->id );
            if(!$reverseActivity )
            {
                $err_no = -3;
                $err_message = 'يوجد مشكلة في إلغاء القيود';
                $status = false;
            }

            Activity::where('id',$activity_master_id)
                ->where('screen_id', 1000)
                ->update([
                'inverse'=>2
            ]);

            Drag::where('id',$activity->document_no)->update([
                'inverse'=>1
            ]);

        }elseif ($activity->activity_id == 4) { //صرف من المستحقات
            $reverseActivity =Helper::reverse_activity_other_date($activity_master_id, '' , auth()->user()->id );
            if(!$reverseActivity )
            {
                $err_no = -4;
                $err_message = 'يوجد مشكلة في إلغاء القيود';
                $status = false;
            }

            Activity::where('id',$activity_master_id)
                ->where('screen_id', 38)
                ->update([
                'inverse'=>2
            ]);

            Drag::where('id',$activity->document_no)->update([
                'inverse'=>1
            ]);

            EmployeeSaving::where('drag_id',$activity->document_no)->delete();

        }elseif ($activity->activity_id == 16) { //قيد يدوي
            $reverseActivity =Helper::reverse_activity_other_date($activity_master_id, '' , auth()->user()->id );
            if(!$reverseActivity )
            {
                $err_no = -5;
                $err_message = 'يوجد مشكلة في إلغاء القيود';
                $status = false;
            }

            Activity::where('id',$activity_master_id)
                ->update([
                'inverse'=>2
            ]);
        }elseif ($activity->activity_id == 1) { //قيد رواتب
            $reverseActivity =Helper::reverse_activity_other_date($activity_master_id, '' , auth()->user()->id );
            if(!$reverseActivity )
            {
                $err_no = -6;
                $err_message = 'يوجد مشكلة في إلغاء القيود';
                $status = false;
            }

            Activity::where('id',$activity_master_id)
                ->update([
                'inverse'=>2
            ]);
        
        }elseif (in_array($activity->activity_id,[6,7,8,9])){ // الفواتير ودفعاتها
            $reverseActivity =Helper::reverse_activity_other_date($activity_master_id, '' , auth()->user()->id );
            if(!$reverseActivity )
            {
                $err_no = -5;
                $err_message = 'يوجد مشكلة في إلغاء القيود';
                $status = false;
            }

            Activity::where('id',$activity_master_id)
                ->update([
                'inverse'=>2
            ]);
        }else{
            $err_no = -7;
            $err_message = 'لم يتم الإلغاء ، نوع الحركة غير معروف';
            $status = false;
        }
       
        return json_encode(
            [
                "status" => $status,
                "err_no" =>$err_no,
                "err_message" =>$err_message
                
            ]
        );

    }
   
    //open builck of employees accounts
    public static function open_employee_accounts( $employee_id ,$category ){
        
        $saving_account  = Helper::get_full_account_no(11);  //مستحقات رواتب مؤجلة
        $retired_account = Helper::get_full_account_no(12);  // مستحقات صندوق التقاعد - حصة الموظف في الادخار
        
        $employee_account = Helper::get_full_account_no(4); // حساب رواتب الموظفين
        
        /*if($category == 1){  // ديوان
            $employee_account = Helper::get_full_account_no(4);
        }elseif($category == 2){  //تنمية
            $employee_account = Helper::get_full_account_no(5);
        }elseif($category == 3){  //الخاصة
            $employee_account = Helper::get_full_account_no(6);
        }elseif($category == 4){  //موازنة
            $employee_account = Helper::get_full_account_no(7);
        }elseif($category == 5){  //مساعدات
            $employee_account = Helper::get_full_account_no(8);
        }elseif($category == 6){  //تقاعد
            $employee_account = Helper::get_full_account_no(9);
        }elseif($category == 7){  //تكافل
            $employee_account = Helper::get_full_account_no(10);
        }
        */


        $constants = DB::table("general_constants_tb")
            ->whereIn("name", ["open_accounts_for_employees"])
            ->pluck("value", "name");

        if( $constants["open_accounts_for_employees"] == 1){
            
            $currencies = Currency::whereIn('id',[1,3])->get();

            foreach($currencies as $currency){
                
                $res_1 = Helper::add_new_account($employee_account->teacher_no, $currency->id , $employee_id  , '' );
                $res_2 = Helper::add_new_account($saving_account->teacher_no, $currency->id , $employee_id  , '' );
                $res_3 = Helper::add_new_account($retired_account->teacher_no, $currency->id , $employee_id  , '' );
            }

        } 
            
    }


    //open builck of customers accounts
    public static function open_customers_accounts( $customer_id , $name ){
        
        $custmer_account  = Helper::get_full_account_no(24);  //حساب العملاء
       

        $constants = DB::table("general_constants_tb")
            ->whereIn("name", ["open_accounts_for_customers"])
            ->pluck("value", "name");

        if( $constants["open_accounts_for_customers"] == 1){
            
            $currencies = Currency::whereIn('id',[3])->get();

            foreach($currencies as $currency){
            
                $res_1 = Helper::add_new_account($custmer_account->teacher_no, $currency->id , $customer_id  , $name );
            
            }

        } 
            
    }

    //open builck of suppliers accounts
    public static function open_suppliers_accounts( $supplier_id , $name ){
        
        $supplier_account  = Helper::get_full_account_no(23);  //حساب الموردين
       

        $constants = DB::table("general_constants_tb")
            ->whereIn("name", ["open_accounts_for_customers"])
            ->pluck("value", "name");

        if( $constants["open_accounts_for_customers"] == 1){
            
            $currencies = Currency::whereIn('id',[3])->get();

            foreach($currencies as $currency){
            
                $res_1 = Helper::add_new_account($supplier_account->teacher_no, $currency->id , $supplier_id  , $name );
            
            }

        } 
            
    }



    // get currency_no by name

    public static function get_currency_id($name){
        return Currency::where('currency_name',$name)->first()->id;
    }

    //get currency value

    public static function get_currency_value($currency_id){
        return Currency::where('id',$currency_id)->first()->price;
    }

    // get teller box info procedure
    public static function get_box_info($user_id, $currency_id){
        // ممكن في المستقبل نعرف لكل مستخدم حساب صندوق
        // حاليا يتم التنفيذ على حساب الخزنة الرئيسية
        // المفترض ان يتم تخزين رقم حساب الصندوق الفرعي مع حساب المستخدم

        $box_teacher_no = 0;
        $box_agent_no = 0;
        $box_account_id = 0;
        $box_all_amount = 0;
        $box_account_name = '';

        $user = User::where('id',$user_id)->first();
        $currency = Currency::where('id',$currency_id)->first();

        $box_account = Helper::get_full_account_no(1); // حساب الصندوق العام

        if($currency ){
            $account = Account::where('teacher_no',$box_account->teacher_no)
                ->where('currency_no',$currency_id)
                ->where('agent_no',$box_account->agent_no)
                ->first();
            
            $box_teacher_no = $account->teacher_no;
            $box_agent_no =   $account->agent_no;
            $box_account_id = $account->id;
            $box_all_amount = $account->all_amount;
            $box_account_name = $account->account_name;

        }

        return json_encode(
            [
                "box_teacher_no"=>$box_teacher_no,
                "box_agent_no" =>$box_agent_no,
                "box_account_id" =>$box_account_id,
                "box_all_amount" =>$box_all_amount,
                "box_account_name" =>$box_account_name,
            ]
        );

    }

    // procedure for check parameters for deposit and withdraw
    public static function check_drag_box_parameters(
        $activity_type_id , $teacher_no , $currency_id , $agent_no, $amount , $screen_id , $user_id
    ){
        $err_no = 0;
        $err_message= '';

        $account_id = 0;
        $box_account_id = 0;
        $box_teacher_no = 0;
        $box_agent_no = 0;
        $box_account_name = '';
        $box_all_amount = 0;
        $deposit = 0;

        $status = true;

        if($amount <= 0){
            $err_no = -1;
            $err_message= 'ادخل قيمة أكبر من الصفر';
            $status = false;
            goto LAST;
        }

        $constants = DB::table("general_constants_tb")
            ->whereIn("name", ["state_program "])
            ->pluck("value", "name");
        $app_status_v = $constants["state_program"] ;
        if($app_status_v == 1){
            $err_no = -2;
            $err_message = 'النظام في حالة إغلاق';
            $status = false;
        }

        // فحص هل يتعامل الصندوق مع الاستاذ ام لاء
        $allawed_teacher = AccountGeneralTeacher::where('teacher_no', $teacher_no)
            ->where('forbid_box_cd', 1)
            ->count();
        if($allawed_teacher == 0){
            $err_no = -3;
            $err_message = 'الحساب لا يتعامل مع سندات الصرف والقبض';
            $status = false;
            goto LAST;
        }

        // فحص الصلاحيات فى الشاشة
        if(!Helper::checkPermission($screen_id)){
            $err_no = -4;
            $err_message = 'لا يوجد صلاحيات للتنفيذ على شاشة سندات الصرف والقبض';
            $status = false;
            goto LAST;
        }

        $box_account = Helper::get_box_info($user_id, $currency_id);
        $box_account = json_decode($box_account);

        $box_account_id = $box_account->box_account_id;
        $box_teacher_no = $box_account->box_teacher_no;
        $box_agent_no   = $box_account->box_agent_no;
        $box_account_name = $box_account->box_account_name;
        $box_all_amount = $box_account->box_all_amount;

        if($activity_type_id == 2){  // 2 سند قبض
            $deposit = 1;
        }
        else{
            $deposit = -1; 
        }

        $account_id = Account::where('teacher_no', $teacher_no)
            ->where('agent_no',$agent_no)
            ->where('currency_no',$currency_id)
            ->first()->id;

        // يفحص هل رصيد الصندوق به رصيد ام لا في حالة سندات الصرف
        if($activity_type_id != 2){  // 2 سند قبض
            if($box_all_amount - $amount < 0){
                $err_no = -5;
                $err_message = 'رصيد الصندوق لا يسمح';
                $status = false;
                goto LAST;
            }
        }

        LAST:
            
        return json_encode(
            [
                "status" => $status,
                'account_id' => $account_id ,
                "box_account_id" =>$box_account_id,
                "box_teacher_no"=>$box_teacher_no,
                "box_agent_no" =>$box_agent_no,
                "box_account_name" =>$box_account_name,
                "err_no" =>$err_no,
                "err_message" =>$err_message
                
            ]
        );
        
    }
    
    
    //$activity_name  اسم الساحب او المودع
    public static function store_drag_box(
        $activity_type_id, $activity_name , $date_in, $teacher_no , $currency_id , $agent_no , $amount, $screen_id , $user_id
    ){
        $err_no = 0;
        $err_message= '';
        $from_prm_v ;
        $to_prm_v ;
        $price_v ;
        $amount_value_v ;
        $notic = '';
        $status = true;

        $parameters = Helper::check_drag_box_parameters(
            $activity_type_id , $teacher_no , $currency_id , $agent_no, $amount , $screen_id , $user_id);
        $parameters=json_decode($parameters);
        if(!$parameters->status){
            return json_encode(
                [
                    "status" => $parameters->status,
                    'drag_id' =>  0,
                    "err_no" =>$parameters->err_no,
                    "err_message" =>$parameters->err_message       
                ]
            );
        }

        $price_v = Helper::get_currency_value($currency_id);
        $amount_value_v = $price_v * $amount;
        // التخزين في جدول الايداع والسحب
        $drag = Drag::create(
            [
                'account_id' => $parameters->account_id ,
                'activity_type_id' => $activity_type_id,
                'activity_date'=>$date_in,
                'amount'=>$amount,
                'refrence' => '',
                'notice' => $activity_name,
                'amount_value'=>$amount_value_v,
                'user_id' => $user_id
            ]
        );

        // القيود
        if($activity_type_id == 2){
            //إيداع - سند قبض
            $from_prm_v = 1;
            $to_prm_v = 0;
            $notic = 'سند قبض نقدي :';
        }else{
            //سحب - سند صرف
            $from_prm_v = 0;
            $to_prm_v = 1;
            $notic = 'سند صرف نقدي :';
        }

        $activityMaster =Helper::add_activity_master ($activity_type_id, $drag->id, 1 , '' , 0, $screen_id, $user_id);
        $activityMaster= json_decode( $activityMaster);
        
        if($activityMaster->err_no < 0 )
        {
            $err_no = -200;
            $err_message= 'حدث خطأ في القيود';
            $status = false;
            log::error('General Error During Execute Procedure -> helper::store_drag_box - add_activity_master:'.$activityMaster->err_no.'err_message:'.$activityMaster->err_message); 
        }
        //FROM ACCOUNT
        $activityDetails = Helper::add_activity_detail(
            $activityMaster->activity_id, $teacher_no, $currency_id, $agent_no,
            $from_prm_v, round($amount,2), $price_v,  round( $amount_value_v  ,2) ,
             $notic . ' '.$activity_name
        );
        if(!$activityDetails)
        {
            $err_no = -300;
            $err_message= 'حدث خطأ في القيود';
            $status = false;
            log::error('General Error During Execute Procedure -> helper::store_drag_box - add_activity_detail:'.$err_no); 

        }
        //TO Account
        $activityDetails = Helper::add_activity_detail(
            $activityMaster->activity_id, $parameters->box_teacher_no, $currency_id, $parameters->box_agent_no,
            $to_prm_v, round($amount,2), $price_v, 
            round( $amount_value_v  ,2)  ,
            $notic . ' '.$activity_name
        );
        if(!$activityDetails)
        {
            $err_no = -400;
            $err_message= 'حدث خطأ في القيود';
            $status = false;
            log::error('General Error During Execute Procedure -> helper::store_drag_box - add_activity_detail'.$err_no); 
        }

        Activity::where('id',$activityMaster->activity_id)
            ->where('document_no',$drag->id)
            ->update([
                'created_at' => $date_in
            ]);
        
        return json_encode(
            [
                "status" => $status,
                'drag_id' =>  $drag->id,
                "err_no" =>$err_no,
                "err_message" =>$err_message       
            ]
        );

    }

     
    
    
    
    
   
}