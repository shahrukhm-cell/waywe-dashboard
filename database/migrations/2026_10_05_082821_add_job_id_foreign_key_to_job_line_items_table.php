<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $foreignKeyExists = collect(Schema::getForeignKeys('job_line_items'))
            ->contains(fn (array $foreignKey) => $foreignKey['columns'] === ['job_id']
                && $foreignKey['foreign_table'] === 'service_jobs');

        if ($foreignKeyExists) {
            return;
        }

        Schema::table('job_line_items', function (Blueprint $table) {
            $table->foreign('job_id')->references('id')->on('service_jobs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        $foreignKeyExists = collect(Schema::getForeignKeys('job_line_items'))
            ->contains(fn (array $foreignKey) => $foreignKey['columns'] === ['job_id']
                && $foreignKey['foreign_table'] === 'service_jobs');

        if (! $foreignKeyExists) {
            return;
        }

        Schema::table('job_line_items', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });
    }
};
