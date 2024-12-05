# API Testing with Postman

This directory contains the Postman collection for testing the To-Do List API.

## Files

- `ToDoAPI.postman_collection.json`: The Postman collection with all API tests.

## How to Run the API Tests

1. **Install Postman**

   - Download and install Postman from [https://www.postman.com/downloads/](https://www.postman.com/downloads/).

2. **Import the Collection**

   - Open Postman.
   - Click on **Import** in the top-left corner.
   - Select the `ToDoAPI.postman_collection.json` file from this directory.

3. **Set Up Environment (If Needed)**

   - If your API requires environment variables (e.g., `baseUrl`), create a new environment in Postman and set the variables accordingly.

4. **Run the Tests**

   - Open the imported collection.
   - Click on **Run** to execute all requests.
   - Verify that each request returns the expected response.

5. **Review Responses**

   - Check the status codes and response bodies to ensure the API is functioning correctly.

## Notes

- Make sure your server is running before executing the tests.
- Adjust the URLs in the requests if your server is running on a different host or port.
