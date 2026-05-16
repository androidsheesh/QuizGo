<?php
namespace App\Http\Controllers;

use App\Services\DeckService;
use App\Services\FlashcardGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $deckService;
    protected $generationService;

    public function __construct(DeckService $deckService, FlashcardGenerationService $generationService)
    {
        $this->deckService = $deckService;
        $this->generationService = $generationService;
    }

    public function show()
    {
        $user = Auth::user();

        $decks = $user ? $this->deckService->getLatestDecksForUser($user) : collect();

        return view('home', compact('user', 'decks'));
    }

    public function generateFromTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        $generation = $this->generationService->generateFromTopic(
            Auth::id(),
            $request->input('topic'),
            $request->input('count', 10)
        );

        return $this->buildRedirectResponse($generation, 'Your topic is being processed in the background. You can move to another page while it finishes.');
    }

    public function generateFromPdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        $generation = $this->generationService->generateFromPdf(
            Auth::id(),
            $request->file('pdf'),
            $request->input('count', 10)
        );

        return $this->buildRedirectResponse($generation, 'Your PDF is being processed in the background. You can move to another page while it finishes.');
    }

    public function generateFromText(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:20',
            'count' => 'nullable|integer|min:1|max:20',
        ]);

        $generation = $this->generationService->generateFromText(
            Auth::id(),
            $request->input('text'),
            $request->input('count', 10)
        );

        return $this->buildRedirectResponse($generation, 'Your text is being processed in the background. You can move to another page while it finishes.');
    }

    /**
     * Helper method to keep controller redirects DRY
     */
    protected function buildRedirectResponse($generation, string $successMessage)
    {
        $response = redirect()->route('mydecks')->with('flashcard_generation_id', $generation->uuid);

        // If the generation didn't fail during dispatch, add the success message
        if ($generation->status !== \App\Models\FlashcardGeneration::STATUS_FAILED) {
            $response->with('success', $successMessage);
        }

        return $response;
    }
}
