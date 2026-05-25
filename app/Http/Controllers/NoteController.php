<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Services\AIService;
use App\Services\TfidfSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class NoteController extends Controller
{
    protected AIService $aiService;
    protected TfidfSearchService $tfidfSearch;

    public function __construct(AIService $aiService, TfidfSearchService $tfidfSearch)
    {
        $this->aiService = $aiService;
        $this->tfidfSearch = $tfidfSearch;
    }

    /**
     * Display a listing of the notes (with pagination and hybrid semantic search).
     *
     * GET /api/notes
     */
    public function index(Request $request)
    {
        $limit = intval($request->query('limit', 10));
        if ($limit < 1) $limit = 10;
        if ($limit > 100) $limit = 100; // Safeguard

        $query = $request->query('search');

        // Setup custom headers if keys are provided dynamically from frontend
        if ($request->hasHeader('X-Gemini-Key') || $request->hasHeader('X-OpenAI-Key')) {
            $this->aiService->setKeys(
                $request->header('X-Gemini-Key'),
                $request->header('X-OpenAI-Key')
            );
        }

        if ($query) {
            $allNotes = Note::all();
            
            if ($allNotes->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'meta' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $limit,
                        'total' => 0,
                        'search_method' => 'none'
                    ]
                ]);
            }

            $searchMethod = 'tfidf';
            $results = [];

            // Attempt AI Vector Embedding Semantic Search
            $queryEmbedding = $this->aiService->generateEmbedding($query);
            
            // Check if we successfully got an embedding AND we have notes with embeddings
            $notesWithEmbeddings = $allNotes->filter(fn($n) => !empty($n->embedding));

            if ($queryEmbedding && $notesWithEmbeddings->isNotEmpty()) {
                $searchMethod = 'vector';
                $scoredNotes = [];
                
                foreach ($allNotes as $note) {
                    $score = 0.0;
                    if (!empty($note->embedding)) {
                        $score = $this->cosineSimilarityFloatVectors($queryEmbedding, $note->embedding);
                    } else {
                        // If this specific note doesn't have an embedding yet, generate one on the fly
                        // to keep the index fresh, or fall back to TF-IDF comparison for it.
                        try {
                            $noteEmbedding = $this->aiService->generateEmbedding($note->title . "\n" . $note->content);
                            if ($noteEmbedding) {
                                $note->embedding = $noteEmbedding;
                                $note->save();
                                $score = $this->cosineSimilarityFloatVectors($queryEmbedding, $noteEmbedding);
                            }
                        } catch (\Exception $e) {
                            // Suppress and leave score as 0
                        }
                    }
                    
                    $noteArray = $note->toArray();
                    $noteArray['search_score'] = round($score, 4);
                    $scoredNotes[] = $noteArray;
                }

                // Sort by vector similarity descending
                usort($scoredNotes, fn($a, $b) => $b['search_score'] <=> $a['search_score']);
                
                // Filter out notes with very low or zero similarity (e.g. score < 0.1)
                $results = array_filter($scoredNotes, fn($n) => $n['search_score'] > 0.1);
            } else {
                // Fall back to our pure-PHP TF-IDF Search Engine
                $results = $this->tfidfSearch->search($allNotes->toArray(), $query);
            }

            // Paginate the array manually
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $itemCollection = collect(array_values($results));
            $slice = $itemCollection->slice(($currentPage - 1) * $limit, $limit)->all();
            
            $paginatedResults = new LengthAwarePaginator(
                $slice, 
                $itemCollection->count(), 
                $limit, 
                $currentPage, 
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            return response()->json([
                'success' => true,
                'data' => $paginatedResults->items(),
                'meta' => [
                    'current_page' => $paginatedResults->currentPage(),
                    'last_page' => $paginatedResults->lastPage(),
                    'per_page' => $paginatedResults->perPage(),
                    'total' => $paginatedResults->total(),
                    'search_method' => $searchMethod
                ]
            ]);
        }

        // Standard paginated listing when no search query is provided
        $paginator = Note::latest()->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'search_method' => 'none'
            ]
        ]);
    }

    /**
     * Store a newly created note.
     *
     * POST /api/notes
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Setup custom headers if keys are provided dynamically
        if ($request->hasHeader('X-Gemini-Key') || $request->hasHeader('X-OpenAI-Key')) {
            $this->aiService->setKeys(
                $request->header('X-Gemini-Key'),
                $request->header('X-OpenAI-Key')
            );
        }

        $validated = $validator->validated();

        // Auto-generate embeddings in background if key is present
        $embedding = null;
        try {
            $embeddingText = $validated['title'] . "\n" . $validated['content'];
            $embedding = $this->aiService->generateEmbedding($embeddingText);
        } catch (\Exception $e) {
            // Log warning but continue so the API call doesn't fail
        }

        $note = Note::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'] ?? 'General',
            'color' => $validated['color'] ?? '#ffffff',
            'embedding' => $embedding
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note created successfully',
            'data' => $note
        ], 201);
    }

    /**
     * Display the specified note.
     *
     * GET /api/notes/{id}
     */
    public function show($id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $note
        ]);
    }

    /**
     * Update the specified note.
     *
     * PUT/PATCH /api/notes/{id}
     */
    public function update(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'summary' => 'nullable|string', // Allows manual summary editing if wanted
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Setup custom headers if keys are provided dynamically
        if ($request->hasHeader('X-Gemini-Key') || $request->hasHeader('X-OpenAI-Key')) {
            $this->aiService->setKeys(
                $request->header('X-Gemini-Key'),
                $request->header('X-OpenAI-Key')
            );
        }

        $data = $validator->validated();

        // Check if content or title changed to regenerate embedding
        $textChanged = false;
        if (isset($data['title']) && $data['title'] !== $note->title) {
            $note->title = $data['title'];
            $textChanged = true;
        }
        if (isset($data['content']) && $data['content'] !== $note->content) {
            $note->content = $data['content'];
            $textChanged = true;
        }

        if (isset($data['category'])) {
            $note->category = $data['category'];
        }
        if (isset($data['color'])) {
            $note->color = $data['color'];
        }
        if (isset($data['summary'])) {
            $note->summary = $data['summary'];
        }

        // If text was modified, clear or regenerate the embedding
        if ($textChanged) {
            try {
                $embeddingText = $note->title . "\n" . $note->content;
                $note->embedding = $this->aiService->generateEmbedding($embeddingText);
            } catch (\Exception $e) {
                // Keep existing or set null
                $note->embedding = null;
            }
        }

        $note->save();

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully',
            'data' => $note
        ]);
    }

    /**
     * Remove the specified note from database.
     *
     * DELETE /api/notes/{id}
     */
    public function destroy($id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found'
            ], 404);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully'
        ]);
    }

    /**
     * Generate an AI-based summary of the note content.
     *
     * POST /api/notes/{id}/summary
     */
    public function summary(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found'
            ], 404);
        }

        // Setup custom headers if keys are provided dynamically
        if ($request->hasHeader('X-Gemini-Key') || $request->hasHeader('X-OpenAI-Key')) {
            $this->aiService->setKeys(
                $request->header('X-Gemini-Key'),
                $request->header('X-OpenAI-Key')
            );
        }

        try {
            $summary = $this->aiService->generateSummary($note->content);
            $note->summary = $summary;
            $note->save();

            return response()->json([
                'success' => true,
                'message' => 'Summary generated successfully',
                'summary' => $summary,
                'data' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to compute cosine similarity between two float arrays.
     */
    protected function cosineSimilarityFloatVectors(array $vec1, array $vec2): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $length = min(count($vec1), count($vec2));
        if ($length === 0) return 0.0;

        for ($i = 0; $i < $length; $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $normA += $vec1[$i] * $vec1[$i];
            $normB += $vec2[$i] * $vec2[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
