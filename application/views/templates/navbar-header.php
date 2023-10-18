<!-- Logo Header -->
<div class="logo-header" data-background-color="blue2">
    <a href="<?= base_url('order') ?>" class="logo">
        <img src="<?= base_url('') ?>assets/logo-kedai.png" alt="navbar brand" class="navbar-brand">
    </a>
    <!-- <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon">
            <i class="icon-menu"></i>
        </span>
    </button> -->
    <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
    <div class="nav-toggle">
        <button class="btn btn-toggle sidenav-overlay-toggler">
            <i class="icon-menu"></i>
        </button>
    </div>
</div>
<!-- End Logo Header -->

<!-- Navbar Header -->
<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
    <div class="container-fluid">
        <div class="collapse" id="search-nav">
            <form class="navbar-left navbar-form nav-search mr-md-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-search pr-1">
                            <i class="fa fa-search search-icon"></i>
                        </button>
                    </div>
                    <input type="text" placeholder="Search ..." class="form-control">
                </div>
            </form>
        </div>
        <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
            <li class="nav-item toggle-nav-search hidden-caret">
                <a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
                    <i class="fa fa-search"></i>
                </a>
            </li>

            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="<?= base_url('manage/menu_add') ?>">
                    Tambah Menu
                </a>
            </li>
            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="<?= base_url('order') ?>">
                    Pemesanan
                </a>
            </li>
            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="<?= base_url('kitchen') ?>">
                    Dapur
                </a>
            </li>

            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#">
                    Ambil Bahan
                </a>
            </li>
            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $this->session->userdata('users_name') ?>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg"><img src="<?= base_url('') ?>assets/logo-kedai.png" alt="image profile" class="avatar-img rounded"></div>
                                <div class="u-text">
                                    <h4><?= $this->session->userdata('users_name') ?></h4>
                                    <p class="text-muted"><?= $this->session->userdata('users_number') ?></p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">My Profile</a>
                            <a class="dropdown-item" href="#">My Balance</a>
                            <a class="dropdown-item" href="#">Inbox</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Account Setting</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Logout</a>
                        </li>
                    </div>
                </ul>
            </li>
            <li class="nav-item dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#" style="pointer-events: none">
                    Buka
                </a>
            </li>
        </ul>
    </div>
</nav>
<!-- End Navbar -->