<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scrape_results', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->string('url_hash', 64);
            $table->string('status')->default('pending');
            $table->longText('html_response')->nullable();
            $table->longText('scrapingbee_response')->nullable();
            $table->boolean('used_scrapingbee')->default(false);
            $table->boolean('used_premium_proxy')->default(false);
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('url_hash');
            $table->index(['url_hash', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrape_results');
    }
};
