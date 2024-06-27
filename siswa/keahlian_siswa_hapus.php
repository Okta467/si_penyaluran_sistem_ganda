<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah siswa?
    if (!isAccessAllowed('siswa')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_keahlian_siswa = $_GET['xid_keahlian_siswa'];
    $id_siswa = $_SESSION['id_siswa'];
    
    // Get keahlian siswa to delete current file_keahlian after data deletion
    $stmt_keahlian_siswa = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_keahlian_siswa, 'SELECT id_siswa, file_keahlian FROM tbl_keahlian_siswa WHERE id=?');
    mysqli_stmt_bind_param($stmt_keahlian_siswa, 'i', $id_keahlian_siswa);
    mysqli_stmt_execute($stmt_keahlian_siswa);

    $result = mysqli_stmt_get_result($stmt_keahlian_siswa);
    $keahlian_siswa = mysqli_fetch_assoc($result);

    if ($keahlian_siswa['id_siswa'] != $id_siswa) {
        $_SESSION['msg'] = 'Tidak boleh menghapus data siswa lain!';
        echo "<meta http-equiv='refresh' content='0;keahlian_siswa.php?go=keahlian_siswa'>";
        return;
    }

    // tbl_keahlian_siswa data statement and execution
    $stmt_hapus = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_hapus, "DELETE FROM tbl_keahlian_siswa WHERE id=?");
    mysqli_stmt_bind_param($stmt_hapus, 'i', $id_keahlian_siswa);

    $delete = mysqli_stmt_execute($stmt_hapus);
    
    // Delete file_keahlian_siswa if data deletio is success
    if ($delete) {
        $target_dir = '../assets/uploads/file_keahlian_siswa/';
        $old_file_keahlian_siswa = $keahlian_siswa['file_keahlian'];
        $file_path_to_unlink = $target_dir . $old_file_keahlian_siswa;
        
        // Delete the old file_keahlian
        if (file_exists($file_path_to_unlink)) {
            unlink("{$target_dir}{$old_file_keahlian_siswa}");
        }
    }

    !$delete
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'delete_success';

    mysqli_stmt_close($stmt_keahlian_siswa);
    mysqli_stmt_close($stmt_hapus);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;keahlian_siswa.php?go=keahlian_siswa'>";
?>