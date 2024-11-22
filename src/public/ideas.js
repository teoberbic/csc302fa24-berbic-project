/*
File Name: ideas.js
Description: Contains the JavaScript functionality for the project.
Sources: 
    - https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js (for DOM manipulation)
    - Quiizzer code (for the structure of the code)
    - Tododles code (for the structure of CRUD operations)
    - https://chatgpt.com (for the structure of the selectIdea function)
*/
$(document).ready(function(){

  fetchIdeas();

  $(document).on('click', '#add-idea', addIdea);
  $(document).on('click', '#remove-idea', removeIdea);
  $(document).on('click', '#update-idea', updateIdea);
});


let selectedIdeaId = null;



// Function to fetch and display all ideas
function fetchIdeas() {
  $.ajax({
    url: '../router.php?action=get&category=ideas',
    method: 'GET',
    success: function(response) {
      console.log(`Status: 200`); // Log the status code
      console.log(response); // Log the response data

      // Check if the response is structured correctly
      if (response.success && Array.isArray(response.ideas)) {
        renderTable(response.ideas);
      } else {
        console.error("Invalid response structure:", response);
        renderTable([]); // Optionally pass an empty array if the structure is incorrect
      }
    },
    error: function(jqXHR) {
      console.error("Failed to fetch ideas:", jqXHR.responseText);
    }
  });
}

// Render ideas into the table
function renderTable(ideas) {
  const tableBody = document.getElementById("ideas-table").querySelector("tbody");
  tableBody.innerHTML = ""; // Clear existing rows

  // Check if the ideas array is empty
  if (ideas.length === 0) {
      const row = document.createElement("tr");
      row.innerHTML = `<td colspan="7">No ideas available.</td>`; // Adjust colspan as needed
      tableBody.appendChild(row);
      return; // Exit the function since there are no ideas to display
  }

  ideas.forEach(idea => {
      const row = document.createElement("tr");
      row.innerHTML = `
          <td>${idea.name}</td>
          <td>${idea.description}</td>
          <td>${idea.category}</td>
          <td>${idea.action_priority}</td>
          <td>${idea.createdAt}</td>
          <td>${idea.updatedAt}</td>
          <td><button onclick="selectIdea(${idea.id})">Select</button></td>
      `;
      tableBody.appendChild(row);
  });
}


// Add a new idea
function addIdea() {
  const name = document.getElementById("name").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const priority = document.getElementById("action_priority").value;

  $.ajax({
    url: '../router.php?action=add&category=ideas',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ name, description, category, priority }),
    success: function() {
      fetchIdeas(); // Refresh table
    }
  });
}

// Select an idea to populate form for updating - ChatGBT generated function - see source above
function selectIdea(id) {
  selectedIdeaId = id;
  
  // Find the idea row in the table
  const idea = Array.from(document.getElementById("ideas-table").querySelectorAll("tr"))
                      .find(row => {
                        const button = row.querySelector("button");
                        return button && button.getAttribute("onclick") === `selectIdea(${id})`;
                      });
                      // This function finds a specific table row (<tr>) in the table with the ID "ideas-table", 
                      //where a button inside the row has an onclick attribute matching the string selectIdea(${id}). 
                      //The ID is passed as an argument to the function.

  if (!idea) {
    console.error(`Idea with ID ${id} not found.`);
    return;
  }

  // Populate the form fields with the selected idea's data
  document.getElementById("name").value = idea.cells[0].textContent;
  document.getElementById("description").value = idea.cells[1].textContent;
  document.getElementById("category").value = idea.cells[2].textContent;
  document.getElementById("action_priority").value = idea.cells[3].textContent;
}

// Update an existing idea
function updateIdea() {
  if (!selectedIdeaId) {
    console.log("Please select an idea to update.");
    return;
  }

  const name = document.getElementById("name").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const priority = document.getElementById("action_priority").value;

  $.ajax({
    url: `../router.php?action=update&category=ideas&id=${selectedIdeaId}`,
    method: 'PUT',
    contentType: 'application/json',
    data: JSON.stringify({ name, description, category, priority }),
    success: function() {
      fetchIdeas(); // Refresh table
      selectedIdeaId = null; // Clear selected ID
    }
  });
}

// Delete the selected idea
function removeIdea() {
  if (!selectedIdeaId) {
    alert("Please select an idea to remove.");
    return;
  }

  $.ajax({
    url: `/router.php?action=delete&category=ideas&id=${selectedIdeaId}`,
    method: 'DELETE',
    success: function() {
      fetchIdeas(); // Refresh table
      selectedIdeaId = null;
    }
  });
}
