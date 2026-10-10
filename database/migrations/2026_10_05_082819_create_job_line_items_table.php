<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_line_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->string('area_item')->nullable();
            $table->decimal('length', 8, 2)->default(0);
            $table->decimal('width', 8, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('time')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_line_items');
    }
};
