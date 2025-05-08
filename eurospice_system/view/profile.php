<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "eurospice_database";

    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle file upload
    $photoPath = "";
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $photoPath = $uploadDir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath);
    }

    // Get form values
    $last_name = $conn->real_escape_string($_POST["last_name"]);
    $first_name = $conn->real_escape_string($_POST["first_name"]);
    $middle_name = $conn->real_escape_string($_POST["middle_name"]);
    $age = $conn->real_escape_string($_POST["age"]);
    $birthdate = $conn->real_escape_string($_POST["birthdate"]);
    $mobile_num = $conn->real_escape_string($_POST["mobile_num"]);
    $address1 = $conn->real_escape_string($_POST["address1"]);
    $city = $conn->real_escape_string($_POST["city"]);
    $province = $conn->real_escape_string($_POST["province"]);
    $postcode = $conn->real_escape_string($_POST["postcode"]);
    $country = $conn->real_escape_string($_POST["country"]);
    $area = $conn->real_escape_string($_POST["area"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $department = $conn->real_escape_string($_POST["department"]);
    $experience = $conn->real_escape_string($_POST["experience"]);
    $additional = $conn->real_escape_string($_POST["additional"]);

    // Insert into database
    $sql = "INSERT INTO user_profile_db (
        last_name, first_name, middle_name, age, birthdate, mobile_num, address1, city,
        province, postcode, country, area, email, department, experience, additional, photo
    ) VALUES (
        '$last_name', '$first_name', '$middle_name', '$age', '$birthdate', '$mobile_num',
        '$address1', '$city', '$province', '$postcode', '$country', '$area',
        '$email', '$department', '$experience', '$additional', '$photoPath'
    )";

    if ($conn->query($sql) === TRUE) {
        $message = "Profile saved successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>User Profile Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background-color: #F15B31;
        }

        #sidebar-nav {
            display: flex;
            flex-direction: column;
        }

        #sidebar-nav a {
            text-decoration: none;
            color: white;
            padding: 10px;
            background-color: #F15B31;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        #sidebar-nav a:hover {
            background-color: #D14118;
        }

        .offcanvas-body {
            background-color: #F15B31;
            padding: 0;
        }

        .offcanvas-header {
            background-color: #F15B31;
            color: white;
        }

        #navbar-container {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 5px;
            border-radius: 15px;
            width: 90%;
        }

        .bg-primary {
            background-color: #D14118 !important;
        }

        #open-sidebar {
            text-decoration: none;
            color: white;
            font-weight: 800;
            font-size: 2rem;
            margin-left: 10px;
            margin-right: 10px;
        }

        .dropdown {
            margin-right: 10px;
        }

        #off-canvas-logo {
            max-width: 400px;
            max-height: 200px;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        table {
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }

        td {
            padding: 8px;
        }

        label {
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 5px;
        }

        button {
            padding: 10px 20px;
            font-weight: bold;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        input[type="file"] {
            margin-top: 10px;
        }

        button {
            display: block;
            margin: 20px auto 0;
            padding: 10px 20px;
            border: none;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            color: green;
            background: #e6ffe6;
            border: 1px solid #b3ffb3;
            border-radius: 5px;
        }

        .content {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100vh;
        }
    </style>
</head>

<body>

    <div class="content">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
            <img src="../assets/images/eurospice-logo.png" alt="Euro Spice Logo" width="100%" id="off-canvas-logo">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasLabel">Welcome Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <nav id="sidebar-nav">
                    <a href="admin_dashboard.php">Dashboard</a>
                    <a href="finance.php">Finance</a>
                    <a href="inventory.php">Inventory</a>
                    <a href="supplier_profile.php">Supplier Profile</a>
                    <a href="supplier_products.php">Supplier Products</a>
                    <a href="user_profile.php">User Profiles</a>
                    <a href="profile.php">Add User Profiles</a>

                    <!-- Add more links as needed -->
                </nav>
            </div>
        </div>

        <div class="navbar navbar-dark bg-primary" id="navbar-container">
            <a class="sidebar-emoji" id="open-sidebar" data-bs-toggle="offcanvas" href="#offcanvas" role="button" aria-controls="offcanvas">
                ☰
            </a>

            <h4 style="color: white">Euro Spice ERP</h4>

            <div class="dropdown" id="user-settings">
                <a class="dropdown-toggle text-white text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    👤
                </a>
                <ul class="dropdown-menu" id="user-dropdown" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#" onclick="logout(); return false;">Log Out</a></li>
                </ul>
            </div>
        </div>

        <div class="spacer-xxlarge"></div>

        <div id="currentDateTime" style="color: white;"></div>

        <div class="spacer-small"></div>

        <?php if (!empty($message)) {
            echo "<div class='message'>$message</div>";
        } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <h2>User Profile</h2>

            <label>Last Name:</label>
            <input type="text" name="last_name" required />

            <label>First Name:</label>
            <input type="text" name="first_name" required />

            <label>Middle Name:</label>
            <input type="text" name="middle_name" />

            <label>Age:</label>
            <input type="number" name="age" />

            <label>Birthdate:</label>
            <input type="date" name="birthdate" />

            <label>Mobile Number:</label>
            <input type="number" name="mobile_num" />

            <label>Street Address:</label>
            <input type="text" name="address1" />

            <label>Baranggay:</label>
            <input type="text" name="area" />

            <label for="Province">Province:</label>
            <select id="province" name="province">
                <option value="">Select Province</option>
            </select>

            <label for="city">City:</label>
            <select id="city" name="city">
                <option value="">Select City</option>
            </select>

            <label>ZIP/Postal code:</label>
            <input type="number" name="postcode" />

            <label>Country:</label>
            <input type="text" name="country" />

            <label>Email:</label>
            <input type="email" name="email" />

            <label>Department:</label>
            <input type="text" name="department" />

            <label>Experience:</label>
            <input type="text" name="experience" />

            <label>Additional Info:</label>
            <textarea name="additional" rows="4"></textarea>

            <label>Upload Photo:</label>
            <input type="file" name="photo" accept="image/*" />

            <button type="submit">Submit Profile</button>
        </form>
        <script>
            function updateDateTime() {
                const dateTimeElement = document.getElementById("currentDateTime");
                if (dateTimeElement) {
                    dateTimeElement.textContent = new Date().toLocaleString();
                }
            }
            setInterval(updateDateTime, 1000);
            updateDateTime();

            function toggleMenu() {
                document.getElementById("dropdownMenu").classList.toggle("show");
            }

            const dataUrl = 'https://raw.githubusercontent.com/flores-jacob/philippine-regions-provinces-cities-municipalities-barangays/master/philippine_provinces_cities_municipalities_and_barangays_2019v2.json';

            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            let provincesData = {};

            fetch(dataUrl)
                .then(response => response.json())
                .then(data => {
                    // Iterate through regions
                    for (const regionKey in data) {
                        const region = data[regionKey];
                        const provinces = region.province_list;

                        // Iterate through provinces
                        for (const provinceName in provinces) {
                            provincesData[provinceName] = provinces[provinceName].municipality_list;
                            const option = document.createElement('option');
                            option.value = provinceName;
                            option.textContent = provinceName;
                            provinceSelect.appendChild(option);
                        }
                    }
                })
                .catch(error => console.error('Error fetching data:', error));

            provinceSelect.addEventListener('change', function() {
                const selectedProvince = this.value;
                citySelect.innerHTML = '<option value="">Select a city/municipality</option>';

                if (selectedProvince && provincesData[selectedProvince]) {
                    const municipalities = provincesData[selectedProvince];
                    for (const cityName in municipalities) {
                        const option = document.createElement('option');
                        option.value = cityName;
                        option.textContent = cityName;
                        citySelect.appendChild(option);
                    }
                }
            });
        </script>
    </div>

    <script>
        // Logout function with notification
        function logout() {
            // Show confirmation dialog
            if (confirm("Are you sure you want to log out?")) {
                // Create notification element
                const notification = document.createElement('div');
                notification.style.position = 'fixed';
                notification.style.top = '20px';
                notification.style.left = '50%';
                notification.style.transform = 'translateX(-50%)';
                notification.style.backgroundColor = '#D14118';
                notification.style.color = 'white';
                notification.style.padding = '15px 25px';
                notification.style.borderRadius = '5px';
                notification.style.zIndex = '9999';
                notification.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                notification.textContent = 'Logging you out...';

                // Add notification to body
                document.body.appendChild(notification);

                // Set timeout to redirect after showing notification
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 1500); // Redirect after 1.5 seconds
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>

</html>