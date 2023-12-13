<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_total',
        'total',
        'customer_id',
        'number',
        'date',
        'due_date',
        'discount',
        'reference',
        'terms_and_condition'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all of the invoice_items for the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
