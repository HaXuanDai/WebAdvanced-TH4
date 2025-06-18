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
        Schema::create('resources', function (Blueprint $table) {
            $table->id(); // Primary key auto-incrementing (ID duy nhất cho mỗi tài nguyên)

            // Các cột chính cho thông tin tài nguyên
            $table->string('title'); // Tên/Tiêu đề của tài nguyên (ví dụ: "Sách giáo khoa Toán 10", "Khóa học React cơ bản")
            $table->string('subject'); // Mã môn học (ví dụ: 'math', 'programming', 'english'). Dùng để lọc.
            $table->string('subject_name')->nullable(); // Tên hiển thị của môn học (ví dụ: 'Toán học'). Có thể để null.
            $table->string('subject_color')->nullable(); // Mã màu Tailwind CSS cho môn học (ví dụ: 'bg-blue-500'). Có thể để null.
            $table->string('type'); // Loại tài nguyên (ví dụ: 'book', 'video', 'article', 'course', 'note').
            $table->string('type_icon')->nullable(); // Icon hiển thị cho loại tài nguyên (ví dụ: '📚', '🎥'). Có thể để null.
            $table->text('description')->nullable(); // Mô tả chi tiết về tài nguyên. Có thể để trống.
            $table->string('url')->nullable(); // URL hoặc liên kết của tài nguyên. Có thể để trống.

            // Các cột cho quản lý tiến độ và ưu tiên
            $table->integer('priority')->default(3); // Độ ưu tiên (ví dụ: 1-5, 5 là rất cao, 1 là thấp). Mặc định là 3 (trung bình).
            $table->date('deadline')->nullable(); // Hạn chót để hoàn thành tài nguyên. Có thể để trống.
            $table->string('status')->default('not_started'); // Trạng thái của tài nguyên: 'not_started', 'in_progress', 'completed'. Mặc định là 'not_started'.

            // Cột lưu trữ các thẻ (tags)
            $table->json('tags')->nullable(); // Lưu trữ các tags liên quan dưới dạng chuỗi JSON (ví dụ: '["basic", "theory", "exam"]'). Có thể để trống.

            $table->timestamps(); // Tự động thêm cột `created_at` và `updated_at` để theo dõi thời gian tạo/cập nhật.
        });
    }

    /**
     * Reverse the migrations.
     * Phương thức này sẽ được chạy khi bạn rollback migration.
     * Nó sẽ xóa bảng 'resources' nếu tồn tại.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};