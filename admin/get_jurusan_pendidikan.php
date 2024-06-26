<?php
    include '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_pendidikan = $_POST['id_pendidikan'];

    $stmt = mysqli_stmt_init($connection);
    $query = 
        "SELECT a.id AS id_jurusan, a.nama_jurusan, b.nama_pendidikan
        FROM tbl_jurusan_pendidikan a
        JOIN tbl_pendidikan b
            ON a.id_pendidikan = b.id
        WHERE id_pendidikan=? ";

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_pendidikan);
    mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);

    $jurusans = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo json_encode($jurusans);

?>