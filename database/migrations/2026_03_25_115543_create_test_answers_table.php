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
        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_session_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->integer('column_number');
            $table->integer('row_number');
            $table->integer('answer_value');
            $table->integer('correct_value')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->boolean('is_revised')->default(false);
            $table->integer('revised_count')->default(0);
            $table->integer('time_taken_seconds')->nullable();
            $table->integer('line_marker')->nullable();
            $table->timestamps();

            $table->foreign('test_session_id')->references('id')->on('test_sessions')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('pauli_questions')->onDelete('cascade');

            $table->index(['test_session_id', 'column_number', 'row_number']);
            $table->index(['test_session_id', 'line_marker']);
            $table->index('is_correct');
            $table->index(['test_session_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_test_answers');
    }
};
