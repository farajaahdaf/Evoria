<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'description',
        'documents_path',
        'status',
    ];

    protected $casts = [
        'documents_path' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
