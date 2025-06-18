<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Validator; // Import Validator facade

class TaskController extends Controller
{
    /**
     * Hiển thị danh sách nhiệm vụ, theo danh sách nhiệm vụ được chọn hoặc mặc định.
     */
    public function index(Request $request)
    {
        $taskListId = $request->query('task_list_id');
        $sort = $request->query('sort', 'created'); // Mặc định là sắp xếp theo ngày tạo

        // Lấy tất cả danh sách nhiệm vụ của người dùng hiện tại
        $taskLists = TaskList::where('user_id', Auth::id())->get();

        // Nếu không có taskListId được chọn hoặc taskLists rỗng, tạo/chọn danh sách mặc định
        if ($taskListId) {
            // Đảm bảo taskListId thuộc về người dùng hiện tại
            $selectedTaskList = TaskList::where('id', $taskListId)->where('user_id', Auth::id())->first();
            if (!$selectedTaskList) {
                // Nếu taskListId không hợp lệ hoặc không thuộc về người dùng, chuyển về danh sách mặc định
                $defaultList = TaskList::firstOrCreate(['name' => 'Nhiệm vụ của tôi', 'user_id' => Auth::id()]);
                $taskListId = $defaultList->id;
            }
        } else {
            $defaultList = TaskList::firstOrCreate(['name' => 'Nhiệm vụ của tôi', 'user_id' => Auth::id()]);
            $taskListId = $defaultList->id;
        }

        // Lấy nhiệm vụ của người dùng hiện tại và task_list_id đã chọn
        $query = Task::where('user_id', Auth::id())
                     ->where('task_list_id', $taskListId);

        // Sắp xếp theo lựa chọn
        switch ($sort) {
            case 'priority':
                $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
                break;
            case 'dueDate':
                $query->orderBy('date_time', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default: // created
                $query->orderBy('created_at', 'desc');
                break;
        }

        $tasks = $query->get();

        // Thống kê
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('completed', true)->count();
        $inProgressTasks = $tasks->where('completed', false)->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        return view('tasks.index', compact(
            'tasks',
            'taskLists',
            'taskListId',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'completionRate',
            'sort' // truyền biến sort vào view để set selected option
        ));
    }


    /**
     * Lưu nhiệm vụ mới.
     */
    public function store(Request $request)
    {
        // Sử dụng Validator thay vì $request->validate() để có thể tùy chỉnh phản hồi JSON nếu cần
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'description' => 'nullable|string',
            'all_day' => 'boolean', // Nếu checkbox không được chọn, nó không gửi lên, Laravel sẽ coi nó là false
            'task_list_id' => 'required|exists:task_lists,id',
            'priority' => 'required|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Lỗi nhập liệu. Vui lòng kiểm tra lại.');
            // Nếu dùng AJAX, bạn sẽ trả về JSON tại đây
            // return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Kiểm tra task_list_id có thuộc về người dùng hiện tại không
        $taskList = TaskList::where('id', $request->input('task_list_id'))
                            ->where('user_id', Auth::id())
                            ->first();
        if (!$taskList) {
            return redirect()->back()->with('error', 'Danh sách nhiệm vụ không hợp lệ hoặc không thuộc về bạn.');
        }

        Task::create([
            'title' => $request->input('title'),
            'date_time' => $request->input('date_time'),
            'description' => $request->input('description'),
            'priority' => $request->input('priority'),
            'all_day' => $request->has('all_day'), // Kiểm tra xem checkbox có được gửi lên không
            'task_list_id' => $request->input('task_list_id'),
            'user_id' => Auth::id(), // Gán user_id của người dùng đang đăng nhập
        ]);

        return redirect()->route('tasks.index', ['task_list_id' => $request->input('task_list_id')])
            ->with('success', 'Đã tạo nhiệm vụ thành công!');
    }

    /**
     * Cập nhật nhiệm vụ.
     */
    public function update(Request $request, Task $task)
    {
        // KIỂM TRA QUYỀN SỞ HỮU (RẤT QUAN TRỌNG)
        if ($task->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa nhiệm vụ này.');
            // Hoặc nếu bạn dự định dùng AJAX:
            // return response()->json(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa nhiệm vụ này.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'description' => 'nullable|string',
            'all_day' => 'boolean', // Chắc chắn rằng nó là boolean (1 hoặc 0)
            'task_list_id' => 'required|exists:task_lists,id',
            'priority' => 'required|in:low,medium,high', // <-- THÊM DÒNG NÀY VÀO VALIDATION
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Lỗi nhập liệu. Vui lòng kiểm tra lại.');
            // Nếu dùng AJAX, bạn sẽ trả về JSON tại đây
            // return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Kiểm tra task_list_id mới có thuộc về người dùng hiện tại không (nếu có thay đổi task_list)
        $newTaskListId = $request->input('task_list_id');
        if ($task->task_list_id != $newTaskListId) {
            $newTaskList = TaskList::where('id', $newTaskListId)
                                   ->where('user_id', Auth::id())
                                   ->first();
            if (!$newTaskList) {
                return redirect()->back()->with('error', 'Danh sách nhiệm vụ được chọn không hợp lệ hoặc không thuộc về bạn.');
            }
        }

        $task->update([
            'title' => $request->input('title'),
            'date_time' => $request->input('date_time'),
            'description' => $request->input('description'),
            'all_day' => $request->has('all_day'), // Kiểm tra xem checkbox có được gửi lên không
            'task_list_id' => $request->input('task_list_id'),
            'priority' => $request->input('priority'), // <-- THÊM DÒNG NÀY VÀO CẬP NHẬT
        ]);

        return redirect()->route('tasks.index', ['task_list_id' => $task->task_list_id])
            ->with('success', 'Cập nhật nhiệm vụ thành công!');
    }

    /**
     * Xóa nhiệm vụ.
     */
    public function destroy(Task $task)
    {
        // KIỂM TRA QUYỀN SỞ HỮU (RẤT QUAN TRỌNG)
        if ($task->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền xóa nhiệm vụ này.');
            // Hoặc nếu bạn dự định dùng AJAX:
            // return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa nhiệm vụ này.'], 403);
        }

        $task->delete();
        return back()->with('success', 'Đã xoá nhiệm vụ thành công!');
    }

    /**
     * Xóa tất cả nhiệm vụ của user hiện tại
     */
    public function destroyAll()
    {
        // Không cần kiểm tra user_id nữa vì query đã làm việc đó
        Task::where('user_id', Auth::id())->delete();
        return redirect()->route('tasks.index')->with('success', 'Đã xóa tất cả nhiệm vụ!');
    }

    /**
     * Chuyển đổi trạng thái hoàn thành của nhiệm vụ.
     */
    public function toggleCompleted(Task $task)
    {
        // KIỂM TRA QUYỀN SỞ HỮU
        if ($task->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền thay đổi trạng thái nhiệm vụ này.');
            // Hoặc nếu bạn dự định dùng AJAX:
            // return response()->json(['success' => false, 'message' => 'Bạn không có quyền thay đổi trạng thái nhiệm vụ này.'], 403);
        }

        $task->completed = !$task->completed;
        $task->save();
        return back()->with('success', 'Cập nhật trạng thái nhiệm vụ thành công!');
    }
}