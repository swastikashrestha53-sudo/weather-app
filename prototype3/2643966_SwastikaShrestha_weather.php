<?php

if (isset($_GET['q'])) { $cityName = $_GET['q']; } else { $cityName = "Sheffield"; }

$conn = mysqli_connect("localhost", "root", "");
// if($conn){ echo "Connection Successful <br>"; }else{ echo "Failed to connect".mysqli_connect_error(); }

$createDatabase = "CREATE DATABASE IF NOT EXISTS prototype3";
// if(mysqli_query($conn, $createDatabase)){ echo "Database Created or already Exists <br>"; }else{ echo "Failed to create database <br>".mysqli_connect_error(); }
mysqli_query($conn, $createDatabase);

mysqli_select_db($conn, 'prototype3');

$createTable = "CREATE TABLE IF NOT EXISTS weather (
    city VARCHAR(100) NOT NULL,
    temp FLOAT NOT NULL,
    condition_main VARCHAR(100),
    condition_desc VARCHAR(100),
    icon VARCHAR(50),
    pressure FLOAT NOT NULL,
    humidity FLOAT NOT NULL,
    wind_speed FLOAT NOT NULL,
    wind_deg FLOAT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
// if(mysqli_query($conn, $createTable)){ echo "Table Created or already Exists <br>"; }else{ echo "Failed to create table <br>".mysqli_connect_error(); }
mysqli_query($conn, $createTable);

$selectAllData = "SELECT * FROM weather WHERE city = '$cityName'";
$result = mysqli_query($conn, $selectAllData);

$needsFetch = false;
if (mysqli_num_rows($result) == 0) {
    $needsFetch = true;
} else {
    $row = mysqli_fetch_assoc($result);
    if ((time() - strtotime($row['created_at'])) > 7200) {
        mysqli_query($conn, "DELETE FROM weather WHERE city = '$cityName'");
        $needsFetch = true;
    }
}

if ($needsFetch) {
    $apiKey = "a9ed0c492bf8dc60f57e124f2084e6ee";
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$cityName&appid=$apiKey&units=metric";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    $temp      = $data['main']['temp'];
    $condMain  = $data['weather'][0]['main'];
    $condDesc  = $data['weather'][0]['description'];
    $icon      = $data['weather'][0]['icon'];
    $pressure  = $data['main']['pressure'];
    $humidity  = $data['main']['humidity'];
    $windSpeed = $data['wind']['speed'];
    $windDeg   = $data['wind']['deg'];

    $insertData = "INSERT INTO weather (city, temp, condition_main, condition_desc, icon, pressure, humidity, wind_speed, wind_deg)
                   VALUES ('$cityName', '$temp', '$condMain', '$condDesc', '$icon', '$pressure', '$humidity', '$windSpeed', '$windDeg')";
    // if(mysqli_query($conn, $insertData)){ echo "Data inserted Successfully"; }else{ echo "Failed to insert data".mysqli_error($conn); }
    mysqli_query($conn, $insertData);
}

// Fetching data from weather table based on city name again after insertion
$result = mysqli_query($conn, $selectAllData);
while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; }

// Encoding fetched data to JSON and sending as response
$json_data = json_encode($rows);
header('Content-Type: application/json');
echo $json_data;

?>