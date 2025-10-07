<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'supplier_id', 'amount', 'file_path', 'status', 'invoice_type', 'invoice_no',
        'used_amount', 'invoice_date', 'description', 'is_general_expense'
    ];

    protected $dates = ['invoice_date'];

    protected $casts = [
        'invoice_date'  => 'date:Y-m-d',
        'is_general_expense' => 'boolean',

    ];

    /**
     * الموظف الذي قدم الفاتورة.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * المورد المرتبط بهذه الفاتورة.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    
    /**
     * سجلات التقفيل التي استخدمت هذه الفاتورة.
     */
    public function closures()
    {
        return $this->hasMany(AdvanceInvoiceClosure::class, 'invoice_id');
    }

    /**
     * الفروع المستفيدة من هذه الفاتورة (علاقة كثير-لكثير).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'expense_invoice_branch', 'expense_invoice_id', 'branch_id');
    }

}
