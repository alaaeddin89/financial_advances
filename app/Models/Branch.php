<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar', 
        'name_en'
    ];

    /**
     * الفواتير المرتبطة بهذا الفرع.
     */
    public function expenseInvoices()
    {
        // الربط عبر الجدول الوسيط expense_invoice_branch
        return $this->belongsToMany(ExpenseInvoice::class, 'expense_invoice_branch', 'branch_id', 'expense_invoice_id');
    }
}
