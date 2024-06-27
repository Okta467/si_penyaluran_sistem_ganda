<?php
include '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah kepala_sekolah?
if (!isAccessAllowed('kepala_sekolah')) :
  session_destroy();
  echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
else :
  include_once '../config/connection.php';
?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <?php include '_partials/head.php' ?>

    <meta name="description" content="Data Pengumuman" />
    <meta name="author" content="" />
    <title>Pengumuman - <?= SITE_NAME ?></title>
  </head>

  <body class="bg-white">
    <?php
    $no = 1;
    $id_tahun_seleksi = $_GET['id_tahun_seleksi'] ?? null;

    $stmt_tahun_seleksi = mysqli_stmt_init($connection);

    if (!$id_tahun_seleksi) {
      mysqli_stmt_prepare($stmt_tahun_seleksi, "SELECT '(Tidak ada)' AS tahun");
    } else {
      mysqli_stmt_prepare($stmt_tahun_seleksi, "SELECT * FROM tbl_tahun_seleksi WHERE id=?");
      mysqli_stmt_bind_param($stmt_tahun_seleksi, 'i', $id_tahun_seleksi);
    }

    mysqli_stmt_execute($stmt_tahun_seleksi);

    $result = mysqli_stmt_get_result($stmt_tahun_seleksi);
    $tahun_seleksi = mysqli_fetch_assoc($result);


    $stmt_pengumuman = mysqli_stmt_init($connection);
    $query_pengumuman =
      "SELECT
        a.id AS id_seleksi,
        b.id AS id_tahun_seleksi, b.tahun,
        c.id AS id_siswa, c.nisn, c.nama_siswa, c.jk, c.alamat, c.tmp_lahir, c.tgl_lahir, c.no_telp, c.email,
        d.id AS id_prestasi_siswa, d.nama_prestasi, d.file_prestasi,
        e.id AS id_keahlian_siswa, e.nama_keahlian, e.file_keahlian,
        f.id AS id_posisi_penempatan, f.nama_posisi,
        g.id AS id_perusahaan, g.nama_perusahaan,
        h.id AS id_jenis_perusahaan, h.nama_jenis,
        i.id AS id_pengumuman, i.keterangan_seleksi
      FROM tbl_seleksi AS a
      INNER JOIN tbl_tahun_seleksi AS b
        ON b.id = a.id_tahun_seleksi
      INNER JOIN tbl_siswa AS c
        ON c.id = a.id_siswa
      LEFT JOIN tbl_prestasi_siswa AS d
        ON c.id = d.id_siswa
      LEFT JOIN tbl_keahlian_siswa AS e
        ON c.id = e.id_siswa
      INNER JOIN tbl_posisi_penempatan AS f
        ON f.id = a.id_posisi_penempatan
      INNER JOIN tbl_perusahaan AS g
        ON g.id = f.id_perusahaan
      LEFT JOIN tbl_jenis_perusahaan AS h
        ON h.id = g.id_jenis_perusahaan
      LEFT JOIN tbl_pengumuman_seleksi AS i
        ON a.id = i.id_seleksi";

    if (!$id_tahun_seleksi) {
      $query_pengumuman .= " ORDER BY a.id DESC";
      mysqli_stmt_prepare($stmt_pengumuman, $query_pengumuman);
    } else {
      $query_pengumuman .= " WHERE a.id_tahun_seleksi=? ORDER BY a.id DESC";

      mysqli_stmt_prepare($stmt_pengumuman, $query_pengumuman);
      mysqli_stmt_bind_param($stmt_pengumuman, 'i', $id_tahun_seleksi);
    }

    mysqli_stmt_execute($stmt_pengumuman);
    $result = mysqli_stmt_get_result($stmt_pengumuman);

    $pengumumans = mysqli_fetch_all($result, MYSQLI_ASSOC);


    mysqli_stmt_close($stmt_tahun_seleksi);
    mysqli_stmt_close($stmt_pengumuman);
    mysqli_close($connection);
    ?>

    <h4 class="text-center mb-4">Laporan Penyaluran Program Sistem Ganda Tahun <?= $tahun_seleksi['tahun'] ?></h4>

    <table class="table table-striped table-bordered table-sm" id="datatablesSimple">
      <thead>
        <tr>
          <th>#</th>
          <th>Tahun</th>
          <th>Siswa</th>
          <th>Posisi Penempatan</th>
          <th>Perusahaan</th>
          <th>Jenis</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$result->num_rows): ?>

          <tr>
            <td colspan="6"><div class="text-center">Tidak ada data</div></td>
          </tr>
        
        <?php else: ?>

          <?php foreach($pengumumans as $pengumuman) : ?>

            <tr>
              <td><?= $no++ ?></td>
              <td><?= $pengumuman['tahun'] ?></td>
              <td><?= $pengumuman['nama_siswa'] ?></td>
              <td><?= $pengumuman['nama_posisi'] ?></td>
              <td><?= $pengumuman['nama_perusahaan'] ?></td>
              <td><?= $pengumuman['nama_jenis'] ?></td>
              <td>
                <?php if (!$pengumuman['keterangan_seleksi']) : ?>

                  <small class="fw-bold text-muted">Belum Di-input</small>

                <?php elseif ($pengumuman['keterangan_seleksi'] === 'lolos') : ?>

                  <small class="fw-bold text-success">Lolos</small>

                <?php else : ?>

                  <small class="fw-bold text-danger">Tidak Lolos</small>

                <?php endif ?>
              </td>
            </tr>

          <?php endforeach ?>

        <?php endif ?>
      </tbody>
    </table>

  </body>

  </html>

<?php endif ?>