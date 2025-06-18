<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{('frontend/css/app.css')}}" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4">
                    <a href="#"><img src="{{ asset('frontend/images/calendar.png') }}" alt="logocld" class="iconheader calendar"></a>
                    <a href="{{ route('tasks.index') }}"><img src="{{ asset('frontend/images/to-do-list.jpg') }}" alt="logotdl" class="iconheader todolist"></a>
                    <a href="{{ route('resources.index') }}">
                        <img src="{{ asset('frontend/images/library.jpg') }}" alt="logolibrary" class="iconheader resourcelibrary">
                    </a>
                </div>

                <div class="col-sm-4 d-flex align-items-center justify-content-center gap-2">
                    <button id="btn-today" class="btn btn-outline-dark">Hôm nay</button>
                    <button id="btn-prev" class="btn text-dark"><img src="{{ ('frontend/images/left-arrow.png')}}" alt=""></button>
                    <button id="btn-next" class="btn text-dark"><img src="{{ ('frontend/images/right-arrow.png')}}" alt=""></button>
                    <p class="month-year-display" id="monthYear"></p>
                </div>
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
        <div>
            <div class="row">
                <div class="col-sm-3 sticky-sidebar">
                    <div class="d-flex justify-content-between align-items-center px-2">
                        <button id="prev-month" class="btn btn-sm "><img src="{{ ('frontend/images/left-arrow.png')}}" alt=""></button>
                        <h4 id="calendar-title"></h4>
                        <button id="next-month" class="btn btn-sm "><img src="{{ ('frontend/images/right-arrow.png')}}" alt=""></button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>T2</th>
                                <th>T3</th>
                                <th>T4</th>
                                <th>T5</th>
                                <th>T6</th>
                                <th>T7</th>
                                <th>CN</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-body">
                        </tbody>
                    </table>
                    <div class="tao">
                        <button data-bs-toggle="modal" data-bs-target="#createTaskModal">
                            <img class="plus" src="{{('frontend/images/plus.png') }}" alt="">Tạo
                        </button>
                    </div>
                    <div class="mt-4 p-3 rounded bg-light border" id="timeStatsBox">
                        <h6 class="mb-3">📊 Thống Kê Thời Gian Tự Học</h6>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Hôm nay</span>
                            <span id="todayTime" class="fw-bold text-primary">0h 0p</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tuần này</span>
                            <span id="weekTime" class="fw-bold text-primary">0h 0p</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tháng này</span>
                            <span id="monthTime" class="fw-bold text-primary">0h 0p</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span>Tổng buổi học</span>
                            <span id="totalEvents" class="fw-bold">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tổng thời gian</span>
                            <span id="totalTime" class="fw-bold text-success">0h 0p</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="calendar-grid">
                        <div id="weekTitle" class="fw-bold fs-5 mb-2"></div> <div class="week-header" id="week-header"></div>
                        <div class="croll">
                            <div class="time-labels" id="time-labels"></div>
                            <div class="week-body" id="week-body"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTaskModalLabel">Tạo buổi học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <form id="createTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Môn học</label>
                            <input type="text" class="form-control" id="taskTitle" placeholder="Nhập tiêu đề..." required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="allDayCheck" onchange="toggleTimeInputs()">
                            <label class="form-check-label" for="allDayCheck">Cả ngày</label>
                        </div>
                        <div class="mb-3 row">
                            <div class="col">
                            <label for="startDate" class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" class="form-control" id="startDate" required>
                            </div>
                            <div class="col" id="endDateGroup">
                            <label for="endDate" class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" class="form-control" id="endDate" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="taskDescription" rows="3" placeholder="Nhập ghi chú..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block mb-2">Đánh giá về buổi học</label>
                            <div class="d-flex gap-2">
                                <div class="form-check rating-option">
                                    <input class="form-check-input" type="radio" name="difficulty" id="difficultyEasy" value="easy">
                                    <label class="form-check-label rating-easy" for="difficultyEasy" style="cursor:pointer;">
                                        <span style="font-size:1.5rem;">😊</span><br>
                                        <span>Dễ</span>
                                    </label>
                                </div>
                                <div class="form-check rating-option">
                                    <input class="form-check-input" type="radio" name="difficulty" id="difficultyMedium" value="medium">
                                    <label class="form-check-label rating-medium" for="difficultyMedium" style="cursor:pointer;">
                                        <span style="font-size:1.5rem;">😐</span><br>
                                        <span>Trung bình</span>
                                    </label>
                                </div>
                                <div class="form-check rating-option">
                                    <input class="form-check-input" type="radio" name="difficulty" id="difficultyHard" value="hard">
                                    <label class="form-check-label rating-hard" for="difficultyHard" style="cursor:pointer;">
                                        <span style="font-size:1.5rem;">😰</span><br>
                                        <span>Khó</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="submitEventForm()">
                        <span>💾</span>
                        Tạo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Chi tiết buổi học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body" id="eventDetailBody">
                    </div>
                <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="editEventBtn" style="display:none;">Chỉnh sửa</button>
            <button type="button" class="btn btn-danger" id="deleteEventBtn" style="display:none;">Xóa</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentDate = new Date();
    let editingEventId = null; // Biến toàn cục lưu id sự kiện đang chỉnh sửa
    
    // Biến lưu ngày hiện tại được chọn (cho lịch tháng nhỏ)
    let selectedDate = new Date(currentDate);

    // Chuyển $events từ PHP sang JS
    // Giả định biến `events` này chứa TẤT CẢ các sự kiện mà bạn muốn hiển thị
    // trong toàn bộ lịch, không chỉ tuần đầu tiên.
    window.allEvents = @json($events);

    // Hàm chọn ngày trong lịch tháng
    function selectDate(year, month, day) {
        selectedDate = new Date(year, month, day);
        currentDate = new Date(year, month, day); // Cập nhật luôn currentDate về ngày vừa chọn

        // Cập nhật lại lịch tháng, tuần và tiêu đề
        taolich(currentDate.getMonth(), currentDate.getFullYear(), false);
        renderWeekView(currentDate); // render tuần dựa trên ngày vừa chọn
        updateWeekTitle();
        renderEventsOnWeekView(window.allEvents); // Vẽ sự kiện theo tuần mới
    }

    // Hàm tạo lịch tháng
    function taolich(month, year, updateMainTitle = true) {
        const taobody = document.getElementById("calendar-body");
        taobody.innerHTML = "";

        // Lấy thứ của ngày 1 trong tháng (0: CN, 1: T2, ..., 6: T7)
        let firstDay = new Date(year, month, 1).getDay();
        // Đổi về index bắt đầu từ Thứ 2 (0: T2, ..., 5: T7, 6: CN)
        firstDay = (firstDay + 6) % 7;

        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();

        let date = 1;
        for (let i = 0; i < 6; i++) {
            const row = document.createElement("tr");
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement("td");
                if (i === 0 && j < firstDay) {
                    cell.innerText = "";
                } else if (date > daysInMonth) {
                    cell.innerText = "";
                } else {
                    cell.innerText = date;
                    if (
                        date === today.getDate() &&
                        month === today.getMonth() &&
                        year === today.getFullYear()
                    ) {
                        cell.classList.add("today");
                    }
                    // Thêm class 'selected-day' nếu là ngày đang được chọn
                    if (
                        date === selectedDate.getDate() &&
                        month === selectedDate.getMonth() &&
                        year === selectedDate.getFullYear()
                    ) {
                        cell.classList.add("selected-day");
                    }

                    cell.style.cursor = "pointer";
                    const thisDate = date;
                    cell.onclick = () => selectDate(year, month, thisDate);
                    date++;
                }
                row.appendChild(cell);
            }
            taobody.appendChild(row);
            if (date > daysInMonth) break;
        }
        if (updateMainTitle) {
            updateMonthTitles(month, year);
        }
    }

    // Hàm cập nhật tiêu đề tháng ở cả hai vị trí
    function updateMonthTitles(month, year) {
        document.getElementById("monthYear").innerText = `Tháng ${month + 1}, ${year}`;
        document.getElementById("calendar-title").innerText = `Tháng ${month + 1}, ${year}`;
    }

    // Hàm cập nhật tiêu đề tuần
    function updateWeekTitle() {
        document.getElementById("weekTitle").innerText = ""; // Xóa tiêu đề tuần, không hiển thị gì cả
    }

    function getWeekDates(date = new Date()) {
        // Lấy thứ của ngày hiện tại (0: CN, 1: T2, ..., 6: T7)
        const day = date.getDay();
        // Tính offset để về thứ 2 đầu tuần
        const offset = day === 0 ? -6 : 1 - day;
        const start = new Date(date);
        start.setDate(date.getDate() + offset);
        start.setHours(0,0,0,0);

        const week = [];
        for (let i = 0; i < 7; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            week.push({
                // Đổi thứ tự nhãn: T2 -> T7, CN
                label: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'][i],
                date: d.getDate(),
                full: d
            });
        }
        return week;
    }

    function renderWeekView(date = new Date()) {
        const weekHeader = document.getElementById("week-header");
        const weekBody = document.getElementById("week-body");
        const timeLabels = document.getElementById("time-labels");

        const week = getWeekDates(date);
        weekHeader.innerHTML = "";
        week.forEach(day => {
            const div = document.createElement("div");
            div.className = "day-header";
            div.innerHTML = `<div>${day.label}</div><div>${day.date}</div>`;
            // Thêm class 'selected-day-header' nếu ngày hiện tại được chọn là một trong các ngày trong tuần đang hiển thị
            if (day.full.toDateString() === selectedDate.toDateString()) {
                div.classList.add('selected-day-header');
            }
            weekHeader.appendChild(div);
        });

        timeLabels.innerHTML = `<div class="hour">GMT+07</div>`;
        for (let h = 1; h < 24; h++) {
            const div = document.createElement("div");
            div.className = "hour";
            div.textContent = h < 12 ? `${h} AM` : h === 12 ? "12 PM" : `${h - 12} PM`;
            timeLabels.appendChild(div);
        }

        weekBody.innerHTML = "";
        for (let h = 0; h < 24; h++) {
            const row = document.createElement("div");
            row.className = "hour-row";
            for (let d = 0; d < 7; d++) {
                const cell = document.createElement("div");
                cell.className = "day-cell";
                row.appendChild(cell);
            }
            weekBody.appendChild(row);
        }
    }

    document.getElementById("prev-month").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        // Khi chuyển tháng, selectedDate sẽ tự động điều chỉnh trong taolich dựa vào currentDate
        taolich(currentDate.getMonth(), currentDate.getFullYear(), true);
        renderWeekView(selectedDate); // Render tuần dựa trên selectedDate
        updateWeekTitle();
        renderEventsOnWeekView(window.allEvents); // Vẽ lại sự kiện
    });

    document.getElementById("next-month").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        // Khi chuyển tháng, selectedDate sẽ tự động điều chỉnh trong taolich dựa vào currentDate
        taolich(currentDate.getMonth(), currentDate.getFullYear(), true);
        renderWeekView(selectedDate); // Render tuần dựa trên selectedDate
        updateWeekTitle();
        renderEventsOnWeekView(window.allEvents); // Vẽ lại sự kiện
    });

    document.getElementById("btn-prev").addEventListener("click", () => {
        currentDate.setDate(currentDate.getDate() - 7);
        selectedDate.setDate(selectedDate.getDate() - 7); // Cập nhật selectedDate theo tuần mới
        taolich(currentDate.getMonth(), currentDate.getFullYear(), true); // Cập nhật lịch tháng để làm nổi bật ngày đúng
        renderWeekView(currentDate); // render tuần mới
        updateWeekTitle();
        renderEventsOnWeekView(window.allEvents); // Vẽ lại sự kiện
    });

    document.getElementById("btn-next").addEventListener("click", () => {
        currentDate.setDate(currentDate.getDate() + 7);
        selectedDate.setDate(selectedDate.getDate() + 7); // Cập nhật selectedDate theo tuần mới
        taolich(currentDate.getMonth(), currentDate.getFullYear(), true); // Cập nhật lịch tháng để làm nổi bật ngày đúng
        renderWeekView(currentDate); // render tuần mới
        updateWeekTitle();
        renderEventsOnWeekView(window.allEvents); // Vẽ lại sự kiện
    });

    document.getElementById("btn-today").addEventListener("click", () => {
        currentDate = new Date();
        selectedDate = new Date(); // Cập nhật selectedDate về hôm nay
        taolich(currentDate.getMonth(), currentDate.getFullYear(), true);
        renderWeekView(currentDate);
        updateWeekTitle();
        renderEventsOnWeekView(window.allEvents); // Vẽ lại sự kiện
    });

    function toggleTimeInputs() {
        const allDay = document.getElementById('allDayCheck').checked;
        const startInput = document.getElementById('startDate');
        const endInput = document.getElementById('endDate');
        const endDateGroup = document.getElementById('endDateGroup');

        if (allDay) {
            startInput.type = 'date';
            endDateGroup.style.display = 'none'; // Ẩn ô kết thúc
        } else {
            startInput.type = 'datetime-local';
            endDateGroup.style.display = 'block'; // Hiện lại ô kết thúc
            endInput.type = 'datetime-local';
        }
    }
    
    function submitEventForm() {
    const title = document.getElementById('taskTitle').value;
    const allDay = document.getElementById('allDayCheck').checked;
    let startDate = document.getElementById('startDate').value;
    let endDate = null;
    if (!allDay) {
        endDate = document.getElementById('endDate').value;
    }
    let startDateISO, endDateISO;
    if (allDay) {
        // Đảm bảo định dạng cho all_day (YYYY-MM-DD 00:00:00)
        startDateISO = startDate ? startDate + ' 00:00:00' : null;
        endDateISO = null; // Cả ngày không có end_time riêng
    } else {
        // Đảm bảo định dạng cho datetime-local (YYYY-MM-DD HH:MM:SS)
        startDateISO = startDate && startDate.length === 16 ? startDate + ':00' : startDate;
        endDateISO = endDate && endDate.length === 16 ? endDate + ':00' : endDate;
    }
    const description = document.getElementById('taskDescription').value;
    const difficulty = document.querySelector('input[name="difficulty"]:checked')?.value || null;

    if (!title || !startDate) {
        alert('Vui lòng nhập tiêu đề và thời gian bắt đầu!');
        return;
    }

    const payload = {
        title: title,
        all_day: !!allDay,
        start_time: startDateISO,
        end_time: endDateISO,
        description: description,
        difficulty: difficulty
    };

    let url;
    let method;

    if (editingEventId) {
        url = `/events/${editingEventId}`; // Endpoint chỉnh sửa
        method = 'PUT'; // Hoặc 'PATCH' tùy thuộc vào route của Laravel
        payload._method = 'PUT'; // Laravel cần _method cho PUT/PATCH qua POST
    } else {
        url = "{{ route('events.store') }}"; // Endpoint tạo mới
        method = 'POST';
    }

    fetch(url, {
        method: 'POST', // Luôn dùng POST vì Laravel cần _method cho PUT/PATCH
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        // Kiểm tra response.ok TẠI ĐÂY
        if (!response.ok) {
            // Nếu có lỗi HTTP (ví dụ: 400, 500)
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json().then(err => { 
                    throw new Error(err.message || "Lỗi server."); 
                });
            } else {
                return response.text().then(text => { 
                    throw new Error("Lỗi server: " + text.slice(0, 100) + "..."); 
                });
            }
        }
        return response.json(); // Phân tích JSON body
    })
    .then(data => { // `data` ở đây là JSON response từ server
        // Nếu server trả về dữ liệu thành công
        if (data.success || data.id) { // Kiểm tra `success` hoặc sự tồn tại của `id` của event mới/cập nhật
            alert(editingEventId ? 'Cập nhật sự kiện thành công!' : 'Tạo sự kiện thành công!');
            
            // Cập nhật lại events trong `window.allEvents`
            if (editingEventId) {
                // Tìm và cập nhật sự kiện trong mảng allEvents
                const index = window.allEvents.findIndex(ev => ev.id === editingEventId);
                if (index !== -1) {
                    // Cập nhật thuộc tính của sự kiện đã có
                    window.allEvents[index] = { ...window.allEvents[index], ...data.event }; 
                    // Giả định backend trả về object event đã cập nhật trong data.event
                }
                editingEventId = null; // Reset biến
            } else {
                // Thêm sự kiện mới vào mảng allEvents
                // Giả định backend trả về event mới tạo trong data.event
                if (data.event) {
                    window.allEvents.push(data.event);
                } else {
                    // Fallback nếu backend không trả về event mới (cần reload)
                    location.reload(); 
                    return; 
                }
            }

            // Đóng modal và reset form
            const modalEl = document.getElementById('createTaskModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            document.getElementById('createTaskForm').reset();
            
            // Sau khi cập nhật mảng, render lại lịch mà không cần tải lại trang
            renderEventsOnWeekView(window.allEvents); // Vẽ lại các sự kiện trên lịch tuần
            calculateTimeStats(window.allEvents); // Cập nhật thống kê thời gian
        } else {
            // Đây là trường hợp server trả về 200 OK nhưng logic nghiệp vụ báo lỗi (ví dụ: validation)
            alert('Lỗi nghiệp vụ: ' + (data.message || 'Không rõ nguyên nhân.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Lỗi: ' + error.message || 'Lỗi mạng hoặc máy chủ không phản hồi.');
    });

    }

    // --- Thống kê thời gian ---
    function calculateTimeStats(events) {
        // Helper: parse date string to Date object
        function parseDate(str) {
            // Handles both 'YYYY-MM-DDTHH:mm' and 'YYYY-MM-DD HH:mm:ss'
            if (!str) return null;
            if (str.includes('T')) return new Date(str);
            return new Date(str.replace(' ', 'T'));
        }

        const now = new Date();
        const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0);
        const todayEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999);

        // Tuần này: từ Chủ nhật đến thứ 7
        const weekStart = new Date(todayStart);
        weekStart.setDate(todayStart.getDate() - todayStart.getDay());
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        weekEnd.setHours(23,59,59,999);

        // Tháng này
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1, 0, 0, 0, 0);
        const monthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59, 999);

        let todayHours = 0, weekHours = 0, monthHours = 0, totalHours = 0, totalEvents = 0;

        events.forEach(ev => {
            let start = parseDate(ev.start_time);
            let end = ev.end_time ? parseDate(ev.end_time) : start;
            if (!start) return;
            if (ev.all_day) {
                // All day = 24h
                end = new Date(start);
                end.setHours(23,59,59,999);
            }
            let duration = (end - start) / (1000 * 60 * 60);
            if (duration <= 0) duration = ev.all_day ? 24 : 1;

            totalHours += duration;
            totalEvents++;

            // Today
            if (start <= todayEnd && end >= todayStart) {
                // Event overlaps today
                let overlapStart = start > todayStart ? start : todayStart;
                let overlapEnd = end < todayEnd ? end : todayEnd;
                let overlap = (overlapEnd - overlapStart) / (1000 * 60 * 60);
                if (overlap > 0) todayHours += overlap;
            }
            // This week
            if (start <= weekEnd && end >= weekStart) {
                let overlapStart = start > weekStart ? start : weekStart;
                let overlapEnd = end < weekEnd ? end : weekEnd;
                let overlap = (overlapEnd - overlapStart) / (1000 * 60 * 60);
                if (overlap > 0) weekHours += overlap;
            }
            // This month
            if (start <= monthEnd && end >= monthStart) {
                let overlapStart = start > monthStart ? start : monthStart;
                let overlapEnd = end < monthEnd ? end : monthEnd;
                let overlap = (overlapEnd - overlapStart) / (1000 * 60 * 60);
                if (overlap > 0) monthHours += overlap;
            }
        });

        function formatTime(hours) {
            const h = Math.floor(hours);
            const m = Math.round((hours - h) * 60);
            return `${h}h ${m}p`;
        }

        document.getElementById('todayTime').textContent = formatTime(todayHours);
        document.getElementById('weekTime').textContent = formatTime(weekHours);
        document.getElementById('monthTime').textContent = formatTime(monthHours);
        document.getElementById('totalEvents').textContent = totalEvents;
        document.getElementById('totalTime').textContent = formatTime(totalHours);
    }

    // Hàm hiển thị event lên lịch tuần (ví dụ: chèn vào các ô giờ tương ứng)
    function renderEventsOnWeekView(events) {
        const weekBody = document.getElementById("week-body");
        // Xóa tất cả các sự kiện cũ trước khi render lại
        const oldEvents = weekBody.querySelectorAll('.event-item');
        oldEvents.forEach(eventDiv => eventDiv.remove());

        const weekDates = getWeekDates(currentDate);
        const weekStart = weekDates[0].full; // Lấy Chủ Nhật đầu tuần hiện tại
        const weekEnd = weekDates[6].full; // Lấy Thứ Bảy cuối tuần hiện tại
        weekEnd.setHours(23,59,59,999); // Set đến cuối ngày Thứ Bảy

        const MILLISECONDS_PER_HOUR = 60 * 60 * 1000;
        const HOUR_HEIGHT = 60; // px chiều cao 1 giờ trong lịch

        events.forEach(event => {
            let start = new Date(event.start_time);
            let end = event.end_time ? new Date(event.end_time) : new Date(event.start_time);

            if(event.all_day) {
                // Nếu sự kiện cả ngày, mở rộng thời gian từ 0h đến 23:59:59
                start.setHours(0,0,0,0);
                end.setHours(23,59,59,999);
            }

            // Nếu sự kiện không nằm trong tuần hiện tại thì bỏ qua
            // Điều kiện này sẽ lọc các sự kiện từ `allEvents`
            if (end < weekStart || start > weekEnd) {
                return;
            }

            // Lặp qua từng ngày mà sự kiện kéo dài trong tuần hiện tại
            let currentSegmentStart = new Date(Math.max(start.getTime(), weekStart.getTime()));
            let currentSegmentEnd;

            while (currentSegmentStart <= end && currentSegmentStart <= weekEnd) {
                let dayOfSegment = new Date(currentSegmentStart);
                dayOfSegment.setHours(0,0,0,0); // Đặt về đầu ngày

                let dayEndBoundary = new Date(dayOfSegment);
                dayEndBoundary.setHours(23,59,59,999); // Cuối ngày hiện tại

                currentSegmentEnd = new Date(Math.min(end.getTime(), dayEndBoundary.getTime(), weekEnd.getTime()));

                if (currentSegmentStart > currentSegmentEnd) {
                    break; // Ngừng nếu không còn thời gian để vẽ
                }

                const jsDay = currentSegmentStart.getDay(); // 0: CN, 1: T2, ..., 6: T7
                const dayIndex = (jsDay + 6) % 7; // 0: T2, ..., 5: T7, 6: CN
                const startHour = currentSegmentStart.getHours() + currentSegmentStart.getMinutes()/60;
                const durationHours = (currentSegmentEnd.getTime() - currentSegmentStart.getTime()) / MILLISECONDS_PER_HOUR;

                const weekBody = document.getElementById("week-body");
                const startRow = Math.floor(startHour);
                const cell = weekBody.children[startRow]?.children[dayIndex];
                
                if (!cell) {
                     currentSegmentStart = new Date(dayEndBoundary.getTime() + MILLISECONDS_PER_HOUR); // Chuyển sang ngày tiếp theo
                     continue;
                }

                const eventDiv = document.createElement("div");
                eventDiv.classList.add("event-item");

                // Gán màu theo độ khó
                if (event.difficulty === 'easy') {
                    eventDiv.classList.add('event-easy');
                } else if (event.difficulty === 'medium') {
                    eventDiv.classList.add('event-medium');
                } else if (event.difficulty === 'hard') {
                    eventDiv.classList.add('event-hard');
                }

                const maxTitleLength = 18;
                let displayTitle = event.title.length > maxTitleLength
                    ? event.title.slice(0, maxTitleLength) + "..."
                    : event.title;
                eventDiv.innerHTML = `<strong title="${event.title.replace(/"/g, '&quot;')}">${displayTitle}</strong>`;

                const top = (startHour - startRow) * HOUR_HEIGHT;
                let height = durationHours * HOUR_HEIGHT;
                if (height < 10) height = 10; // Đảm bảo event có chiều cao tối thiểu

                eventDiv.style.position = "absolute";
                eventDiv.style.top = `${top}px`;
                eventDiv.style.height = `${height}px`;
                eventDiv.style.left = "2px";
                eventDiv.style.right = "2px";
                eventDiv.style.backgroundColor = '#0d6efd';
                eventDiv.style.color = '#fff';
                eventDiv.style.borderRadius = "4px";
                eventDiv.style.padding = "2px 5px";
                eventDiv.style.fontSize = "0.85rem";
                eventDiv.style.overflow = "hidden";
                eventDiv.style.whiteSpace = "nowrap";
                eventDiv.style.textOverflow = "ellipsis";
                eventDiv.style.cursor = "pointer";

                cell.style.position = "relative";
                cell.appendChild(eventDiv);

                // Thêm trình xử lý sự kiện click cho từng sự kiện
                eventDiv.addEventListener('click', function() {
                    // Fetch chi tiết sự kiện khi click
                    fetch(`/events/${event.id}`)
                        .then(res => res.json())
                        .then(data => {
                            let startDisplay = new Date(data.start_time).toLocaleString('vi-VN');
                            let endDisplay = data.end_time ? new Date(data.end_time).toLocaleString('vi-VN') : 'N/A';

                            let html = `
                                <div><strong>Môn học:</strong> ${data.title}</div>
                                <div><strong>Bắt đầu:</strong> ${startDisplay}</div>
                                <div><strong>Kết thúc:</strong> ${data.all_day ? 'Cả ngày' : endDisplay}</div>
                                <div><strong>Đánh giá:</strong> ${data.difficulty === 'easy' ? '😊 Dễ' : data.difficulty === 'medium' ? '😐 Trung bình' : data.difficulty === 'hard' ? '😰 Khó' : ''}</div>
                                <div><strong>Ghi chú:</strong> ${data.description ?? ''}</div>
                            `;
                            document.getElementById('eventDetailBody').innerHTML = html;
                            let modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                            const deleteBtn = document.getElementById('deleteEventBtn');
                            const editBtn = document.getElementById('editEventBtn');
                            deleteBtn.style.display = 'inline-block';
                            editBtn.style.display = 'inline-block';

                            // Xử lý nút Xóa
                            deleteBtn.onclick = function() {
                                if (confirm('Bạn có chắc muốn xóa sự kiện này?')) {
                                    fetch(`/events/${event.id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(res => {
                                        if (res.ok) {
                                            modal.hide();
                                            location.reload();
                                        } else {
                                            res.text().then(text => alert(text));
                                        }
                                    });
                                }
                            };

                            // Xử lý nút Chỉnh sửa
                            editBtn.onclick = function() {
                                editingEventId = data.id;
                                document.getElementById('taskTitle').value = data.title;
                                document.getElementById('allDayCheck').checked = data.all_day;
                                if (data.all_day) {
                                    document.getElementById('startDate').type = 'date';
                                    // Sử dụng hàm mới để định dạng
                                    document.getElementById('startDate').value = formatDateForInput(data.start_time, 'date');
                                    document.getElementById('endDateGroup').style.display = 'none';
                                } else {
                                    document.getElementById('startDate').type = 'datetime-local';
                                    // Sử dụng hàm mới để định dạng
                                    document.getElementById('startDate').value = formatDateForInput(data.start_time, 'datetime-local');
                                    document.getElementById('endDateGroup').style.display = 'block';
                                    document.getElementById('endDate').type = 'datetime-local';
                                    // Sử dụng hàm mới để định dạng
                                    document.getElementById('endDate').value = formatDateForInput(data.end_time, 'datetime-local');
                                }
                                document.getElementById('taskDescription').value = data.description ?? '';
                                toggleTimeInputs();
                                modal.hide();
                                let createModal = new bootstrap.Modal(document.getElementById('createTaskModal'));
                                createModal.show();
                                };

                            modal.show();
                        });
                });
                
                // Chuẩn bị cho phân đoạn tiếp theo (ngày tiếp theo)
                currentSegmentStart = new Date(currentSegmentEnd.getTime() + 1); // Bắt đầu từ 1ms sau khi phân đoạn này kết thúc
            }
        });

        // Sau khi render xong, cập nhật thống kê
        calculateTimeStats(events);
    }

    // ... (các hàm và biến khác của bạn)

    // Hàm mới để định dạng ngày/giờ cho input HTML
    function formatDateForInput(dateTimeString, type) {
        if (!dateTimeString) {
            return '';
        }
        const date = new Date(dateTimeString);
        if (isNaN(date.getTime())) { // Kiểm tra xem có phải là ngày không hợp lệ không
            console.error("Invalid date string provided to formatDateForInput:", dateTimeString);
            return '';
        }

        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');

        if (type === 'date') {
            return `${year}-${month}-${day}`;
        } else if (type === 'datetime-local') {
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
        return ''; // Mặc định trả về rỗng nếu loại không xác định
    }

// ... (các hàm khác của bạn)

    // Khởi tạo ban đầu
    taolich(currentDate.getMonth(), currentDate.getFullYear(), true);
    renderWeekView(currentDate);
    updateWeekTitle();
    renderEventsOnWeekView(window.allEvents); // Gọi lần đầu để hiển thị sự kiện

    function fetchEventsAndRender(focusEventId = null) {
    fetch("{{ route('calendar.layout') }}", {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        window.allEvents = data.events;
        // Nếu có focusEventId (vừa chỉnh sửa hoặc tạo), chuyển lịch sang ngày mới
        if (focusEventId) {
            const event = window.allEvents.find(ev => ev.id === focusEventId);
            if (event) {
                // Lấy ngày bắt đầu của sự kiện
                let start = event.start_time.replace(' ', 'T');
                let dateObj = new Date(start);
                currentDate = new Date(dateObj);
                selectedDate = new Date(dateObj);
                taolich(currentDate.getMonth(), currentDate.getFullYear(), true);
                renderWeekView(currentDate);
                updateWeekTitle();
            }
        }
        renderEventsOnWeekView(window.allEvents);
        // Đóng modal và reset form
        const modalEl = document.getElementById('createTaskModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
        document.getElementById('createTaskForm').reset();
    })
    .catch(error => {
        alert('Không thể tải lại sự kiện mới!');
        console.error(error);
    });
}
</script>
</body>
</html>
