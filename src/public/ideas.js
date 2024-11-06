$(document).ready(function(){

  fetchIdeas();

  $(document).on('click', '#add-idea', addIdea);
  $(document).on('click', '#remove-idea', removeIdea);
  $(document).on('click', '#update-idea', updateIdea);
});


let selectedIdeaId = null;



// Function to fetch and display all ideas
function fetchIdeas() {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "../router.php?action=get&category=ideas", true);

  xhr.onload = function() {
    console.log(`Status: ${xhr.status}`); // Log the status code
    if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        console.log(response); // Log the response data

        // Check if the response is structured correctly
        if (response.success && Array.isArray(response.ideas)) {
            renderTable(response.ideas);
        } else {
            console.error("Invalid response structure:", response);
            renderTable([]); // Optionally pass an empty array if the structure is incorrect
        }
    } else {
        console.error("Failed to fetch ideas:", xhr.responseText);
    }
};

  xhr.send();
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
          <td>${idea.date_created}</td>
          <td>${idea.date_updated}</td>
          <td><button onclick="selectIdea(${idea.id})">Select</button></td>
      `;
      tableBody.appendChild(row);
  });
}


// Add a new idea
function addIdea() {
  console.log("got here");
  const name = document.getElementById("name").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const priority = document.getElementById("priority").value;
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../router.php?action=add&category=ideas", true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onload = function() {
    if (xhr.status === 200) {
      fetchIdeas(); // Refresh table
    }
  };

  const data = JSON.stringify({ name, description, category, priority });
  xhr.send(data);
}

// Select an idea to populate form for updating
function selectIdea(id) {
  selectedIdeaId = id;
  const idea = Array.from(document.getElementById("ideas-table").querySelectorAll("tr"))
                      .find(row => row.querySelector("button").getAttribute("onclick") === `selectIdea(${id})`);

  document.getElementById("name").value = idea.cells[0].textContent;
  document.getElementById("description").value = idea.cells[1].textContent;
  document.getElementById("category").value = idea.cells[2].textContent;
  document.getElementById("priority").value = idea.cells[3].textContent;
}

// Update an existing idea
function updateIdea() {
  if (!selectedIdeaId) {
    alert("Please select an idea to update.");
    return;
  }

  const name = document.getElementById("name").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const priority = document.getElementById("priority").value;

  const xhr = new XMLHttpRequest();
  xhr.open("PUT", `/router.php?action=updateIdea&id=${selectedIdeaId}`, true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onload = function() {
    if (xhr.status === 200) {
      fetchIdeas(); // Refresh table
      selectedIdeaId = null; // Clear selected ID
    }
  };

  const data = JSON.stringify({ name, description, category, priority });
  xhr.send(data);
}

// Delete the selected idea
function removeIdea() {
  if (!selectedIdeaId) {
    alert("Please select an idea to remove.");
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("DELETE", `/router.php?action=deleteIdea&id=${selectedIdeaId}`, true);

  xhr.onload = function() {
    if (xhr.status === 200) {
      fetchIdeas(); // Refresh table
      selectedIdeaId = null;
    }
  };

  xhr.send();
}
