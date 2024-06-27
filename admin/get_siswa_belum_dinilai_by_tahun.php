<?php
include_once '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah admin?
if (!isAccessAllowed('admin')) {
    session_destroy();
    echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
    return;
}

include_once '../config/connection.php';

$id_tahun_seleksi = $_POST['id_tahun_seleksi'];


// Get siswa yang sudah dinliai
$stmt_sudah_diseleksi = mysqli_stmt_init($connection);
$query =
    "SELECT a.id AS id_siswa, a.nama_siswa
    FROM tbl_siswa AS a
    LEFT JOIN tbl_seleksi AS b
        ON a.id = b.id_siswa
    WHERE b.id_tahun_seleksi=?
    ORDER BY nama_siswa ASC";

mysqli_stmt_prepare($stmt_sudah_diseleksi, $query);
mysqli_stmt_bind_param($stmt_sudah_diseleksi, 'i', $id_tahun_seleksi);
mysqli_stmt_execute($stmt_sudah_diseleksi);

$result = mysqli_stmt_get_result($stmt_sudah_diseleksi);
$siswa_sudah_diseleksis = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Get all siswa
$stmt_siswa = mysqli_stmt_init($connection);
$query = "SELECT a.id AS id_siswa, a.nama_siswa FROM tbl_siswa AS a ORDER BY nama_siswa ASC";

mysqli_stmt_prepare($stmt_siswa, $query);
mysqli_stmt_execute($stmt_siswa);

$result = mysqli_stmt_get_result($stmt_siswa);
$siswas = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Delete siswa yang sudah diseleksi dari data siswa
$i = 0;
$siswa_belum_diseleksis = [];

foreach ($siswa_sudah_diseleksis as $item) {
    $id_siswa = $item['id_siswa'];
    $siswa_belum_diseleksis[$id_siswa] = $item;
}

foreach ($siswas as $item) {
    $id_siswa = $item['id_siswa'];
    
    if (isset($siswa_belum_diseleksis[$id_siswa])) {
        unset($siswa_belum_diseleksis[$id_siswa]);
    } else {
        $siswa_belum_diseleksis[$id_siswa] = $item;
    }
}

// Reset array key (from 0), else it'll return JS Object instead of array
$siswa_belum_diseleksis = array_values($siswa_belum_diseleksis);

mysqli_stmt_close($stmt_sudah_diseleksi);
mysqli_stmt_close($stmt_siswa);
mysqli_close($connection);

echo json_encode($siswa_belum_diseleksis);
?>