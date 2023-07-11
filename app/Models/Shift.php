<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_date',
        'shift_time_start',
        'shift_time_end',
        'beginning_cash',
        'payments_cash',
        'refunds_cash',
        'payments',
        'withdrawal_amounts',
        'total_sales',
        'discounts',
        'cash_money',
        'card',
        'status',
        'company_id',
        'user_id',
    ];

    public function cashes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CashManagement::class, 'shift_id', 'id');
    }

    public function UserShift(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function CompanyShift(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CompanyCode::class, 'company_id', 'id');
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
