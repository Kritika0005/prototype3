<?php
//making connection between php and db
$serverName = "localhost";
$userName = "root";
$password = "";
$conn = mysqli_connect($serverName, $userName, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//making or creating db
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS Prototype3");
//seleting the made db
mysqli_select_db($conn, "Prototype3");

//creating a table
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS weatherInfo (
        city VARCHAR(50),
        main VARCHAR(50),
        description VARCHAR(250),
        temp FLOAT,
        humidity FLOAT,
        wind_speed FLOAT,
        wind_deg FLOAT,
        pressure FLOAT,
        time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

//using default city name or getting one
if (isset($_GET['q'])) {
    $cityName = $_GET['q'];
} else {
    $cityName = "Kathmandu";
}

// encoding city name in the url
$encodedCityName = urlencode($cityName);

//making sure there is data in db and api call is made for the same city only after 2 hrs
$result = mysqli_query($conn, "
    SELECT * FROM weatherInfo
    WHERE city = '$cityName' AND time > (NOW() - INTERVAL 2 HOUR)
");

// fetching data from api when not found
if (mysqli_num_rows($result) == 0) {
    $apiKey = "93db5fe84d1f7498aea0c434b854ebc7";
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$encodedCityName&units=metric&appid=$apiKey";
    $data = json_decode(file_get_contents($url), true);

    //inserting that fetched data in db
    if (isset($data['main'])) {
        mysqli_query($conn, "
            INSERT INTO weatherInfo (city, main, description, temp, humidity, wind_speed, wind_deg, pressure)
            VALUES (
                '$cityName',
                '{$data['weather'][0]['main']}',
                '{$data['weather'][0]['description']}',
                '{$data['main']['temp']}',
                '{$data['main']['humidity']}',
                '{$data['wind']['speed']}',
                '{$data['wind']['deg']}',
                '{$data['main']['pressure']}'
            )
        ");
    }else {
        // Return an error message for invalid city name
        $json_data = json_encode(["error" => "Please check the spelling of the city you entered and try again. City not found!"]);
        header('Content-Type: application/json');
        echo $json_data;
        exit;
    }
}
//fetching the inserted data
$result = mysqli_query($conn, "
    SELECT * FROM weatherInfo
    WHERE city = '$cityName' AND time > (NOW() - INTERVAL 2 HOUR)
");
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
//converting data to json
$json_data = json_encode($rows);

header('Content-Type: application/json');
echo $json_data;
?>