<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AdvanceInvoiceClosure extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true; 

    protected $fillable = [
        'advance_id', 'invoice_id', 'closed_amount', 'closure_date',
        'closed_by_user_id', 'accountant_approved' , 'accountant_approval_date' , 
        'is_rejected' , 'rejection_reason' , 'rejected_by_id' , 'rejected_at'
    ];
    protected $dates = ['deleted_at']; // حقل حذف الناعم

    protected $casts = [
        'closure_date' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * العهدة التي تم تقفيل جزء منها.
     */
    public function advance()
    {
        return $this->belongsTo(FinancialAdvance::class, 'advance_id');
    }

    /**
     * الفاتورة المستخدمة في التقفيل.
     */
    public function invoice()
    {
        return $this->belongsTo(ExpenseInvoice::class, 'invoice_id');
    }

    /**
     * المحاسب الذي قام بالتقفيل.
     */
    public function closer()
    {
        return $this->belongsTo(User::class, 'accountant_approved_by_user_id');
    }
}
