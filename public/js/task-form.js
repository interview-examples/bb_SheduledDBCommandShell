function editTask(id, command, description, executeAt) {
    document.getElementById('taskId').value = id;
    document.getElementById('command').value = command;
    document.getElementById('description').value = description;
    document.getElementById('executeAt').value = executeAt;
    document.getElementById('formTitle').innerText = 'Edit Task';
    document.getElementById('action').value = 'edit';
}

function resetForm() {
    document.getElementById('taskId').value = '';
    document.getElementById('command').value = '';
    document.getElementById('description').value = '';
    document.getElementById('executeAt').value = '';
    document.getElementById('formTitle').innerText = 'Create Task';
    document.getElementById('action').value = 'create';
}

function deleteTask(id) {
    if (confirm('Are you sure you want to delete this task?')) {
        document.getElementById('taskId').value = id;
        document.getElementById('action').value = 'delete';
        document.getElementById('taskForm').submit();
    }
}

function deleteAllTasks() {
    if (confirm('Are you sure you want to delete all tasks?')) {
        document.getElementById('action').value = 'deleteAll';
        document.getElementById('taskForm').submit();
    }
}
