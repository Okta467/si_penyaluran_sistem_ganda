<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $stmt1 = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            a.id AS id_siswa, a.nama_siswa, a.jk, a.alamat, a.tmp_lahir, a.tgl_lahir, a.no_telp, a.email,
            b.username
        FROM tbl_siswa AS a
        LEFT JOIN tbl_pengguna AS b
            ON a.id_pengguna = b.id
        WHERE b.id IS NULL";

    mysqli_stmt_prepare($stmt1, $query);
    mysqli_stmt_execute($stmt1);

	$result = mysqli_stmt_get_result($stmt1);

    $siswas = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt1);
    mysqli_close($connection);

    echo json_encode($siswas);

?>