<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('headline');
            $table->string('hero_badge')->nullable();
            $table->text('hero_description');
            $table->longText('about_description');
            $table->string('profile_image')->nullable();
            $table->string('location')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('availability_status')->default('available')->index();
            $table->string('availability_text');
            $table->string('cv_file')->nullable();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general')->index();
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon_key')->nullable();
            $table->string('brand_color', 7)->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('overview');
            $table->longText('background')->nullable();
            $table->longText('problem')->nullable();
            $table->longText('goal')->nullable();
            $table->text('role')->nullable();
            $table->longText('process')->nullable();
            $table->longText('challenges')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('result')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('github_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_technology', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'technology_id']);
        });

        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->string('alt_text');
            $table->text('caption')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon_key')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('organization');
            $table->string('location')->nullable();
            $table->string('logo')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false)->index();
            $table->longText('description');
            $table->json('responsibilities')->nullable();
            $table->json('technologies')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->string('institution');
            $table->string('degree');
            $table->string('study_program');
            $table->string('location')->nullable();
            $table->string('logo')->nullable();
            $table->unsignedSmallInteger('start_year');
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('certificate_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('issuer');
            $table->date('issued_at');
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->string('certificate_image')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('organization')->nullable();
            $table->string('subject');
            $table->longText('message');
            $table->string('status')->default('unread')->index();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->longText('answer');
            $table->json('keywords')->nullable();
            $table->string('action_label')->nullable();
            $table->string('action_url')->nullable();
            $table->unsignedInteger('priority')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_faqs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('certificate_categories');
        Schema::dropIfExists('educations');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('project_technology');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('profiles');
    }
};
