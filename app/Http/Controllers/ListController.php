<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskList; // Đảm bảo bạn đã import đúng model TaskList
use Illuminate\Support\Facades\Auth; // Để sử dụng auth()->id() hoặc Auth::id()

class ListController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        TaskList::create([
            'name' => $request->input('name'),
            'user_id' => auth()->id(), // Thêm dòng này
        ]);

        return back()->with('success', 'Tạo danh sách thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $list = TaskList::findOrFail($id);

        // Kiểm tra xem user có quyền sửa không (tuỳ ứng dụng)
        if ($list->user_id !== auth()->id()) {
            abort(403, 'Không có quyền chỉnh sửa danh sách này.');
        }

        $list->update([
            'name' => $request->input('name'),
        ]);

        return back()->with('success', 'Cập nhật danh sách thành công!');
    }

    /**
     * Xóa một danh sách nhiệm vụ.
     *
     * @param  \App\Models\TaskList  $list // Sử dụng route model binding
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskList $list) // Thay $id bằng TaskList $list để dùng route model binding
    {
        // RẤT QUAN TRỌNG: Kiểm tra quyền sở hữu trước khi xóa
        if ($list->user_id !== Auth::id()) { // Sử dụng Auth::id() cho nhất quán
            abort(403, 'Bạn không có quyền xóa danh sách nhiệm vụ này.');
        }

        // Xóa danh sách nhiệm vụ
        // Lưu ý: Bạn cần xem xét xem có nên xóa TẤT CẢ nhiệm vụ thuộc danh sách này không.
        // Hoặc di chuyển các nhiệm vụ này sang một danh sách mặc định khác.
        // Hiện tại, nếu có nhiệm vụ trong danh sách này, việc xóa list có thể gặp lỗi nếu có khóa ngoại (foreign key constraint)
        // Nếu bạn muốn xóa cascade, hãy định nghĩa trong model TaskList:
        // public static function boot() { parent::boot(); static::deleting(function($list) { $list->tasks()->delete(); }); }
        $list->delete();

        return redirect()->back()->with('success', 'Danh sách nhiệm vụ đã được xóa thành công!');
    }
}