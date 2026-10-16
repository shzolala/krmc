<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'destination_id',
    'club_id',
    'coach_profile_id',
    'guide_type',
])]
class DestinationGuide extends Model
{
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function coachProfile(): BelongsTo
    {
        return $this->belongsTo(CoachProfile::class);
    }
}
