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
        Schema::create('vacation_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vacation_type_id')->constrained()->cascadeOnDelete();
            $table->integer('year');
            $table->integer('balance')->default(0);
            $table->integer('used')->default(0);
            $table->integer('remaining')->default(0);
            $table->timestamps();

            $table->unique(['user_id' , 'vacation_type_id' , 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_balances');
    }
};
