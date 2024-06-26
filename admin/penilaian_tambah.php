<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah admin?
    if (!isAccessAllowed('admin')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_tahun_penilaian = $_POST['xid_tahun_penilaian'];
    $id_siswa           = $_POST['xid_siswa'];
    $nilai_prestasi     = $_POST['xnilai_prestasi'];
    $nilai_keahlian     = $_POST['xnilai_keahlian'];

    $stmt_penilaian = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_penilaian, "SELECT id FROM tbl_penilaian_seleksi WHERE id_siswa=? AND id_tahun_penilaian=?");
    mysqli_stmt_bind_param($stmt_penilaian, 'ii', $id_siswa, $id_tahun_penilaian);
    mysqli_stmt_execute($stmt_penilaian);

    $result = mysqli_stmt_get_result($stmt_penilaian);
    $penilaian = mysqli_fetch_assoc($result);

    if ($penilaian) {
        $_SESSION['msg'] = 'Penilaian untuk siswa dan tahun penilaian tersebut sudah ada!';
        echo "<meta http-equiv='refresh' content='0;penilaian.php?go=penilaian'>";
        return;
    }

    $stmt_insert = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_insert, "INSERT INTO tbl_penilaian_seleksi (id_siswa, id_tahun_penilaian, nilai_prestasi, nilai_keahlian) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, 'iidd', $id_siswa, $id_tahun_penilaian, $nilai_prestasi, $nilai_keahlian);

    $insert = mysqli_stmt_execute($stmt_insert);

    !$insert
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'save_success';

    mysqli_stmt_close($stmt_penilaian);
    mysqli_stmt_close($stmt_insert);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;penilaian.php?go=penilaian'>";
?>