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
    
    $nip                 = $_POST['xnip'];
    $nama_guru           = htmlspecialchars($purifier->purify($_POST['xnama_guru']));
    $username            = $nip;
    $password            = password_hash($_POST['xpassword'], PASSWORD_DEFAULT);
    $hak_akses           = $_POST['xhak_akses'];
    $jk                  = $_POST['xjk'];
    $alamat              = htmlspecialchars($purifier->purify($_POST['xalamat']));
    $tmp_lahir           = htmlspecialchars($purifier->purify($_POST['xtmp_lahir']));
    $tgl_lahir           = $_POST['xtgl_lahir'];
    $tahun_ijazah        = $_POST['xtahun_ijazah'];
    $id_jabatan          = $_POST['xid_jabatan'];
    $id_pangkat_golongan = $_POST['xid_pangkat_golongan'];
    $id_pendidikan       = $_POST['xid_pendidikan'];
    $id_jurusan          = $_POST['xid_jurusan'] ?? null;
    
    $is_allowed_hak_akses = in_array($_POST['xhak_akses'], ['guru', 'kepala_sekolah']); 

    if (!$is_allowed_hak_akses) {
        $_SESSION['msg'] = 'Hak yang dipilih tidak diperbolehkan!';
        echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
        return;
    }

    // Turn off autocommit mode
    mysqli_autocommit($connection, false);

    // Initialize the success flag
    $success = true;

    // Begin the transaction
    try {
        // Pengguna statement preparation and execution
        $stmt_pengguna  = mysqli_stmt_init($connection);
        $query_pengguna = "INSERT INTO tbl_pengguna (username, password, hak_akses) VALUES (?, ?, ?)";
        
        if (!mysqli_stmt_prepare($stmt_pengguna, $query_pengguna)) {
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }
        
        mysqli_stmt_bind_param($stmt_pengguna, 'sss', $username, $password, $hak_akses);
        
        if (!mysqli_stmt_execute($stmt_pengguna)) {
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }

        // Pegawai statement preparation and execution
        $stmt_guru  = mysqli_stmt_init($connection);
        $query_guru = "INSERT INTO tbl_guru 
        (
            id_pengguna
            , id_jabatan
            , id_pangkat_golongan
            , id_pendidikan
            , id_jurusan_pendidikan
            , nip
            , nama_guru
            , jk
            , alamat
            , tmp_lahir
            , tgl_lahir
            , tahun_ijazah
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if (!mysqli_stmt_prepare($stmt_guru, $query_guru)) {
            $_SESSION['msg'] = 'Statement Pegawai preparation failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }
        
        $id_pengguna = mysqli_insert_id($connection);
        mysqli_stmt_bind_param($stmt_guru, 'iiiiissssssi', $id_pengguna, $id_jabatan, $id_pangkat_golongan, $id_pendidikan, $id_jurusan, $nip, $nama_guru, $jk, $alamat, $tmp_lahir, $tgl_lahir, $tahun_ijazah);
        
        if (!mysqli_stmt_execute($stmt_guru)) {
            $_SESSION['msg'] = 'Statement Pegawai preparation failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }

        // Commit the transaction if all statements succeed
        if (!mysqli_commit($connection)) {
            $_SESSION['msg'] = 'Transaction commit failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }

    } catch (Exception $e) {
        // Roll back the transaction if any statement fails
        $success = false;
        mysqli_rollback($connection);
        echo 'Transaction failed: ' . $e->getMessage();
    }

    // Close the statements
    mysqli_stmt_close($stmt_pengguna);
    mysqli_stmt_close($stmt_guru);

    // Turn autocommit mode back on
    mysqli_autocommit($connection, true);

    // Close the connection
    mysqli_close($connection);

    !$success
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'save_success';

    echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
?>
