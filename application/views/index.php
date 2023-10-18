<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kedai</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="<?= base_url() ?>assets/logo-kedai.ico" type="image/x-icon" />

    <?php include 'templates/top.php'; ?>
</head>

<body>
    <div class="wrapper overlay-sidebar">
        <div class="main-header">

            <?php include 'templates/navbar-header.php'; ?>
        </div>

        <!-- sidebar -->

        <div class="main-panel">
            <div class="container">
                <!-- <div class="panel-header bg-primary-gradient">
                    <div class="page-inner py-5">
                        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                            <div>
                                <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                                <h5 class="text-white op-7 mb-2">Premium Bootstrap 4 Admin Dashboard</h5>
                            </div>
                            <div class="ml-md-auto py-2 py-md-0">
                                <a href="#" class="btn btn-white btn-border btn-round mr-2">Manage</a>
                                <a href="#" class="btn btn-secondary btn-round">Add Customer</a>
                            </div>
                        </div>
                    </div>
                </div> -->

                <?php include 'pages/' . $page_name . '.php'; ?>
            </div>

            <?php include 'templates/footer.php'; ?>
        </div>
        <?php include 'templates/quick-sidebar.php'; ?>
    </div>

    <?php include 'templates/bottom.php'; ?>
</body>

</html>