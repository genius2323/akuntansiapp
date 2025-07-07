<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left Navbar -->
  <ul class="navbar-nav">
    <li class="nav-item d-lg-none">
  <a class="nav-link" data-widget="pushmenu" href="#" role="button">
    <i class="fas fa-bars"></i>
  </a>
</li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?= site_url('/') ?>" class="nav-link">Home</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-user"></i> <?= session()->get('username') ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <a href="<?= site_url('logout') ?>" class="dropdown-item">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </li>
  </ul>

  <!-- Right Navbar -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" href="<?= site_url('logout') ?>">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </li>
  </ul>
</nav>