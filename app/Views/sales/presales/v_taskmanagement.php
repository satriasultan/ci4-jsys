<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">
<!-- 
    <link href="styles.css" rel="stylesheet"> -->
</head>
<style>
        /* Global Styles */
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Kanban Board Styles */
    /* .kanban-board {
        min-height: 70vh;
    } */

    .kanban-board {
        display: flex;
        gap: 1rem;
        min-height: 70vh;
        flex-wrap: nowrap;
    }

    .kanban-column {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        height: fit-content;
        min-height: 500px;
    }

    .column-header {
        padding: 1rem;
        border-radius: 12px 12px 0 0;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .column-body {
        padding: 1rem;
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
    }

    /* Task Card Styles */
    .task-card {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #007bff;
        /* cursor: move; */
        transition: all 0.3s ease;
        position: relative;
    }

    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .task-card.dragging {
        opacity: 0.5;
        transform: rotate(5deg);
    }

    /* Priority Indicators */
    .task-card.priority-high {
        border-left-color: #dc3545;
    }

    .task-card.priority-medium {
        border-left-color: #ffc107;
    }

    .task-card.priority-low {
        border-left-color: #28a745;
    }

    /* Task Card Content */
    .task-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-size: 1.1rem;
    }

    .task-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .task-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.8rem;
    }

    .task-assignee {
        background: #e9ecef;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        color: #495057;
    }

    .priority-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .priority-high {
        background: #dc3545;
        color: white;
    }

    .priority-medium {
        background: #ffc107;
        color: #212529;
    }

    .priority-low {
        background: #28a745;
        color: white;
    }

    /* Progress Bar Styles */
    .progress-container {
        margin-bottom: 0.5rem;
    }

    .progress {
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    .progress-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: #495057;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0.25rem;
    }

    /* Task Actions */
    .task-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .task-card:hover .task-actions {
        opacity: 1;
    }

    .task-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        margin-left: 0.25rem;
    }

    /* Drag and Drop Styles */
    .column-body.drag-over {
        background: rgba(0, 123, 255, 0.1);
        border: 2px dashed #007bff;
        border-radius: 8px;
    }

    .sortable-ghost {
        opacity: 0.4;
    }

    .sortable-chosen {
        transform: scale(1.05);
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 12px 12px;
    }

    /* Form Styles */
    /* Thumb styling - Webkit (Chrome, Safari, Edge) */
    input[type="range"]::-webkit-slider-thumb {
        background-color: #007bff;
        border: none;
        border-radius: 50%;
        height: 16px;
        width: 16px;
        margin-top: -6px; /* align thumb vertically */
        cursor: pointer;
        -webkit-appearance: none;
    }

    /* Track styling - Webkit */
    input[type="range"]::-webkit-slider-runnable-track {
        background-color: #dee2e6;
        height: 6px;
        border-radius: 3px;
    }

    /* Thumb styling - Firefox */
    input[type="range"]::-moz-range-thumb {
        background-color: #007bff;
        border: none;
        border-radius: 50%;
        height: 16px;
        width: 16px;
        cursor: pointer;
    }

    /* Track styling - Firefox */
    input[type="range"]::-moz-range-track {
        background-color: #dee2e6;
        height: 6px;
        border-radius: 3px;
    }

    /* Remove outline on focus */
    input[type="range"]:focus {
        outline: none;
    }


    /* Responsive Design */
    @media (max-width: 768px) {
        .kanban-column {
            margin-bottom: 1rem;
        }
        
        .column-body {
            max-height: 400px;
        }
        
        .task-card {
            padding: 0.75rem;
        }
        
        .task-title {
            font-size: 1rem;
        }
    }

    /* Animation for new tasks */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .task-card.new-task {
        animation: slideIn 0.3s ease;
    }

    /* Empty column placeholder */
    .empty-column {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 2rem;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        margin: 1rem 0;
    }

    /* Progress bar color variations */
    .progress-bar.bg-danger {
        background: linear-gradient(45deg, #dc3545, #c82333) !important;
    }

    .progress-bar.bg-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800) !important;
    }

    .progress-bar.bg-success {
        background: linear-gradient(45deg, #28a745, #1e7e34) !important;
    }

    .progress-bar.bg-info {
        background: linear-gradient(45deg, #17a2b8, #138496) !important;
    }

    .status-badge {
        font-size: 12px;        /* lebih kecil dari default badge besar */
        padding: 3px 8px;       /* padding yang pas untuk baris tabel */
        border-radius: 0.25rem; /* sudut agak membulat tapi tidak berlebihan */
        font-weight: 500;       /* sedikit tebal */
        line-height: 1.2;       /* agar tidak terlalu tinggi */
    }

    .kanban-scroll-wrapper {
        overflow-x: auto;
        padding-bottom: 1rem;
    }

    /* Ubah .kanban-board jadi flex horizontal */
    .kanban-board {
        display: flex;
        gap: 1rem;
        min-height: 70vh;
        flex-wrap: nowrap; /* supaya tidak wrap ke bawah */
    }

    /* Kolom tetap lebar konsisten */
    .kanban-column {
        min-width: 340px;
        max-width: 760px;
        flex: 0 0 auto;
    }

    #taskProgress {
        width: 100%;
        height: 8px;
        border-radius: 10px;
        background: #e0e0e0;
        outline: none;
        -webkit-appearance: none;
    }

    /* Webkit browsers (Chrome, Safari) */
    #taskProgress::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #4CAF50;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Firefox */
    #taskProgress::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #4CAF50;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    #taskProgress {
        width: 100%;
        height: 10px;
        border-radius: 5px;
        background: linear-gradient(90deg, #4CAF50 0%, #e0e0e0 0%);
        outline: none;
        -webkit-appearance: none;
        transition: background 0.3s ease;
    }

        #taskProgress::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #4CAF50;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
        transition: all 0.2s ease;
    }

        #taskProgress::-webkit-slider-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
    }


    #editTaskProgress {
        width: 100%;
        height: 8px;
        border-radius: 10px;
        background: #e0e0e0;
        outline: none;
        -webkit-appearance: none;
    }

    /* Webkit browsers (Chrome, Safari) */
    #editTaskProgress::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #4CAF50;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Firefox */
    #editTaskProgress::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #4CAF50;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    #editTaskProgress {
        width: 100%;
        height: 10px;
        border-radius: 5px;
        background: linear-gradient(90deg, #4CAF50 0%, #e0e0e0 0%);
        outline: none;
        -webkit-appearance: none;
        transition: background 0.3s ease;
    }

        #editTaskProgress::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #4CAF50;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
        transition: all 0.2s ease;
    }

        #editTaskProgress::-webkit-slider-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
    }

    
