<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('council_name');
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->unique(['province', 'municipality', 'barangay']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('workspace_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('sk_position')->nullable();
            $table->string('role')->default('participant')->index();
            $table->string('account_status')->default('active')->index();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->string('category');
            $table->text('objectives')->nullable();
            $table->string('venue')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('registration_deadline')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('image_path')->nullable();
            $table->text('requirements')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_public')->default(false)->index();
            $table->timestamps();
            $table->unique(['workspace_id', 'slug']);
            $table->index(['workspace_id', 'status', 'starts_at']);
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('venue')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_public')->default(false)->index();
            $table->timestamps();
            $table->index(['workspace_id', 'starts_at']);
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('body');
            $table->string('image_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_public')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['workspace_id', 'slug']);
            $table->index(['workspace_id', 'status', 'published_at']);
        });

        Schema::create('program_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->unique(['program_id', 'email']);
            $table->index(['workspace_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_registrations');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('events');
        Schema::dropIfExists('programs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('workspace_id');
            $table->dropColumn(['phone', 'sk_position', 'role', 'account_status']);
        });
        Schema::dropIfExists('workspaces');
    }
};
