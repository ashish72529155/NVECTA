<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed User
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed Notes for immediate rich testing
        \App\Models\Note::create([
            'title' => 'Understanding Laravel Service Containers',
            'content' => "The Laravel service container is a powerful tool for managing class dependencies and performing dependency injection. Dependency injection is a fancy phrase for: class dependencies are 'injected' into the class via the constructor or, in some cases, setter methods. It helps decouple components.",
            'category' => 'Code',
            'color' => '#3b82f6', // Indigo/Blue
        ]);

        \App\Models\Note::create([
            'title' => 'SaaS Platform for Developer Portfolios',
            'content' => "Create an elegant micro-SaaS platform that integrates directly with GitHub, dev.to, and Medium. It parses developer repositories and automatically generates a highly interactive, 3D portfolio using Three.js and Tailwind. Add dynamic analytics to show visitor counts, geographic distribution, and top clicked repositories.",
            'category' => 'Ideas',
            'color' => '#f43f5e', // Accent Rose
        ]);

        \App\Models\Note::create([
            'title' => 'Action Items for NVECTA Integration',
            'content' => "1. Finalize RESTful APIs for note taking CRUD.\n2. Integrate Gemini LLM to generate cognitive content summaries.\n3. Construct the local TF-IDF semantic query similarity logic.\n4. Polish the Tailwind dashboard SPA interface.\n5. Deploy to AWS EC2 using a secure Docker container.",
            'category' => 'Work',
            'color' => '#eab308', // Amber/Yellow
        ]);

        \App\Models\Note::create([
            'title' => 'Health and Workout Blueprint',
            'content' => "Start morning workout routine with 20 minutes of flexibility training. Follow up with a 5k jog at a steady pace (heart rate around 140 bpm). Breakfast should consist of protein-rich oatmeal with bananas, raw honey, and chia seeds for optimum muscle recovery.",
            'category' => 'Personal',
            'color' => '#10b981', // Emerald/Green
        ]);

        \App\Models\Note::create([
            'title' => 'Implementing Cosine Similarity in Python',
            'content' => "To calculate cosine similarity in Python, represent documents as vectors of word frequencies. Import numpy and use:\nsimilarity = np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b)).\nAlternatively, use sklearn's TfidfVectorizer and cosine_similarity function for massive scalability.",
            'category' => 'Code',
            'color' => '#3b82f6',
        ]);
    }
}
