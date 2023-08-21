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
        Schema::create('wallets_month', function (Blueprint $table) {
            $table->id();
            $table->double('opening-balance');
            $table->double('closing_balance');
            $table->unsignedBigInteger('wallet_id');
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets_month');
    }
};
