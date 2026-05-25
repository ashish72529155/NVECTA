<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Notes List can be retrieved.
     */
    public function test_can_get_notes_list(): void
    {
        Note::create([
            'title' => 'Test Note 1',
            'content' => 'Content 1',
            'category' => 'Work',
            'color' => '#ffffff'
        ]);

        Note::create([
            'title' => 'Test Note 2',
            'content' => 'Content 2',
            'category' => 'Personal',
            'color' => '#f43f5e'
        ]);

        $response = $this->getJson('/api/notes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'category',
                        'color',
                        'summary',
                        'embedding',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'search_method'
                ]
            ])
            ->assertJson([
                'success' => true,
                'meta' => [
                    'total' => 2
                ]
            ]);
    }

    /**
     * Test single Note can be retrieved.
     */
    public function test_can_get_single_note(): void
    {
        $note = Note::create([
            'title' => 'Secrets of the Service Container',
            'content' => 'Class dependencies are injected automatically.',
            'category' => 'Code',
            'color' => '#3b82f6'
        ]);

        $response = $this->getJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $note->id,
                    'title' => 'Secrets of the Service Container',
                    'content' => 'Class dependencies are injected automatically.',
                    'category' => 'Code',
                    'color' => '#3b82f6'
                ]
            ]);
    }

    /**
     * Test non-existent note returns 404.
     */
    public function test_getting_missing_note_returns_404(): void
    {
        $response = $this->getJson('/api/notes/9999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Note not found'
            ]);
    }

    /**
     * Test Note can be created with valid inputs.
     */
    public function test_can_create_note_with_valid_data(): void
    {
        $payload = [
            'title' => 'New AI Idea',
            'content' => 'Integrate local TF-IDF semantic embeddings fallback.',
            'category' => 'Ideas',
            'color' => '#f43f5e'
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Note created successfully',
                'data' => [
                    'title' => 'New AI Idea',
                    'content' => 'Integrate local TF-IDF semantic embeddings fallback.',
                    'category' => 'Ideas',
                    'color' => '#f43f5e'
                ]
            ]);

        $this->assertDatabaseHas('notes', [
            'title' => 'New AI Idea'
        ]);
    }

    /**
     * Test creating a note fails validation.
     */
    public function test_note_creation_fails_validation_for_missing_fields(): void
    {
        $payload = [
            'title' => '',
            'content' => ''
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error'
            ])
            ->assertJsonValidationErrors(['title', 'content']);
    }

    /**
     * Test Note can be updated successfully.
     */
    public function test_can_update_note(): void
    {
        $note = Note::create([
            'title' => 'Old Title',
            'content' => 'Old Content',
            'category' => 'General'
        ]);

        $payload = [
            'title' => 'Refined Title',
            'content' => 'Refined Content',
            'category' => 'Code',
            'color' => '#3b82f6'
        ];

        $response = $this->putJson("/api/notes/{$note->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Note updated successfully',
                'data' => [
                    'id' => $note->id,
                    'title' => 'Refined Title',
                    'content' => 'Refined Content',
                    'category' => 'Code',
                    'color' => '#3b82f6'
                ]
            ]);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Refined Title'
        ]);
    }

    /**
     * Test Note can be deleted.
     */
    public function test_can_delete_note(): void
    {
        $note = Note::create([
            'title' => 'Disposable Memo',
            'content' => 'To be purged immediately.'
        ]);

        $response = $this->deleteJson("/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Note deleted successfully'
            ]);

        $this->assertDatabaseMissing('notes', [
            'id' => $note->id
        ]);
    }

    /**
     * Test AI summary generation.
     */
    public function test_can_generate_note_summary(): void
    {
        $note = Note::create([
            'title' => 'Long Lecture Memo',
            'content' => 'Laravel service containers represent a powerful tool to carry dependency bindings automatically. This decouples class bindings. Inversion of control is a great structural design choice.'
        ]);

        $response = $this->postJson("/api/notes/{$note->id}/summary");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Summary generated successfully'
            ])
            ->assertJsonStructure([
                'success',
                'summary',
                'data' => [
                    'summary'
                ]
            ]);

        $this->assertNotNull($note->fresh()->summary);
    }
}
