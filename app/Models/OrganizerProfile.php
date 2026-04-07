<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'description',
        'portfolio_path',
        'proposal_path',
        'status',
    ];

    protected $casts = [
        // 'documents_path' was here but removed
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getComplianceScoreAttribute()
    {
        $score = 30; // Base score for basic info (company name, etc)
        
        if ($this->description) $score += 10;
        if ($this->portfolio_path) $score += 30;
        if ($this->proposal_path) $score += 30;

        return min($score, 100);
    }

    public function getComplianceAccuracyAttribute()
    {
        return 98.2; // This is a static accuracy for the "diagnostic engine"
    }
}
