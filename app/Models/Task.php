<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'date_time',
        'description',
        'priority', // thêm dòng này
        'all_day',
        'task_list_id',
        'completed',
        'user_id', // Thêm dòng này
    ];

    protected $casts = [
        'all_day' => 'boolean',
        'date_time' => 'datetime',
    ];
}
