<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory; // Dùng cho factory nếu bạn muốn tạo dữ liệu giả (seeder)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subject',
        'subject_name', // Thêm cột này nếu bạn lưu tên hiển thị của môn học
        'subject_color', // Thêm cột này nếu bạn lưu màu sắc cho môn học
        'type',
        'type_icon', // Thêm cột này nếu bạn lưu icon cho loại tài nguyên
        'description',
        'url',
        'priority',
        'deadline',
        'status',
        'tags', // Thêm cột này để lưu trữ các tags
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array', // Laravel sẽ tự động serialize/deserialize mảng tags thành JSON
        'deadline' => 'date', // Laravel sẽ tự động chuyển đổi chuỗi ngày thành đối tượng Carbon
    ];

    /**
     * Tên bảng nếu khác với tên số nhiều của model (mặc định là 'resources').
     * protected $table = 'my_resources';
     */

    /**
     * Tên cột primary key nếu không phải 'id'.
     * protected $primaryKey = 'resource_id';
     */

    /**
     * Chỉ định nếu model không sử dụng timestamps (created_at, updated_at).
     * public $timestamps = false;
     */
}