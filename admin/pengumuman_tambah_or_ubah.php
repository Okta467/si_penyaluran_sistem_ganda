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
    
    $id_penilaian = $_POST['xid_penilaian'];
    $keterangan_seleksi = $_POST['xketerangan_seleksi'];

    if (!in_array($keterangan_seleksi, ['lolos', 'tidak_lolos'])) {
        $_SESSION['msg'] = 'Keterangan harus bernilai `Lolos` atau `Tidak Lolos`!';
        echo "<meta http-equiv='refresh' content='0;jabatan.php?go=jabatan'>";
        return;
    }

    $stmt_penilaian = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_penilaian, "SELECT id FROM tbl_pengumuman_seleksi WHERE id_penilaian_seleksi=?");
    mysqli_stmt_bind_param($stmt_penilaian, 'i', $id_penilaian);
    mysqli_stmt_execute($stmt_penilaian);

    $result = mysqli_stmt_get_result($stmt_penilaian);
    $penilaian = mysqli_fetch_assoc($result);

    $success = true;

    // Insert if no data penilaian, else update
    if (!$penilaian) {
        $stmt = mysqli_stmt_init($connection);
    
        mysqli_stmt_prepare($stmt, "INSERT INTO tbl_pengumuman_seleksi (id_penilaian_seleksi, keterangan_seleksi) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'is', $id_penilaian, $keterangan_seleksi);
        
        if (!mysqli_stmt_execute($stmt)) {
            $success = false;
            $_SESSION['msg'] = 'Error insert: ' . mysqli_stmt_error($stmt);
        }
    } else {
        $stmt = mysqli_stmt_init($connection);
    
        mysqli_stmt_prepare($stmt, "UPDATE tbl_pengumuman_seleksi SET keterangan_seleksi=? WHERE id_penilaian_seleksi=?");
        mysqli_stmt_bind_param($stmt, 'si', $keterangan_seleksi, $id_penilaian);
        
        if (!mysqli_stmt_execute($stmt)) {
            $success = false;
            $_SESSION['msg'] = 'Error update: ' . mysqli_stmt_error($stmt);
        }
    }

    !$success
        ? ''
        : $_SESSION['msg'] = 'save_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;pengumuman.php?go=pengumuman'>";
?>