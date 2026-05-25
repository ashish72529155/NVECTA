<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected ?string $geminiKey;
    protected ?string $openaiKey;

    public function __construct()
    {
        // Try getting keys from config or request headers for flexibility
        $this->geminiKey = config('services.gemini.key') ?: request()->header('X-Gemini-Key');
        $this->openaiKey = config('services.openai.key') ?: request()->header('X-OpenAI-Key');
    }

    /**
     * Set dynamic API keys (e.g. from request headers)
     */
    public function setKeys(?string $geminiKey, ?string $openaiKey): self
    {
        if ($geminiKey) $this->geminiKey = $geminiKey;
        if ($openaiKey) $this->openaiKey = $openaiKey;
        return $this;
    }

    /**
     * Generate an AI summary for a note's content.
     */
    public function generateSummary(string $content): string
    {
        if (empty(trim($content))) {
            return "Note content is empty.";
        }

        // Try Gemini API first
        if ($this->geminiKey) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->geminiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => "Provide a concise, engaging summary of the following note. Keep it under 3 sentences:\n\n" . $content]
                                ]
                            ]
                        ]
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $summary = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($summary) {
                        return trim($summary);
                    }
                }
                Log::warning('Gemini Summary generation failed: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Gemini Summary Exception: ' . $e->getMessage());
            }
        }

        // Try OpenAI API next
        if ($this->openaiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->openaiKey}",
                    'Content-Type' => 'application/json'
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "Provide a concise, engaging summary of the following note. Keep it under 3 sentences:\n\n" . $content
                        ]
                    ],
                    'max_tokens' => 100
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $summary = $data['choices'][0]['message']['content'] ?? null;
                    if ($summary) {
                        return trim($summary);
                    }
                }
                Log::warning('OpenAI Summary generation failed: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('OpenAI Summary Exception: ' . $e->getMessage());
            }
        }

        // Fallback: Pure PHP Heuristic Extractive Summarization
        return $this->generateLocalSummary($content);
    }

    /**
     * Generate embedding vector for a note's text content.
     */
    public function generateEmbedding(string $text): ?array
    {
        if (empty(trim($text))) {
            return null;
        }

        // Try Gemini Embedding API
        if ($this->geminiKey) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key={$this->geminiKey}", [
                        'model' => 'models/text-embedding-004',
                        'content' => [
                            'parts' => [
                                ['text' => $text]
                            ]
                        ]
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $values = $data['embedding']['values'] ?? null;
                    if (is_array($values)) {
                        return $values;
                    }
                }
                Log::warning('Gemini Embedding failed: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('Gemini Embedding Exception: ' . $e->getMessage());
            }
        }

        // Try OpenAI Embedding API
        if ($this->openaiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->openaiKey}",
                    'Content-Type' => 'application/json'
                ])->post('https://api.openai.com/v1/embeddings', [
                    'model' => 'text-embedding-3-small',
                    'input' => $text
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $values = $data['data'][0]['embedding'] ?? null;
                    if (is_array($values)) {
                        return $values;
                    }
                }
                Log::warning('OpenAI Embedding failed: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('OpenAI Embedding Exception: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Local summary generator using sentence scoring (extractive summarization).
     */
    protected function generateLocalSummary(string $content): string
    {
        // Clean text
        $content = strip_tags($content);
        
        // Split into sentences using punctuation boundaries
        $sentences = preg_split('/(?<=[.?!])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        
        if (count($sentences) <= 2) {
            return "[Offline Summary] " . $content;
        }

        // Simple word frequency scoring
        $words = str_word_count(strtolower($content), 1);
        $stopWords = ['the', 'is', 'at', 'which', 'on', 'and', 'a', 'to', 'in', 'of', 'for', 'it', 'i', 'that', 'you', 'he', 'she', 'they', 'we', 'this', 'with', 'are', 'was', 'were', 'be', 'an', 'as', 'but', 'by'];
        
        $wordFrequencies = [];
        foreach ($words as $word) {
            if (!in_array($word, $stopWords) && strlen($word) > 2) {
                $wordFrequencies[$word] = ($wordFrequencies[$word] ?? 0) + 1;
            }
        }

        // Score sentences based on frequency of their words
        $sentenceScores = [];
        foreach ($sentences as $index => $sentence) {
            $sentenceWords = str_word_count(strtolower($sentence), 1);
            $score = 0;
            foreach ($sentenceWords as $w) {
                if (isset($wordFrequencies[$w])) {
                    $score += $wordFrequencies[$w];
                }
            }
            // Normalize by sentence length to avoid bias towards extremely long sentences
            $sentenceScores[$index] = count($sentenceWords) > 0 ? $score / count($sentenceWords) : 0;
        }

        // Sort by score and keep the top 2 sentences
        arsort($sentenceScores);
        $topIndices = array_slice(array_keys($sentenceScores), 0, 2);
        sort($topIndices); // Sort back to chronological order

        $summarySentences = [];
        foreach ($topIndices as $idx) {
            $summarySentences[] = trim($sentences[$idx]);
        }

        return "[Offline Summary] " . implode(' ', $summarySentences);
    }
}
