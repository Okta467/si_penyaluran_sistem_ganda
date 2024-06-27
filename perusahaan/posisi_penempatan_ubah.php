<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah perusahaan?
    if (!isAccessAllowed('perusahaan')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    require_once '../vendors/htmlpurifier/HTMLPurifier.auto.php';
    include_once '../config/connection.php';

    // to sanitize user input
    $config   = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    
    $id_posisi_penempatan = $_POST['xid_posisi_penempatan'];
    $id_perusahaan = $_SESSION['id_perusahaan'];
    $nama_posisi = htmlspecialchars($purifier->purify($_POST['xnama_posisi']));

    $stmt_posisi_penempatan = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_posisi_penempatan, "SELECT id_perusahaan FROM tbl_posisi_penempatan WHERE id=?");
    mysqli_stmt_bind_param($stmt_posisi_penempatan, 'i', $id_posisi_penempatan);
    mysqli_stmt_execute($stmt_posisi_penempatan);

    $result = mysqli_stmt_get_result($stmt_posisi_penempatan);
    $posisi_penempatan = mysqli_fetch_assoc($result);
    $current_id_perusahaan = $posisi_penempatan['id_perusahaan'];

    if ($id_perusahaan != $current_id_perusahaan) {
        $_SESSION['msg'] = 'Tidak boleh mengubah posisi penempatan dari perusahaan lain!';
        echo "<meta http-equiv='refresh' content='0;posisi_penempatan.php?go=posisi_penempatan'>";
        return;
    }

    $stmt_update = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_update, "UPDATE tbl_posisi_penempatan SET nama_posisi=? WHERE id=?");
    mysqli_stmt_bind_param($stmt_update, 'si', $nama_posisi, $id_posisi_penempatan);

    $update = mysqli_stmt_execute($stmt_update);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt_posisi_penempatan);
    mysqli_stmt_close($stmt_update);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;posisi_penempatan.php?go=posisi_penempatan'>";
?>