// Sample data - In a real Laravel app, this would come from an API endpoint
const sampleResources = [
    {
        id: 1,
        title: "Clean Code - Robert C. Martin",
        subject: "programming",
        subjectName: "Lập trình",
        subjectColor: "bg-green-500",
        type: "book",
        typeIcon: "📚",
        description: "Cuốn sách hướng dẫn viết code sạch và dễ bảo trì",
        status: "in_progress",
        priority: 4,
        tags: ["advanced", "theory"],
        url: "https://example.com",
        deadline: "2024-12-31"
    },
    {
        id: 2,
        title: "JavaScript Fundamentals",
        subject: "programming",
        subjectName: "Lập trình",
        subjectColor: "bg-green-500",
        type: "video",
        typeIcon: "🎥",
        description: "Serie video học JavaScript từ cơ bản đến nâng cao",
        status: "completed",
        priority: 3,
        tags: ["basic", "practice"],
        url: "https://youtube.com/example"
    },
    {
        id: 3,
        title: "Calculus I - MIT OpenCourseWare",
        subject: "math",
        subjectName: "Toán học",
        subjectColor: "bg-blue-500",
        type: "course",
        typeIcon: "🎓",
        description: "Khóa học Giải tích I miễn phí từ MIT",
        status: "not_started",
        priority: 5,
        tags: ["advanced", "theory"],
        url: "https://ocw.mit.edu/example"
    },
    {
        id: 4,
        title: "IELTS Speaking Practice",
        subject: "english",
        subjectName: "Tiếng Anh",
        subjectColor: "bg-yellow-500",
        type: "article",
        typeIcon: "📄",
        description: "Bài viết hướng dẫn luyện nói IELTS hiệu quả",
        status: "in_progress",
        priority: 4,
        tags: ["practice", "exam"],
        deadline: "2024-11-15"
    },
    {
        id: 5,
        title: "Physics Lab Notes",
        subject: "science",
        subjectName: "Khoa học",
        subjectColor: "bg-purple-500",
        type: "note",
        typeIcon: "📝",
        description: "Ghi chú thí nghiệm vật lý",
        status: "completed",
        priority: 2,
        tags: ["practice", "theory"]
    },
    {
        id: 6,
        title: "React Complete Guide",
        subject: "programming",
        subjectName: "Lập trình",
        subjectColor: "bg-green-500",
        type: "course",
        typeIcon: "🎓",
        description: "Khóa học React.js toàn diện",
        status: "in_progress",
        priority: 5,
        tags: ["advanced", "practice"],
        deadline: "2024-12-01"
    }
];

let filteredResources = [...sampleResources];
let selectedTags = [];

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
                        <button onclick="changeStatus(${resource.id}, 'completed')" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/10 rounded-b-lg">Đã hoàn thành</button>
                    </div>
                </div>
            </div>
        </div>
        
        <h3 class="text-lg font-bold text-white mb-2">${resource.title}</h3>
        <p class="text-gray-300 text-sm mb-4 line-clamp-2">${resource.description}</p>
        
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-gray-400">Độ ưu tiên: ${priorityStars}</span>
            ${resource.deadline ? `<span class="text-sm text-orange-400"><i class="fas fa-calendar mr-1"></i>${resource.deadline}</span>` : ''}
        </div>
        
        <div class="flex flex-wrap gap-1 mb-4">
            ${resource.tags.map(tag => {
                const tagInfo = tagMap[tag];
                return `<span class="px-2 py-1 rounded-full text-xs font-medium text-white ${tagInfo.color}">${tagInfo.text}</span>`;
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

// Event listeners for filters
document.getElementById('searchInput').addEventListener('input', filterResources);
document.getElementById('subjectFilter').addEventListener('change', filterResources);
document.getElementById('typeFilter').addEventListener('change', filterResources);
document.getElementById('statusFilter').addEventListener('change', filterResources);

// Modal functions
function openModal() {
    document.getElementById('addResourceModal').classList.remove('hidden');
    document.getElementById('addResourceModal').classList.add('flex');
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

function changeStatus(id, newStatus) {
    const resource = sampleResources.find(r => r.id === id);
    if (resource) {
        resource.status = newStatus;
        renderResources();
    }
    document.getElementById(`dropdown-${id}`).classList.add('hidden');
}

function openResource(id) {
    const resource = sampleResources.find(r => r.id === id);
    if (resource && resource.url) {
        window.open(resource.url, '_blank');
    }
}

function editResource(id) {
    alert(`Chỉnh sửa tài nguyên ID: ${id} (Tính năng sẽ được phát triển)`);
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
document.getElementById('addResourceForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    // In a real Laravel app, you'd send this data to a backend API endpoint
    const formData = {
        id: sampleResources.length + 1, // This ID generation is for frontend demo only
        title: document.getElementById('resourceTitle').value,
        subject: document.getElementById('resourceSubject').value,
        subjectName: document.getElementById('resourceSubject').selectedOptions[0].text,
        subjectColor: getSubjectColor(document.getElementById('resourceSubject').value),
        type: document.getElementById('resourceType').value,
        typeIcon: document.getElementById('resourceType').selectedOptions[0].text.split(' ')[0],
        description: document.getElementById('resourceDescription').value,
        url: document.getElementById('resourceUrl').value,
        priority: parseInt(document.getElementById('resourcePriority').value),
        deadline: document.getElementById('resourceDeadline').value,
        status: 'not_started', // Default status for new resources
        tags: [...selectedTags]
    };

    sampleResources.push(formData); // Add to local array for demo
    filterResources(); // Re-render with new resource
    closeModal();
    
    // Show success message
    const message = document.createElement('div');
    message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    message.textContent = 'Tài nguyên đã được thêm thành công!';
    document.body.appendChild(message);
    setTimeout(() => message.remove(), 3000);
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

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Initialize
renderResources();