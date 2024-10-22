<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    // TODO: Check blow URL to handle controller stuff.
    // This model dont need controller, it just needs a worker to queue notifications.
    // URL: https://chatgpt.com/c/326af34b-6f84-4853-adf2-586c8945beba
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'send_type',
        'receptor'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
