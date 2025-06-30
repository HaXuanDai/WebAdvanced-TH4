# 📚 Dự án: Hỗ Trợ và Theo Dõi Việc Tự Học

## 1️⃣ Mô tả dự án

**Hỗ Trợ và Theo Dõi Việc Tự Học** là một ứng dụng web cá nhân hóa giúp người dùng quản lý quá trình tự học một cách toàn diện. Dành cho học sinh, sinh viên, người đi làm, hoặc bất kỳ ai đang học một kỹ năng mới, ứng dụng tập trung vào việc hình thành thói quen học tập bền vững, theo dõi tiến độ và đánh giá kết quả.

Các chức năng chính:
- **Calendar**: Ghi nhận lịch học, tổng hợp số buổi và giờ học.
- **To-do List**: Tạo và quản lý nhiệm vụ học tập, theo dõi tiến độ.
- **Library**: Lưu trữ giáo trình, khóa học, liên kết học trực tuyến, đánh dấu trạng thái hoàn thành.

---

## 2️⃣ Công nghệ sử dụng

- ✅ **PHP (Laravel Framework)**
- ✅ **Laravel Breeze**
- ✅ **MySQL (Aiven Cloud)**
- ✅ **Blade Template**
- ✅ **Tailwind CSS**

---

## 3️⃣ Kiến trúc hệ thống

### 3.1 Sơ đồ Database
![LoginView](image/StructDiagram.png)
Ứng dụng gồm các bảng chính:
- `users`: người dùng hệ thống
- `study_sessions`: phiên học (thời gian, nội dung)
- `tasks`: nhiệm vụ học tập (To-do list)
- `learning_resources`: tài nguyên học tập (Library)

### 3.2 Các chức năng chính
![LoginView](image/ActivityDiagram.png)

#### 3.2.1 Quản lý người dùng
- Đăng ký / Đăng nhập / Đăng xuất
![LoginView](image/Register.png)
![LoginView](image/Login.png)
- Cập nhật thông tin cá nhân
- Đổi / reset mật khẩu
![LoginView](image/Reset.png)

#### 3.2.2 Quản lý thời gian học (Calendar)
- Ghi nhận ngày, giờ học, nội dung
- Hiển thị lịch học dạng calendar
- Thống kê số buổi & số giờ học
![LoginView](image/Calendar1.png)

![LoginView](image/Calendar2.png)

#### 3.2.3 Quản lý công việc (To-do List)
- Thêm / sửa / xóa nhiệm vụ
- Cập nhật trạng thái: đang làm / đã hoàn thành
- Thống kê tiến độ nhiệm vụ

![LoginView](image/Todolist1.png)

![LoginView](image/Todolist2.png)

![LoginView](image/Todolist3.png)

#### 3.2.4 Thư viện học liệu (Library)
- Thêm sách / khóa học / link học tập
- Cập nhật trạng thái: chưa học / đang học / đã hoàn thành
- Theo dõi tiến độ hoàn thành từng tài nguyên

![LoginView](image/Library1.png)

![LoginView](image/Library2.png)

### 3.3 Sơ đồ thuật toán

![LoginView](image/Diagram.png)

---

## 4️⃣ Các đối tượng chính

| Tên bảng | Vai trò | Mối quan hệ |
|----------|---------|--------------|
| `User` | Tài khoản người dùng | 1-n với `StudySession`, `Task`, `ResourceLibrary` |
| `Task` | Nhiệm vụ học tập | thuộc về `User` |
| `ResourceLibrary` | Tài nguyên học tập | thuộc về `User` |

---

## 5️⃣ Ví dụ một số đoạn mã chính

### 5.1 `ProfileController.php`
```
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        $request->user()->save();
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}
```

### 5.2 `EvenController.php`
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('user_id', Auth::id())->get();
        if (request()->ajax()) {
            return response()->json(['events' => $events], 200);
        }
        return view('calendar.layout', compact('events'));
    }

    public function create()
    {
        return view('calendar.layout'); 
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'all_day' => 'boolean',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors() 
            ], 422); 
        }
        $event = Event::create([
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'all_day' => $request->all_day,
            'description' => $request->description,
            'user_id' => Auth::id(), 
            'difficulty' => $request->difficulty,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Sự kiện đã được tạo thành công!',
            'event' => $event->toArray() 
        ], 201); 
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        if ($event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xem sự kiện này.'], 403); 
        }
        return response()->json($event, 200);
    }

    public function edit(Event $event)
    {
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa sự kiện này.');
        }
        return view('calendar.layout', compact('event')); 
    }

    public function update(Request $request, Event $event)
    {
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
        return response()->json([
            'success' => true,
            'message' => 'Sự kiện đã được tạo thành công!',
            'event' => $event->toArray() 
        ], 201);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        if ($event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa sự kiện này.'], 403);
        }
        $event->delete();
        return response()->json(['message' => 'Sự kiện đã được xóa thành công!'], 200);
    }
}
```

### 5.3 `User.php`
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```
### 5.4 `Event.php`
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'start_time',
        'end_time',
        'all_day',
        'description',
        'user_id',
        'difficulty'
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'all_day' => 'boolean',
    ];
}
```

