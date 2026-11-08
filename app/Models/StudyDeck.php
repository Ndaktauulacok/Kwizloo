<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudyDeck extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'source_text',
        'total_words',
        'mode',
        'status',
        'progress',
        'error_message',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'progress' => 'integer',
    ];

    /**
     * @return HasMany<Topic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
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

    /**
     * @return HasMany<StudySession, $this>
     */
    public function studySessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }

    /**
     * @return HasMany<AgentLog, $this>
     */
    public function agentLogs(): HasMany
    {
        return $this->hasMany(AgentLog::class)->latest();
    }

    /**
     * @return HasOne<TeacherAiReport, $this>
     */
    public function teacherAiReport(): HasOne
    {
        return $this->hasOne(TeacherAiReport::class);
    }
}
