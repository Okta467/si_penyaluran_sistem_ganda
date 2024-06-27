<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah perusahaan?
    if (!isAccessAllowed('perusahaan')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_seleksi = $_POST['xid_seleksi'];
    $id_posisi_penempatan = $_POST['xid_posisi_penempatan'];
    $id_perusahaan = $_SESSION['id_perusahaan'];

    $stmt_posisi_penempatan = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_posisi_penempatan, "SELECT id_perusahaan FROM tbl_posisi_penempatan WHERE id=?");
    mysqli_stmt_bind_param($stmt_posisi_penempatan, 'i', $id_posisi_penempatan);
    mysqli_stmt_execute($stmt_posisi_penempatan);

    $result = mysqli_stmt_get_result($stmt_posisi_penempatan);
    $posisi_penempatan = mysqli_fetch_assoc($result);
    $current_id_perusahaan = $posisi_penempatan['id_perusahaan'];

    if ($id_perusahaan != $current_id_perusahaan) {
        $_SESSION['msg'] = 'Tidak boleh memilih posisi penempatan dari perusahaan lain!';
        echo "<meta http-equiv='refresh' content='0;seleksi.php?go=seleksi'>";
        return;
    }

    $stmt_update = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_update, "UPDATE tbl_seleksi SET id_posisi_penempatan=? WHERE id=?");
    mysqli_stmt_bind_param($stmt_update, 'ii', $id_posisi_penempatan, $id_seleksi);

    $update = mysqli_stmt_execute($stmt_update);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt_posisi_penempatan);
    mysqli_stmt_close($stmt_update);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;seleksi.php?go=seleksi'>";
?>