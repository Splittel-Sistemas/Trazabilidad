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
        Schema::table('role_user', function (Blueprint $table) {
            /*$table->foreignId('user_id')->constrained()->after('id')->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->after('user_id')->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_user', function (Blueprint $table) {
            //
        });
    }
};
