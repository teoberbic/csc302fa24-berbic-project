/*
 * File Name: todo.js
 * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
 * Sources:
 *    - Quizzer PDO Code (for creating SQL tables
 *   - chatgbt.com (assistance with modal CRUD operations and how they should be structured)
 *   - https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm (for the confirm dialog line 195)
 *   - copilot.ai (for template creation with the createTodoItem function)
 */

document.addEventListener('DOMContentLoaded', () => {
    // Create references to the DOM elements that will be constantly accessed
    const todoList = document.getElementById('todo-list');
    const addRootBtn = document.getElementById('add-root-item');
    const modal = document.getElementById('modal');
    const closeModal = document.getElementById('close-modal');
    const itemForm = document.getElementById('item-form');
    const modalTitle = document.getElementById('modal-title');
    const parentIdInput = document.getElementById('parent-id');

    // Fetches and renders a to-do list when page loads
    fetchTodos();

    // The event listener to open modal for only adding a needed root todo item
    addRootBtn.addEventListener('click', () => {
        openModal();
    });

    // Event listener to close the modal
    closeModal.addEventListener('click', () => {
        closeModalFunc();
    });

    // When a form submission is made, add the item to the list
    itemForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const title = document.getElementById('item-title').value.trim(); // Trim removes any whitespace
        const parent_id = parentIdInput.value || null; // If no parent_id is provided, parent_id will be null

        if (title) {
            addItem(title, parent_id);
            closeModalFunc(); // Close the modal after adding the item
        }
    });

    // Function to fetch to-do items from my REST API
    function fetchTodos() {
        $.ajax({
            url: '../router.php?action=get&category=todo',
            method: 'GET',
            success: function(data) {
                todoList.innerHTML = '';
                data.forEach(item => {
                    const li = createTodoItem(item);
                    todoList.appendChild(li);
                });
            },
            error: function(error) {
                console.error('Error fetching todos:', error);
            }
        });
    }

    

    // Function to create a to-do item element
    function createTodoItem(item) {
        const li = document.createElement('li');
        li.classList.add('todo-item');
        li.setAttribute('data-id', item.id);

        // Checkbox
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.classList.add('checkbox');
        checkbox.checked = item.completed;
        checkbox.addEventListener('change', () => toggleComplete(item.id, checkbox.checked));
        li.appendChild(checkbox);

        // Title
        const title = document.createElement('span');
        title.classList.add('title');
        title.textContent = item.title;
        li.appendChild(title);

        // Actions
        const actions = document.createElement('div');
        actions.classList.add('actions');

        // Add Sub-Item Button
        const addBtn = document.createElement('button');
        addBtn.innerHTML = '<i class="fas fa-plus"></i>';
        addBtn.title = 'Add Sub-Item';
        addBtn.classList.add('btn', 'action-btn');
        addBtn.addEventListener('click', () => {
            openModal(item.id);
        });
        actions.appendChild(addBtn);

        // Edit Button
        const editBtn = document.createElement('button');
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.title = 'Edit Item';
        editBtn.classList.add('btn', 'action-btn');
        editBtn.addEventListener('click', () => {
            openModal(null, item);
        });
        actions.appendChild(editBtn);

        // Delete Button
        const deleteBtn = document.createElement('button');
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
        deleteBtn.title = 'Delete Item';
        deleteBtn.classList.add('btn', 'action-btn');
        deleteBtn.addEventListener('click', () => deleteItem(item.id));
        actions.appendChild(deleteBtn);

        li.appendChild(actions);

        // If the item has children, render them recursively
        if (item.children && item.children.length > 0) {
            const childUl = document.createElement('ul');
            childUl.classList.add('child-items');
            item.children.forEach(child => {
                const childLi = createTodoItem(child);
                childUl.appendChild(childLi);
            });
            li.appendChild(childUl);
        }

        return li;
    }

    // Function to open the modal
    function openModal(parentId = null, item = null) {
        modal.classList.remove('hidden');
        if (item) {  // If an item is provided, it's an edit operation
            modalTitle.textContent = 'Edit Item';
            document.getElementById('item-title').value = item.title;
            parentIdInput.value = item.parent_id || '';
        } else { // Otherwise, it's an add operation
            modalTitle.textContent = 'Add New Item';
            document.getElementById('item-title').value = '';
            parentIdInput.value = parentId;
        }
    }

    // Function to close the modal
    function closeModalFunc() {
        modal.classList.add('hidden');
        itemForm.reset();
        parentIdInput.value = '';
        modalTitle.textContent = 'Add New Item';
    }

    // Function to add a new item via the API
    function addItem(title, parent_id) {
        $.ajax({
            url: '../router.php?action=add&category=todo',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ title, parent_id }),
            success: function(data) {
                if (data.success) {
                    fetchTodos();
                } else {
                    alert('Error adding item.');
                }
            },
            error: function(error) {
                console.error('Error adding item:', error);
            }
        });
    }

    // Function to toggle the completion status of an item
    function toggleComplete(id, completed) {
        $.ajax({
            url: '../router.php?action=update&category=todo',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ id, completed }),
            success: function(data) {
                if (!data.success) {
                    alert('Error updating item.');
                }
            },
            error: function(error) {
                console.error('Error updating item:', error);
            }
        });
    }

    // Function to delete an item via the API
    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this item and all its sub-items?')) {  // Display a confirmation dialog before deleting
            $.ajax({
                url: '../router.php?action=delete&category=todo',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify({ id }),
                success: function(data) {
                    if (data.success) {
                        fetchTodos();
                    } else {
                        alert('Error deleting item.');
                    }
                },
                error: function(error) {
                    console.error('Error deleting item:', error);
                }
            });
        }
    }
});
