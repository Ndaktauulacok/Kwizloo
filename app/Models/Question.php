<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_deck_id',
        'topic_id',
        'question',
        'options',
        'correct_index',
        'difficulty',
        'explanation',
        'quote',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'correct_index' => 'integer',
        'difficulty' => 'integer',
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

    public function isCorrect(int $index): bool
    {
        return $this->correct_index === $index;
    }

    public function getCorrectOptionTextAttribute(): string
    {
        return $this->options[$this->correct_index] ?? '';
    }
}
