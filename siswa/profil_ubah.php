<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah siswa?
    if (!isAccessAllowed('siswa')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    require_once '../vendors/htmlpurifier/HTMLPurifier.auto.php';
    include_once '../config/connection.php';

    // to sanitize user input
    $config   = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    
    $id_siswa    = $_SESSION['id_siswa'];
    $id_pengguna = $_SESSION['id_pengguna'];
    $password    = $_POST['xpassword'] ? password_hash($_POST['xpassword'], PASSWORD_DEFAULT) : null;
    $alamat      = htmlspecialchars($purifier->purify($_POST['xalamat']));
    $tmp_lahir   = htmlspecialchars($purifier->purify($_POST['xtmp_lahir']));
    $tgl_lahir   = $_POST['xtgl_lahir'];
    $no_telp     = isset($_POST['xno_telp']) ? '62' . $_POST['xno_telp'] : null;
    $email       = htmlspecialchars($purifier->purify($_POST['xemail']));

    mysqli_autocommit($connection, false);

    $success = true;

    try {
        // Siswa statement preparation and execution
        $stmt_siswa  = mysqli_stmt_init($connection);
        $query_siswa = "UPDATE tbl_siswa SET
            alamat = ?
            , tmp_lahir = ?
            , tgl_lahir = ?
            , no_telp = ?
            , email = ?
        WHERE id = ?";
        
        if (!mysqli_stmt_prepare($stmt_siswa, $query_siswa)) {
            $_SESSION['msg'] = 'Statement Siswa preparation failed: ' . mysqli_stmt_error($stmt_siswa);
            echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
            return;
        }
        
        mysqli_stmt_bind_param($stmt_siswa, 'sssssi', $alamat, $tmp_lahir, $tgl_lahir, $no_telp, $email, $id_siswa);
        
        if (!mysqli_stmt_execute($stmt_siswa)) {
            $_SESSION['msg'] = 'Statement Siswa preparation failed: ' . mysqli_stmt_error($stmt_siswa);
            echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
            return;
        }

        if ($password) {
            // Pengguna statement preparation and execution
            $stmt_pengguna  = mysqli_stmt_init($connection);
            $query_pengguna = "UPDATE tbl_pengguna SET password=? WHERE id=?";
            
            if (!mysqli_stmt_prepare($stmt_pengguna, $query_pengguna)) {
                $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
                echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
                return;
            }
            
            mysqli_stmt_bind_param($stmt_pengguna, 'si', $password, $id_pengguna);
            
            if (!mysqli_stmt_execute($stmt_pengguna)) {
                $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
                echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
                return;
            }
        }

        // Commit the transaction if all statements succeed
        if (!mysqli_commit($connection)) {
            $_SESSION['msg'] = 'Transaction commit failed: ' . mysqli_stmt_error($stmt_pengguna);
            echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
            return;
        }

    } catch (Exception $e) {
        // Roll back the transaction if any statement fails
        $success = false;
        mysqli_rollback($connection);
        $_SESSION['msg'] = 'Transaction failed: ' . $e->getMessage();
    }

    !$success
        ? ''
        : $_SESSION['msg'] = 'save_success';

    mysqli_stmt_close($stmt_siswa);

    !$password
        ? ''
        : mysqli_stmt_close($stmt_pengguna);

    mysqli_autocommit($connection, true);

    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
?>
