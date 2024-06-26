<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_penilaian       = $_POST['xid_penilaian'];
    $id_tahun_penilaian = $_POST['xid_tahun_penilaian'];
    $nilai_prestasi     = $_POST['xnilai_prestasi'];
    $nilai_keahlian     = $_POST['xnilai_keahlian'];

    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "UPDATE tbl_penilaian_seleksi SET id_tahun_penilaian=?, nilai_prestasi=?, nilai_keahlian=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'iddi', $id_tahun_penilaian, $nilai_prestasi, $nilai_keahlian, $id_penilaian);

    $update = mysqli_stmt_execute($stmt);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'update_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;penilaian.php?go=penilaian'>";
?>