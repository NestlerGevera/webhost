<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Spice | My Cart</title>
    <link rel="icon" type="x-icon" href="../assets/images/eurospice-favicon.png">
    <link rel="stylesheet" href="css/responsive.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link
        href="https://cdn.prod.website-files.com/66a833f537135b05bc1eaecb/css/maria-bettinas-dynamite-site.webflow.05b59e178.css"
        rel="stylesheet" type="text/css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        @font-face {
            font-family: 'Maragsa Display';
            src: url('../assets/fonts/Maragsa-Display.otf') format('opentype');
            font-weight: normal;
            font-style: normal;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('../assets/images/eurospice-grid.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .navbar-container h3 {
            font-size: 1rem;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            padding: 10px;
            color: white;
        }

        .navbar-container h3 a {
            text-decoration: underline;
        }

        .navbar {
            background-color: #b82720;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-transparent">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#"><img src="assets/images/eurospice-logo-white-no-bg.svg"
                    alt=""></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="../view/client_pos.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="../view/client_products_pos.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="../view/client_profile.php">Profile</a>
                    </li>
                </ul>

                <div class="user-container d-flex me-auto mb-2 mb-lg-0">
                    <a class="log-out-btn" href="../index.html">Log out</a>
                </div>

                <div class="user-container d-flex me-auto mb-2 mb-lg-0">
                    <a class="log-out-btn" href="../view/cart.php">🛒</a>
                </div>

                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-warning" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>

</html>