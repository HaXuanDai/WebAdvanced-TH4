<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource; // Đảm bảo bạn đã tạo Resource Model và Migration
use Illuminate\Validation\ValidationException;

class ResourceController extends Controller
{
    /**
     * Hiển thị trang thư viện tài nguyên.
     * Trong một ứng dụng lớn, bạn có thể truyền dữ liệu ban đầu từ đây.
     */
    public function index()
    {
        $total = \App\Models\Resource::count();
        $completed = \App\Models\Resource::where('status', 'completed')->count();
        $inProgress = \App\Models\Resource::where('status', 'in_progress')->count();
        $notStarted = \App\Models\Resource::where('status', 'not_started')->count();
        return view('library.library', compact('total', 'completed', 'inProgress', 'notStarted'));
    }

    /**
     * Lấy danh sách tài nguyên dưới dạng JSON (dành cho các yêu cầu AJAX/Fetch từ frontend).
     */
    public function getResources(Request $request)
    {
        $query = Resource::query();

        // Lọc theo tìm kiếm
        if ($search = $request->input('search')) {
            $query->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        // Lọc theo môn học
        if ($subject = $request->input('subject')) {
            $query->where('subject', $subject);
        }

        // Lọc theo loại
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Lọc theo trạng thái
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Có thể thêm phân trang nếu cần
        $resources = $query->orderBy('priority', 'desc')->get(); // Sắp xếp theo ưu tiên

        return response()->json($resources);
    }

    /**
     * Lưu trữ một tài nguyên mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'description' => 'nullable|string',
                'url' => 'nullable|url|max:2048', // Giới hạn độ dài URL
                'priority' => 'required|integer|min:1|max:5',
                'deadline' => 'nullable|date',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50', // Mỗi tag là một chuỗi
            ]);

            // Gán màu sắc và icon dựa trên loại tài nguyên
            $subjectColors = [
                'math' => 'bg-blue-500',
                'programming' => 'bg-green-500',
                'english' => 'bg-yellow-500',
                'science' => 'bg-purple-500',
            ];

            $typeIcons = [
                'book' => '📚',
                'video' => '🎥',
                'article' => '📄',
                'course' => '🎓',
                'note' => '📝',
            ];

            $resource = Resource::create([
                'title' => $validatedData['title'],
                'subject' => $validatedData['subject'],
                'subject_name' => $this->getSubjectName($validatedData['subject']), // Thêm tên hiển thị
                'subject_color' => $subjectColors[$validatedData['subject']] ?? 'bg-gray-500',
                'type' => $validatedData['type'],
                'type_icon' => $typeIcons[$validatedData['type']] ?? '❓',
                'description' => $validatedData['description'],
                'url' => $validatedData['url'],
                'priority' => $validatedData['priority'],
                'deadline' => $validatedData['deadline'],
                'status' => 'not_started', // Trạng thái mặc định khi thêm mới
                'tags' => json_encode($validatedData['tags'] ?? []), // Lưu tags dưới dạng JSON
            ]);

            return response()->json($resource, 201); // 201 Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu nhập không hợp lệ.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm tài nguyên: ' . $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    /**
     * Cập nhật trạng thái của một tài nguyên.
     */
    public function updateStatus(Request $request, Resource $resource)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:not_started,in_progress,completed',
            ]);

            $resource->update([
                'status' => $validated['status']
            ]);

            return response()->json(['message' => 'Cập nhật trạng thái thành công.', 'resource' => $resource]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Trạng thái không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mở tài nguyên (chuyển hướng đến URL).
     */
    public function openUrl(Resource $resource)
    {
        if ($resource->url) {
            return redirect()->away($resource->url);
        }
        return response()->json(['message' => 'Tài nguyên này không có URL.'], 404);
    }

    /**
     * Hàm helper để lấy tên môn học hiển thị.
     * Có thể chuyển ra một service hoặc trait riêng nếu cần dùng nhiều nơi.
     */
    private function getSubjectName($subjectCode)
    {
        $subjectNames = [
            'math' => 'Toán học',
            'programming' => 'Lập trình',
            'english' => 'Tiếng Anh',
            'science' => 'Khoa học',
        ];
        return $subjectNames[$subjectCode] ?? 'Chung';
    }

    public function destroy(Resource $resource)
    {
        try {
            $resource->delete();
            return response()->json(['message' => 'Xóa tài nguyên thành công.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xóa tài nguyên: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Resource $resource)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:2048',
            'priority' => 'required|integer|min:1|max:5',
            'deadline' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);
        $resource->update([
            'title' => $validatedData['title'],
            'subject' => $validatedData['subject'],
            'type' => $validatedData['type'],
            'description' => $validatedData['description'],
            'url' => $validatedData['url'],
            'priority' => $validatedData['priority'],
            'deadline' => $validatedData['deadline'],
            'tags' => json_encode($validatedData['tags'] ?? []),
        ]);
        return response()->json($resource);
    }

    // Bạn có thể thêm các phương thức edit, update (full), destroy tại đây
    // public function edit(Resource $resource) { ... }
    // public function update(Request $request, Resource $resource) { ... }
    // public function destroy(Resource $resource) { ... }
}
