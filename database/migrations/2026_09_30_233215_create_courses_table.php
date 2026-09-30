<?php

use App\Enums\CourseStatus;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->string('category')->nullable()->index();
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->string('level')->nullable();
            $table->unsignedSmallInteger('duration_weeks')->nullable();
            $table->unsignedSmallInteger('sessions_count')->nullable();
            $table->decimal('monthly_fee', 10, 2);
            $table->string('status', 32)->default(CourseStatus::Draft->value)->index();
            $table->text('prerequisites')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
