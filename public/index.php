<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOMES: Milflora Homes Subdivision App</title>
    
    <!-- Link to your custom CSS file -->
    <!--<link rel="stylesheet" href="assets/css/index_style.css">

    Leaflet CSS and JS for the map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Add a Google Font for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #E9EEDE; 
            color: #161A07;
        }

        .container {
            text-align: center;
            padding: 40px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            color: #161A07;
            background: linear-gradient(to right,hsl(110, 44.40%, 64.70%), #2B7F49);
        }

        .logo {
            margin-bottom: 10px; 
        }

        .logo img {
            width: 100px; 
            height: auto;
        }

        h1 {
            font-family: 'Garamond', sans-serif;
            font-size: 3rem;
            font-weight: 600;
            color: #161A07;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        p {
            font-size: 1.1rem;
            color: #161A07; 
            margin-bottom: 30px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap; 
        }

        .buttons a {
            text-decoration: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            color: #161A07; 
            position: relative;
            transition: color 0.3s ease;
        }

        .buttons a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background-color: #161A07; 
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }

        .buttons a:hover::after {
            width: 100%;
        }

        .buttons a:hover {
            color: #161A07; 
        }

        .buttons a.guest::after {
            background-color: #161A07; 
        }

        .buttons a.guest:hover {
            color: #161A07; 
        }

        #map {
            height: 500px;
            width: 100%;
            margin-top: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="assets/icons/templogo.png" alt="Milflora Homes Logo"> 
        </div>

        <h1>Welcome to Milflora Homes Homeowners Information System</h1>
        <p>Create a profile and stay up to date on what's happening in our neighborhood</p>
    
        <div class="buttons">
            <a href="login.php">Log In</a>
            <a href="signup.php">Sign Up</a>
            <a href="guest/guestRegistration.php" class="guest">Login as Guest</a> 
        </div>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Map Script -->
    <script>
        // Initialize the map
        var map = L.map('map').setView([14.9696, 120.9041], 16); // Milflora Homes coordinates
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://tile.openstreetmap.de/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add a marker for Milflora Homes
        var milfloraMarker = L.marker([14.9696, 120.9041]).addTo(map)
            .bindPopup('<b>Milflora Homes</b><br>Sabang, Baliwag, Bulacan.')
            .openPopup();

        // Add nearby landmarks
        var landmarks = [
            {
                name: "Baliwag Polytechnic College",
                coords: [14.9525, 120.9040],
                link: "https://example.com/baliwag-polytechnic"
            },
            {
                name: "St. Augustine Church",
                coords: [14.9570, 120.9015],
                link: "https://example.com/st-augustine-church"
            },
            {
                name: "Baliwag Market",
                coords: [14.9510, 120.9032],
                link: "https://example.com/baliwag-market"
            }
        ];

        landmarks.forEach(function(landmark) {
            L.marker(landmark.coords).addTo(map)
                .bindTooltip(`<b>${landmark.name}</b><br><a href="${landmark.link}" target="_blank">Learn more</a>`, {
                    permanent: false,
                    direction: 'top'
                });
        });

        // Lock the map in place
        map.dragging.disable();
        map.scrollWheelZoom.disable();
    </script>
</body>
</html>