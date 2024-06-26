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
    
    $id_pangkat_golongan   = $_POST['xid_pangkat_golongan'];
    $nama_pangkat_golongan = htmlspecialchars($purifier->purify($_POST['xnama_pangkat_golongan']));

    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "UPDATE tbl_pangkat_golongan SET nama_pangkat_golongan=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'si', $nama_pangkat_golongan, $id_pangkat_golongan);

    $update = mysqli_stmt_execute($stmt);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;pangkat_golongan.php?go=pangkat_golongan'>";
?>