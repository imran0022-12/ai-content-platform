<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class GenerateController extends Controller
{
    private $costPerToken = 0.000002;

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'type' => 'required|in:blog,social,email,tagline,product,seo'
        ]);

        $user = $request->user();

        // Check daily limit (10 generations per day for free users)
        $todayCount = Generation::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 10) {
            return response()->json([
                'success' => false,
                'message' => 'Daily limit reached (10 generations). Come back tomorrow!'
            ], 429);
        }

        // Get system prompt based on content type
        $systemPrompt = $this->getSystemPrompt($request->type);

        try {
            // Use OpenRouter API
            $client = new Client();

            $response = $client->post('https://openrouter.ai/api/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => env('APP_URL', 'http://localhost:8000'),
                    'X-Title' => 'AI Content Platform',
                ],
                'json' => [
                    'model' => 'openai/gpt-3.5-turbo', // You can also use 'meta-llama/llama-3.2-3b-instruct:free' for free
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $request->prompt
                        ]
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7
                ],
                'timeout' => 30
            ]);

            $resultData = json_decode($response->getBody(), true);

            // Extract the generated content
            $result = $resultData['choices'][0]['message']['content'];
            $tokensUsed = $resultData['usage']['total_tokens'] ?? rand(100, 300);
            $cost = $tokensUsed * $this->costPerToken;

            // Save to database
            $generation = Generation::create([
                'user_id' => $user->id,
                'prompt' => $request->prompt,
                'result' => $result,
                'type' => $request->type,
                'model_used' => 'openrouter-gpt-3.5-turbo',
                'tokens_used' => $tokensUsed,
                'cost_usd' => $cost,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $generation->id,
                    'result' => $result,
                    'tokens_used' => $tokensUsed,
                    'cost_usd' => $cost,
                    'type' => $generation->type
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OpenRouter Error: ' . $e->getMessage());

            // Fallback to mock response if API fails
            $fallbackResult = $this->generateMockResponse($request->prompt, $request->type);
            $tokensUsed = rand(100, 300);
            $cost = $tokensUsed * $this->costPerToken;

            // Save fallback to database
            $generation = Generation::create([
                'user_id' => $user->id,
                'prompt' => $request->prompt,
                'result' => $fallbackResult,
                'type' => $request->type,
                'model_used' => 'fallback-mock',
                'tokens_used' => $tokensUsed,
                'cost_usd' => $cost,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $generation->id,
                    'result' => $fallbackResult,
                    'tokens_used' => $tokensUsed,
                    'cost_usd' => $cost,
                    'type' => $generation->type,
                    'note' => 'Using fallback response. API error: ' . $e->getMessage()
                ]
            ]);
        }
    }

    public function history(Request $request)
    {
        $generations = Generation::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $generations
        ]);
    }

    public function show($id)
    {
        $generation = Generation::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $generation
        ]);
    }

    public function destroy($id)
    {
        $generation = Generation::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $generation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Generation deleted successfully'
        ]);
    }

    public function stats(Request $request)
    {
        $user = $request->user();

        $todayCount = Generation::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        $totalCount = Generation::where('user_id', $user->id)->count();
        $totalTokens = Generation::where('user_id', $user->id)->sum('tokens_used');
        $totalCost = Generation::where('user_id', $user->id)->sum('cost_usd');

        return response()->json([
            'success' => true,
            'data' => [
                'today_count' => $todayCount,
                'total_count' => $totalCount,
                'total_tokens' => $totalTokens,
                'total_cost' => number_format($totalCost, 6)
            ]
        ]);
    }

    private function generateMockResponse($prompt, $type)
    {
        $responses = [
            'blog' => "📝 **Blog Post: " . ucfirst($prompt) . "**\n\n" .
                      "## Introduction\nWelcome to this guide about " . $prompt . ".\n\n" .
                      "## Key Benefits\n• Easy to implement\n• Cost effective\n• Great results\n\n" .
                      "*Note: Connect to OpenRouter for real AI content*",

            'social' => "🌟 Just discovered " . $prompt . "! 🚀\n\n" .
                        "This is amazing! #Trending #Success\n\n" .
                        "*Connect OpenRouter for real posts*",

            'email' => "Subject: About " . ucfirst($prompt) . "\n\n" .
                       "Dear Customer,\n\nHere's what you need to know about " . $prompt . ".\n\n" .
                       "*Connect OpenRouter for real emails*",

            'tagline' => "🎯 **Taglines for " . ucfirst($prompt) . "**\n\n" .
                         "1. \"" . ucfirst($prompt) . " - Excellence\"\n" .
                         "2. \"Your " . ucfirst($prompt) . ", Your Way\"\n" .
                         "*Connect OpenRouter for more options*",

            'product' => "**" . ucfirst($prompt) . "** - Premium Quality\n\n" .
                         "Features:\n• High performance\n• Durable design\n• Great value\n\n" .
                         "*Connect OpenRouter for real descriptions*",

            'seo' => "**SEO for " . ucfirst($prompt) . "**\n\n" .
                     "Title: Best " . ucfirst($prompt) . " Guide\n" .
                     "Description: Learn about " . $prompt . " today!\n\n" .
                     "*Connect OpenRouter for real SEO*"
        ];

        return $responses[$type] ?? $responses['blog'];
    }

    private function getSystemPrompt($type)
    {
        $prompts = [
            'blog' => 'You are a professional blog writer. Write engaging, SEO-friendly blog posts. Keep it under 300 words.',
            'social' => 'You are a social media expert. Write engaging posts for Facebook, Twitter, Instagram. Use relevant hashtags.',
            'email' => 'You are an email marketing specialist. Write professional email newsletters with subject line.',
            'tagline' => 'You are a creative copywriter. Generate 5 catchy, memorable taglines or slogans.',
            'product' => 'You are an e-commerce copywriter. Write compelling product descriptions with features and benefits.',
            'seo' => 'You are an SEO expert. Write meta titles (50-60 chars) and meta descriptions (150-160 chars).'
        ];

        return $prompts[$type] ?? $prompts['blog'];
    }
}
