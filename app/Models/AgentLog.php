<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_deck_id',
        'topic_id',
        'kind',
        'message',
        'badge',
        'context_data',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'context_data' => 'array',
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
