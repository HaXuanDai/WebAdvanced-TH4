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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->boolean('all_day')->default(false);
        $table->timestamp('start_time');
        $table->timestamp('end_time')->nullable();
        $table->timestamps();
    });
    Schema::table('events', function (Blueprint $table) {
        $table->string('difficulty')->nullable()->after('description');
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
        Schema::dropIfExists('events');
    }
};
