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
    
    $id_jurusan_pendidikan = $_POST['xid_jurusan_pendidikan'];
    $id_pendidikan         = $_POST['xid_pendidikan'];
    $nama_jurusan          = htmlspecialchars($purifier->purify($_POST['xnama_jurusan']));

    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "UPDATE tbl_jurusan_pendidikan SET id_pendidikan=?, nama_jurusan=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'isi', $id_pendidikan, $nama_jurusan, $id_jurusan_pendidikan);

    $update = mysqli_stmt_execute($stmt);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;jurusan_pendidikan.php?go=jurusan_pendidikan'>";
?>