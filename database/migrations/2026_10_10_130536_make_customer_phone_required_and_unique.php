<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        $duplicate = DB::table('customers')
            ->select('phone')
            ->whereNotNull('phone')
            ->whereRaw("TRIM(phone) != ''")
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException('Cannot make customer phone unique while duplicate phone numbers exist: ' . $duplicate->phone);
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS customers_phone_unique ON customers (phone)');

            return;
        }

        $phoneUniqueIndexExists = collect(Schema::getIndexes('customers'))
            ->contains(fn (array $index) => $index['unique'] && $index['columns'] === ['phone']);

        if ($phoneUniqueIndexExists) {
            return;
        }

        Schema::table('customers', function ($table) {
            $table->unique('phone', 'customers_phone_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('customers')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS customers_phone_unique');

            return;
        }

        Schema::table('customers', function ($table) {
            $table->dropUnique('customers_phone_unique');
        });
    }
};
