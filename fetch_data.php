<?php
function getAccessToken() {
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
        error_log("cURL Error: $err");
        return null;
    } else {
        $response = json_decode($response, true);
        return $response['oauth']['access_token'] ?? null;
    }
}

function fetchData($accessToken) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.baubuddy.de/dev/index.php/v1/tasks/select",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("cURL Error: $err");
        return null;
    } else {
        return $response;
    }
}

$accessToken = getAccessToken();
if ($accessToken) {
    $data = fetchData($accessToken);
    if ($data) {
        header('Content-Type: application/json');
        echo $data;
    } else {
        echo json_encode(["error" => "Failed to fetch data"]);
    }
} else {
    echo json_encode(["error" => "Failed to get access token"]);
}
?>
