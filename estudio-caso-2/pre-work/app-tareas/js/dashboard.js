document.addEventListener('DOMContentLoaded', function () {

    let isEditMode = false;
    let edittingId;
    let tasks = [];
    const TASK_API_URL = 'backend/tasks.php';
    const COMMENT_API_URL = 'backend/comments.php';

    async function loadTasks() {
        try {
            const response = await fetch(TASK_API_URL, {
                method: 'GET',
                credentials: 'include'
            });
            if (response.ok) {
                tasks = await response.json();
                renderTasks(tasks);
            } else {
                if (response.status === 401) {
                    window.location.href = 'index.html';
                }
                console.error("Error al obtener tareas");
            }
        } catch (err) {
            console.error(err);
        }
    }

    function renderTasks(tasks) {
        const taskList = document.getElementById('task-list');
        taskList.innerHTML = '';
        tasks.forEach(function (task) {
            let commentsList = '';
            if (task.comments && task.comments.length > 0) {
                commentsList = '<ul class="list-group list-group-flush">';
                task.comments.forEach(comment => {
                    commentsList += `
                        <li class="list-group-item">
                            ${comment.comment}
                            <button type="button" class="btn btn-sm btn-link remove-comment" 
                                data-taskid="${task.id}" data-commentid="${comment.id}">
                                Remove
                            </button>
                        </li>`;
                });
                commentsList += '</ul>';
            }

            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${task.title}</h5>
                        <p class="card-text">${task.description}</p>
                        <p class="card-text"><small class="text-muted">Due: ${task.dueDate}</small></p>
                        ${commentsList}
                        <button type="button" class="btn btn-sm btn-link add-comment" data-id="${task.id}">
                            Add Comment
                        </button>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button class="btn btn-secondary btn-sm edit-task" data-id="${task.id}">Edit</button>
                        <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                    </div>
                </div>`;
            taskList.appendChild(taskCard);
        });

        document.querySelectorAll('.edit-task').forEach(button => {
            button.addEventListener('click', handleEditTask);
        });

        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', handleDeleteTask);
        });

        document.querySelectorAll('.add-comment').forEach(button => {
            button.addEventListener('click', function (e) {
                document.getElementById("comment-task-id").value = e.target.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById("commentModal"));
                modal.show();
            });
        });

        document.querySelectorAll('.remove-comment').forEach(button => {
            button.addEventListener('click', handleDeleteComment);
        });
    }

    document.getElementById('comment-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const comment = document.getElementById('task-comment').value.trim();
        const taskId = parseInt(document.getElementById('comment-task-id').value);

        if (!comment) {
            alert('Please enter a comment before submitting.');
            return;
        }

        try {
            const response = await fetch(COMMENT_API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ task_id: taskId, comment: comment })
            });

            if (response.ok) {
                alert("Comment added successfully!");
                const modal = bootstrap.Modal.getInstance(document.getElementById('commentModal'));
                modal.hide();
                loadTasks();
            } else {
                console.error("Error adding comment");
            }
        } catch (err) {
            console.error(err);
        }
    });

    async function handleDeleteComment(event) {
        const commentId = parseInt(event.target.dataset.commentid);

        try {
            const response = await fetch(`${COMMENT_API_URL}?id=${commentId}`, {
                method: 'DELETE',
                credentials: 'include'
            });

            if (response.ok) {
                alert("Comment deleted successfully!");
                loadTasks();
            } else {
                console.error("Error deleting comment");
            }
        } catch (err) {
            console.error(err);
        }
    }

    function handleEditTask(event) {
        try {
            const taskId = parseInt(event.target.dataset.id);
            const task = tasks.find(t => t.id === taskId);

            document.getElementById('task-title').value = task.title;
            document.getElementById('task-desc').value = task.description;
            document.getElementById('due-date').value = task.dueDate;

            isEditMode = true;
            edittingId = taskId;

            const modal = new bootstrap.Modal(document.getElementById("taskModal"));
            modal.show();
        } catch (error) {
            alert("Error trying to edit a task");
            console.error(error);
        }
    }

    function handleDeleteTask(event) {
        const id = parseInt(event.target.dataset.id);
        const index = tasks.findIndex(t => t.id === id);
        tasks.splice(index, 1);
        loadTasks();
    }

    document.getElementById('task-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const title = document.getElementById("task-title").value;
        const description = document.getElementById("task-desc").value;
        const dueDate = document.getElementById("due-date").value;

        if (isEditMode) {
            const task = tasks.find(t => t.id === edittingId);
            task.title = title;
            task.description = description;
            task.dueDate = dueDate;
        } else {
            const newTask = {
                id: tasks.length + 1,
                title: title,
                description: description,
                dueDate: dueDate
            };
            tasks.push(newTask);
        }
        const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
        modal.hide();
        loadTasks();
    });

    document.getElementById('commentModal').addEventListener('show.bs.modal', function () {
        document.getElementById('comment-form').reset();
    });

    document.getElementById('taskModal').addEventListener('show.bs.modal', function () {
        if (!isEditMode) {
            document.getElementById('task-form').reset();
        }
    });

    document.getElementById("taskModal").addEventListener('hidden.bs.modal', function () {
        edittingId = null;
        isEditMode = false;
    });

    loadTasks();

});
