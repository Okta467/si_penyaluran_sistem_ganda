<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $stmt = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            a.id AS id_perusahaan, a.nama_perusahaan, a.alamat_perusahaan,
            f.id AS id_pengguna, f.username, f.hak_akses
        FROM tbl_perusahaan AS a
        LEFT JOIN tbl_pengguna AS f
            ON f.id = a.id_pengguna
        WHERE f.id IS NULL";

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);

    $perusahaans = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo json_encode($perusahaans);

?>