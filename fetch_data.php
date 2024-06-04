<?php
// Fetch the token
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.baubuddy.de/index.php/login",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode(["username" => "365", "password" => "1"]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz",
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error #:" . $err);
}

$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON decode error: " . json_last_error_msg());
}

if (!isset($data['oauth']['access_token'])) {
    die("Failed to retrieve access token");
}

$accessToken = $data['oauth']['access_token'];

// Fetch the task data
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.baubuddy.de/dev/index.php/v1/tasks/select",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $accessToken,
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error #:" . $err);
}

$tasks = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON decode error: " . json_last_error_msg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .color-code {
            width: 50px;
            height: 20px;
            display: inline-block;
        }
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(fetchData, 3600000); // Auto-refresh every 60 minutes
            document.getElementById('searchInput').addEventListener('keyup', searchTable);
            document.getElementById('openModal').addEventListener('click', openModal);
            document.getElementById('closeModal').addEventListener('click', closeModal);
            document.getElementById('imageInput').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const image = document.getElementById('selectedImage');
                    image.src = e.target.result;
                    image.style.display = 'block';
                }
                reader.readAsDataURL(file);
            });

            function fetchData() {
                fetch('same_file.php') // This should be the same file URL
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error:', data.error);
                            return;
                        }
                        updateTable(data);
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            function updateTable(data) {
                const tbody = document.querySelector('#taskTable tbody');
                tbody.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.task}</td>
                        <td>${item.title}</td>
                        <td>${item.description}</td>
                        <td><div class="color-code" style="background-color:${item.colorCode};"></div></td>
                    `;
                    tbody.appendChild(row);
                });
            }

            function searchTable() {
                const input = document.getElementById('searchInput');
                const filter = input.value.toLowerCase();
                const table = document.getElementById('taskTable');
                const trs = table.getElementsByTagName('tr');

                for (let i = 1; i < trs.length; i++) {
                    const tds = trs[i].getElementsByTagName('td');
                    let display = false;
                    for (let j = 0; j < tds.length; j++) {
                        if (tds[j].innerText.toLowerCase().includes(filter)) {
                            display = true;
                            break;
                        }
                    }
                    trs[i].style.display = display ? '' : 'none';
                }
            }

            function openModal() {
                document.getElementById('myModal').style.display = 'block';
            }

            function closeModal() {
                document.getElementById('myModal').style.display = 'none';
            }
            
        });
    </script>
</head>
<body>
    <h1>API Verileri</h1>
    <input type="text" id="searchInput" placeholder="Search for tasks..">
    <br>
    <table id="taskTable">
        <thead>
            <tr>
                <th>Task</th>
                <th>Title</th>
                <th>Description</th>
                <th>Color Code</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tasks)) : ?>
                <?php foreach ($tasks as $task) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['task']); ?></td>
                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td><div class="color-code" style="background-color:<?php echo htmlspecialchars($task['colorCode']); ?>;"></div></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">No data available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Modal Trigger Button -->
    <button id="openModal">Open Modal</button>
    
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Select and Display Image</h2>
            <input type="file" id="imageInput" accept="image/*">
            <img id="selectedImage" style="display:none; max-width: 100%; height: auto;">
        </div>
    </div>
    <div id="myModal" style="display:none;">
        <div style="padding:20px; background:#fff; border:1px solid #ccc;">
            <input type="file" id="imageInput">
            <img id="selectedImage" src="#" alt="Selected Image" style="display:none; max-width:100%;">
        </div>
    </div>
</body>
</html>
