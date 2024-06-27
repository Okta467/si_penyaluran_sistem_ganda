<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_tahun_seleksi     = $_POST['xid_tahun_seleksi'];
    $id_siswa             = $_POST['xid_siswa'];
    $id_posisi_penempatan = $_POST['xid_posisi_penempatan'];

    $stmt_seleksi = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_seleksi, "SELECT id FROM tbl_seleksi WHERE id_siswa=? AND id_tahun_seleksi=?");
    mysqli_stmt_bind_param($stmt_seleksi, 'ii', $id_siswa, $id_tahun_seleksi);
    mysqli_stmt_execute($stmt_seleksi);

    $result = mysqli_stmt_get_result($stmt_seleksi);
    $seleksi = mysqli_fetch_assoc($result);

    if ($seleksi) {
        $_SESSION['msg'] = 'Penilaian untuk siswa dan tahun seleksi tersebut sudah ada!';
        echo "<meta http-equiv='refresh' content='0;seleksi.php?go=seleksi'>";
        return;
    }

    $stmt_insert = mysqli_stmt_init($connection);
    $query_insert = "INSERT INTO tbl_seleksi
    (
        id_tahun_seleksi
        , id_siswa
        , id_posisi_penempatan
    )
    VALUES (?, ?, ?)";

    mysqli_stmt_prepare($stmt_insert, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, 'iii', $id_tahun_seleksi, $id_siswa, $id_posisi_penempatan);

    $insert = mysqli_stmt_execute($stmt_insert);

    !$insert
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'save_success';

    mysqli_stmt_close($stmt_seleksi);
    mysqli_stmt_close($stmt_insert);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;seleksi.php?go=seleksi'>";
?>