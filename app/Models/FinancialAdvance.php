<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount', 'issued_by_user_id', 'issue_date', 
        'status', 'confirmation_date', 'closed_amount', 'remaining_balance', 'description'
    ];
    
    // تأكد من أن التاريخ issue_date يتم تخزينه ككائن Carbon
    protected $dates = ['issue_date', 'confirmation_date'];

    protected $casts = [
        'issue_date'  => 'date:Y-m-d',
        'confirmation_date'=> 'date:Y-m-d',

    ];

    /**
     * الموظف المستلم للعهدة.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * المحاسب الذي سجل العهدة.
     */
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    /**
     * سجلات التقفيل المرتبطة بهذه العهدة.
     */
    public function closures()
    {
        return $this->hasMany(AdvanceInvoiceClosure::class, 'advance_id');
    }
}
