<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah siswa?
    if (!isAccessAllowed('siswa')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_siswa = $_POST['id_siswa'];

    $stmt1 = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            a.id AS id_siswa, a.nisn, a.nama_siswa, a.jk, a.alamat, a.tmp_lahir, a.tgl_lahir, a.no_telp, a.email,
            b.id AS id_kelas, b.nama_kelas,
            f.id AS id_pengguna, f.username, f.hak_akses
        FROM tbl_siswa AS a
        LEFT JOIN tbl_kelas AS b
            ON a.id_kelas = b.id
        LEFT JOIN tbl_pengguna AS f
            ON a.id_pengguna = f.id
        WHERE a.id=?";

    mysqli_stmt_prepare($stmt1, $query);
    mysqli_stmt_bind_param($stmt1, 'i', $id_siswa);
    mysqli_stmt_execute($stmt1);

	$result = mysqli_stmt_get_result($stmt1);

    $siswas = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt1);
    mysqli_close($connection);

    echo json_encode($siswas);

?>