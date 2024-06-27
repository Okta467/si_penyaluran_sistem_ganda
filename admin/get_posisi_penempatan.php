<?php
    include '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_perusahaan = $_POST['id_perusahaan'];

    $stmt = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            a.id AS id_posisi_penempatan, a.nama_posisi,
            b.id AS id_perusahaan, b.nama_perusahaan, b.alamat_perusahaan,
            c.id AS id_jenis_perusahaan, c.nama_jenis
        FROM tbl_posisi_penempatan AS a
        JOIN tbl_perusahaan AS b
            ON b.id = a.id_perusahaan
        LEFT JOIN tbl_jenis_perusahaan AS c
            ON c.id = b.id_jenis_perusahaan
        WHERE a.id_perusahaan=?";

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_perusahaan);
    mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);

    $posisi_perusahaans = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo json_encode($posisi_perusahaans);

?>