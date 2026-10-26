<?php

namespace App\Http\Controllers;

use App\Models\Generation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GenerationStatusController extends Controller
{
    /**
     * Progress of one of the user's background generations (flashcard deck or quiz).
     */
    public function __invoke(string $uuid): JsonResponse
    {
        $generation = Generation::query()
            ->where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'id' => $generation->uuid,
            'kind' => $generation->kind,
            'status' => $generation->status,
            'result_url' => $generation->resultUrl(),
            'error' => $generation->status === Generation::STATUS_FAILED
                ? [
                    'code' => $generation->error_code,
                    'message' => 'Generation failed. Please try a different prompt or try again later.',
                ]
                : null,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
