<?php

namespace Tests\Feature;

use App\Livewire\Chatbot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatbotStatusPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_answers_status_questions_in_chinese_and_links_to_the_status_page(): void
    {
        Livewire::test(Chatbot::class)
            ->call('open')
            ->set('userInput', '网站状态怎么样？')
            ->call('sendMessage')
            ->call('generateReply')
            ->assertSee('公开系统状态页面')
            ->assertSee(config('services.store.status_url'));
    }

    public function test_chatbot_answers_status_questions_in_malay(): void
    {
        Livewire::test(Chatbot::class)
            ->call('open')
            ->set('userInput', 'Apakah status sistem anda?')
            ->call('sendMessage')
            ->call('generateReply')
            ->assertSee('Status Sistem');
    }
}
