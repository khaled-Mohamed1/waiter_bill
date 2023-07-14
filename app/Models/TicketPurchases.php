<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPurchases extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ticket_id',
        'price',
        'quantity',
        'discount'
    ];

    public function ProductTicketPurchases(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function TicketTicketPurchases(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }


    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->timezone('Asia/Kuwait')->format('Y-m-d H:i');
    }

    public function getUpdatedAtAttribute($value): string
    {
        return Carbon::parse($value)->timezone('Asia/Kuwait')->format('Y-m-d H:i');
    }
}
