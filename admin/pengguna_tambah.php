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
    
    $id_siswa             = $_POST['xid_siswa'] ?? NULL;
    $id_guru              = $_POST['xid_guru'] ?? NULL;
    $id_perusahaan        = $_POST['xid_perusahaan'] ?? NULL;
    $username             = htmlspecialchars($purifier->purify($_POST['xusername']));
    $password             = password_hash($_POST['xpassword'], PASSWORD_DEFAULT);
    $is_allowed_hak_akses = in_array($_POST['xhak_akses'], ['perusahaan', 'guru', 'kepala_sekolah', 'siswa', 'admin']); 
    $hak_akses            = $is_allowed_hak_akses ? $_POST['xhak_akses'] : NULL;

    if (!$is_allowed_hak_akses) {
        $_SESSION['msg'] = 'Hak akses yang diinput tidak diperbolehkan!';
        echo "<meta http-equiv='refresh' content='0;pengguna.php?go=pengguna'>";
        return;
    }

    mysqli_autocommit($connection, false);

    $success = true;
    
    if ($hak_akses === 'admin'):
        
        $stmt_pengguna = mysqli_stmt_init($connection);

        mysqli_stmt_prepare($stmt_pengguna, "INSERT INTO tbl_pengguna (username, password, hak_akses) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_pengguna, 'sss', $username, $password, $hak_akses);

        if (!mysqli_stmt_execute($stmt_pengguna)):
            $success = false;
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
        endif;

    elseif ($hak_akses === 'siswa'):
        
        $stmt_pengguna = mysqli_stmt_init($connection);

        mysqli_stmt_prepare($stmt_pengguna, "INSERT INTO tbl_pengguna (username, password, hak_akses) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_pengguna, 'sss', $username, $password, $hak_akses);

        if (!mysqli_stmt_execute($stmt_pengguna)):
            $success = false;
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
        endif;

        $stmt_siswa = mysqli_stmt_init($connection);
        $id_pengguna = mysqli_insert_id($connection);

        mysqli_stmt_prepare($stmt_siswa, "UPDATE tbl_siswa SET id_pengguna=? WHERE id=?");
        mysqli_stmt_bind_param($stmt_siswa, 'ii', $id_pengguna, $id_siswa);

        if (!mysqli_stmt_execute($stmt_siswa)):
            $success = false;
            $_SESSION['msg'] = 'Statement Siswa preparation failed: ' . mysqli_stmt_error($stmt_siswa);
        endif;

    elseif (in_array($hak_akses, ['guru', 'kepala_sekolah'])):

        $stmt_pengguna = mysqli_stmt_init($connection);

        mysqli_stmt_prepare($stmt_pengguna, "INSERT INTO tbl_pengguna (username, password, hak_akses) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_pengguna, 'sss', $username, $password, $hak_akses);

        if (!mysqli_stmt_execute($stmt_pengguna)):
            $success = false;
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
        endif;

        $stmt_guru = mysqli_stmt_init($connection);
        $id_pengguna = mysqli_insert_id($connection);

        mysqli_stmt_prepare($stmt_guru, "UPDATE tbl_guru SET id_pengguna=? WHERE id=?");
        mysqli_stmt_bind_param($stmt_guru, 'ii', $id_pengguna, $id_guru);

        if (!mysqli_stmt_execute($stmt_guru)):
            $success = false;
            $_SESSION['msg'] = 'Statement Siswa preparation failed: ' . mysqli_stmt_error($stmt_guru);
        endif;

    elseif ($hak_akses === 'perusahaan'):
        
        $stmt_pengguna = mysqli_stmt_init($connection);

        mysqli_stmt_prepare($stmt_pengguna, "INSERT INTO tbl_pengguna (username, password, hak_akses) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_pengguna, 'sss', $username, $password, $hak_akses);

        if (!mysqli_stmt_execute($stmt_pengguna)):
            $success = false;
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
        endif;

        $stmt_perusahaan = mysqli_stmt_init($connection);
        $id_pengguna = mysqli_insert_id($connection);

        mysqli_stmt_prepare($stmt_perusahaan, "UPDATE tbl_perusahaan SET id_pengguna=? WHERE id=?");
        mysqli_stmt_bind_param($stmt_perusahaan, 'ii', $id_pengguna, $id_perusahaan);

        if (!mysqli_stmt_execute($stmt_perusahaan)):
            $success = false;
            $_SESSION['msg'] = 'Statement Perusahaan preparation failed: ' . mysqli_stmt_error($stmt_perusahaan);
        endif;

    endif;
    
    !$success
        ? mysqli_rollback($connection)
        : mysqli_commit($connection);

    !$success
        ? ''
        : $_SESSION['msg'] = 'save_success';

    mysqli_stmt_close($stmt_pengguna);

    !isset($stmt_siswa)
        ? ''
        : mysqli_stmt_close($stmt_siswa);

    !isset($stmt_guru)
        ? ''
        : mysqli_stmt_close($stmt_guru);

    !isset($stmt_perusahaan)
        ? ''
        : mysqli_stmt_close($stmt_perusahaan);

    mysqli_autocommit($connection, true);
    
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;pengguna.php?go=pengguna'>";
?>