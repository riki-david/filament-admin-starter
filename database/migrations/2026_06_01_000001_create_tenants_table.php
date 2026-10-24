<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug', 63)->unique();
            $table->text('address')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('timezone', 40)->default('Asia/Jakarta');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
