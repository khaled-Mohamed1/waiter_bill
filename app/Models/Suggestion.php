<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'phone_number',
        'notes',
        'company_id'
    ];

    public function CompanySuggestion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
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
