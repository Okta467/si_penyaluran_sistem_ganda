<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_jenis_perusahaan = $_GET['xid_jenis_perusahaan'];

    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "DELETE FROM tbl_jenis_perusahaan WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id_jenis_perusahaan);

    $delete = mysqli_stmt_execute($stmt);

    !$delete
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'delete_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;jenis_perusahaan.php?go=jenis_perusahaan'>";
?>