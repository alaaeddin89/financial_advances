<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;
    protected $fillable=['name',"full_name","email","role","password","created_at","updated_at"];

    public $timestamps = true;

    protected $hidden = [
        'password',
        'remember_token',
    ];
   
    protected $casts = [
        'email_verified_at' => 'datetime',

    ];

    public function getCreatedAtAttribute($value){

        return date("Y-m-d",strtotime($value));
    }

    /**
     * العهد التي سجلت باسم هذا الموظف (المستلم للعهدة).
     */
    public function financialAdvances()
    {
        return $this->hasMany(FinancialAdvance::class, 'user_id');
    }

    /**
     * العهد التي سجلها هذا الموظف (المدير/المحاسب) كـ Issued By.
     */
    public function issuedAdvances()
    {
        return $this->hasMany(FinancialAdvance::class, 'issued_by_user_id');
    }

    /**
     * الفواتير التي رفعها هذا الموظف.
     */
    public function expenseInvoices()
    {
        return $this->hasMany(ExpenseInvoice::class, 'user_id');
    }

    // عمليات التقفيل التي قام بها الموظف
    public function performedClosures(): HasMany
    {
        return $this->hasMany(AdvanceInvoiceClosure::class, 'closed_by_user_id');
    }

    /**
     * الموردين الذين سجلهم هذا الموظف.
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'user_id');
    }

}
