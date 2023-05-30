<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_code',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class, 'company_id', 'id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class, 'company_id', 'id');
    }

    public function customers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Customer::class, 'company_id', 'id');
    }

    public function receipts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Receipt::class, 'company_id', 'id');
    }

    public function suggestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Suggestion::class, 'company_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Gaza')->format('Y-m-d H:i');
    }


    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Gaza')->format('Y-m-d H:i');
    }
}
