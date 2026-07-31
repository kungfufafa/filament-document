<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emeterai_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('emeterai_id')->unique();
            $table->string('document_name')->nullable();
            $table->timestamp('stamped_at')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'stamped_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emeterai_usages');
    }
};
