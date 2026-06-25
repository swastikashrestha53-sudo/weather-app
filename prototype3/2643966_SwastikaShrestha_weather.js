const defaultCity = "Sheffield";

async function fetchWeather(city) {
    const url = `http://localhost/prototype3/2643966_SwastikaShrestha_weather.php?q=${city}`;

    let data;

    if (navigator.onLine) {
        // Online part fetch from API and save to localStorage
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error("City not found");
            }
            const dataArray = await response.json();
            console.log(dataArray);

            if (!dataArray || dataArray.length === 0) {
                throw new Error("No data returned");
            }

            // Save data to localStorage
            localStorage.setItem(city, JSON.stringify(dataArray));
            data = dataArray[0];

        } catch (error) {
            alert("Oops! " + error.message);
            return;
        }
    } else {
        // Offline part Retrieve data from localStorage
        const cached = localStorage.getItem(city);
        if (!cached) {
            alert("You are offline and no cached data is available.");
            return;
        }
        data = JSON.parse(cached)[0];
    }

    document.getElementById("cityName").textContent = data.city;

    let now = new Date();
    let options = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
    document.getElementById("displayDate").textContent = now.toLocaleDateString('en-US', options);

    document.getElementById("conditionMain").textContent = data.condition_main;
    document.getElementById("conditionDesc").textContent = data.condition_desc;

    let iconCode = data.icon;
    document.getElementById("weatherIcon").src = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

    document.getElementById("tempDisplay").textContent = Math.round(data.temp) + "°C";
    document.getElementById("pressureVal").textContent = data.pressure;
    document.getElementById("humidityVal").textContent = data.humidity;
    document.getElementById("windSpeedVal").textContent = data.wind_speed;
    document.getElementById("windDirVal").textContent = data.wind_deg;
}

document.getElementById("searchBtn").addEventListener("click", () => {
    const cityValue = document.getElementById("cityInput").value;
    if (cityValue !== "") {
        fetchWeather(cityValue);
    } else {
        alert("Please enter city name first");
    }
});

fetchWeather(defaultCity);