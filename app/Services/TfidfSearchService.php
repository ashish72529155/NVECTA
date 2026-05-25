<?php

namespace App\Services;

class TfidfSearchService
{
    protected array $stopWords = [
        'the', 'is', 'at', 'which', 'on', 'and', 'a', 'to', 'in', 'of', 'for', 'it', 'i', 
        'that', 'you', 'he', 'she', 'they', 'we', 'this', 'with', 'are', 'was', 'were', 
        'be', 'an', 'as', 'but', 'by', 'or', 'if', 'then', 'else', 'from', 'up', 'down',
        'my', 'your', 'his', 'her', 'their', 'our', 'about', 'how', 'why', 'what'
    ];

    /**
     * Search notes using TF-IDF similarity.
     * Returns an array of notes sorted by similarity score.
     */
    public function search(array $notes, string $query): array
    {
        if (empty(trim($query)) || empty($notes)) {
            return $notes;
        }

        // Tokenize query
        $queryTokens = $this->tokenize($query);
        if (empty($queryTokens)) {
            return $notes;
        }

        // Prepare document tokens (weighting titles 3x more than content)
        $documents = [];
        $vocabulary = [];
        
        foreach ($notes as $note) {
            $titleTokens = $this->tokenize($note['title']);
            $contentTokens = $this->tokenize($note['content']);
            
            // Weight title tokens heavier
            $weightedTokens = [];
            foreach ($titleTokens as $token => $count) {
                $weightedTokens[$token] = ($weightedTokens[$token] ?? 0) + ($count * 3);
                $vocabulary[$token] = true;
            }
            foreach ($contentTokens as $token => $count) {
                $weightedTokens[$token] = ($weightedTokens[$token] ?? 0) + $count;
                $vocabulary[$token] = true;
            }
            
            $documents[$note['id']] = $weightedTokens;
        }

        // Add query tokens to vocabulary
        foreach ($queryTokens as $token => $count) {
            $vocabulary[$token] = true;
        }

        $vocabulary = array_keys($vocabulary);
        $totalDocs = count($notes);

        // Calculate IDF for each term in vocabulary
        $idf = [];
        foreach ($vocabulary as $term) {
            $docCount = 0;
            foreach ($documents as $docTokens) {
                if (isset($docTokens[$term])) {
                    $docCount++;
                }
            }
            // Smooth IDF calculation to avoid division by zero or negative values
            $idf[$term] = log((1 + $totalDocs) / (1 + $docCount)) + 1;
        }

        // Compute TF-IDF vectors for documents
        $docVectors = [];
        foreach ($documents as $docId => $docTokens) {
            $vector = [];
            foreach ($vocabulary as $term) {
                $tf = $docTokens[$term] ?? 0;
                $vector[$term] = $tf * $idf[$term];
            }
            $docVectors[$docId] = $vector;
        }

        // Compute TF-IDF vector for query
        $queryVector = [];
        foreach ($vocabulary as $term) {
            $tf = $queryTokens[$term] ?? 0;
            $queryVector[$term] = $tf * $idf[$term];
        }

        // Compute cosine similarity between query vector and each document vector
        $scoredNotes = [];
        foreach ($notes as $note) {
            $docId = $note['id'];
            $docVector = $docVectors[$docId];
            
            $similarity = $this->cosineSimilarity($queryVector, $docVector);
            
            // Include score in the returned object/array
            $noteArray = is_object($note) ? $note->toArray() : $note;
            $noteArray['search_score'] = round($similarity, 4);
            
            // We only keep it if the similarity score is greater than zero
            // or if we want to return all, but filter out 0 score if they searched for specific terms.
            // Let's keep it if similarity > 0, otherwise set score to 0.
            $scoredNotes[] = $noteArray;
        }

        // Sort notes by search_score in descending order
        usort($scoredNotes, function ($a, $b) {
            return $b['search_score'] <=> $a['search_score'];
        });

        // Filter out zero-similarity results if it is a specific query
        // but keep at least something or return sorted list
        return array_filter($scoredNotes, function ($note) {
            return $note['search_score'] > 0;
        });
    }

    /**
     * Helper to tokenize a text string: lowercase, strip punctuation, strip stop words.
     * Returns an associative array of word => count.
     */
    protected function tokenize(string $text): array
    {
        $text = strtolower(strip_tags($text));
        // Replace punctuation with spaces
        $text = preg_replace('/[^\w\s]/', ' ', $text);
        
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $tokenCounts = [];

        foreach ($words as $word) {
            if (strlen($word) > 1 && !in_array($word, $this->stopWords)) {
                $tokenCounts[$word] = ($tokenCounts[$word] ?? 0) + 1;
            }
        }

        return $tokenCounts;
    }

    /**
     * Compute cosine similarity between two vectors.
     */
    protected function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vec1 as $term => $val1) {
            $val2 = $vec2[$term] ?? 0;
            $dotProduct += $val1 * $val2;
            $normA += $val1 * $val1;
        }

        foreach ($vec2 as $term => $val2) {
            $normB += $val2 * $val2;
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
