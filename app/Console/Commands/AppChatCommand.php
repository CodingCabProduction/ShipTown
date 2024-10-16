<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AppChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:chat {--prompt= : The prompt to send to ChatGPT}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ask ChatGPT';

    public function handle(): void
    {
        // get the prompt argument from console
        $prompt = $this->option('prompt');

//        $this->info('Prompt: ' . $prompt);

        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])
            ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'assistant',
                    'content' => implode('. ', [
                        'You are a Laravel developer working on a project',
                        'You are developing functionality requested by user',
                        'User does not want to use any external packages, unless its unavoidable then ask me',
                        'User wants to use Laravel built-in features only where possible',
                        'Here is list of installed modules: '. Module::query()->get()->implode('name', ', '),
                        'Im am a program so i need to you to always respond in json format with key "content" and value as your response, please do not include block of code or language identifiers in the file content, not at the start or end of the file content',
                        'At a times you will need to access file when you are asked to show a file or its content, if you need file content please add "file_request" key to response with a list of files you require to access',
                    ])
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        $responseMessage = $response->json('choices.0.message.content');

        $this->info($responseMessage);
    }
}
