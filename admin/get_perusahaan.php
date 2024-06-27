<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_perusahaan = $_POST['id_perusahaan'];

    $stmt1 = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            a.id AS id_perusahaan, a.nama_perusahaan, a.alamat_perusahaan,
            b.id AS id_jenis_perusahaan, b.nama_jenis,
            f.id AS id_pengguna, f.username, f.hak_akses
        FROM tbl_perusahaan AS a
        LEFT JOIN tbl_jenis_perusahaan AS b
            ON b.id = a.id_jenis_perusahaan
        LEFT JOIN tbl_pengguna AS f
            ON f.id = a.id_pengguna
        WHERE a.id=?";

    mysqli_stmt_prepare($stmt1, $query);
    mysqli_stmt_bind_param($stmt1, 'i', $id_perusahaan);
    mysqli_stmt_execute($stmt1);

	$result = mysqli_stmt_get_result($stmt1);

    $perusahaans = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt1);
    mysqli_close($connection);

    echo json_encode($perusahaans);

?>