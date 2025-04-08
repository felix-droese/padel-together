<?php

use App\Models\User;
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
        Schema::table('game_payments', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'payer_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_payments', function (Blueprint $table) {
            $table->dropForeign(['payer_id']);
            $table->dropColumn('payer_id');
        });
    }
};
