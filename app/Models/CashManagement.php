<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'value',
        'note',
        'type',
    ];

    public function ShiftCashManagement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
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
