<?php
$current_page = $_GET['go'] ?? '';
$user_logged_in = $_SESSION['nama_pegawai'] ?? $_SESSION['nama_guest'] ?? $_SESSION['username'];
?>

<nav class="sidenav shadow-right sidenav-light">
  <div class="sidenav-menu">
    <div class="nav accordion" id="accordionSidenav">
      <!-- Sidenav Menu Heading (Core)-->
      <div class="sidenav-menu-heading">Core</div>
      
      <a class="nav-link <?php if ($current_page === 'dashboard') echo 'active' ?>" href="index.php?go=dashboard">
        <div class="nav-link-icon"><i data-feather="activity"></i></div>
        Dashboard
      </a>

      <div class="sidenav-menu-heading">Perusahaan</div>

      <a class="nav-link <?php if ($current_page === 'profil') echo 'active' ?>" href="profil.php?go=profil">
        <div class="nav-link-icon"><i data-feather="briefcase"></i></div>
        Profil
      </a>
      
      <div class="sidenav-menu-heading">Seleksi</div>
      
      <a class="nav-link <?php if ($current_page === 'seleksi') echo 'active' ?>" href="seleksi.php?go=seleksi">
        <div class="nav-link-icon"><i data-feather="edit-2"></i></div>
        Seleksi
      </a>
      
      <a class="nav-link <?php if ($current_page === 'pengumuman') echo 'active' ?>" href="pengumuman.php?go=pengumuman">
        <div class="nav-link-icon"><i data-feather="flag"></i></div>
        Pengumuman
      </a>
      
      <a class="nav-link <?php if ($current_page === 'posisi_penempatan') echo 'active' ?>" href="posisi_penempatan.php?go=posisi_penempatan">
        <div class="nav-link-icon"><i data-feather="tool"></i></div>
        Posisi Penempatan
      </a>
      
      <div class="sidenav-menu-heading">Lainnya</div>
      
      <a class="nav-link" href="<?= base_url('logout.php') ?>">
        <div class="nav-link-icon"><i data-feather="log-out"></i></div>
        Keluar
      </a>

    </div>
  </div>
  <!-- Sidenav Footer-->
  <div class="sidenav-footer">
    <div class="sidenav-footer-content">
      <div class="sidenav-footer-subtitle">Anda masuk sebagai:</div>
      <div class="sidenav-footer-title"><?= ucwords($user_logged_in) ?></div>
    </div>
  </div>
</nav>