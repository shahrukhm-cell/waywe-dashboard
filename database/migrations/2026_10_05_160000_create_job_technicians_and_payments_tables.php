<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->unique();
        });

        Schema::create('job_technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->string('technician_name');
            $table->string('time')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('job_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->string('receipt_no')->unique();
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('account_reference')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->unsignedTinyInteger('card_expiry_month')->nullable();
            $table->unsignedSmallInteger('card_expiry_year')->nullable();
            $table->text('explanation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_payments');
        Schema::dropIfExists('job_technicians');

        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropColumn('invoice_number');
        });
    }
};
