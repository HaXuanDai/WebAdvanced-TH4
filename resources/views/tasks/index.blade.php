<?php
use App\Models\TaskList; // Thêm dòng này
$taskLists = TaskList::where('user_id', auth()->id())->get();
?>

@php
  // Tính toán thống kê nhiệm vụ
  $totalTasks = $tasks->count();
  $completedTasks = $tasks->where('completed', true)->count();
  $completionRate = $totalTasks > 0 ? round($completedTasks / $totalTasks * 100) : 0;
@endphp


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('frontend/css/todolist.css') }}" rel="stylesheet">
</head>
<body>
  <header>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-4">
          <a href="{{ route('calendar.layout') }}"><img src="{{ asset('frontend/images/calendar.png') }}" alt="logocld" class="iconheader calendar"></a>
          <a href="#"><img src="{{ asset('frontend/images/to-do-list.jpg') }}" alt="logotdl" class="iconheader todolist"></a>
          <a href="{{ route('resources.index') }}">
                        <img src="{{ asset('frontend/images/library.jpg') }}" alt="logolibrary" class="iconheader resourcelibrary">
                    </a>
        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4 text-end dropdown">
          <a href="#" class="d-inline-flex align-items-center text-decoration-none text-dark" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Auth::user()->name }}
            <img src="{{ asset('frontend/images/profile.png') }}" alt="Profile" class="rounded-circle ms-2 iconheader" style="width: 50px; height: 50px;">
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Log Out</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <section>
    <div class="row">
      <div class="col-sm-3">
        <button data-bs-toggle="modal" data-bs-target="#createTaskModal">
          <img src="{{ asset('frontend/images/plus.png') }}" alt=""> Tạo
        </button>
        <button data-bs-toggle="modal" data-bs-target="#createListModal">
          <img src="{{ asset('frontend/images/plus.png') }}" alt=""> Tạo danh sách mới
        </button>
        <button class="btn btn-light w-100 text-start b3" type="button" data-bs-toggle="collapse" data-bs-target="#taskListDropdown" aria-expanded="false" aria-controls="taskListDropdown">
          <img src="{{ asset('frontend/images/tick.png') }}" alt=""> Danh sách nhiệm vụ
        </button>

        <div class="collapse show" id="taskListDropdown">
          <div class="card card-body">
            @forelse ($taskLists as $list)
              <div class="d-flex justify-content-between align-items-center mb-2">
                <form method="GET" action="{{ route('tasks.index') }}" style="flex-grow: 1;">
                  <input type="hidden" name="task_list_id" value="{{ $list->id }}">
                  <button 
                    type="submit" 
                    class="btn btn-light w-100 text-start {{ (isset($taskListId) && $taskListId == $list->id) ? 'active' : '' }}">
                    {{ $list->name }}
                  </button>
                </form>
                <div class="ms-2 d-flex gap-1 flex-shrink-0">
                  <button class="btn btn-sm btn-outline-primary px-2 py-1" data-bs-toggle="modal" data-bs-target="#editListModal{{ $list->id }}">Sửa</button>
                  <form method="POST" action="{{ route('task-lists.destroy', $list->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger px-2 py-1" onclick="return confirm('Bạn có chắc muốn xoá danh sách này?')">Xoá</button>
                  </form>
                </div>


              </div>

              <!-- Modal sửa danh sách -->
              <div class="modal fade" id="editListModal{{ $list->id }}" tabindex="-1" aria-labelledby="editListModalLabel{{ $list->id }}" aria-hidden="true">
                <div class="modal-dialog">
                  <form method="POST" action="{{ route('task-lists.update', $list->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                     <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 1rem;">
                      <h5 class="modal-title" style="margin: 0; white-space: nowrap;">Sửa danh sách</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" style="width: 1rem; height: 1rem;"></button>
                    </div>

                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="editListName{{ $list->id }}" class="form-label">Tên danh sách</label>
                          <input type="text" class="form-control" id="editListName{{ $list->id }}" name="name" value="{{ $list->name }}" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            @empty
              <p class="text-muted">Chưa có danh sách nào.</p>
            @endforelse
          </div>
        </div>

        <select id="sort-select" onchange="sortTasks()" style="margin-right: 10px; padding: 8px; border-radius: 8px; border: 2px solid #e9ecef;">
          <option value="created" {{ $sort === 'created' ? 'selected' : '' }}>Sắp xếp theo ngày tạo</option>
          <option value="priority" {{ $sort === 'priority' ? 'selected' : '' }}>Sắp xếp theo độ ưu tiên</option>
          <option value="dueDate" {{ $sort === 'dueDate' ? 'selected' : '' }}>Sắp xếp theo hạn</option>
          <option value="title" {{ $sort === 'title' ? 'selected' : '' }}>Sắp xếp theo tên</option>
        </select>

        <script>
          function sortTasks() {
              const sort = document.getElementById('sort-select').value;
              const params = new URLSearchParams(window.location.search);
              params.set('sort', sort);
              window.location.href = window.location.pathname + '?' + params.toString();
          }
        </script>

        <!-- Hộp thống kê nhiệm vụ -->
        <div class="mt-4 p-3 rounded bg-light border" id="taskStatsBox">
            <h4>📊 Thống kê nhiệm vụ</h4>
            <div>Tổng nhiệm vụ: <b>{{ $totalTasks }}</b></div>
            <div>Đã hoàn thành: <b>{{ $completedTasks }}</b></div>
            <div>Đang hoàn thành: <b>{{ $inProgressTasks }}</b></div> <!-- Dòng mới -->
            <div>Tỷ lệ hoàn thành: <b>{{ $completionRate }}%</b></div>
        </div>
      </div>

      <div class="col-sm-9 content-right">
        <h2>📝 Danh sách các nhiệm vụ</h2>
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <ul class="list-group">
          @forelse($tasks as $task)
                      <li class="list-group-item py-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="d-flex align-items-center">
              <form action="{{ route('tasks.toggleCompleted', $task->id) }}" method="POST" class="d-inline me-3">
                  @csrf
                  @method('PATCH')
                  <input type="checkbox" onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>
              </form>
              <span class="fw-bold fs-5 align-middle {{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}">
                  {{ $task->title }}
              </span>
          </div>
          <div class="text-end">
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">Sửa</button>
              <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xoá nhiệm vụ này không?')">Xoá</button>
              </form>
          </div>
      </div>

      <!--Mô tả công việc-->
      @if($task->description)
          <div class="mt-2 text-secondary" style="font-size: 1rem;">
              {{ $task->description }}
          </div>
      @endif

      <!--Hạn chót và Mức độ ưu tiên-->
      <div class="d-flex align-items-center gap-2 mt-2">
          @php
              $deadline = \Carbon\Carbon::parse($task->date_time);
              $now = \Carbon\Carbon::now();
              $diffHours = $deadline->diffInHours($now, false);
              $isNearDeadline = !$task->completed && $diffHours <= 24 && $deadline > $now;
          @endphp
          <span class="badge px-3 py-2 fs-6 {{ $isNearDeadline ? 'bg-danger' : 'bg-info' }}">
              <i class="bi bi-clock"></i>
              {{ $task->all_day ? 'Cả ngày' : 'Hạn: '.$deadline->format('d/m/Y') }} <!--Ví dụ: Hạn: 13/06/2025-->
          </span>
          <span class="badge px-3 py-2 fs-6
              {{ $task->priority == 'high' ? 'bg-danger' : ($task->priority == 'medium' ? 'bg-warning text-dark' : 'bg-success') }}">
              {{ $task->priority == 'high' ? 'Cao' : ($task->priority == 'medium' ? 'Trung bình' : 'Thấp') }} <!--Ví dụ: Trung bình-->
          </span>
      </div>
      </li>
          @empty
            <li class="list-group-item">Chưa có nhiệm vụ nào.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </section>

  <!-- Modal tạo danh sách -->
  <div class="modal fade" id="createListModal" tabindex="-1" aria-labelledby="createListModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('task-lists.store') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Tạo danh sách mới</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="listName" class="form-label">Tên danh sách</label>
              <input type="text" class="form-control" id="listName" name="name" placeholder="Nhập tên danh sách..." required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Tạo</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal tạo nhiệm vụ -->
  <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tạo nhiệm vụ mới</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="taskTitle" class="form-label">Tiêu đề</label>
              <input type="text" name="title" class="form-control" id="taskTitle" required>
            </div>
            <div class="mb-3">
              <label for="taskDate" class="form-label">Hạn chót</label>
              <input type="datetime-local" name="date_time" class="form-control" id="taskDate" required>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="all_day" id="allDayCheck" value="1">
              <label class="form-check-label" for="allDayCheck">Cả ngày</label>
            </div>
            <div class="mb-3">
              <label for="taskDescription" class="form-label">Mô tả</label>
              <textarea class="form-control" name="description" id="taskDescription" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="taskPriority" class="form-label">Mức độ ưu tiên</label>
              <select name="priority" id="taskPriority" class="form-select" required>
                <option value="low">Thấp</option>
                <option value="medium" selected>Trung bình</option>
                <option value="high">Cao</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="taskList" class="form-label">Chọn danh sách nhiệm vụ</label>
              <select name="task_list_id" id="taskList" class="form-select" required>
                <option value="" disabled selected>-- Chọn danh sách --</option>
                @foreach($taskLists as $list)
                  <option value="{{ $list->id }}">{{ $list->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer border-secondary">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Tạo</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const allDayCheck = document.getElementById('allDayCheck');
      const taskDateInput = document.getElementById('taskDate');

      function toggleDateInputType() {
        taskDateInput.type = allDayCheck.checked ? 'date' : 'datetime-local';
      }

      toggleDateInputType();
      allDayCheck.addEventListener('change', toggleDateInputType);
    });
  </script>

  <!-- Modal sửa nhiệm vụ -->
  @foreach ($tasks as $task)
    <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1" aria-labelledby="editTaskModalLabel{{ $task->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" action="{{ route('tasks.update', $task->id) }}">
          @csrf
          @method('PUT')
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Chỉnh sửa nhiệm vụ</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="taskTitle{{ $task->id }}" class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" id="taskTitle{{ $task->id }}" value="{{ $task->title }}" required>
              </div>
              <div class="mb-3">
                <label for="taskDate{{ $task->id }}" class="form-label">Hạn chót</label>
                <input type="{{ $task->all_day ? 'date' : 'datetime-local' }}" name="date_time" class="form-control" id="taskDate{{ $task->id }}" value="{{ \Carbon\Carbon::parse($task->date_time)->format($task->all_day ? 'Y-m-d' : 'Y-m-d\TH:i') }}" required>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="all_day" id="allDayEditCheck{{ $task->id }}" value="1" {{ $task->all_day ? 'checked' : '' }}>
                <label class="form-check-label" for="allDayEditCheck{{ $task->id }}">Cả ngày</label>
              </div>
              <div class="mb-3">
                <label for="taskDescription{{ $task->id }}" class="form-label">Mô tả</label>
                <textarea class="form-control" name="description" id="taskDescription{{ $task->id }}" rows="3">{{ $task->description }}</textarea>
              </div>
              <div class="mb-3">
                <label for="taskPriority{{ $task->id }}" class="form-label">Mức độ ưu tiên</label>
                <select name="priority" id="taskPriority{{ $task->id }}" class="form-select" required>
                  <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Thấp</option>
                  <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Trung bình</option>
                  <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>Cao</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="taskList{{ $task->id }}" class="form-label">Chọn danh sách nhiệm vụ</label>
                <select name="task_list_id" id="taskList{{ $task->id }}" class="form-select" required>
                  @foreach($taskLists as $list)
                    <option value="{{ $list->id }}" {{ $task->task_list_id == $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer border-secondary">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  @endforeach

</body>
</html>