/*
 * File Name: todo.js
 * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
 * Sources:
 *    - Quizzer PDO Code (for creating SQL tables
 *    - chatgbt.com (assistance with modal CRUD operations and how they should be structured) & structure of the code in vanllia JS
 *    - https://developer.mozilla.org/en-US/docs/Web/API/Window/confirm (for the confirm dialog in deleteItem)
 *    - copilot.ai (for template creation with the createTodoItem function)
 *    - https://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/ (for the hierarchical structure of the to-do items)
 *    - https://www.youtube.com/watch?v=FVTBluc7AeM (creating a modal)
 */

document.addEventListener('DOMContentLoaded', () => { // ChatGPT: Changed from jQuery to vanilla JavaScript for this structure of code
    // Create references to the DOM elements that will be constantly accessed
    const todoList = document.getElementById('todo-list');
    const addRootBtn = document.getElementById('add-root-item');
    const modal = document.getElementById('modal');
    const closeButtonModal = document.getElementById('close-modal');
    const itemForm = document.getElementById('item-form');
    const modalTitle = document.getElementById('modal-title');
    const parentIdInput = document.getElementById('parent-id');

    // Fetches and renders a to-do list when page loads
    fetchTodos();

    // Event listener to navigate to a different page when a button is clicked
    const navigateBtn1 = document.getElementById('btn-home');
    const navigateBtn2 = document.getElementById('btn-ideas');

    navigateBtn1.addEventListener('click', () => {
        window.location.href = 'index.html';
    });

    navigateBtn2.addEventListener('click', () => {
        window.location.href = 'ideas.html';
    });
    addRootBtn.addEventListener('click', () => {
        openModal();
});

    // Event listener to close the modal
    closeButtonModal.addEventListener('click', closeModalFunc);

    // When a form submission is made, add or update the item
    itemForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const title = document.getElementById('item-title').value.trim(); // Trim removes any whitespace
        const parent_id = parentIdInput.value || null; // If no parent_id is provided, parent_id will be null
        const id = itemForm.dataset.itemId || null; // Get the item ID from the dataset

        if (title) {
            if (id) {
                // Update existing item
                updateItem(id, { title, parent_id });
            } else {
                // Add new item
                addItem(title, parent_id);
            }
            closeModalFunc(); // Close the modal after adding/updating the item
        }
    });

    // Function to fetch to-do items from the REST API
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
    // This function is recursive and will create child items if they exist
    // ChatGP helped immensely with all of this function
    function createTodoItem(item) {
        const li = document.createElement('li'); // Create a new list item element
        li.classList.add('todo-item');
        li.setAttribute('data-id', item.id); // Set the data-id attribute to the item's id

        // Checkbox
        const checkbox = document.createElement('input'); // Create a new input element
        checkbox.type = 'checkbox';
        checkbox.classList.add('checkbox');
        checkbox.checked = item.completed; // Set the checked status of the checkbox
        checkbox.addEventListener('change', () => toggleComplete(item.id, checkbox.checked)); // Add an event listener to toggle the completion status
        li.appendChild(checkbox); // Append the checkbox to the list item

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
            itemForm.dataset.itemId = item.id; // Store the item ID in the form's dataset
        } else { // Otherwise, it's an add operation
            modalTitle.textContent = 'Add New Item';
            document.getElementById('item-title').value = '';
            parentIdInput.value = parentId;
            delete itemForm.dataset.itemId; // Ensure no item ID is stored
        }
    }

    // Function to close the modal
    function closeModalFunc() {
        modal.classList.add('hidden');
        itemForm.reset();
        parentIdInput.value = '';
        modalTitle.textContent = 'Add New Item';
        delete itemForm.dataset.itemId; // Clear the item ID from the form's dataset
    }

    // Function to add a new item via the API
    function addItem(title, parent_id) {
        $.ajax({
            url: '../router.php?action=add&category=todo',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ title: title, parent_id: parent_id }),
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

    // Function to update an existing item via the API
    function updateItem(id, data) {
        data.id = id; // Ensure the ID is included in the data sent to the server
        $.ajax({
            url: '../router.php?action=update&category=todo',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    fetchTodos();
                } else {
                    alert('Error updating item.');
                }
            },
            error: function(error) {
                console.error('Error updating item:', error);
            }
        });
    }

    // Function to toggle the completion status of an item
    function toggleComplete(id, completed) {
        console.log("got here");
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
                data: JSON.stringify({id }),
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
