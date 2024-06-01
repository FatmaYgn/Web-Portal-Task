Hello dear web portal dev prospect!

This repository is a playground for your submission which should use PHP in the backend and HTML/JS in the frontend.

Before getting started, please hit the `Use this template` button to create a new repository on which you commit and push your code regularly for the task below. Once you are done, please mail us the link to your repository.

Good luck and have fun ☘️

# Task

Develop a web page that connects to a remote API, downloads a dataset, displays a table with the downloaded dataset, and provides some basic search and filter functions.

In particular, the web page should:

- Request the data located at `https://api.baubuddy.de/dev/index.php/v1/tasks/select` from PHP
- Display the downloaded data in a table showing `task`, `title`, `description` and `colorCode`. The displayed HTML element for the `colorCode` should have its color set accordingly
- Create a search which allows searching for any of the data in the table
- Implement auto-refresh functionality which requests the data from above every 60 minutes and updates the table with the new data without reloading the web page. The data should be fetched via PHP
- Outside the table, create a button that opens a modal. In this modal, there should be another button that allows you to select any image from the file system. When you have selected the image, it will be displayed in the modal
  - Note that this is not linked to the data from above
 
# Aim
This project aims to create a web page that connects to a remote API, downloads a dataset, and displays it in a table. The table will allow basic search and filter functions, and the page will also support auto-refreshing the data every 60 minutes without reloading. Additionally, the project includes a modal for selecting and displaying an image from the file system.

Here are the detailed steps and functionalities of the project:

# Functionalities:
  <ol>
    <li>Fetch Data from Remote API:
      <ul>
        <li>The project makes a POST request to https://api.baubuddy.de/index.php/login to obtain an access token.</li>
        <li>Using the access token, it makes another request to https://api.baubuddy.de/dev/index.php/v1/tasks/select to fetch task data.</li>
      </ul>
    </li>
    <li>Display Data in a Table:
      <ul>
        <li>The fetched data includes task details such as task, title, description, and colorCode.</li>
        <li>These details are displayed in an HTML table, where the colorCode is represented with the corresponding background color.</li>
      </ul>
    </li>
    <li>Search and Filter Functionality:
      <ul>
        <li>An input field allows users to search and filter the displayed data based on any text match within the table.</li>
      </ul>
    </li>
    <li>Auto-Refresh:
    <ul>
      <li>The table data is refreshed automatically every 60 minutes by re-fetching data from the API via PHP without reloading the page.</li>
    </ul>
    </li>
    <li>Image Selection and Display in a Modal:
    <ul>
      <li>A button opens a modal window.</li>
      <li>Inside the modal, another button allows users to select an image file from their system, which is then displayed within the modal.</li>
    </ul>
    </li>
  </ol>

# Authorization

It is mandatory that your requests to the API are authorized. You can find the required request below:

This is how it looks in `curl`:

```bash
curl --request POST \
  --url https://api.baubuddy.de/index.php/login \
  --header 'Authorization: Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz' \
  --header 'Content-Type: application/json' \
  --data '{
        "username":"365",
        "password":"1"
}'
```

The response will contain a JSON object, having the access token in `json["oauth"]["access_token"]`. For all subsequent calls this has to be added to the request headers as `Authorization: Bearer {access_token}`.

A possible implementation in `PHP` could be the following. You don't have to adopt this, you can also customize it or use another network library.

```php
<?php
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://api.baubuddy.de/index.php/login",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"username\":\"365\", \"password\":\"1\"}",
  CURLOPT_HTTPHEADER => [
    "Authorization: Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz",
    "Content-Type: application/json"
  ],
]);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
?>
```
