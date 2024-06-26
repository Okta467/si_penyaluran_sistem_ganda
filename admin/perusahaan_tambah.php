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
    
    $id_jenis_perusahaan = $_POST['xid_jenis_perusahaan'];
    $nama_perusahaan     = htmlspecialchars($purifier->purify($_POST['xnama_perusahaan']));
    $username            = htmlspecialchars($purifier->purify($_POST['xusername']));
    $password            = password_hash($_POST['xpassword'], PASSWORD_DEFAULT);
    $hak_akses           = 'perusahaan';
    $alamat_perusahaan   = htmlspecialchars($purifier->purify($_POST['xalamat_perusahaan']));

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
            echo "<meta http-equiv='refresh' content='0;perusahaan.php?go=perusahaan'>";
            return;
        }
        
        mysqli_stmt_bind_param($stmt_pengguna, 'sss', $username, $password, $hak_akses);
        
        if (!mysqli_stmt_execute($stmt_pengguna)) {
            $_SESSION['msg'] = 'Statement Pengguna preparation failed: ' . mysqli_stmt_error($stmt_pengguna);
            echo "<meta http-equiv='refresh' content='0;perusahaan.php?go=perusahaan'>";
            return;
        }

        // Siswa statement preparation and execution
        $stmt_perusahaan  = mysqli_stmt_init($connection);
        $query_perusahaan = "INSERT INTO tbl_perusahaan 
        (
            id_pengguna
            , id_jenis_perusahaan
            , nama_perusahaan
            , alamat_perusahaan
        ) 
        VALUES (?, ?, ?, ?)";
        
        if (!mysqli_stmt_prepare($stmt_perusahaan, $query_perusahaan)) {
            $_SESSION['msg'] = 'Statement Siswa preparation failed: ' . mysqli_stmt_error($stmt_perusahaan);
            echo "<meta http-equiv='refresh' content='0;perusahaan.php?go=perusahaan'>";
            return;
        }
        
        $id_pengguna = mysqli_insert_id($connection);
        mysqli_stmt_bind_param($stmt_perusahaan, 'iiss', $id_pengguna, $id_jenis_perusahaan, $nama_perusahaan, $alamat_perusahaan);
        
        if (!mysqli_stmt_execute($stmt_perusahaan)) {
            $_SESSION['msg'] = 'Statement Siswa preparation failed: ' . mysqli_stmt_error($stmt_perusahaan);
            echo "<meta http-equiv='refresh' content='0;perusahaan.php?go=perusahaan'>";
            return;
        }

        // Commit the transaction if all statements succeed
        if (!mysqli_commit($connection)) {
            $_SESSION['msg'] = 'Transaction commit failed: ' . mysqli_stmt_error($stmt_perusahaan);
            echo "<meta http-equiv='refresh' content='0;perusahaan.php?go=perusahaan'>";
            return;
        }

    } catch (Exception $e) {
        // Roll back the transaction if any statement fails
        $success = false;
        mysqli_rollback($connection);
        $_SESSION['msg'] = 'Transaction failed: ' . $e->getMessage();
    }

    // Close the statements
    mysqli_stmt_close($stmt_pengguna);
    mysqli_stmt_close($stmt_perusahaan);

    // Turn autocommit mode back on
    mysqli_autocommit($connection, true);

    // Close the connection
    mysqli_close($connection);

    !$success
        ? ''
        : $_SESSION['msg'] = 'save_success';

    echo "<meta http-equiv='refresh' content='0;perusahaan.php?go=perusahaan'>";
?>
