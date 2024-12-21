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
        Schema::create('follows', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("follower_id")->constrained("users")->onDelete("cascade")->unique();
            $table->foreignUuid("followed_id")->constrained("users")->onDelete("cascade")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_follows');
    }
};
