<?php
	session_start();
	include_once 'config/connection.php';

	// cek apakah tombol submit ditekan sebelum memproses verifikasi login
	if (!isset($_POST['xsubmit'])) {
		$_SESSION['msg'] = 'other_error';
		echo "<meta http-equiv='refresh' content='0;index.php'>";
		return;
	}

	$username = $_POST['xusername'];
	$password = $_POST['xpassword'];


	// jalankan mysql prepare statement untuk mencegah SQL Inject
	$stmt = mysqli_stmt_init($connection);

	mysqli_stmt_prepare($stmt, "SELECT * FROM tbl_pengguna WHERE username=?");
	mysqli_stmt_bind_param($stmt, 's', $username);
	mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);
	$user   = mysqli_fetch_assoc($result);

	mysqli_stmt_close($stmt);


	// redirect ke halaman login jika pengguna tidak ditemukan
	if (!$user) {
		$_SESSION['msg'] = 'user_not_found';
		echo "<meta http-equiv='refresh' content='0;index.php'>";
		return;
	}

	// cek apakah passwordnya benar?
	if (!password_verify($password, $user['password'])) {
		$_SESSION['msg'] = 'wrong_password';
		echo "<meta http-equiv='refresh' content='0;index.php'>";
		return;
	}

    // Get id_siswa if hak_akses is siswa
    if ($user['hak_akses'] === 'siswa') {
        $query_siswa = mysqli_query($connection, "SELECT id, nama_siswa, email FROM tbl_siswa WHERE id_pengguna = {$user['id']} LIMIT 1");
        $siswa = mysqli_fetch_assoc($query_siswa);
    }

    // Get id_guru if hak akses is kepala_sekolah
    if ($user['hak_akses'] === 'kepala_sekolah') {
        $query_guru = mysqli_query($connection, "SELECT id, nama_guru FROM tbl_guru WHERE id_pengguna = {$user['id']} LIMIT 1");
        $guru = mysqli_fetch_assoc($query_guru);
    }

    // Get id_perusahaan if hak akses is kepala_sekolah
    if ($user['hak_akses'] === 'perusahaan') {
        $query_perusahaan = mysqli_query($connection, "SELECT id, nama_perusahaan FROM tbl_perusahaan WHERE id_pengguna = {$user['id']} LIMIT 1");
        $perusahaan = mysqli_fetch_assoc($query_perusahaan);
    }

	// set sesi user sekarang
	$_SESSION['id_pengguna']     = $user['id'];
	$_SESSION['id_siswa']        = $siswa['id'] ?? null;
	$_SESSION['id_guru']         = $guru['id'] ?? null;
	$_SESSION['id_perusahaan']   = $perusahaan['id'] ?? null;
	$_SESSION['nama_siswa']      = $siswa['nama_siswa'] ?? null;
	$_SESSION['nama_guru']       = $guru['nama_guru'] ?? null;
	$_SESSION['nama_perusahaan'] = $perusahaan['nama_perusahaan'] ?? null;
	$_SESSION['username']        = $user['username'];
	$_SESSION['hak_akses']       = $user['hak_akses'];
	$_SESSION['email']           = $siswa['email'] ?? 'default_email@gmail.com';

	// Update last login user
	$last_login = date('Y-m-d H:i:s');
	$query_update = mysqli_query($connection, "UPDATE tbl_pengguna SET last_login = '{$last_login}' WHERE id = {$user['id']}");

	// alihkan user ke halamannya masing-masing
	switch ($user['hak_akses']) {
		case 'admin':
			header("location:admin");
			break;

		case 'siswa':
			header("location:siswa/index.php?go=dashboard");
			break;
			
		case 'guru':
			header("location:logout.php");
			break;
			
		case 'kepala_sekolah':
			header("location:kepala_sekolah/index.php?go=dashboard");
			break;
			
		case 'perusahaan':
			header("location:perusahaan/index.php?go=dashboard");
			break;
		
		default:
			$_SESSION['msg'] = 'hak akses not found!';
			header("location:logout.php");
			break;
	}
?>