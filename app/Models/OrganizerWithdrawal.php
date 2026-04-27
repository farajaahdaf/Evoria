<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerWithdrawal extends Model
{
    protected $fillable = [
        'organizer_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
}
