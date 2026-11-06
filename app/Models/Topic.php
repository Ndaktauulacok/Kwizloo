<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_deck_id',
        'name',
        'summary',
        'status',
        'hp',
        'max_hp',
        'right_count',
        'wrong_count',
        'attempts',
        'losses',
        'mistakes',
        'strategy',
        'rematch_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'mistakes' => 'array',
        'strategy' => 'array',
        'rematch_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<StudyDeck, $this>
     */
    public function studyDeck(): BelongsTo
    {
        return $this->belongsTo(StudyDeck::class);
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * @return HasMany<Flashcard, $this>
     */
    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    public function getAccuracyPercentageAttribute(): int
    {
        $total = $this->right_count + $this->wrong_count;
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->right_count / $total) * 100);
    }

    public function isBoss(): bool
    {
        return $this->status === 'boss';
    }

    public function isDefeated(): bool
    {
        return $this->status === 'tewas';
    }
}
