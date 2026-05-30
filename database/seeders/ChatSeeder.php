<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create users
        $users = collect([
            'Alice',
            'Bob',
            'Charlie',
            'David',
            'Eve',
        ])->map(function ($name) {
            return User::create([
                'name' => $name,
                'email' => strtolower($name) . '@test.com',
                'password' => bcrypt('password'),
            ]);
        });

        // 2. Create conversations (1-to-1 chats)
        $users->each(function ($user, $index) use ($users) {

            // skip last user pairing
            if (! isset($users[$index + 1])) return;

            $otherUser = $users[$index + 1];

            $conversation = Conversation::create();

            // pivot
            $conversation->users()->attach([
                $user->id,
                $otherUser->id
            ]);

            // 3. Messages
            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'message' => "Hi I'm {$user->name}",
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $otherUser->id,
                'message' => "Hello {$user->name}, I'm {$otherUser->name}",
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'message' => "How are you?",
            ]);
        });
    }
}
