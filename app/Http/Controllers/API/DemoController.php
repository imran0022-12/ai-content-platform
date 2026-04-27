<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DemoController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'type' => 'required|string'
        ]);

        // Mock response for demo
        $responses = [
            'blog' => "This is a sample blog post about: " . $request->prompt . "\n\nThis is demo content. Sign up for full access!",
            'tagline' => "Sample taglines for: " . $request->prompt . "\n1. Amazing Tagline 1\n2. Amazing Tagline 2",
            // Add more types as needed
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'result' => $responses[$request->type] ?? $responses['blog'],
                'tokens_used' => rand(100, 200)
            ]
        ]);
    }
}
