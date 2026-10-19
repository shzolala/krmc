<?php

namespace App\Models;

use Database\Factories\ProjectActivityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['support_project_id', 'title', 'description', 'happened_on'])]
class ProjectActivity extends Model
{
    /** @use HasFactory<ProjectActivityFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['happened_on' => 'date'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SupportProject::class, 'support_project_id');
    }
}
