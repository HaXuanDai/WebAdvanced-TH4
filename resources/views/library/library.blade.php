<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/library.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 min-h-screen">
    <header>
    <div class="container-fluid">
      <div class="row d-flex align-items-center">
        <div class="col-sm-4 d-flex align-items-center">
            <a href="{{ route('calendar.layout') }}"><img src="{{ asset('frontend/images/calendar.png') }}" alt="logocld" class="iconheader calendar"></a>
            <a href="{{ route('tasks.index') }}"><img src="{{ asset('frontend/images/to-do-list.jpg') }}" alt="logotdl" class="iconheader todolist"></a>
            <a href="#"><img src="{{ asset('frontend/images/library.jpg') }}" alt="logolibrary" class="iconheader resourcelibrary"></a>
        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4 d-flex justify-content-end dropdown">
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
    <!-- Header -->
    <div class="glass-effect mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-start h-16">
                <button id="addResourceBtn" class="bg-gradient-to-r from-green-300 to-blue-500 hover:from-green-500 hover:to-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Thêm Tài nguyên
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="glass-effect rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-white mb-2" id="stat-total">{{ $total }}</div>
                <div class="text-gray-300">Tổng tài nguyên</div>
            </div>
            <div class="glass-effect rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2" id="stat-completed">{{ $completed }}</div>
                <div class="text-gray-300">Đã hoàn thành</div>
            </div>
            <div class="glass-effect rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2" id="stat-inprogress">{{ $inProgress }}</div>
                <div class="text-gray-300">Đang học</div>
            </div>
            <div class="glass-effect rounded-xl p-6 text-center">
                <div class="text-3xl font-bold text-gray-400 mb-2" id="stat-notstarted">{{ $notStarted }}</div>
                <div class="text-gray-300">Chưa bắt đầu</div>
            </div>
        </div>

        <div class="glass-effect rounded-xl p-6 mb-8">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <!-- Tìm kiếm -->
        <div class="lg:col-span-2">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Tìm kiếm tài nguyên..."
                       class="w-full pl-10 pr-4 py-2 bg-white/10 hover:bg-white/20 focus:bg-white/30 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <i class="fas fa-search absolute left-3 top-3 text-gray-300"></i>
            </div>
        </div>

        <!-- Lọc theo môn -->
        <select id="subjectFilter"
                class="bg-white/10 hover:bg-white/20 focus:bg-white/30 border border-white/20 text-white px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            <option value="">Tất cả môn học</option>
            <option value="math">Toán học</option>
            <option value="programming">Lập trình</option>
            <option value="english">Tiếng Anh</option>
            <option value="science">Khoa học</option>
        </select>

        <!-- Lọc theo loại -->
        <select id="typeFilter"
                class="bg-white/10 hover:bg-white/20 focus:bg-white/30 border border-white/20 text-white px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            <option value="">Tất cả loại</option>
            <option value="book">Sách</option>
            <option value="video">Video</option>
            <option value="article">Bài viết</option>
            <option value="course">Khóa học</option>
        </select>

        <!-- Lọc theo trạng thái -->
        <select id="statusFilter"
                class="bg-white/10 hover:bg-white/20 focus:bg-white/30 border border-white/20 text-white px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            <option value="">Tất cả trạng thái</option>
            <option value="not_started">Chưa bắt đầu</option>
            <option value="in_progress">Đang học</option>
            <option value="completed">Đã hoàn thành</option>
        </select>
    </div>
