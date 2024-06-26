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
    
    $id_pengguna          = $_POST['xid_pengguna'];
    $username             = htmlspecialchars($purifier->purify($_POST['xusername']));
    $password             = password_hash($_POST['xpassword'], PASSWORD_DEFAULT);
    $is_allowed_hak_akses = in_array($_POST['xhak_akses'], ['perusahaan', 'kepala_sekolah', 'siswa', 'admin']); 
    $hak_akses_yg_diinput = $is_allowed_hak_akses ? $_POST['xhak_akses'] : NULL;

    if (!$is_allowed_hak_akses) {
        $_SESSION['msg'] = 'Hak akses yang diinput tidak diperbolehkan!';
        echo "<meta http-equiv='refresh' content='0;pengguna.php?go=pengguna'>";
        return;
    }

    $stmt_select = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt_select, "SELECT hak_akses FROM tbl_pengguna WHERE id=?");
    mysqli_stmt_bind_param($stmt_select, 'i', $id_pengguna);
    mysqli_stmt_execute($stmt_select);

    $result = mysqli_stmt_get_result($stmt_select);
    
    $pengguna_saat_ini = mysqli_fetch_assoc($result);
    $hak_akses_saat_ini = $pengguna_saat_ini['hak_akses'];

    /**
     * Cek apakah hak akses yg diinput diperbolehkan:
     *   admin          --> harus 'admin'
     *   siswa          --> harus 'siswa'
     *   guru           --> harus 'guru' atau 'kepala_sekolah'
     *   kepala_sekolah --> harus 'guru' atau 'kepala_sekolah'
    */
    if (
        $hak_akses_saat_ini === 'admin'
        && $hak_akses_yg_diinput !== 'admin'
        || $hak_akses_saat_ini === 'siswa'
        && $hak_akses_yg_diinput !== 'siswa'
        || in_array($hak_akses_saat_ini, ['guru', 'kepala_sekolah'])
        && !in_array($hak_akses_yg_diinput, ['guru', 'kepala_sekolah'])
    ) {
        $_SESSION['msg'] = 'Hak akses yang diinput tidak diperbolehkan!';
        echo "<meta http-equiv='refresh' content='0;pengguna.php?go=pengguna'>";
        return;
    }

    $stmt = mysqli_stmt_init($connection);
    
    if (!$password) {
        mysqli_stmt_prepare($stmt, "UPDATE tbl_pengguna SET username=?, hak_akses=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $username, $hak_akses_yg_diinput, $id_pengguna);
    } else {
        mysqli_stmt_prepare($stmt, "UPDATE tbl_pengguna SET username=?, password=?, hak_akses=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssi', $username, $password, $hak_akses_yg_diinput, $id_pengguna);
    }

    $update = mysqli_stmt_execute($stmt);

    !$update
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'save_success';
        
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_select);
    mysqli_close($connection);

    echo "<meta http-equiv='refresh' content='0;pengguna.php?go=pengguna'>";
?>