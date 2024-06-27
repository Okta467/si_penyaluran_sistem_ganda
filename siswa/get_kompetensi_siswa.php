<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah siswa?
    if (!isAccessAllowed('siswa')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_kompetensi_siswa = $_POST['id_kompetensi_siswa'];

    $stmt = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            d.id AS id_kompetensi_siswa, d.nama_kompetensi, d.file_kompetensi,
            a.id AS id_siswa, a.nisn, a.nama_siswa, a.jk, a.alamat, a.tmp_lahir, a.tgl_lahir, a.no_telp, a.email,
            b.id AS id_kelas, b.nama_kelas,
            c.id AS id_wali_kelas, c.nama_guru AS nama_wali_kelas,
            f.id AS id_pengguna, f.username, f.hak_akses
        FROM tbl_kompetensi_siswa AS d
        INNER JOIN tbl_siswa AS a
            ON a.id = d.id_siswa
        LEFT JOIN tbl_kelas AS b
            ON b.id = a.id_kelas
        LEFT JOIN tbl_guru AS c
            ON c.id = b.id_wali_kelas
        LEFT JOIN tbl_pengguna AS f
            ON f.id = a.id_pengguna
        WHERE d.id=?";

    mysqli_stmt_prepare($stmt, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_kompetensi_siswa);
    mysqli_stmt_execute($stmt);

	$result = mysqli_stmt_get_result($stmt);

    $kompetensi_siswas = !$result
        ? array()
        : mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    echo json_encode($kompetensi_siswas);

?>