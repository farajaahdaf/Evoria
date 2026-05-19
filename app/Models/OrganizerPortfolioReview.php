<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerPortfolioReview extends Model
{
    protected $fillable = [
        'organizer_profile_id',
        'portfolio_path',
        'score',
        'risk_level',
        'breakdown',
        'findings',
        'extracted_text',
        'template_version',
        'analyzed_at',
        'error_message',
    ];

    protected $casts = [
        'breakdown' => 'array',
        'findings' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function organizerProfile()
    {
        return $this->belongsTo(OrganizerProfile::class);
    }
}
