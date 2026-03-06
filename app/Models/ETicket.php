<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ETicket extends Model
{
    protected $fillable = [
        'order_item_id',
        'ticket_code',
        'qr_code_path',
        'status',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
