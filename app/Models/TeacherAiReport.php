<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAiReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_deck_id',
        'status',
        'summary',
        'misconceptions',
        'recommendations',
        'error_message',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'misconceptions' => 'array',
        'recommendations' => 'array',
    ];

    /**
     * @return BelongsTo<StudyDeck, $this>
     */
    public function studyDeck(): BelongsTo
    {
        return $this->belongsTo(StudyDeck::class);
    }
}
