<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Để truy cập người dùng đang đăng nhập

class EventController extends Controller
{
    /**
     * Hiển thị danh sách các sự kiện.
     * Trả về JSON nếu là yêu cầu AJAX, ngược lại trả về view.
     */
    public function index()
    {
        // Lấy tất cả sự kiện của người dùng hiện tại
        // Đảm bảo chỉ người dùng đã đăng nhập mới có thể xem sự kiện của họ
        $events = Event::where('user_id', Auth::id())->get();

        if (request()->ajax()) {
            return response()->json(['events' => $events], 200);
        }

        // Đây là phương thức trả về view chính của lịch
        // Nên nó sẽ tải tất cả sự kiện lần đầu
        return view('calendar.layout', compact('events'));
    }

    /**
     * Hiển thị form tạo sự kiện mới.
     * (Thường không cần thiết nếu bạn dùng modal/AJAX cho form)
     */
    public function create()
    {
        // Nếu bạn mở modal tạo sự kiện thông qua AJAX, phương thức này có thể không được sử dụng trực tiếp.
        // Tuy nhiên, nếu bạn có một trang riêng để tạo, nó sẽ trả về view đó.
        return view('calendar.layout'); // Giả định form nằm trong layout chung
    }

    /**
     * Lưu trữ một sự kiện mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        // Sử dụng Validator để xử lý validation mạnh mẽ và trả về lỗi chi tiết
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            // 'after_or_equal:start_time' sẽ đảm bảo end_time không đứng trước start_time
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'all_day' => 'boolean',
            'description' => 'nullable|string',
            // 'in' đảm bảo giá trị của difficulty nằm trong danh sách cho phép
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);

        // Nếu validation thất bại, trả về phản hồi JSON với lỗi và mã 422
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors() // Trả về tất cả lỗi để frontend có thể hiển thị chi tiết
            ], 422); // 422 Unprocessable Entity
        }

        // Tạo sự kiện mới và gán user_id của người dùng hiện tại
        $event = Event::create([
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'all_day' => $request->all_day,
            'description' => $request->description,
            'user_id' => Auth::id(), // Gán ID của người dùng đang đăng nhập
            'difficulty' => $request->difficulty,
        ]);

        // Trả về phản hồi JSON cho việc tạo thành công
        return response()->json([
            'success' => true,
            'message' => 'Sự kiện đã được tạo thành công!',
            'event' => $event->toArray() // Trả về đối tượng sự kiện đã tạo dưới dạng mảng
        ], 201); // 201 Created
    }

    /**
     * Hiển thị thông tin chi tiết của một sự kiện cụ thể.
     */
    public function show($id)
    {
        // Tìm sự kiện hoặc báo lỗi 404 nếu không tìm thấy
        $event = Event::findOrFail($id);

        // Kiểm tra quyền sở hữu: chỉ người tạo sự kiện mới được xem chi tiết
        if ($event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xem sự kiện này.'], 403); // 403 Forbidden
        }

        return response()->json($event, 200);
    }

    /**
     * Hiển thị form chỉnh sửa sự kiện.
     * (Tương tự như `create`, thường không dùng nếu dùng modal/AJAX)
     */
    public function edit(Event $event)
    {
        // Kiểm tra quyền sở hữu
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa sự kiện này.');
        }
        return view('calendar.layout', compact('event')); // Giả định form nằm trong layout chung
    }

    /**
     * Cập nhật một sự kiện hiện có trong cơ sở dữ liệu.
     * LƯU Ý: Phương thức này không được sử dụng trong luồng "tạo mới và xóa cũ" của bạn
     * nhưng vẫn được giữ lại để hoàn chỉnh API RESTful.
     */
    public function update(Request $request, Event $event)
    {
        // Kiểm tra quyền sở hữu
        if ($event->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa sự kiện này.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'all_day' => 'boolean',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->all());

        // Trong EventController.php, phương thức store
        return response()->json([
            'success' => true,
            'message' => 'Sự kiện đã được tạo thành công!',
            'event' => $event->toArray() // Trả về đối tượng sự kiện đã tạo dưới dạng mảng
        ], 201);
    }

    /**
     * Xóa một sự kiện khỏi cơ sở dữ liệu.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa sự kiện này.'], 403);
        }

        $event->delete();

        // Luôn trả về JSON cho các yêu cầu AJAX
        return response()->json(['message' => 'Sự kiện đã được xóa thành công!'], 200);
    }
}