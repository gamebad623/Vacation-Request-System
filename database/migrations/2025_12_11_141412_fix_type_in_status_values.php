<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('vacation_requests')
        ->where('status', 'aproved_manager')
        ->update(['status' => 'approved_manager']);

    // Adjust the enum values (MySQL). Requires doctrine/dbal for change()
        Schema::table('vacation_requests', function ($table) {
        $table->enum('status', ['pending', 'approved_manager','rejected_manager', 'approved', 'rejected'])
              ->default('pending')
              ->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacation_requests', function ($table) {
            $table->enum('status', ['pending', 'aproved_manager','rejected_manager', 'approved', 'rejected'])
                  ->default('pending')
                  ->change();
        });

        DB::table('vacation_requests')
            ->where('status', 'approved_manager')
            ->update(['status' => 'aproved_manager']);


    }
};
