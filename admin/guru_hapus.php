<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_guru = $_GET['xid_guru'];
    $id_pengguna = $_GET['xid_pengguna'];

    mysqli_autocommit($connection, false);

    $success = true;

    try {
        $stmt_guru = mysqli_stmt_init($connection);
        $query_guru = "DELETE FROM tbl_guru WHERE id=?";

        if (!mysqli_stmt_prepare($stmt_guru, $query_guru)) {
            $_SESSION['msg'] = 'Transaction Pegawai failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }
        
        mysqli_stmt_bind_param($stmt_guru, 'i', $id_guru);

        if (!mysqli_stmt_execute($stmt_guru)) {
            $_SESSION['msg'] = 'Transaction Pegawai failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }

        $stmt_pengguna = mysqli_stmt_init($connection);
        $query_pengguna = "DELETE FROM tbl_pengguna WHERE id=?";

        if (!mysqli_stmt_prepare($stmt_pengguna, $query_pengguna)) {
            $_SESSION['msg'] = 'Transaction Pengguna failed: ' . mysqli_stmt_error($stmt_pengguna);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }
        
        mysqli_stmt_bind_param($stmt_pengguna, 'i', $id_pengguna);

        if (!mysqli_stmt_execute($stmt_pengguna)) {
            $_SESSION['msg'] = 'Transaction Pengguna failed: ' . mysqli_stmt_error($stmt_pengguna);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
            return;
        }

        if (!mysqli_commit($connection)) {
            $_SESSION['msg'] = 'Transaction commit failed: ' . mysqli_stmt_error($stmt_guru);
            echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
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

    mysqli_stmt_close($stmt_guru);
    mysqli_stmt_close($stmt_pengguna);

    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;guru.php?go=guru'>";
?>