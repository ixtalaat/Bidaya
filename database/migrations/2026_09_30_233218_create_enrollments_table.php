<?php

use App\Enums\EnrollmentStatus;
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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->date('enrollment_date');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('monthly_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('status', 32)->default(EnrollmentStatus::Active->value)->index();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['group_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
