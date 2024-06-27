<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
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
    $id_perusahaan = $_POST['xid_perusahaan'];
    $nama_posisi = htmlspecialchars($purifier->purify($_POST['xnama_posisi']));

    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "UPDATE tbl_posisi_penempatan SET id_perusahaan=?, nama_posisi=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'isi', $id_perusahaan, $nama_posisi, $id_posisi_penempatan);

    $update = mysqli_stmt_execute($stmt);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;posisi_penempatan.php?go=posisi_penempatan'>";
?>