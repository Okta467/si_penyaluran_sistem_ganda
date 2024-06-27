<?php
    include '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_siswa = $_POST['id_siswa'];

    $stmt = mysqli_stmt_init($connection);
    $query = 
        "SELECT b.nama_siswa, a.nama_prestasi, a.file_prestasi
        FROM tbl_prestasi_siswa AS a
        JOIN tbl_siswa AS b
            ON b.id = a.id_siswa
        WHERE b.id=?";

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_siswa);
    mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);

    $prestasi_siswas = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo json_encode($prestasi_siswas);

?>