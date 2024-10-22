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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->constrained('guardians')->cascadeOnDelete();
            $table->string('guardian_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('value', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->char('type',1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
