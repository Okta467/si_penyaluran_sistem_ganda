<?php if (!isset($_SESSION['msg'])): ?>

<?php elseif ($_SESSION['msg'] === 'save_success'): ?>

  <script>
    Swal.fire({
      title: "Berhasil!",
      text: "Data berhasil disimpan!",
      icon: "success",
      timer: 3000,
      confirmButtonColor: "#3085d6",
      confirmButtonText: "OK",
    });
  </script>

<?php elseif ($_SESSION['msg'] === 'update_success'): ?>

  <script>
    Swal.fire({
      title: "Berhasil!",
      text: "Data berhasil diubah!",
      icon: "success",
      timer: 3000,
      confirmButtonColor: "#3085d6",
      confirmButtonText: "OK",
    });
  </script>

<?php elseif ($_SESSION['msg'] === 'delete_success'): ?>

  <script>
    Swal.fire({
      title: "Berhasil!",
      text: "Data berhasil dihapus!",
      icon: "success",
      timer: 3000,
      confirmButtonColor: "#3085d6",
      confirmButtonText: "OK",
    });
  </script>

<?php elseif ($_SESSION['msg'] === 'user_not_found'): ?>

  <script>
    Swal.fire({
      title: "Gagal!",
      text: "Pengguna tidak ditemukan!",
      icon: "error",
      timer: 3000,
      confirmButtonColor: "#e81502",
      confirmButtonText: "OK",
    });
  </script>
  
<?php elseif ($_SESSION['msg'] === 'wrong_password'): ?>

  <script>
    Swal.fire({
      title: "Gagal!",
      text: "Password yang di-input salah!",
      icon: "error",
      timer: 3000,
      confirmButtonColor: "#e81502",
      confirmButtonText: "OK",
    });
  </script>

<?php elseif ($_SESSION['msg'] === 'other_error'): ?>

  <script>
    Swal.fire({
      title: "Error!",
      text: "Terjadi kesalahan!",
      icon: "error",
      timer: 3000,
      confirmButtonColor: "#e81502",
      confirmButtonText: "OK",
    });
  </script>

<?php elseif ($_SESSION['msg'] !== ''): ?>

  <script>
    Swal.fire({
      title: "Error!",
      text: "<?= $_SESSION['msg'] ?>",
      icon: "error",
      confirmButtonColor: "#e81502",
      confirmButtonText: "OK",
    });
  </script>

<?php endif ?>

<?php unset($_SESSION['msg']) ?>