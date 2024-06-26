<!DOCTYPE html>
<html lang="en">

<head>
  <?php session_start() ?>
  <?php include 'config/config.php' ?>
  <?php include 'login_head.php' ?>
  <?php include 'helpers/isAccessAllowedHelper.php' ?>
  <?php include 'helpers/isAlreadyLoginHelper.php' ?>

  <?php isset($_SESSION['hak_akses']) ? isAlreadyLoggedIn($_SESSION['hak_akses']) : '' ?>

  <meta name="author" content="" />
  <meta name="Description" content="Halaman Login">
  <title>Login - <?= SITE_NAME ?></title>
</head>

<body class="bg-primary">
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container-xl px-4">
          <div class="row justify-content-center">
            <div class="col-lg-5">
              <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header justify-content-center">
                  <h3 class="fw-light my-4"></i>Login</h3>
                </div>
                <div class="card-body">
                  <form action="login_verification.php" method="POST">
                    
                    <div class="mb-3">
                      <label class="small mb-1" for="inputPassword">Username</label>
                      <input name="xusername" type="text" class="form-control" id="xusername" placeholder="Enter username" autocomplete="username" />
                    </div>
                    
                    <div class="mb-3">
                      <label class="small mb-1" for="inputPassword">Password</label>
                      <input name="xpassword" type="password" class="form-control" id="inputPassword" placeholder="Enter password" autocomplete="current-password" />
                    </div>
                    
                    <div class="mb-3">
                      <div class="form-check">
                        <input class="form-check-input" id="rememberPasswordCheck" type="checkbox" value="" />
                        <label class="form-check-label" for="rememberPasswordCheck">Remember password</label>
                      </div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                      <a class="small invisible" href="#">Forgot Password?</a>
                      <button name="xsubmit" type="submit" class="btn btn-primary">Login</a>
                    </div>

                  </form>
                </div>
                <div class="card-footer text-center">
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    
    <div id="layoutAuthentication_footer">
      <footer class="footer-admin mt-auto footer-dark">
        <div class="container-xl px-4">
          <div class="row">
            <div class="col-md-6 small">Copyright &copy; <?= SITE_NAME_SHORT . ' ' . date('Y') ?></div>
            <div class="col-md-6 text-md-end small">
              <a href="#!">Privacy Policy</a>
              &middot;
              <a href="#!">Terms &amp; Conditions</a>
            </div>
          </div>
        </div>
      </footer>
    </div>

  </div>

  <?php include_once 'login_script.php' ?>
  <?php include_once 'helpers/sweetalert2_notify.php' ?>
  
</body>

</html>