<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name_ar', 'name_en', 'tax_id_no', 'commercial_register_no',
        'phone', 'national_address', 'building_number' , 'sub_number', 'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    
    /**
     * الموظف الذي سجل المورد.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * الفواتير التي قدمها هذا المورد.
     */
    public function invoices()
    {
        return $this->hasMany(ExpenseInvoice::class);
    }
}
