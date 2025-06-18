<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->nullable();

            // Thêm khóa ngoại sau khi thêm cột
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('task_lists', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Xóa foreign key trước
            $table->dropColumn('user_id');    // Rồi mới xóa cột
        });
    }
};
