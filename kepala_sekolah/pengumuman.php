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

  <body class="nav-fixed">
    <!--============================= TOPNAV =============================-->
    <?php include '_partials/topnav.php' ?>
    <!--//END TOPNAV -->
    <div id="layoutSidenav">
      <div id="layoutSidenav_nav">
        <!--============================= SIDEBAR =============================-->
        <?php include '_partials/sidebar.php' ?>
        <!--//END SIDEBAR -->
      </div>
      <div id="layoutSidenav_content">
        <main>
          <!-- Main page content-->
          <div class="container-xl px-4 mt-5">

            <!-- Custom page header alternative example-->
            <div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row mb-4">
              <div class="me-4 mb-3 mb-sm-0">
                <h1 class="mb-0">Pengumuman</h1>
                <div class="small">
                  <span class="fw-500 text-primary"><?= date('D') ?></span>
                  &middot; <?= date('M d, Y') ?> &middot; <?= date('H:i') ?> WIB
                </div>
              </div>

              <!-- Date range picker example-->
              <div class="input-group input-group-joined border-0 shadow w-auto">
                <span class="input-group-text"><i data-feather="calendar"></i></span>
                <input class="form-control ps-0 pointer" id="litepickerRangePlugin" value="Tanggal: <?= date('d M Y') ?>" readonly />
              </div>

            </div>

            <!-- Tools Cetak Pengumuman -->
            <div class="card mb-4 mt-5">
              <div class="card-header">
                <div>
                  <i data-feather="settings" class="me-2 mt-1"></i>
                  Data Pengumuman
                </div>
              </div>
              <div class="card-body">
                <div class="row gx-3">
                  <div class="col-md-2 mb-3">
                    <label class="small mb-1" for="xid_tahun_seleksi_filter">Tahun Penilaian</label>
                    <select name="xid_tahun_seleksi_filter" class="form-control" id="xid_tahun_seleksi_filter" required>
                      <option value="">-- Pilih --</option>
                      <?php
                      $query_tahun_seleksi = mysqli_query($connection, 
                        "SELECT a.*, IFNULL(b.jml_seleksi, 0) AS jml_seleksi
                        FROM tbl_tahun_seleksi AS a
                        LEFT JOIN
                        (
                          SELECT  COUNT(*) AS jml_seleksi, id_tahun_seleksi
                          FROM tbl_seleksi
                          GROUP BY  id_tahun_seleksi
                        ) AS b
                        ON a.id = b.id_tahun_seleksi
                        ORDER BY a.tahun DESC")
                      ?>
                      <?php while ($tahun_seleksi = mysqli_fetch_assoc($query_tahun_seleksi)): ?>
              
                        <option value="<?= $tahun_seleksi['id'] ?>" data-tahun="<?= $tahun_seleksi['tahun'] ?>"><?= "{$tahun_seleksi['tahun']} ({$tahun_seleksi['jml_seleksi']} data)" ?></option>
              
                      <?php endwhile ?>
                    </select>
                  </div>
                  <div class="col-md-2 mb-3">
                    <label class="small mb-1 invisible" for="xreset_filter_tahun">Reset Filter Tahun</label>
                    <button class="btn btn-dark w-100" id="xreset_filter_tahun" type="button">
                      <i data-feather="repeat" class="me-1"></i>
                      Reset Filter
                    </button>
                  </div>
                  <div class="col-md-2 mb-3">
                    <label class="small mb-1 invisible" for="xcetak_pengumuman">Filter Button</label>
                    <button class="btn btn-primary w-100" id="xcetak_pengumuman" type="button">
                      <i data-feather="printer" class="me-1"></i>
                      Cetak
                    </button>
                  </div>
                </div>
              </div>
            </div>
              
            <!-- Main page content-->
            <div class="card card-header-actions mb-4 mt-5">
              <div class="card-header">
                <div>
                  <i data-feather="flag" class="me-2 mt-1"></i>
                  Data Pengumuman
                </div>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tahun</th>
                      <th>Siswa</th>
                      <th>Posisi</th>
                      <th>Perusahaan</th>
                      <th>Jenis</th>
                      <th>File Prestasi</th>
                      <th>File Kompetensi</th>
                      <th>Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_pengumuman = mysqli_query($connection, 
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
                        ON a.id = i.id_seleksi
                      ORDER BY a.id DESC");

                    while ($pengumuman = mysqli_fetch_assoc($query_pengumuman)):
                      $link_file_prestasi = !$pengumuman['file_prestasi'] 
                        ? null 
                        : base_url_return('assets/uploads/file_prestasi_siswa/') . $pengumuman['file_prestasi'];

                      $link_file_keahlian = !$pengumuman['file_keahlian'] 
                        ? null 
                        : base_url_return('assets/uploads/file_keahlian_siswa/') . $pengumuman['file_keahlian'];
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $pengumuman['tahun'] ?></td>
                        <td><?= $pengumuman['nama_siswa'] ?></td>
                        <td><?= $pengumuman['nama_posisi'] ?></td>
                        <td><?= $pengumuman['nama_perusahaan'] ?></td>
                        <td><?= $pengumuman['nama_jenis'] ?></td>
                        <td>
                          <?php if (!$link_file_prestasi): ?>

                            <small class="text-muted">Tidak ada</small>

                          <?php else: ?>

                            <a class="btn btn-xs rounded-pill bg-purple-soft text-purple" href="<?= $link_file_prestasi ?>" target="_blank">
                              <i data-feather="eye" class="me-1"></i>Preview
                            </a>
                            <a class="btn btn-xs rounded-pill bg-blue-soft text-blue" href="<?= $link_file_prestasi ?>" download>
                              <i data-feather="download-cloud" class="me-1"></i>Download
                            </a>
                          
                          <?php endif ?>
                        </td>
                        <td>
                          <?php if (!$link_file_keahlian): ?>

                            <small class="text-muted">Tidak ada</small>

                          <?php else: ?>
                          
                            <a class="btn btn-xs rounded-pill bg-purple-soft text-purple" href="<?= $link_file_keahlian ?>" target="_blank">
                              <i data-feather="eye" class="me-1"></i>Preview
                            </a>
                            <a class="btn btn-xs rounded-pill bg-blue-soft text-blue" href="<?= $link_file_keahlian ?>" download>
                              <i data-feather="download-cloud" class="me-1"></i>Download
                            </a>
                          
                          <?php endif ?>
                        </td>
                        <td>
                          <?php if (!$pengumuman['keterangan_seleksi']): ?>

                            <small class="fw-bold text-muted">Belum Di-input</small>
                            
                          <?php elseif ($pengumuman['keterangan_seleksi'] === 'lolos'): ?>

                            <small class="fw-bold text-success">Lolos</small>
                              
                          <?php else: ?>
                            
                            <small class="fw-bold text-danger">Tidak Lolos</small>
                            
                          <?php endif ?>
                        </td>
                      </tr>

                    <?php endwhile ?>
                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
        </main>
        
        <!--============================= FOOTER =============================-->
        <?php include '_partials/footer.php' ?>
        <!--//END FOOTER -->

      </div>
    </div>
    
    <?php include '_partials/script.php' ?>
    <?php include '../helpers/sweetalert2_notify.php' ?>
    
    <!-- PAGE SCRIPT -->
    <script>
      $(document).ready(function() {

        const id_tahun_seleksi_filter = $('#xid_tahun_seleksi_filter');

        const datatablesSimple = document.getElementById('datatablesSimple');
        let dataTable = new simpleDatatables.DataTable(datatablesSimple);
        
        initSelect2(id_tahun_seleksi_filter, {
          width: '100%',
          dropdownParent: "body"
        });
        
        
        id_tahun_seleksi_filter.on('change', function() {
          const tahun_seleksi = $(this).find(':selected').data('tahun');

          !tahun_seleksi
            ? dataTable.refresh()
            : dataTable.search(`${tahun_seleksi}`);
        });
        

        $('#xreset_filter_tahun').on('click', function() {
          id_tahun_seleksi_filter.val('').trigger('change');
          dataTable.refresh();
        });


        $('#xcetak_pengumuman').on('click', function() {
          const id_tahun_seleksi = $('#xid_tahun_seleksi_filter').val();
          const url = `laporan_pengumuman.php?id_tahun_seleksi=${id_tahun_seleksi}`;
          
          printExternal(url);
        });
        
      });
    </script>

  </body>

  </html>

<?php endif ?>