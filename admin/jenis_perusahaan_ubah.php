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
    
    $id_jenis_perusahaan   = $_POST['xid_jenis_perusahaan'];
    $nama_jenis = htmlspecialchars($purifier->purify($_POST['xnama_jenis']));

    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "UPDATE tbl_jenis_perusahaan SET nama_jenis=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'si', $nama_jenis, $id_jenis_perusahaan);

    $update = mysqli_stmt_execute($stmt);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;jenis_perusahaan.php?go=jenis_perusahaan'>";
?>