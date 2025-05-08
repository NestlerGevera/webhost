<?php
// role_selection.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | Sign In As?</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <style>
        body {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            background-color: #F15B31;
        }

        .container-me {
            font-size: 10px;
            width: 400px;
            height: 150px;
            background-color: white;
            padding: 40px;
            border-radius: 100px;
            box-shadow: -30px 30px 0px rgba(0, 0, 0, 0.1);
            display: grid;
            place-items: center;
        }

        p {
            margin-top: 30px;
            margin-bottom: 10px;
        }

        nav a img {
            width: 70px;
            height: 70px;
        }
    </style>
</head>

<body>
    <nav>
        <a href="../index.html"><img src="../assets/images/arrow-down.png" alt="go back to login page"></a>
    </nav>

    <div class="container-me">
        <h1 class="text-center">Sign Up as Client</h1>
        <br>
        <form action="../view/sign_up.php" method="POST" class="role-form">
            <button type="submit" class="btn btn-warning">Sign In</button>
        </form>
    </div>

    <div class="container-me">
        <h1 class="text-center">Apply as our Supplier</h1>
        <br>
        <form action="../view/apply_supplier.php" method="POST" class="role-form">
            <button type="submit" class="btn btn-warning">Apply</button>
        </form>
    </div>

    <script>
        // simple confirmation before either form submits
        document.querySelectorAll('.role-form').forEach(form => {
            form.addEventListener('submit', e => {
                const btn = form.querySelector('button');
                const actionText = btn.textContent.trim();
                if (!confirm(`You clicked “${actionText}”. Continue?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-7z6c4e8b2f"></script>
</body>

</html>