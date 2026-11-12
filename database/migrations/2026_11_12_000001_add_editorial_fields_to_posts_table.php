<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->string('category', 50)->default('Tech')->after('slug')->index();
            $table->string('cover_image', 500)->nullable()->after('meta_description');
            $table->longText('source_body')->nullable()->after('source_url');
            $table->string('ai_model', 120)->nullable()->after('source_body');
            $table->string('tone', 50)->nullable()->after('ai_model');
        });

        Schema::table('post_fetch_logs', function (Blueprint $table): void {
            $table->string('ai_model', 120)->nullable()->after('source_url');
            $table->string('tone', 50)->nullable()->after('ai_model');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropColumn(['category', 'cover_image', 'source_body', 'ai_model', 'tone']);
        });

        Schema::table('post_fetch_logs', function (Blueprint $table): void {
            $table->dropColumn(['ai_model', 'tone']);
        });
    }
};
