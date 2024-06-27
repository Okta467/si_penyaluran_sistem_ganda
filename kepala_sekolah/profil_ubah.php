<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah kepala_sekolah?
    if (!isAccessAllowed('kepala_sekolah')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    require_once '../vendors/htmlpurifier/HTMLPurifier.auto.php';
    include_once '../config/connection.php';

    // to sanitize user input
    $config   = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    
    $id_guru             = $_SESSION['id_guru'];
    $id_pengguna         = $_SESSION['id_pengguna'];
    $password            = $_POST['xpassword'] ? password_hash($_POST['xpassword'], PASSWORD_DEFAULT) : null;
    $alamat              = htmlspecialchars($purifier->purify($_POST['xalamat']));
    $tmp_lahir           = htmlspecialchars($purifier->purify($_POST['xtmp_lahir']));
    $tgl_lahir           = $_POST['xtgl_lahir'];
    $tahun_ijazah        = $_POST['xtahun_ijazah'];
    $id_jabatan          = $_POST['xid_jabatan'];
    $id_pangkat_golongan = $_POST['xid_pangkat_golongan'];
    $id_pendidikan       = $_POST['xid_pendidikan'];
    $id_jurusan          = $_POST['xid_jurusan'] ?? null;

    // Turn off autocommit mode
    mysqli_autocommit($connection, false);

    // Initialize the success flag
    $success = true;

    // Begin the transaction
    try {
        // Pegawai statement preparation and execution
        $stmt_guru  = mysqli_stmt_init($connection);
        $query_guru = "UPDATE tbl_guru SET
            id_jabatan = ?
            , id_pangkat_golongan = ?
            , id_pendidikan = ?
            , id_jurusan_pendidikan = ?
            , alamat = ?
            , tmp_lahir = ?
            , tgl_lahir = ?
            , tahun_ijazah = ?
        WHERE id = ?";
        
        if (!mysqli_stmt_prepare($stmt_guru, $query_guru)) {
            $_SESSION['msg'] = 'Statement Pegawai preparation failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
            return;
        }
        
        mysqli_stmt_bind_param($stmt_guru, 'iiiisssii', $id_jabatan, $id_pangkat_golongan, $id_pendidikan, $id_jurusan, $alamat, $tmp_lahir, $tgl_lahir, $tahun_ijazah, $id_guru);
        
        if (!mysqli_stmt_execute($stmt_guru)) {
            $_SESSION['msg'] = 'Statement Pegawai preparation failed: ' . mysqli_stmt_error($stmt_guru);
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

    // Close the statements
    mysqli_stmt_close($stmt_guru);

    !$password
        ? ''
        : mysqli_stmt_close($stmt_pengguna);

    // Turn autocommit mode back on
    mysqli_autocommit($connection, true);

    // Close the connection
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;profil.php?go=profil'>";
?>
