<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "eurospice_database";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT * FROM user_profile_db WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $json_data = json_encode($row);
        $module = 'user_profile_db';

        // Save to archive_bin
        $stmt = $conn->prepare("INSERT INTO archive_bin (module_name, original_id, data) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $module, $id, $json_data);
        $stmt->execute();

        // Do NOT delete the photo file yet — handle that in archive_bin permanent delete
        $conn->query("DELETE FROM user_profile_db WHERE id = $id");
    }
    header("Location: user_profile.php");
    exit;
}


// Handle Edit Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $fields = ['last_name', 'first_name', 'middle_name', 'age', 'birthdate', 'mobile_num', 'address1', 'city', 'province', 'postcode', 'country', 'area', 'email', 'department', 'experience', 'additional'];
    $updates = [];
    foreach ($fields as $field) {
        $value = $conn->real_escape_string($_POST[$field]);
        $updates[] = "$field = '$value'";
    }

    // Handle photo update
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        $photoPath = $uploadDir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath);
        $updates[] = "photo = '$photoPath'";
    }

    $updateStr = implode(", ", $updates);
    $conn->query("UPDATE user_profile_db SET $updateStr WHERE id = $id");
    header("Location: user_profile.php");
    exit;
}

// Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";
$sql = "SELECT * FROM user_profile_db";
if (!empty($search)) {
    $sql .= " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR department LIKE '%$search%' OR country LIKE '%$search%'";
}
$sql .= " ORDER BY id DESC";
$profiles = $conn->query($sql);

$editingId = isset($_GET['edit']) ? intval($_GET['edit']) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profiles</title>
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

        h2 {
            text-align: center;
        }

        .search-box {
            text-align: center;
            margin-bottom: 20px;
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

        .profile-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            width: 340px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            height: 450px;
        }

        .profile-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }

        .profile-card h3 {
            margin-top: 10px;
        }

        .actions a,
        .actions button {
            margin-right: 10px;
            background: #007BFF;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .actions a.delete {
            background: #dc3545;
        }

        .actions {
            margin-top: 50px;
        }

        form.edit-form {
            margin-top: 15px;
        }

        .field {
            margin-bottom: 10px;
        }

        .content {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100vh;
        }

        .search-box form {
            display: flex;
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

        <h2 style="color: white;">User Profiles</h2>

        <!-- <div class="search-box">
                <form method="GET" action="user_profile.php">
                    <input type="text" name="search" placeholder="Search by name, department, country..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Search</button>
                </form>
            </div> -->

        <div class="profile-container">
            <?php while ($row = $profiles->fetch_assoc()): ?>
                <div class="profile-card">
                    <?php if ($editingId === intval($row['id'])): ?>
                        <form class="edit-form" method="POST" enctype="multipart/form-data" action="user_profile.php">
                            <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                            <?php
                            function field($label, $name, $val)
                            {
                                echo "<div class='field'><label>$label</label><input type='text' name='$name' value=\"" . htmlspecialchars($val) . "\"></div>";
                            }
                            field("Last Name", "last_name", $row['last_name']);
                            field("First Name", "first_name", $row['first_name']);
                            field("Middle Name", "middle_name", $row['middle_name']);
                            field("Age", "age", $row['age']);
                            field("Birthdate", "birthdate", $row['birthdate']);
                            field("Mobile", "mobile_num", $row['mobile_num']);
                            field("Address 1", "address1", $row['address1']);
                            field("city", "city", $row['city']);
                            field("Province", "province", $row['province']);
                            field("Postcode", "postcode", $row['postcode']);
                            field("Country", "country", $row['country']);
                            field("Area", "area", $row['area']);
                            field("Email", "email", $row['email']);
                            field("Department", "department", $row['department']);
                            field("Experience", "experience", $row['experience']);
                            echo "<div class='field'><label>Additional Info</label><textarea name='additional'>" . htmlspecialchars($row['additional']) . "</textarea></div>";
                            ?>
                            <label>New Photo:</label>
                            <input type="file" name="photo"><br><br>
                            <button type="submit">Save</button>
                            <a href="user_profile.php">Cancel</a>
                        </form>
                    <?php else: ?>
                        <img src="<?= (!empty($row['photo']) && file_exists($row['photo'])) ? $row['photo'] : 'https://via.placeholder.com/300x180?text=No+Photo' ?>">
                        <h3><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></h3>
                        <p><strong>Department:</strong> <?= htmlspecialchars($row['department']) ?></p>
                        <p><strong>Country:</strong> <?= htmlspecialchars($row['country']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>Mobile:</strong> <?= htmlspecialchars($row['mobile_num']) ?></p>
                        <div class="actions">
                            <a href="user_profile.php?edit=<?= $row['id'] ?>">Edit</a>
                            <a class="delete" href="user_profile.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this profile?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

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
    </script>

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

<?php $conn->close(); ?>