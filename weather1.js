// getting html elements from index.html
let weatherButton = document.getElementById("weatherButton");
let weatherInput = document.getElementById("weatherInput");
let weatherOutput = document.getElementById("weatherOutput");

//making a function that gets and displays weather data
async function getAndDisplayWeather(city) {
    let data;
    // Checking whether the user is online or offline
    if (navigator.onLine) {
        try {
            // Fetching all the weather related data from the server
            const response = await fetch(`http://localhost/Prototype3/connection.php?q=${encodeURIComponent(city)}`);
            data = await response.json();
            //saving the data in local storage
            localStorage.setItem(city, JSON.stringify(data));
        } catch (error) {
            alert("Something went wrong! Try again later.");
            return;
        }
    } else {
        // when offline, get the cached data from the local storage
        data = JSON.parse(localStorage.getItem(city));
        if (!data) {
            weatherOutput.innerHTML = `<p>No cached data for ${city}.</p>`;
            return;
        }
    }

    // what happens when no data is available
    if (data.length === 0) {
        weatherOutput.innerHTML = `<p>No data for ${city}.</p>`;
        return;
    }

    let currentDate = new Date();
    let date = currentDate.getDate();
    let month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][currentDate.getMonth()];
    let year = currentDate.getFullYear();

    // displaying weather information
    weatherOutput.innerHTML = `
        <h3>${data[0].city}</h3>
        <p>Day and Date: ${date} ${month} ${year}</p>
        <p>Main Weather: ${data[0].main}</p>
        <p>Weather Condition: ${data[0].description}</p>
        <p>Temperature: ${data[0].temp}°C</p>
        <p>Humidity: ${data[0].humidity}%</p>
        <p>Wind Speed: ${data[0].wind_speed} m/s</p>
        <p>Wind Direction: ${data[0].wind_deg}°</p>
        <p>Pressure: ${data[0].pressure} hPa</p>
    `;
}

// adding eventlistener to a button
weatherButton.addEventListener("click", function () {
    let city = weatherInput.value.trim();
    if (!city) {
        alert("You did not enter any city! Please enter one.");
        return;
    }
    // Fetching and displaying weather data
    getAndDisplayWeather(city);
});

