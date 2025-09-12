<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manta_resources', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('capacity')->default(1);
            $table->string('title');
            $table->string('location')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['active','capacity']);
        });

        Schema::create('manta_rooms', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('capacity')->default(1);
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['active','capacity']);
        });

        Schema::create('manta_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type'); // products|resources|rooms
            $table->unsignedBigInteger('owner_id');
            $table->tinyInteger('weekday'); // 1..7
            $table->time('start_time');
            $table->time('end_time');
            // no timestamps
            $table->index(['owner_type','owner_id','weekday']);
        });

        Schema::create('manta_calendar_exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type'); // products|resources|rooms
            $table->unsignedBigInteger('owner_id');
            $table->date('date');
            $table->boolean('is_closed')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['owner_type','owner_id','date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_calendar_exceptions');
        Schema::dropIfExists('manta_opening_hours');
        Schema::dropIfExists('manta_rooms');
        Schema::dropIfExists('manta_resources');
    }
};
