<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('prompt');
            $table->text('result');
            $table->string('type'); // blog, social, email, tagline, product, seo
            $table->string('model_used')->default('gpt-3.5-turbo');
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost_usd', 10, 8)->default(0);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