</style>
<body>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!--h1 class="m-0"><?php echo ucwords(strtolower(trim($x['namamenu'])));?></h1-->
                
                        <h1 class="m-0"> Sales Task Management</h1>
                        
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <div class="float-right" style="margin-right: 5px"><i style="color:transparent;"><?php //echo $t; ?></i> Menu ID <?php echo $version; ?></div>
                        <input type="hidden" id="classmenu" value="<?= str_replace('.','_',$kodemenu) ?>" required>
                        <?php foreach ($y as $y1) { ?>
                            <?php if( trim($y1->kodemenu)!=trim($kodemenu)) { ?>
                                <li class="breadcrumb-item"><a href="<?php echo base_url( trim($y1->linkmenu)) ; ?>"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo  trim($y1->namamenu); ?></a></li>
                            <?php } else { ?>
                                <li class="breadcrumb-item active"><i class="fa <?php echo trim($y1->iconmenu); ?>"></i> <?php echo trim($y1->namamenu); ?></li>
                            <?php } ?>
                        <?php } ?>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3"><h3 class="card-title">Sales Task Management</h3></li>
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-two-home-tab" data-bs-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Task Board</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-two-profile-tab" data-bs-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">Task List</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <!-- <h3 class="mb-4">
                                Sales Task Management
                            </h3> -->
                            <div class="text-left">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                    <i class="fa fa-plus"></i> Add New Task
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                            <div class="kanban-scroll-wrapper">
                                <!-- <div class="row kanban-board"> -->
                                <div class="kanban-board">

                                <!-- To Do Column -->

                                    <div class="kanban-column" data-status="todo">
                                        <div class="column-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fa fa-clipboard-list"></i>
                                                To Do
                                                <span class="badge bg-light text-dark ms-2" id="todo-count">0</span>
                                            </h5>
                                        </div>
                                        <div class="column-body" id="todo-column">
                                            <!-- Tasks will be dynamically added here -->
                                        </div>
                                    </div>


                                    <!-- In Progress Column -->

                                    <div class="kanban-column" data-status="inprogress">
                                        <div class="column-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fa fa-spinner"></i>
                                                In Progress
                                                <span class="badge bg-white text-dark ms-2" id="inprogress-count">0</span>
                                            </h5>
                                        </div>
                                        <div class="column-body" id="inprogress-column">
                                            <!-- Tasks will be dynamically added here -->
                                        </div>
                                    </div>


                                    <!-- Review Column -->

                                    <div class="kanban-column" data-status="review">
                                        <div class="column-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fa fa-eye"></i>
                                                Review
                                                <span class="badge bg-light text-dark ms-2" id="review-count">0</span>
                                            </h5>
                                        </div>
                                        <div class="column-body" id="review-column">
                                            <!-- Tasks will be dynamically added here -->
                                        </div>
                                    </div>


                                    <!-- Done Column -->

                                    <div class="kanban-column" data-status="done">
                                        <div class="column-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fa fa-check-circle"></i>
                                                Done
                                                <span class="badge bg-light text-dark ms-2" id="done-count">0</span>
                                            </h5>
                                        </div>
                                        <div class="column-body" id="done-column">
                                            <!-- Tasks will be dynamically added here -->
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form id="frm-examplee" action="#" method="POST">
                                        <div class="table-wrapper" style="overflow-x: auto;">
                                        <table id="t_task" class="table table-bordered table-striped"  style="text-wrap:nowrap; text-align: center;"  cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle">Act</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle">Task No.</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle;min-width: 100px;">Task Title</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle;min-width: 200px;">Description</td>
                                                
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle">Priority</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle">Assignee</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle">Report To</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle;min-width: 200px">Initial Progress (%)</td>
                                                <td style="font-weight: bolder; text-align: center; vertical-align: middle">Status</td>

                                                <td style="width: 50px;font-weight: bolder; text-align: center; vertical-align: middle">Input Date</td>
                                                <td style="width: 50px;font-weight: bolder; text-align: center; vertical-align: middle">Input By</td>
                                                <td style="width: 50px;font-weight: bolder; text-align: center; vertical-align: middle">Update By</td>
                                                <td style="width: 50px;font-weight: bolder; text-align: center; vertical-align: middle">Update Date</td>


                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                        </div>
                                    
                                    </form>
                                </div>
                            </div>
                    
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
    

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="taskTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskAssignee" class="form-label">Assignee</label>
                            <input type="text" class="form-control" id="taskAssignee" style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label for="taskPriority" class="form-label">Priority</label>
                            <select class="form-control" id="taskPriority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="taskProgress" class="form-label">Initial Progress (%)</label>
                            <input type="range" class="form-control" id="taskProgress" min="0" max="100" value="0">
                            <div class="d-flex justify-content-between">
                                <small>0%</small>
                                <small id="progressValue">0%</small>
                                <small>100%</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="taskReporter" class="form-label">Report To</label>
                            <input type="text" class="form-control" id="taskReporter" style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sub Tasks</label>

                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="subTaskInput" placeholder="Enter sub task...">
                                <button type="button" class="btn btn-success" id="addSubTaskBtnAdd">Add</button>
                            </div>

                            <table class="table table-sm table-bordered align-middle" id="subTaskTableAdd">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Sub Task</th>
                                    <th style="width: 10%">Done</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTask">Save Task</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task 
                        <span id="editTaskIdDisplay" class="badge bg-secondary ms-2" style="font-size: 0.75rem;"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <input type="hidden" id="editTaskId">
                        <div class="mb-3">
                            <label for="editTaskTitle" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="editTaskTitle" required>
                            <input type="hidden" id="editTaskStatus">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editTaskDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskAssignee" class="form-label">Assignee</label>
                            <input type="text" class="form-control" id="editTaskAssignee" style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label for="editTaskPriority" class="form-label">Priority</label>
                            <select class="form-control" id="editTaskPriority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskProgress" class="form-label">Progress (%)</label>
                            <input type="range" class="form-control" id="editTaskProgress" min="0" max="100">
                            <div class="d-flex justify-content-between">
                                <small>0%</small>
                                <small id="editProgressValue">0%</small>
                                <small>100%</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editTaskReporter" class="form-label">Report To</label>
                            <input type="text" class="form-control" id="editTaskReporter" style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sub Tasks</label>

                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="subTaskInputEdit" placeholder="Enter sub task...">
                                <button type="button" class="btn btn-success" id="addSubTaskBtnEdit">Add</button>
                            </div>

                            <table class="table table-sm table-bordered align-middle" id="subTaskTableEdit">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Sub Task</th>
                                    <th style="width: 10%">Done</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateTask">Update Task</button>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript" src="<?= base_url('assets/pagejs/sales/taskmanagement.js') ?>"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?php echo base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</body>
</html>
