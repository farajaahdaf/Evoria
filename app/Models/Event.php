<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id',
        'category_id',
        'title',
        'slug',
        'banner_path',
        'description',
        'start_time',
        'end_time',
        'location_name',
        'address',
        'portfolio_path',
        'proposal_path',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
