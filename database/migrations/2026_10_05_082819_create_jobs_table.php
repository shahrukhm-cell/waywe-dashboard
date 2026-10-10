<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('quote_estimate_number')->nullable()->unique();
            $table->string('job_number')->nullable()->unique();
            $table->string('po_number')->nullable();
            $table->string('job_type')->default('estimate');
            $table->string('status')->default('draft');
            $table->date('confirmed_date')->nullable();
            $table->date('booked_date')->nullable();
            $table->string('site_full_name')->nullable();
            $table->string('site_telephone')->nullable();
            $table->string('site_business')->nullable();
            $table->string('site_title')->nullable();
            $table->string('site_address')->nullable();
            $table->string('site_city')->nullable();
            $table->string('site_state')->nullable();
            $table->string('site_postal_code')->nullable();
            $table->string('billing_full_name')->nullable();
            $table->string('billing_telephone')->nullable();
            $table->string('billing_business')->nullable();
            $table->string('billing_title')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('crew_route')->nullable();
            $table->string('scheduled_day')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('scheduled_time')->nullable();
            $table->string('best_time')->nullable();
            $table->string('source')->nullable();
            $table->decimal('source_percent', 10, 2)->default(0);
            $table->decimal('source_amount', 10, 2)->default(0);
            $table->string('agent')->nullable();
            $table->decimal('agent_percent', 10, 2)->default(0);
            $table->decimal('agent_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('other_tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->decimal('invoice_total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('receipt_no')->nullable();
            $table->date('transaction_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_jobs');
    }
};