</div>

        <!-- Resources Grid -->
        <div id="resourcesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Resources will be inserted here by JavaScript -->
        </div>
    </div>

    <!-- Add Resource Modal -->
    <div id="addResourceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="glass-effect rounded-xl p-8 m-4 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Thêm Tài nguyên Mới</h2>
                <button id="closeModal" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="addResourceForm" class="space-y-4">
                <div>
                    <label class="block text-gray-300 mb-2">Tên tài nguyên *</label>
                    <input type="text" id="resourceTitle" required 
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:border-blue-400">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 mb-2">Môn học *</label>
                        <select id="resourceSubject" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-400">
                            <option value="">Chọn môn học</option>
                            <option value="math">Toán học</option>
                            <option value="programming">Lập trình</option>
                            <option value="english">Tiếng Anh</option>
                            <option value="science">Khoa học</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2">Loại tài nguyên *</label>
                        <select id="resourceType" required class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-400">
                            <option value="">Chọn loại</option>
                            <option value="book">📚 Sách</option>
                            <option value="video">🎥 Video</option>
                            <option value="article">📄 Bài viết</option>
                            <option value="course">🎓 Khóa học</option>
                            <option value="note">📝 Ghi chú</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2">Mô tả</label>
                    <textarea id="resourceDescription" rows="3" 
                              class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:border-blue-400"></textarea>
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2">URL hoặc Link</label>
                    <input type="url" id="resourceUrl" 
                           class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:border-blue-400">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 mb-2">Độ ưu tiên</label>
                        <select id="resourcePriority" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-400">
                            <option value="1">⭐ Thấp</option>
                            <option value="2">⭐⭐ Trung bình thấp</option>
                            <option value="3" selected>⭐⭐⭐ Trung bình</option>
                            <option value="4">⭐⭐⭐⭐ Cao</option>
                            <option value="5">⭐⭐⭐⭐⭐ Rất cao</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2">Deadline</label>
                        <input type="date" id="resourceDeadline" 
                               class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:border-blue-400">
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2">Tags</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm border border-gray-400 text-gray-300 hover:border-blue-400 hover:text-blue-400" data-tag="basic">Cơ bản</button>
                        <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm border border-gray-400 text-gray-300 hover:border-blue-400 hover:text-blue-400" data-tag="advanced">Nâng cao</button>
                        <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm border border-gray-400 text-gray-300 hover:border-blue-400 hover:text-blue-400" data-tag="practice">Thực hành</button>
                        <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm border border-gray-400 text-gray-300 hover:border-blue-400 hover:text-blue-400" data-tag="theory">Lý thuyết</button>
                        <button type="button" class="tag-btn px-3 py-1 rounded-full text-sm border border-gray-400 text-gray-300 hover:border-blue-400 hover:text-blue-400" data-tag="exam">Ôn thi</button>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="cancelBtn" class="px-6 py-2 text-gray-300 hover:text-white transition-colors">
                        Hủy
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-green-400 to-blue-500 hover:from-green-500 hover:to-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                        Thêm Tài nguyên
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sample data
        let sampleResources = [];
        let filteredResources = [];

        let selectedTags = [];

        // Khi load trang, lấy dữ liệu từ API
        async function fetchResourcesFromAPI() {
            try {
                const response = await fetch('/api/resources');
                if (!response.ok) throw new Error('Không thể lấy dữ liệu tài nguyên');
                const data = await response.json();
                // Chuẩn hóa dữ liệu để hiển thị
                sampleResources = data.map(resource => ({
                    ...resource,
                    subjectName: getSubjectName(resource.subject),
                    subjectColor: getSubjectColor(resource.subject),
                    typeIcon: getTypeIcon(resource.type),
                    tags: Array.isArray(resource.tags)
                        ? resource.tags
                        : (resource.tags ? JSON.parse(resource.tags) : []),
                }));
                filteredResources = [...sampleResources];
                renderResources();
            } catch (err) {
                alert('Lỗi khi tải tài nguyên: ' + err.message);
            }
        }

        // Gọi hàm fetch khi trang load
        fetchResourcesFromAPI();

        // Status mapping
        const statusMap = {
            'not_started': { text: 'Chưa bắt đầu', color: 'bg-gray-500' },
            'in_progress': { text: 'Đang học', color: 'bg-blue-500' },
            'completed': { text: 'Đã hoàn thành', color: 'bg-green-500' }
        };

        // Tag mapping
        const tagMap = {
            'basic': { text: 'Cơ bản', color: 'bg-gray-600' },
            'advanced': { text: 'Nâng cao', color: 'bg-red-500' },
            'practice': { text: 'Thực hành', color: 'bg-green-600' },
            'theory': { text: 'Lý thuyết', color: 'bg-blue-600' },
            'exam': { text: 'Ôn thi', color: 'bg-yellow-600' }
        };

        // Render resources
        function renderResources() {
            const grid = document.getElementById('resourcesGrid');
            grid.innerHTML = '';

            filteredResources.forEach(resource => {
                const card = createResourceCard(resource);
                grid.appendChild(card);
            });
            updateStats();
        }

        function formatDeadline(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            if (isNaN(d)) return '';
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function createResourceCard(resource) {
            const div = document.createElement('div');
            div.className = 'resource-card rounded-xl p-6 card-hover animate-fade-in';
            
            const statusInfo = statusMap[resource.status];
            const priorityStars = '⭐'.repeat(resource.priority);
            
            div.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl">${resource.typeIcon}</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium text-white ${resource.subjectColor}">
                            ${resource.subjectName}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium text-white ${statusInfo.color}">
                            ${statusInfo.text}
                        </span>
                        <div class="relative">
                            <button class="text-gray-400 hover:text-white" onclick="toggleDropdown(${resource.id})">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="dropdown-${resource.id}" class="hidden absolute right-0 mt-2 w-32 glass-effect rounded-lg shadow-lg z-10">
                                <button onclick="changeStatus(${resource.id}, 'not_started')" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/10 rounded-t-lg">Chưa bắt đầu</button>
                                <button onclick="changeStatus(${resource.id}, 'in_progress')" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/10">Đang học</button>
                                <button onclick="changeStatus(${resource.id}, 'completed')" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/10">Đã hoàn thành</button>
                                <button onclick="deleteResource(${resource.id})" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:text-white hover:bg-red-500 rounded-b-lg"><i class='fas fa-trash-alt mr-1'></i>Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-white mb-2">${resource.title}</h3>
                <p class="text-gray-300 text-sm mb-4 line-clamp-2">${resource.description}</p>
                
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-gray-400">Độ ưu tiên: ${priorityStars}</span>
                    ${resource.deadline ? `<span class="text-sm text-orange-400 flex items-center"><i class="fas fa-calendar mr-1"></i>${formatDeadline(resource.deadline)}</span>` : `<span class="text-sm text-gray-500 italic">Không có deadline</span>`}
                </div>
                
                <div class="flex flex-wrap gap-1 mb-4">
                    ${resource.tags.map(tag => {
                        const tagInfo = tagMap[tag];
                        return `<span class="px-2 py-1 rounded-full text-xs font-medium text-white ${tagInfo ? tagInfo.color : ''}">${tagInfo ? tagInfo.text : tag}</span>`;
                    }).join('')}
                </div>
                
                <div class="flex justify-between items-center">
                    <button onclick="openResource(${resource.id})" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        <i class="fas fa-external-link-alt mr-1"></i>Mở tài nguyên
                    </button>
                    <button onclick="editResource(${resource.id})" class="text-gray-400 hover:text-white">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            `;
            
            return div;
        }

        // Filter functions
        function filterResources() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const subject = document.getElementById('subjectFilter').value;
            const type = document.getElementById('typeFilter').value;
            const status = document.getElementById('statusFilter').value;

            filteredResources = sampleResources.filter(resource => {
                const matchSearch = !search || 
                    resource.title.toLowerCase().includes(search) ||
                    resource.description.toLowerCase().includes(search);
                const matchSubject = !subject || resource.subject === subject;
                const matchType = !type || resource.type === type;
                const matchStatus = !status || resource.status === status;

                return matchSearch && matchSubject && matchType && matchStatus;
            });

            renderResources();
        }

        // Event listeners
        document.getElementById('searchInput').addEventListener('input', filterResources);
        document.getElementById('subjectFilter').addEventListener('change', filterResources);
        document.getElementById('typeFilter').addEventListener('change', filterResources);
        document.getElementById('statusFilter').addEventListener('change', filterResources);

        // Modal functions
        function openModal() {
            document.getElementById('addResourceModal').classList.remove('hidden');
            document.getElementById('addResourceModal').classList.add('flex');
            window.editingResourceId = null;
            document.getElementById('addResourceForm').reset();
            selectedTags = [];
            updateTagButtons();
        }

        function closeModal() {
            document.getElementById('addResourceModal').classList.add('hidden');
            document.getElementById('addResourceModal').classList.remove('flex');
            document.getElementById('addResourceForm').reset();
            selectedTags = [];
            updateTagButtons();
        }

        // Tag selection
        function updateTagButtons() {
            document.querySelectorAll('.tag-btn').forEach(btn => {
                const tag = btn.dataset.tag;
                if (selectedTags.includes(tag)) {
                    btn.classList.add('border-blue-400', 'text-blue-400', 'bg-blue-400/20');
                } else {
                    btn.classList.remove('border-blue-400', 'text-blue-400', 'bg-blue-400/20');
                }
            });
        }

        // Action functions
        function toggleDropdown(id) {
            const dropdown = document.getElementById(`dropdown-${id}`);
            dropdown.classList.toggle('hidden');
        }

        // Sửa hàm changeStatus để lưu trạng thái vào database
        async function changeStatus(id, newStatus) {
            const resource = sampleResources.find(r => r.id === id);
            if (!resource) return;
            try {
                const response = await fetch(`/api/resources/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                if (!response.ok) {
                    const data = await response.json();
                    alert('Lỗi khi cập nhật trạng thái: ' + (data.message || 'Không thể cập nhật trạng thái'));
                    return;
                }
                // Cập nhật trạng thái trong mảng và render lại
                resource.status = newStatus;
                renderResources();
                // Đóng dropdown nếu có
                const dropdown = document.getElementById(`dropdown-${id}`);
                if (dropdown) dropdown.classList.add('hidden');
            } catch (err) {
                alert('Có lỗi xảy ra khi cập nhật trạng thái!');
            }
        }

        function openResource(id) {
            const resource = sampleResources.find(r => r.id === id);
            if (resource && resource.url) {
                window.open(resource.url, '_blank');
            }
        }

        function editResource(id) {
            const resource = sampleResources.find(r => r.id === id);
            if (!resource) return;
            openModal();
            document.getElementById('resourceTitle').value = resource.title;
            document.getElementById('resourceSubject').value = resource.subject;
            document.getElementById('resourceType').value = resource.type;
            document.getElementById('resourceDescription').value = resource.description || '';
            document.getElementById('resourceUrl').value = resource.url || '';
            document.getElementById('resourcePriority').value = resource.priority;
            let deadline = resource.deadline;
            if (deadline) {
                if (deadline.includes('T')) {
                    deadline = deadline.split('T')[0];
                } else if (deadline.length > 10) {
                    deadline = deadline.substring(0, 10);
                }
            }
            document.getElementById('resourceDeadline').value = deadline || '';
            selectedTags = Array.isArray(resource.tags) ? resource.tags : [];
            updateTagButtons();
            window.editingResourceId = id;
        }

        // Event listeners for modal
        document.getElementById('addResourceBtn').addEventListener('click', openModal);
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelBtn').addEventListener('click', closeModal);

        // Tag button listeners
        document.querySelectorAll('.tag-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const tag = btn.dataset.tag;
                if (selectedTags.includes(tag)) {
                    selectedTags = selectedTags.filter(t => t !== tag);
                } else {
                    selectedTags.push(tag);
                }
                updateTagButtons();
            });
        });

        // Form submission
        document.getElementById('addResourceForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                title: document.getElementById('resourceTitle').value,
                subject: document.getElementById('resourceSubject').value,
                type: document.getElementById('resourceType').value,
                description: document.getElementById('resourceDescription').value,
                url: document.getElementById('resourceUrl').value,
                priority: parseInt(document.getElementById('resourcePriority').value),
                deadline: document.getElementById('resourceDeadline').value,
                tags: [...selectedTags]
            };

            if (window.editingResourceId) {
                // Chế độ chỉnh sửa
                try {
                    const response = await fetch(`/api/resources/${window.editingResourceId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    });
                    if (!response.ok) {
                        const errorData = await response.json();
                        alert('Lỗi: ' + (errorData.message || 'Không thể cập nhật tài nguyên'));
                        return;
                    }
                    const updatedResource = await response.json();
                    // Cập nhật lại trong sampleResources
                    const idx = sampleResources.findIndex(r => r.id === window.editingResourceId);
                    if (idx !== -1) {
                        sampleResources[idx] = {
                            ...updatedResource,
                            subjectName: getSubjectName(updatedResource.subject),
                            subjectColor: getSubjectColor(updatedResource.subject),
                            typeIcon: getTypeIcon(updatedResource.type),
                            tags: Array.isArray(updatedResource.tags) ? updatedResource.tags : (updatedResource.tags ? JSON.parse(updatedResource.tags) : []),
                        };
                    }
                    filterResources();
                    closeModal();
                    window.editingResourceId = null;
                } catch (err) {
                    alert('Có lỗi xảy ra khi cập nhật!');
                }
            } else {
                // Chế độ thêm mới (giữ nguyên code cũ)
                try {
                    const response = await fetch('/api/resources', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        alert('Lỗi: ' + (data.message || 'Không thể thêm tài nguyên'));
                        return;
                    }

                    // Thêm vào danh sách và render lại
                    sampleResources.push({
                        ...data,
                        subjectName: getSubjectName(data.subject),
                        subjectColor: getSubjectColor(data.subject),
                        typeIcon: getTypeIcon(data.type),
                        tags: Array.isArray(data.tags)
                            ? data.tags
                            : (data.tags ? JSON.parse(data.tags) : []),
                    });
                    filterResources();
                    closeModal();

                    // Thông báo thành công
                    // ...
                } catch (err) {
                    alert('Có lỗi xảy ra khi gửi dữ liệu! ' + err);
                }
            }
        });

        function getSubjectColor(subject) {
            const colorMap = {
                'math': 'bg-blue-500',
                'programming': 'bg-green-500',
                'english': 'bg-yellow-500',
                'science': 'bg-purple-500'
            };
            return colorMap[subject] || 'bg-gray-500';
        }

        function getSubjectName(subject) {
            const nameMap = {
                'math': 'Toán học',
                'programming': 'Lập trình',
                'english': 'Tiếng Anh',
                'science': 'Khoa học'
            };
            return nameMap[subject] || subject;
        }

        function getTypeIcon(type) {
            const iconMap = {
                'book': '📚',
                'video': '🎥',
                'article': '📄',
                'course': '🎓',
                'note': '📝'
            };
            return iconMap[type] || type;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // Sau khi thêm tài nguyên mới thành công, cập nhật lại bảng thống kê
        function updateStats() {
            const total = sampleResources.length;
            const completed = sampleResources.filter(r => r.status === 'completed').length;
            const inProgress = sampleResources.filter(r => r.status === 'in_progress').length;
            const notStarted = sampleResources.filter(r => r.status === 'not_started').length;
            document.getElementById('stat-total').textContent = total;
            document.getElementById('stat-completed').textContent = completed;
            document.getElementById('stat-inprogress').textContent = inProgress;
            document.getElementById('stat-notstarted').textContent = notStarted;
        }

        // Thêm hàm xóa tài nguyên
        async function deleteResource(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa tài nguyên này?')) return;
            try {
                const response = await fetch(`/api/resources/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) {
                    const data = await response.json();
                    alert('Lỗi khi xóa: ' + (data.message || 'Không thể xóa tài nguyên'));
                    return;
                }
                // Xóa khỏi mảng và cập nhật giao diện
                sampleResources = sampleResources.filter(r => r.id !== id);
                filterResources();
                // Đóng dropdown nếu có
                const dropdown = document.getElementById(`dropdown-${id}`);
                if (dropdown) dropdown.classList.add('hidden');
            } catch (err) {
                alert('Có lỗi xảy ra khi xóa tài nguyên!');
            }
        }

        // Initialize
        renderResources();
    </script>
</body>
</html>