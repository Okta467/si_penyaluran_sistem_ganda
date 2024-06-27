<?php
include '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah siswa?
if (!isAccessAllowed('siswa')) :
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
                      <th>Posisi Penempatan</th>
                      <th>Perusahaan</th>
                      <th>Jenis</th>
                      <th>Daftar File Prestasi</th>
                      <th>Daftar File Keahlian</th>
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
                        IFNULL(d.jml_file_prestasi, 0) AS jml_file_prestasi,
                        IFNULL(e.jml_file_keahlian, 0) AS jml_file_keahlian,
                        f.id AS id_posisi_penempatan, f.nama_posisi,
                        g.id AS id_perusahaan, g.nama_perusahaan,
                        h.id AS id_jenis_perusahaan, h.nama_jenis,
                        i.id AS id_pengumuman, i.keterangan_seleksi
                      FROM tbl_seleksi AS a
                      INNER JOIN tbl_tahun_seleksi AS b
                        ON b.id = a.id_tahun_seleksi
                      INNER JOIN tbl_siswa AS c
                        ON c.id = a.id_siswa
                      LEFT JOIN
                      (
                        SELECT id_siswa, COUNT(id) jml_file_prestasi 
                        FROM tbl_prestasi_siswa 
                        GROUP BY id_siswa
                      ) AS d
                        ON c.id = d.id_siswa
                      LEFT JOIN
                      (
                        SELECT id_siswa, COUNT(id) jml_file_keahlian
                        FROM tbl_keahlian_siswa 
                        GROUP BY id_siswa
                      ) AS e
                        ON c.id = e.id_siswa
                      INNER JOIN tbl_posisi_penempatan AS f
                        ON f.id = a.id_posisi_penempatan
                      INNER JOIN tbl_perusahaan AS g
                        ON g.id = f.id_perusahaan
                      LEFT JOIN tbl_jenis_perusahaan AS h
                        ON h.id = g.id_jenis_perusahaan
                      LEFT JOIN tbl_pengumuman_seleksi AS i
                        ON a.id = i.id_seleksi
                      WHERE c.id = {$_SESSION['id_siswa']}") or die(mysqli_error($connection));

                    while ($pengumuman = mysqli_fetch_assoc($query_pengumuman)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $pengumuman['tahun'] ?></td>
                        <td><?= $pengumuman['nama_siswa'] ?></td>
                        <td><?= $pengumuman['nama_posisi'] ?></td>
                        <td><?= $pengumuman['nama_perusahaan'] ?></td>
                        <td><?= $pengumuman['nama_jenis'] ?></td>
                        <td>
                          <?php if (!$pengumuman['jml_file_prestasi']): ?>

                            <small class="text-muted">Tidak ada</small>

                          <?php else: ?>
                          
                            <button type="button" class="btn btn-xs rounded-pill btn-outline-primary toggle_daftar_prestasi_siswa" data-id_siswa="<?= $pengumuman['id_siswa'] ?>">
                              <i data-feather="list" class="me-1"></i>
                              Daftar
                              <span class="btn btn-sm rounded-pill btn-outline-primary py-0 px-2 ms-1"><?= $pengumuman['jml_file_prestasi'] ?></button>
                            </button>
                          
                          <?php endif ?>
                        </td>
                        <td>
                          <?php if (!$pengumuman['jml_file_keahlian']): ?>

                            <small class="text-muted">Tidak ada</small>

                          <?php else: ?>
                          
                            <button type="button" class="btn btn-xs rounded-pill btn-outline-primary toggle_daftar_keahlian_siswa" data-id_siswa="<?= $pengumuman['id_siswa'] ?>">
                              <i data-feather="list" class="me-1"></i>
                              Daftar
                              <span class="btn btn-sm rounded-pill btn-outline-primary py-0 px-2 ms-1"><?= $pengumuman['jml_file_keahlian'] ?></button>
                            </button>
                          
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
    
    <!--============================= MODAL DAFTAR FILE SISWA =============================-->
    <div class="modal fade" id="ModalDaftarFileSiswa" tabindex="-1" role="dialog" aria-labelledby="ModalDaftarFileSiswaTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalDaftarFileSiswaTitle"><i data-feather="book" class="me-2 mt-1"></i>Daftar Jurusan</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <table class="table table-striped" id="table_daftar_file_siswa">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Siswa</th>
                    <th>Nama</th>
                    <th>File</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

            </div>
            <div class="modal-footer">
              <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/.modal-daftar-file-siswa -->
    
    <?php include '_partials/script.php' ?>
    <?php include '../helpers/sweetalert2_notify.php' ?>

  </body>

  <!-- PAGE SCRIPT -->
   <script>
    $(document).ready(function() {
      
      let tableDaftarFileSiswa = document.getElementById("table_daftar_file_siswa");

      if (tableDaftarFileSiswa) {
        var datatableDaftarFileSiswa = new simpleDatatables.DataTable(tableDaftarFileSiswa, {
          fixedHeader: true,
          pageLength: 5,
          lengthMenu: [
            [3, 5, 10, 25, 50, 100],
            [3, 5, 10, 25, 50, 100],
          ]
        });
      }

      
      $('.toggle_daftar_prestasi_siswa').on('click', function() {
        const id_siswa = $(this).data('id_siswa');
        
        $('#ModalDaftarFileSiswa .modal-title').html(`<i data-feather="star" class="me-2 mt-1"></i>Daftar Prestasi Siswa`);
      
        $.ajax({
          url: 'get_prestasi_siswa_by_id_siswa.php',
          method: 'POST',
          dataType: 'JSON',
          data: {
            'id_siswa': id_siswa
          },
          success: function(data) {
            // add datatables row
            let i = 1;
            let rowsData = [];
            
            for (key in data) {
              let filePrestasi = data[key]['file_prestasi'];
      
              if (!filePrestasi) {
                filePrestasiHtml = `<small class="text-muted">Tidak ada</small>`;
              } else {
                filePrestasiPath = "<?= base_url_return('assets/uploads/file_prestasi_siswa/') ?>" + filePrestasi;
                
                // Preview button
                filePrestasiHtml = 
                  `<a class="btn btn-xs rounded-pill bg-purple-soft text-purple" href="${filePrestasi}" target="_blank">
                    <i data-feather="eye" class="me-1"></i>Preview
                  </a>`;
                
                // Download button
                filePrestasiHtml +=
                  `<a class="btn btn-xs rounded-pill bg-blue-soft text-blue" href="${filePrestasi}" download>
                    <i data-feather="download-cloud" class="me-1"></i>Download
                  </a>`;
              }
              
              rowsData.push([i++, data[key]['nama_siswa'], data[key]['nama_prestasi'], filePrestasiHtml]);
            }
      
            datatableDaftarFileSiswa.destroy();
            datatableDaftarFileSiswa.init();
            datatableDaftarFileSiswa.insert({
              data: rowsData
            });
      
            // Re-init all feather icons
            feather.replace();
            
            $('#ModalDaftarFileSiswa').modal('show');
          },
          error: function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
            console.log("ajax call went wrong:" + error);
          }
        })
      });


      $('.toggle_daftar_keahlian_siswa').on('click', function() {
        const id_siswa = $(this).data('id_siswa');
        
        $('#ModalDaftarFileSiswa .modal-title').html(`<i data-feather="star" class="me-2 mt-1"></i>Daftar Keahlian Siswa`);

        $.ajax({
          url: 'get_keahlian_siswa_by_id_siswa.php',
          method: 'POST',
          dataType: 'JSON',
          data: {
            'id_siswa': id_siswa
          },
          success: function(data) {
            // add datatables row
            let i = 1;
            let rowsData = [];
            
            for (key in data) {
              let fileKeahlian = data[key]['file_keahlian'];

              if (!fileKeahlian) {
                fileKeahlianHtml = `<small class="text-muted">Tidak ada</small>`;
              } else {
                fileKeahlianPath = "<?= base_url_return('assets/uploads/file_keahlian_siswa/') ?>" + fileKeahlian;
                
                // Preview button
                fileKeahlianHtml = 
                  `<a class="btn btn-xs rounded-pill bg-purple-soft text-purple" href="${fileKeahlianPath}" target="_blank">
                    <i data-feather="eye" class="me-1"></i>Preview
                  </a>`;
                
                // Download button
                fileKeahlianHtml +=
                  `<a class="btn btn-xs rounded-pill bg-blue-soft text-blue" href="${fileKeahlianPath}" download>
                    <i data-feather="download-cloud" class="me-1"></i>Download
                  </a>`;
              }
              
              rowsData.push([i++, data[key]['nama_siswa'], data[key]['nama_keahlian'], fileKeahlianHtml]);
            }
      
            datatableDaftarFileSiswa.destroy();
            datatableDaftarFileSiswa.init();
            datatableDaftarFileSiswa.insert({
              data: rowsData
            });

            // Re-init all feather icons
            feather.replace();
            
            $('#ModalDaftarFileSiswa').modal('show');
          },
          error: function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
            console.log("ajax call went wrong:" + error);
          }
        })
      });

    })
   </script>

  </html>

<?php endif ?>