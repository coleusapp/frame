<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('frame.table_prefix').'frames', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->string('search_query')->nullable();
            $table->json('sources');
            $table->unsignedInteger('interval_seconds')->default(60);
            $table->unsignedSmallInteger('display_width')->default(config('frame.display.width'));
            $table->unsignedSmallInteger('display_height')->default(config('frame.display.height'));
            $table->unsignedSmallInteger('rotation')->default(0);
            $table->unsignedSmallInteger('inset_top')->default(0);
            $table->unsignedSmallInteger('inset_right')->default(0);
            $table->unsignedSmallInteger('inset_bottom')->default(0);
            $table->unsignedSmallInteger('inset_left')->default(0);
            $table->string('background_color', 7)->default('#000000');
            $table->boolean('show_caption')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('frame.table_prefix').'frames');
    }
};
