<?php

use App\Enums\StudentStatus;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name')->index();
            $table->date('date_of_birth');
            $table->string('gender', 16);
            $table->string('photo_path')->nullable();
            $table->string('address')->nullable();
            $table->string('school')->nullable();
            $table->string('grade_level')->nullable();
            $table->string('status', 32)->default(StudentStatus::Active->value)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
