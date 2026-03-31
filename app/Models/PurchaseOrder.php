<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    // Tambahkan atau pastikan baris ini ada:
    protected $fillable = [
        'po_number',    // Ini yang bikin error tadi
        'supplier_id',
        'order_date',
        'status',
        'notes',
        'total_amount'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}