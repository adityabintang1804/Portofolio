<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        config([
            'ai.groq.api_key' => 'test-groq-key',
            'ai.gemini.api_key' => 'test-gemini-key',
            'ai.groq.url' => 'https://api.groq.test/chat/completions',
            'ai.gemini.url' => 'https://api.gemini.test/models',
        ]);
    }

    public function test_chatbot_validates_input_before_calling_provider(): void
    {
        Http::fake();
        $this->postJson('/chatbot/message', ['message' => ''])->assertUnprocessable()->assertJsonValidationErrors('message');
        $this->postJson('/chatbot/message', ['message' => str_repeat('a', 501)])->assertUnprocessable()->assertJsonValidationErrors('message');
        Http::assertNothingSent();
    }

    public function test_direct_database_question_does_not_call_ai(): void
    {
        Http::fake();
        $this->postJson('/chatbot/message', ['message' => 'Apa skill utama Aditya?'])
            ->assertOk()->assertJsonPath('source', 'database')->assertJsonFragment(['label' => 'Lihat profil']);
        Http::assertNothingSent();
    }

    public function test_local_faq_is_used_without_ai(): void
    {
        Http::fake();
        $this->postJson('/chatbot/message', ['message' => 'Apakah Aditya tersedia untuk magang?'])
            ->assertOk()->assertJsonPath('source', 'faq');
        Http::assertNothingSent();
    }

    public function test_groq_is_primary_provider_with_portfolio_context(): void
    {
        Http::fake([
            'https://api.groq.test/*' => Http::response(['choices' => [['message' => ['content' => 'Aditya relevan untuk posisi tersebut berdasarkan proyek dan skill yang tercatat.']]]]),
        ]);

        $this->postJson('/chatbot/message', ['message' => 'Apakah Aditya cocok untuk posisi junior full-stack developer?'])
            ->assertOk()->assertJsonPath('source', 'ai')->assertJsonPath('message', 'Aditya relevan untuk posisi tersebut berdasarkan proyek dan skill yang tercatat.');

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.groq.test/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer test-groq-key')
            && str_contains($request['messages'][1]['content'], 'CONTEXT PORTFOLIO'));
    }

    public function test_gemini_is_used_when_groq_fails(): void
    {
        Http::fake([
            'https://api.groq.test/*' => Http::response([], 429),
            'https://api.gemini.test/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Jawaban Gemini berdasarkan data portfolio.']]]]]]),
        ]);

        $this->postJson('/chatbot/message', ['message' => 'Rangkum profil Aditya dalam tiga kalimat.'])
            ->assertOk()->assertJsonPath('source', 'ai')->assertJsonPath('message', 'Jawaban Gemini berdasarkan data portfolio.');

        Http::assertSentCount(2);
    }

    public function test_static_fallback_is_used_when_both_providers_fail(): void
    {
        Http::fake([
            'https://api.groq.test/*' => Http::response([], 503),
            'https://api.gemini.test/*' => Http::response([], 500),
        ]);

        $this->postJson('/chatbot/message', ['message' => 'Rangkum profil Aditya secara profesional.'])
            ->assertOk()->assertJsonPath('source', 'fallback')->assertJsonCount(3, 'actions');
    }

    public function test_disabled_chatbot_returns_service_unavailable(): void
    {
        SiteSetting::query()->where('key', 'chatbot_enabled')->update(['value' => 'false']);
        $this->postJson('/chatbot/message', ['message' => 'Apa skill Aditya?'])->assertServiceUnavailable();
    }

    public function test_api_keys_are_never_shared_with_frontend(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('test-groq-key')->assertDontSee('test-gemini-key');
    }
}
