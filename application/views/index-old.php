<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kedai</title>

    <?php include 'templates/top.php'; ?>
</head>

<body>
    <div class="wrapper overlay-sidebar">
        <!-- include header -->
        <?php include 'templates/navbar-header.php'; ?>

        <div class="main-panel">
            <div class="container">
                <!-- include pages -->
                <?php include 'pages/' . $page_name . '.php'; ?>
            </div>
        </div>

        <?php include 'templates/footer.php'; ?>
        <?php include 'templates/quick-sidebar.php'; ?>
    </div>
    <?php include 'templates/bottom.php'; ?>
</body>



</html>