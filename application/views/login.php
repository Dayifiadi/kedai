<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Login</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <?php include 'templates/top.php'; ?>
</head>

<body class="login">
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            <h3 class="text-center">Login</h3>
            <form action="<?= base_url('login/auth') ?>" method="POST">
                <div class="login-form">
                    <div class="form-group form-floating-label">
                        <input id="number" name="number" type="text" class="form-control input-border-bottom" value="123" required>
                        <label for=" number" class="placeholder">Number</label>
                    </div>
                    <div class="form-group form-floating-label">
                        <input id="password" name="password" type="password" class="form-control input-border-bottom" value="" required>
                        <label for="password" class="placeholder">Password</label>
                        <div class="show-password">
                            <i class="icon-eye"></i>
                        </div>
                    </div>
                    <div class="row form-sub m-0">
                        <a href="#" class="link float-right">Lupa Password ?</a>
                    </div>
                    <div class="form-action mb-3">
                        <button type="submit" class="btn btn-warning btn-rounded btn-block">Login</button>
                    </div>
                    <div class="login-account">
                        <hr />
                        <span class="msg">Belum punya akun ?</span>
                        <a href="#" id="show-signup" class="link">Daftar</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- <div class="container container-signup animated fadeIn">
            <h3 class="text-center">Sign Up</h3>
            <div class="login-form">
                <div class="form-group form-floating-label">
                    <input id="fullname" name="fullname" type="text" class="form-control input-border-bottom" required>
                    <label for="fullname" class="placeholder">Fullname</label>
                </div>
                <div class="form-group form-floating-label">
                    <input id="email" name="email" type="email" class="form-control input-border-bottom" required>
                    <label for="email" class="placeholder">Email</label>
                </div>
                <div class="form-group form-floating-label">
                    <input id="passwordsignin" name="passwordsignin" type="password" class="form-control input-border-bottom" required>
                    <label for="passwordsignin" class="placeholder">Password</label>
                    <div class="show-password">
                        <i class="icon-eye"></i>
                    </div>
                </div>
                <div class="form-group form-floating-label">
                    <input id="confirmpassword" name="confirmpassword" type="password" class="form-control input-border-bottom" required>
                    <label for="confirmpassword" class="placeholder">Confirm Password</label>
                    <div class="show-password">
                        <i class="icon-eye"></i>
                    </div>
                </div>
                <div class="row form-sub m-0">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="agree" id="agree">
                        <label class="custom-control-label" for="agree">I Agree the terms and conditions.</label>
                    </div>
                </div>
                <div class="form-action">
                    <a href="#" id="show-signin" class="btn btn-danger btn-link btn-login mr-3">Cancel</a>
                    <a href="#" class="btn btn-primary btn-rounded btn-login">Sign Up</a>
                </div>
            </div>
        </div> -->
    </div>

    <?php include 'templates/bottom.php'; ?>
</body>

</html>