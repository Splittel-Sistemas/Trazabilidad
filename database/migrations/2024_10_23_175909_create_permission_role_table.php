<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        DB::table('permission_role')->insert([
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 1, 'permission_id' => 2],
            ['role_id' => 1, 'permission_id' => 3],
            ['role_id' => 1, 'permission_id' => 4],
            ['role_id' => 1, 'permission_id' => 5],
            ['role_id' => 1, 'permission_id' => 6],
            ['role_id' => 1, 'permission_id' => 7],
            ['role_id' => 1, 'permission_id' => 8],
            ['role_id' => 1, 'permission_id' => 9],
            ['role_id' => 1, 'permission_id' => 10],
            ['role_id' => 1, 'permission_id' => 11],
            ['role_id' => 1, 'permission_id' => 12],
            ['role_id' => 1, 'permission_id' => 13],
            ['role_id' => 1, 'permission_id' => 14],
            ['role_id' => 1, 'permission_id' => 15],
            ['role_id' => 1, 'permission_id' => 16],
            ['role_id' => 1, 'permission_id' => 17],
            ['role_id' => 1, 'permission_id' => 18],
            ['role_id' => 1, 'permission_id' => 19],
            ['role_id' => 1, 'permission_id' => 20],
            ['role_id' => 1, 'permission_id' => 21],
            ['role_id' => 1, 'permission_id' => 22],
            ['role_id' => 1, 'permission_id' => 23],
            ['role_id' => 1, 'permission_id' => 24],
            ['role_id' => 1, 'permission_id' => 25],
            ['role_id' => 1, 'permission_id' => 26],
            ['role_id' => 1, 'permission_id' => 27],
            ['role_id' => 1, 'permission_id' => 28],
            ['role_id' => 1, 'permission_id' => 29],
            ['role_id' => 1, 'permission_id' => 30],
            ['role_id' => 1, 'permission_id' => 31],
            ['role_id' => 1, 'permission_id' => 32],
            ['role_id' => 1, 'permission_id' => 33],
            ['role_id' => 1, 'permission_id' => 34],
            ['role_id' => 1, 'permission_id' => 35],
            ['role_id' => 1, 'permission_id' => 36],
            ['role_id' => 1, 'permission_id' => 37],
            ['role_id' => 1, 'permission_id' => 38],
            ['role_id' => 1, 'permission_id' => 39],
            ['role_id' => 1, 'permission_id' => 40],
            ['role_id' => 1, 'permission_id' => 41],
            ['role_id' => 1, 'permission_id' => 42],
            ['role_id' => 1, 'permission_id' => 43],
            ['role_id' => 1, 'permission_id' => 44],
            ['role_id' => 1, 'permission_id' => 45],
            ['role_id' => 1, 'permission_id' => 46],
            ['role_id' => 1, 'permission_id' => 47],
            ['role_id' => 1, 'permission_id' => 48],
            ['role_id' => 1, 'permission_id' => 49],
            ['role_id' => 1, 'permission_id' => 50],
            ['role_id' => 1, 'permission_id' => 51],
            ['role_id' => 1, 'permission_id' => 52],
            ['role_id' => 1, 'permission_id' => 53],
            ['role_id' => 1, 'permission_id' => 54],
            ['role_id' => 1, 'permission_id' => 55],
            ['role_id' => 1, 'permission_id' => 56],
            ['role_id' => 1, 'permission_id' => 57],
            ['role_id' => 1, 'permission_id' => 58],
            ['role_id' => 1, 'permission_id' => 59],
            ['role_id' => 1, 'permission_id' => 60],
            ['role_id' => 1, 'permission_id' => 61],
            ['role_id' => 1, 'permission_id' => 62],
            ['role_id' => 1, 'permission_id' => 63],
            ['role_id' => 1, 'permission_id' => 64],
            ['role_id' => 1, 'permission_id' => 65],
            ['role_id' => 1, 'permission_id' => 66],
            ['role_id' => 1, 'permission_id' => 67],
            ['role_id' => 1, 'permission_id' => 68],
            ['role_id' => 1, 'permission_id' => 69],
            ['role_id' => 1, 'permission_id' => 70],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
