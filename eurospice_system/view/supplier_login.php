<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Apply as our Supplier!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        body {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            background-color: #F15B31;
        }

        .container-me {
            width: 400px;
            height: 800px;
            background-color: white;
            padding: 40px;
            border-radius: 100px;
            margin-top: 100px;
            box-shadow: -30px 30px 0px rgba(0, 0, 0, 0.1);
        }

        p {
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .space {
            height: 100px;
            width: 100%;
        }

        nav a img {
            width: 70px;
            width: 70px;
        }
    </style>
    <script>
        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirm = document.getElementById("confirm_password").value;

            if (password !== confirm) {
                alert("Passwords do not match.");
                return false; // prevent form submission
            }

            return true; // allow submission
        }
    </script>

</head>

<body>
    <div class="spacer-large"></div>
    <nav>
        <a href="../index.html"><img src="../assets/images/arrow-down.png" alt="go back to login page"></a>
    </nav>
    <div class="container-me">
        <h1 class="text-center">Sign In as Supplier</h1>
        <form action="../view/dashboard.php" method="POST" onsubmit="return validatePasswords()">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required
                    pattern="(?=.*\d).{8,}"
                    title="Password must be at least 8 characters long and contain at least one number.">
            </div>

            <button type="submit" class="btn btn-warning">Sign In</button>
        </form>

        <p>Already have an Account?</p>

        <form action="../view/apply_supplier.php" method="$_GET">
            <button type="submit" class="btn btn-warning">Sign Up</button>
        </form>
    </div>

    <div class="space"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-7z6c4e8b2f"></script>
</body>

</html>