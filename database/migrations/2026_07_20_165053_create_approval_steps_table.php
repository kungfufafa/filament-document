<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('approval_id')->constrained()->cascadeOnDelete();
            $table->string('approver_name');
            $table->string('approver_email');
            $table->unsignedInteger('step_order')->default(1);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();

            $table->index(['approval_id', 'step_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
