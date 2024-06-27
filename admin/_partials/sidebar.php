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

      <div class="sidenav-menu-heading">Pengguna</div>
      
      <a class="nav-link <?php if ($current_page === 'pengguna') echo 'active' ?>" href="pengguna.php?go=pengguna">
        <div class="nav-link-icon"><i data-feather="users"></i></div>
        Pengguna
      </a>

      <a class="nav-link <?php if ($current_page === 'siswa') echo 'active' ?>" href="siswa.php?go=siswa">
        <div class="nav-link-icon"><i data-feather="user"></i></div>
        Siswa
      </a>

      <a class="nav-link <?php if ($current_page === 'guru') echo 'active' ?>" href="guru.php?go=guru">
        <div class="nav-link-icon"><i data-feather="user"></i></div>
        Guru
      </a>

      <a class="nav-link <?php if ($current_page === 'perusahaan') echo 'active' ?>" href="perusahaan.php?go=perusahaan">
        <div class="nav-link-icon"><i data-feather="briefcase"></i></div>
        Perusahaan
      </a>
      
      <div class="sidenav-menu-heading">Perusahaan</div>
      
      <a class="nav-link <?php if ($current_page === 'seleksi') echo 'active' ?>" href="seleksi.php?go=seleksi">
        <div class="nav-link-icon"><i data-feather="edit-2"></i></div>
        Seleksi
      </a>
      
      <a class="nav-link <?php if ($current_page === 'pengumuman') echo 'active' ?>" href="pengumuman.php?go=pengumuman">
        <div class="nav-link-icon"><i data-feather="flag"></i></div>
        Pengumuman
      </a>
      
      <a class="nav-link <?php if ($current_page === 'tahun_seleksi') echo 'active' ?>" href="tahun_seleksi.php?go=tahun_seleksi">
        <div class="nav-link-icon"><i data-feather="calendar"></i></div>
        Tahun Seleksi
      </a>
      
      <a class="nav-link <?php if ($current_page === 'jenis_perusahaan') echo 'active' ?>" href="jenis_perusahaan.php?go=jenis_perusahaan">
        <div class="nav-link-icon"><i data-feather="tool"></i></div>
        Jenis Perusahaan
      </a>
      
      <a class="nav-link <?php if ($current_page === 'posisi_penempatan') echo 'active' ?>" href="posisi_penempatan.php?go=posisi_penempatan">
        <div class="nav-link-icon"><i data-feather="tool"></i></div>
        Posisi Penempatan
      </a>
      
      <div class="sidenav-menu-heading">Detail Guru</div>

      <a class="nav-link <?php if ($current_page === 'jabatan') echo 'active' ?>" href="jabatan.php?go=jabatan">
        <div class="nav-link-icon"><i data-feather="briefcase"></i></div>
        Jabatan
      </a>

      <a class="nav-link <?php if ($current_page === 'pangkat_golongan') echo 'active' ?>" href="pangkat_golongan.php?go=pangkat_golongan">
        <div class="nav-link-icon"><i data-feather="briefcase"></i></div>
        Pangkat / Golongan
      </a>

      <a class="nav-link <?php if ($current_page === 'pendidikan') echo 'active' ?>" href="pendidikan.php?go=pendidikan">
        <div class="nav-link-icon"><i data-feather="book"></i></div>
        Pendidikan
      </a>

      <a class="nav-link <?php if ($current_page === 'jurusan_pendidikan') echo 'active' ?>" href="jurusan_pendidikan.php?go=jurusan_pendidikan">
        <div class="nav-link-icon"><i data-feather="book"></i></div>
        Jurusan
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