<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketScanLog extends Model
{
    protected $fillable = [
        'event_id',
        'organizer_id',
        'e_ticket_id',
        'ticket_code',
        'status_type',
        'message',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function eTicket()
    {
        return $this->belongsTo(ETicket::class);
    }
}
