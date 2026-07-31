<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_recipient_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->unsignedInteger('page')->default(1);
            $table->decimal('x', 8, 4)->default(0);
            $table->decimal('y', 8, 4)->default(0);
            $table->decimal('width', 8, 4)->default(20);
            $table->decimal('height', 8, 4)->default(8);
            $table->boolean('required')->default(true);
            $table->text('value')->nullable();
            $table->timestamp('filled_at')->nullable();
            $table->timestamps();

            $table->index(['document_id', 'page']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_fields');
    }
};
