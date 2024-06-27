<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah kepala_sekolah?
    if (!isAccessAllowed('kepala_sekolah')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    include_once '../config/connection.php';
    
    $id_guru = $_POST['id_guru'];

    $stmt1 = mysqli_stmt_init($connection);
    $query = 
        "SELECT
            a.id AS id_guru, a.nip, a.nama_guru, a.jk, a.alamat, a.tmp_lahir, a.tgl_lahir, a.tahun_ijazah,
            b.id AS id_jabatan, b.nama_jabatan, 
            c.id AS id_pangkat_golongan, c.nama_pangkat_golongan, c.tipe AS tipe_pangkat_golongan,
            d.id AS id_pendidikan, d.nama_pendidikan,
            e.id AS id_jurusan_pendidikan, e.nama_jurusan AS nama_jurusan_pendidikan,
            f.id AS id_pengguna, f.username, f.hak_akses
        FROM tbl_guru AS a
        LEFT JOIN tbl_jabatan AS b
            ON a.id_jabatan = b.id
        LEFT JOIN tbl_pangkat_golongan AS c
            ON a.id_pangkat_golongan = c.id
        LEFT JOIN tbl_pendidikan AS d
            ON a.id_pendidikan = d.id
        LEFT JOIN tbl_jurusan_pendidikan AS e
            ON a.id_jurusan_pendidikan = e.id
        LEFT JOIN tbl_pengguna AS f
            ON a.id_pengguna = f.id
        WHERE a.id=?";

    mysqli_stmt_prepare($stmt1, $query);
    mysqli_stmt_bind_param($stmt1, 'i', $id_guru);
    mysqli_stmt_execute($stmt1);

	$result = mysqli_stmt_get_result($stmt1);

    $gurus = !$result
        ? array()
        : mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_stmt_close($stmt1);
    mysqli_close($connection);

    echo json_encode($gurus);

?>