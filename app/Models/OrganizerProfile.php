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
        'status',
    ];

    protected $casts = [
        // 'documents_path' was here but removed
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolioReviews()
    {
        return $this->hasMany(OrganizerPortfolioReview::class);
    }

    public function latestPortfolioReview()
    {
        return $this->hasOne(OrganizerPortfolioReview::class)->latestOfMany();
    }

    public function getComplianceScoreAttribute()
    {
        $review = $this->latestPortfolioReview;

        if ($review) {
            return $review->score;
        }

        if (! $this->portfolio_path) {
            return 0;
        }

        $score = 30; // Base score for basic info (company name, etc)
        
        if ($this->description) $score += 10;
        if ($this->portfolio_path) $score += 60;

        return min($score, 100);
    }

    public function getComplianceAccuracyAttribute()
    {
        if (! $this->portfolio_path && ! $this->latestPortfolioReview) {
            return 0;
        }

        return $this->latestPortfolioReview ? 100 : 98.2;
    }
}
