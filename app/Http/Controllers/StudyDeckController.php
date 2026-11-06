<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UploadNoteRequest;
use App\Models\StudyDeck;
use App\Services\AdaptiveAgentService;
use App\Services\DocumentParserService;
use App\Services\StudyDeckGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudyDeckController extends Controller
{
    public function __construct(
        private readonly StudyDeckGeneratorService $deckGenerator,
        private readonly DocumentParserService $documentParser,
        private readonly AdaptiveAgentService $adaptiveAgent,
    ) {}

    public function create(): View
    {
        $recentDecks = StudyDeck::latest()->take(5)->get();

        return view('decks.create', compact('recentDecks'));
    }

    public function store(UploadNoteRequest $request): RedirectResponse
    {
        if ($request->boolean('use_sample')) {
            $deck = $this->deckGenerator->createFromSample();

            return redirect()->route('decks.show', $deck)
                ->with('status', 'Pek contoh Fotosintesis berjaya disediakan!');
        }

        $noteText = (string) $request->input('note_text', '');
        $title = (string) $request->input('title', '');

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $extractedText = $this->documentParser->extractTextFromUploadedFile($file);
            $noteText = $extractedText;
            if ($title === '') {
                $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            }
        }

        if (mb_strlen(trim($noteText)) < 80) {
            return back()->withInput()->withErrors([
                'note_text' => 'Nota terlalu pendek atau tiada teks diekstrak daripada fail. Sila masukkan sekurang-kurangnya 80 aksara.',
            ]);
        }

        $deck = $this->deckGenerator->createFromText($title, $noteText);

        return redirect()->route('decks.show', $deck)
            ->with('status', 'Pek modul berjaya dicipta daripada nota anda!');
    }

    public function show(StudyDeck $deck): View
    {
        $deck->load(['topics.questions', 'topics.flashcards', 'agentLogs']);
        $nextMission = $this->adaptiveAgent->determineNextMission($deck);

        return view('decks.show', [
            'deck' => $deck,
            'topics' => $deck->topics,
            'nextMission' => $nextMission,
            'activities' => AdaptiveAgentService::ACTIVITIES,
            'logs' => $deck->agentLogs->take(12),
        ]);
    }
}
