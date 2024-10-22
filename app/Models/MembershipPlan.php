<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'max_books_allowed',
        'borrow_duration_days',
        'price',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
