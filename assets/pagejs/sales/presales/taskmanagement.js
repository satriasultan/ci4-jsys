class KanbanBoard {
    constructor() {
        this.tasks = [];
        this.taskIdCounter = 1;
        this.init();
    }

    init() {
        this.setupEventListeners();
        // this.setupDragAndDrop();
        this.loadSampleData();
        this.updateColumnCounts();
    }

    setupEventListeners() {
        // Add task modal events
        document.getElementById('saveTask').addEventListener('click', () => this.addTask());
        document.getElementById('updateTask').addEventListener('click', () => this.updateTask());
        
        // Progress slider events
        document.getElementById('taskProgress').addEventListener('input', (e) => {
            document.getElementById('progressValue').textContent = e.target.value + '%';
        });
        
        document.getElementById('editTaskProgress').addEventListener('input', (e) => {
            document.getElementById('editProgressValue').textContent = e.target.value + '%';
        });

        // Form reset on modal close
        document.getElementById('addTaskModal').addEventListener('hidden.bs.modal', () => {
            document.getElementById('addTaskForm').reset();
            document.getElementById('progressValue').textContent = '0%';
        });
    }

    setupDragAndDrop() {
        const columns = ['todo-column', 'inprogress-column', 'review-column',  'done-column'];
        
        columns.forEach(columnId => {
            const column = document.getElementById(columnId);
            new Sortable(column, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onStart: (evt) => {
                    evt.item.classList.add('dragging');
                },
                onEnd: (evt) => {
                    evt.item.classList.remove('dragging');
                    this.updateTaskStatus(evt.item.dataset.taskId, evt.to.id);
                    this.updateColumnCounts();
                }
            });
        });
    }

    addTask() {
        const title = $('#taskTitle').val().trim();
        const description = $('#taskDescription').val().trim();
        const assignee = $('#taskAssignee').val().trim();
        const priority = $('#taskPriority').val();
        const progress = parseInt($('#taskProgress').val());
        const reporter = $('#taskReporter').val().trim();

        // ambil daftar subtask dari tabel
        const subtasks = [];
        $('#subTaskTableAdd tbody tr').each(function() {
            const subTitle = $(this).find('.subtask-input').val().trim();
            const isDone = $(this).find('.subtask-done').is(':checked');
            if (subTitle) {
                subtasks.push({ sub_title: subTitle, is_done: isDone });
            }
        });

        if (!title) {
            Swal.fire({
                icon: 'warning',
                title: 'Judul belum diisi',
                text: 'Please enter a task title',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        const formData = {
            title,
            description,
            assignee,
            priority,
            progress,
            reporter,
            status: 'todo',
            subtasks
        };

        $.ajax({
            url: HOST_URL + 'sales/presales/addTask',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    const newTask = { id: response.id, ...formData };
                    this.tasks.push(newTask);
                    this.renderTask(newTask);
                    this.updateColumnCounts();
                    $('#addTaskModal').modal('hide'); // pakai jQuery Bootstrap
                    $('#addTaskForm')[0].reset();
                    $('#progressValue').text('0%');
                    $('#subTaskTableAdd tbody').empty();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal memperbarui task'
                    });
                }
            },
            error: (xhr, status, error) => console.error(error)
        });
    }


    updateTask() {
        const taskId = $('#editTaskId').val().trim();
        const title = $('#editTaskTitle').val().trim();
        const description = $('#editTaskDescription').val().trim();
        const assignee = $('#editTaskAssignee').val().trim();
        const priority = $('#editTaskPriority').val();
        const progress = parseInt($('#editTaskProgress').val());
        const reporter = $('#editTaskReporter').val().trim();
        const status = $('#editTaskStatus').val().trim();

        // Ambil daftar subtask dari tabel
        const subtasks = [];
        $('#subTaskTableEdit tbody tr').each(function() {
            const subId = $(this).data('subid') || null;
            const subTitle = $(this).find('.subtask-input').val().trim();
            const isDone = $(this).find('.subtask-done').is(':checked');
            if (subTitle) {
                subtasks.push({ id: subId, sub_title: subTitle, is_done: isDone });
            }
        });

        if (!title) {
            Swal.fire({
                icon: 'warning',
                title: 'Judul belum diisi',
                text: 'Please enter a task title',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const formData = {
            id: taskId,
            title,
            description,
            assignee,
            priority,
            progress,
            status,
            reporter,
            subtasks
        };

        $.ajax({
            url: HOST_URL + 'sales/presales/updateTask',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Task berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {

                         // PERBAIKAN: Update task yang sudah ada, bukan push baru
                        const taskIndex = this.tasks.findIndex(task => task.id === taskId);
                        if (taskIndex !== -1) {
                            // Update task yang ada dengan data baru
                            this.tasks[taskIndex] = { ...this.tasks[taskIndex], ...formData };
                            
                            // Render ulang task yang di-update
                            this.renderTask(this.tasks[taskIndex]);
                        }
                        this.updateColumnCounts();
                        reload_tableTask()
                        this.loadSampleData()
                        // Tutup modal dengan instance yang sudah aktif
                        $('#editTaskModal').modal('hide');


                        // Reset form dan subtask
                        $('#editTaskForm')[0].reset();
                        $('#subTaskTableEdit tbody').empty();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal memperbarui task'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error('Update error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memperbarui task'
                });
            }
        });
    }



    updateTaskStatus(taskId, columnId) {
        const statusMap = {
            'todo-column': 'todo',
            'inprogress-column': 'inprogress',
            'review-column': 'review',
            'done-column': 'done'
        };

        const task = this.tasks.find(t => t.id == taskId);
        if (task) {
            task.status = statusMap[columnId];
            
            // Auto-update progress based on status
            if (task.status === 'done' && task.progress < 100) {
                task.progress = 100;
                this.updateTaskProgress(taskId, 100);
            } else if (task.status === 'todo' && task.progress > 0) {
                task.progress = 0;
                this.updateTaskProgress(taskId, 0);
            }
        }
    }

    updateTaskProgress(taskId, progress) {
        const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
        if (taskElement) {
            const progressBar = taskElement.querySelector('.progress-bar');
            const progressText = taskElement.querySelector('.progress-percentage');
            
            progressBar.style.width = progress + '%';
            progressText.textContent = progress + '%';
            
            // Update progress bar color based on percentage
            progressBar.className = 'progress-bar';
            if (progress < 25) {
                progressBar.classList.add('bg-danger');
            } else if (progress < 50) {
                progressBar.classList.add('bg-warning');
            } else if (progress < 100) {
                progressBar.classList.add('bg-info');
            } else {
                progressBar.classList.add('bg-success');
            }
        }
    }

    renderTask(task) {
        const columnId = task.status + '-column';
        const column = document.getElementById(columnId);
        
        // Remove empty placeholder if exists
        const emptyPlaceholder = column.querySelector('.empty-column');
        if (emptyPlaceholder) {
            emptyPlaceholder.remove();
        }
        
        const taskHTML = this.createTaskHTML(task);
        column.insertAdjacentHTML('beforeend', taskHTML);
        
        // Add animation class
        const taskElement = column.lastElementChild;
        taskElement.classList.add('new-task');
        setTimeout(() => taskElement.classList.remove('new-task'), 300);
    }

    createTaskHTML(task) {
        const progressBarClass = this.getProgressBarClass(task.progress);
        const priorityBadgeClass = `priority-${task.priority}`;

        // 🔹 Render daftar subtask (kalau ada)
        let subtasksHTML = '';
        if (Array.isArray(task.subtasks) && task.subtasks.length > 0) {
            subtasksHTML = `
                <div class="subtasks mt-2">
                    <small class="fw-bold d-block mb-1">
                        <i class="fa fa-list-check me-1"></i> Sub Tasks
                    </small>
                    <ul class="list-group list-group-flush">
                        ${task.subtasks
                            .map(
                                (sub, i) => `
                                <li class="list-group-item d-flex justify-content-between align-items-center px-2 py-1">
                                    <div>
                                        <input type="checkbox" class="form-check-input me-2" 
                                            ${sub.is_done === true || sub.is_done === 't' ? 'checked' : ''}
                                            disabled>
                                        <span ${sub.is_done === true || sub.is_done === 't' ? 'class="text-decoration-line-through"' : ''}>
                                            ${sub.sub_title}
                                        </span>
                                    </div>
                                    <small class="text-muted">${sub.inputby}</small>
                                </li>
                            `
                            )
                            .join('')}
                    </ul>
                </div>
            `;
        }

        // 🔹 Gabungkan ke dalam card utama
        return `
            <div class="task-card" data-task-id="${task.id}">
                <div class="task-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="kanban.editTask('${task.id}')">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="kanban.deleteTask('${task.id}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                
                <div class="task-title">${task.title}</div>
                ${task.description ? `<div class="task-description">${task.description}</div>` : ''}
                
                <div class="task-meta">
                    <span class="task-assignee">
                        <i class="fa fa-user"></i> ${task.assignee}
                    </span>
                    <span class="priority-badge ${priorityBadgeClass}">
                        ${task.priority}
                    </span>
                </div>
                
                <div class="progress-container mt-2">
                    <div class="progress">
                        <div class="progress-bar ${progressBarClass}" 
                            style="width: ${task.progress}%"></div>
                    </div>
                    <div class="progress-text">
                        <span>Progress</span>
                        <span class="progress-percentage">${task.progress}%</span>
                    </div>
                </div>

                ${subtasksHTML} <!-- ⬅️ tampilkan daftar subtask di bawah progress bar -->
            </div>
        `;
    }


    getProgressBarClass(progress) {
        if (progress < 25) return 'bg-danger';
        if (progress < 50) return 'bg-warning';
        if (progress < 100) return 'bg-info';
        return 'bg-success';
    }

    editTask(taskId) {
        const task = this.tasks.find(t => t.id === taskId);
        if (task) {
            document.getElementById('editTaskId').value = task.id;
            document.getElementById('editTaskTitle').value = task.title;
            document.getElementById('editTaskDescription').value = task.description;
            document.getElementById('editTaskAssignee').value = task.assignee;
            document.getElementById('editTaskPriority').value = task.priority;
            document.getElementById('editTaskProgress').value = task.progress;
            document.getElementById('editProgressValue').textContent = task.progress + '%';
            document.getElementById('editTaskReporter').value = task.reportedto;
            document.getElementById('editTaskIdDisplay').textContent = task.id;
            document.getElementById('editTaskStatus').value = task.status;

           // Kosongkan tabel subtasks
            const $tbody = $('#subTaskTableEdit tbody');
            $tbody.empty();

            // Render daftar subtask
            if (Array.isArray(task.subtasks) && task.subtasks.length > 0) {
                task.subtasks.forEach((st, index) => {
                    const newRow = `
                        <tr data-subid="${st.id || ''}">
                            <td>${index + 1}</td>
                            <td>
                                <input type="text" class="form-control subtask-input" 
                                    value="${st.sub_title}" readonly>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input subtask-done"
                                    ${st.is_done === 't' ? 'checked' : ''}>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-subtaskEdit">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $tbody.append(newRow);
                });
            } else {
                $tbody.append(`
                    <tr><td colspan="4" class="text-center text-muted">No sub tasks yet</td></tr>
                `);
            }
            // const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
            // modal.show();

            $('#editTaskModal').modal('show');
        }
    }

    deleteTask(taskId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This task will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove from internal list
                this.tasks = this.tasks.filter(t => t.id !== taskId);

                // Remove from DOM
                const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
                if (taskElement) {
                    taskElement.remove();
                }

                // Update column counts
                this.updateColumnCounts();

                // Optional: show success message
                Swal.fire({
                    title: 'Deleted!',
                    text: 'The task has been removed.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }


    updateColumnCounts() {
        const counts = {
            todo: this.tasks.filter(t => t.status === 'todo').length,
            inprogress: this.tasks.filter(t => t.status === 'inprogress').length,
            review: this.tasks.filter(t => t.status === 'review').length,
            done: this.tasks.filter(t => t.status === 'done').length
        };

        document.getElementById('todo-count').textContent = counts.todo;
        document.getElementById('inprogress-count').textContent = counts.inprogress;
        document.getElementById('review-count').textContent = counts.review;
        document.getElementById('done-count').textContent = counts.done;

        // Add empty placeholders if columns are empty
        this.addEmptyPlaceholders();
    }

    addEmptyPlaceholders() {
        const columns = [
            { id: 'todo-column', message: 'No tasks to do' },
            { id: 'inprogress-column', message: 'No tasks in progress' },
            { id: 'review-column', message: 'No tasks in review' },
            { id: 'done-column', message: 'No completed tasks' }
        ];

        columns.forEach(col => {
            const column = document.getElementById(col.id);
            const hasCards = column.querySelector('.task-card');
            const hasPlaceholder = column.querySelector('.empty-column');

            if (!hasCards && !hasPlaceholder) {
                column.innerHTML = `<div class="empty-column">${col.message}</div>`;
            } else if (hasCards && hasPlaceholder) {
                hasPlaceholder.remove();
            }
        });
    }

    loadSampleData() {
        fetch(HOST_URL + 'sales/presales/list_task_board', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.status && Array.isArray(result.tasks)) {
                this.tasks = result.tasks;

                // Atur taskIdCounter agar tidak conflict kalau nanti tambah manual
                this.taskIdCounter = this.tasks.length > 0
                    ? Math.max(...this.tasks.map(t => parseInt(t.id.replace('STM-', '') || 0))) + 1
                    : 1;

                this.tasks.forEach(task => this.renderTask(task));
                this.updateColumnCounts();
            } else {
                console.warn('No tasks returned or failed status');
            }
        })
        .catch(error => {
            console.error('Error loading tasks:', error);
        });
    }

    
}

var table;
var firstLoad = true;

function tableTask() {
    var initTable = function () {
        var table = $('#t_task');
        table.DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "language": languageDatatable(), // pastikan Anda punya fungsi ini
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": false,
            "bFilter": true,
            "lengthMenu": [
                [10, 25, 50, -1],
                ['10 rows', '25 rows', '50 rows', 'Show all']
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'pageLength',
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    exportOptions: {
                        columns: ':not(:first-child)' // sesuaikan jika kolom pertama adalah tombol aksi
                    }
                }
            ],
            "createdRow": function (row, data, dataIndex) {
                // Custom styling per cell jika dibutuhkan
                var priority = $('td', row).eq(4).text().trim(); // Priority column
                if (priority === 'High') {
                    $('td', row).eq(4).css('background-color', '#f8d7da'); // merah
                } else if (priority === 'Medium') {
                    $('td', row).eq(4).css('background-color', '#fff3cd'); // kuning
                } else if (priority === 'Low') {
                    $('td', row).eq(4).css('background-color', '#d4edda'); // hijau
                }
            },
            "ajax": {
                "url": HOST_URL + 'sales/presales/list_task', // Ganti dengan URL controller Anda
                "type": "POST",
                "data": function (data) {
                    // Tambahkan filter jika perlu
                    data.status_filter = $('#status_filter').val();
                    data.assignee_filter = $('#assignee_filter').val();
                    data.firstLoad = firstLoad ? 1 : 0;
                    firstLoad = false;
                },
                "dataFilter": function (data) {
                    var json = jQuery.parseJSON(data);
                    json.draw = json.dataTables.draw;
                    json.recordsTotal = json.dataTables.recordsTotal;
                    json.recordsFiltered = json.dataTables.recordsFiltered;
                    json.data = json.dataTables.data;
                    return JSON.stringify(json);
                }
            },
            "columnDefs": [
                {
                    "targets": [0], // Kolom Act (tombol aksi) tidak bisa di-sort
                    "orderable": false,
                },
            ],
        });
    };

    return initTable();
}

function reload_tableTask() {
    $('#t_task').DataTable().ajax.reload();
}

$('#addSubTaskBtnAdd').on('click', function() {
    const subTaskVal = $('#subTaskInput').val().trim();
    if (!subTaskVal) return;

    const rowCount = $('#subTaskTableAdd tbody tr').length + 1;
    const newRow = `
        <tr>
            <td>${rowCount}</td>
            <td><input type="text" class="form-control subtask-input" value="${subTaskVal}" readonly></td>
            <td class="text-center"><input type="checkbox" class="form-check-input subtask-done"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-subtask"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#subTaskTableAdd tbody').append(newRow);
    $('#subTaskInput').val('');
});


$('#addSubTaskBtnEdit').on('click', function() {
    const subTaskVal = $('#subTaskInputEdit').val().trim();
    if (!subTaskVal) return;

    const rowCount = $('#subTaskTableEdit tbody tr').length + 1;
    const newRow = `
        <tr>
            <td>${rowCount}</td>
            <td><input type="text" class="form-control subtask-input" value="${subTaskVal}" readonly></td>
            <td class="text-center"><input type="checkbox" class="form-check-input subtask-done"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-subtaskEdit"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
    $('#subTaskTableEdit tbody').append(newRow);
    $('#subTaskInputEdit').val('');
});


$(document).on('click', '.remove-subtask', function() {
    $(this).closest('tr').remove();
    $('#subTaskTableAdd tbody tr').each(function(i) {
        $(this).find('td:first').text(i + 1);
    });
});

$(document).on('click', '.remove-subtaskEdit', function() {
    $(this).closest('tr').remove();
    $('#subTaskTableEdit tbody tr').each(function(i) {
        $(this).find('td:first').text(i + 1);
    });
});


// Initialize the Kanban board when the page loads
let kanban;
document.addEventListener('DOMContentLoaded', () => {
    kanban = new KanbanBoard();
    tableTask()
});
