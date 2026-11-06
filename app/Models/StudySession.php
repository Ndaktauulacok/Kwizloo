<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_deck_id',
        'topic_id',
        'activity_type',
        'score',
        'right_count',
        'wrong_count',
        'best_combo',
        'duration_seconds',
    ];

    /**
     * @return BelongsTo<StudyDeck, $this>
     */
    public function studyDeck(): BelongsTo
    {
        return $this->belongsTo(StudyDeck::class);
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
