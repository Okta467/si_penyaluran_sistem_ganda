<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah siswa?
    if (!isAccessAllowed('siswa')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_prestasi_siswa = $_GET['xid_prestasi_siswa'];
    $id_siswa = $_SESSION['id_siswa'];
    
    // Get prestasi siswa to delete current file_prestasi after data deletion
    $stmt_prestasi_siswa = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_prestasi_siswa, 'SELECT id_siswa, file_prestasi FROM tbl_prestasi_siswa WHERE id=?');
    mysqli_stmt_bind_param($stmt_prestasi_siswa, 'i', $id_prestasi_siswa);
    mysqli_stmt_execute($stmt_prestasi_siswa);

    $result = mysqli_stmt_get_result($stmt_prestasi_siswa);
    $prestasi_siswa = mysqli_fetch_assoc($result);

    if ($prestasi_siswa['id_siswa'] != $id_siswa) {
        $_SESSION['msg'] = 'Tidak boleh menghapus data siswa lain!';
        echo "<meta http-equiv='refresh' content='0;prestasi_siswa.php?go=prestasi_siswa'>";
        return;
    }

    // tbl_prestasi_siswa data statement and execution
    $stmt_hapus = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_hapus, "DELETE FROM tbl_prestasi_siswa WHERE id=?");
    mysqli_stmt_bind_param($stmt_hapus, 'i', $id_prestasi_siswa);

    $delete = mysqli_stmt_execute($stmt_hapus);
    
    // Delete file_prestasi_siswa if data deletio is success
    if ($delete) {
        $target_dir = '../assets/uploads/file_prestasi_siswa/';
        $old_file_prestasi_siswa = $prestasi_siswa['file_prestasi'];
        $file_path_to_unlink = $target_dir . $old_file_prestasi_siswa;
        
        // Delete the old file_prestasi
        if (file_exists($file_path_to_unlink)) {
            unlink("{$target_dir}{$old_file_prestasi_siswa}");
        }
    }

    !$delete
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'delete_success';

    mysqli_stmt_close($stmt_prestasi_siswa);
    mysqli_stmt_close($stmt_hapus);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;prestasi_siswa.php?go=prestasi_siswa'>";
?>