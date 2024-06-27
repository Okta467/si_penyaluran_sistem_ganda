<?php
include '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah perusahaan?
if (!isAccessAllowed('perusahaan')) :
  session_destroy();
  echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
else :
  include_once '../config/connection.php';
?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <?php include '_partials/head.php' ?>

    <meta name="description" content="Data Seleksi" />
    <meta name="author" content="" />
    <title>Seleksi - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Seleksi</h1>
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

            <!-- Page Information Description -->
            <div class="card card-header-actions mb-4 mt-5">
              <div class="bg-gray-100 rounded p-3 border border-primary">
                <h6 class="text-blue">
                  <i data-feather="info" class="me-1"></i>
                  Informasi
                </h6>
                <p class="small mb-0">Data yang ditampilkan merupakan <span class="text-danger">siswa</span> yang <span class="text-danger">sudah ditentukan</span> untuk perusahaan Anda. Perusahaan dapat <span class="text-danger">me-review file prestasi dan keahlian</span> serta <span class="text-danger">mengubah posisi</span> penempatan sebelum memberikan <span class="text-danger">keterangan lolos/tidak</span> pada halaman <a href="pengumuman.php?go=pengumuman">pengumuman seleksi</a>.</p>
              </div>
            </div>
            
            <!-- Main page content-->
            <div class="card card-header-actions mb-4 mt-5">
              <div class="card-header">
                <div>
                  <i data-feather="edit-2" class="me-2 mt-1"></i>
                  Data Seleksi
                </div>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tahun</th>
                      <th>Siswa</th>
                      <th>Kelas</th>
                      <th>Posisi Penempatan</th>
                      <th>Perusahaan</th>
                      <th>Jenis</th>
                      <th>Daftar File Prestasi</th>
                      <th>Daftar File Keahlian</th>
                      <th>Ubah Posisi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_seleksi = mysqli_query($connection, 
                      "SELECT
                        a.id AS id_seleksi,
                        b.id AS id_tahun_seleksi, b.tahun,
                        c.id AS id_siswa, c.nisn, c.nama_siswa, c.jk, c.alamat, c.tmp_lahir, c.tgl_lahir, c.no_telp, c.email,
                        IFNULL(d.jml_file_prestasi, 0) AS jml_file_prestasi,
                        IFNULL(e.jml_file_keahlian, 0) AS jml_file_keahlian,
                        f.id AS id_posisi_penempatan, f.nama_posisi,
                        g.id AS id_perusahaan, g.nama_perusahaan,
                        h.id AS id_jenis_perusahaan, h.nama_jenis,
                        i.id AS id_kelas, i.nama_kelas
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
                      LEFT JOIN tbl_kelas AS i
                        ON i.id = c.id_kelas
                      WHERE g.id = {$_SESSION['id_perusahaan']}
                      ORDER BY a.id DESC");

                    while ($seleksi = mysqli_fetch_assoc($query_seleksi)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <div class="text-center">
                            <?= $seleksi['tahun'] ?>
                          </div>
                        </td>
                        <td><?= $seleksi['nama_siswa'] ?></td>
                        <td>
                          <div class="text-center">
                            <?= $seleksi['nama_kelas'] ?>
                          </div>
                        </td>
                        <td><?= $seleksi['nama_posisi'] ?></td>
                        <td><?= $seleksi['nama_perusahaan'] ?></td>
                        <td><?= $seleksi['nama_jenis'] ?></td>
                        <td>
                          <?php if (!$seleksi['jml_file_prestasi']): ?>

                            <small class="text-muted">Tidak ada</small>

                          <?php else: ?>
                          
                            <button type="button" class="btn btn-xs rounded-pill btn-outline-primary toggle_daftar_prestasi_siswa" data-id_siswa="<?= $seleksi['id_siswa'] ?>">
                              <i data-feather="list" class="me-1"></i>
                              Daftar
                              <span class="btn btn-sm rounded-pill btn-outline-primary py-0 px-2 ms-1"><?= $seleksi['jml_file_prestasi'] ?></button>
                            </button>
                          
                          <?php endif ?>
                        </td>
                        <td>
                          <?php if (!$seleksi['jml_file_keahlian']): ?>

                            <small class="text-muted">Tidak ada</small>

                          <?php else: ?>
                          
                            <button type="button" class="btn btn-xs rounded-pill btn-outline-primary toggle_daftar_keahlian_siswa" data-id_siswa="<?= $seleksi['id_siswa'] ?>">
                              <i data-feather="list" class="me-1"></i>
                              Daftar
                              <span class="btn btn-sm rounded-pill btn-outline-primary py-0 px-2 ms-1"><?= $seleksi['jml_file_keahlian'] ?></button>
                            </button>
                          
                          <?php endif ?>
                        </td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_seleksi="<?= $seleksi['id_seleksi'] ?>" 
                            data-id_tahun_seleksi="<?= $seleksi['id_tahun_seleksi'] ?>"
                            data-id_perusahaan="<?= $seleksi['id_perusahaan'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
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
    
    <!--============================= MODAL INPUT JURUSAN =============================-->
    <div class="modal fade" id="ModalInputSeleksi" tabindex="-1" role="dialog" aria-labelledby="ModalInputSeleksiTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputSeleksiTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_seleksi" name="xid_seleksi">
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_tahun_seleksi">Tahun Seleksi <span class="text-danger fw-bold">*</span></label>
                <select name="xid_tahun_seleksi" class="form-control mb-1 select2" id="xid_tahun_seleksi" required>
                  <option value="">-- Pilih --</option>
                  <?php $query_tahun_seleksi = mysqli_query($connection, "SELECT * FROM tbl_tahun_seleksi ORDER BY tahun DESC") ?>
                  <?php while ($tahun_seleksi = mysqli_fetch_assoc($query_tahun_seleksi)): ?>
          
                    <option value="<?= $tahun_seleksi['id'] ?>"><?= $tahun_seleksi['tahun'] ?></option>
          
                  <?php endwhile ?>
                </select>
                <small class="text-muted">Pilih tahun terlebih dahulu untuk melihat daftar siswa.</small>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_siswa">Siswa <span class="text-danger fw-bold">*</span></label>
                <select name="xid_siswa" class="form-control mb-1 select2" id="xid_siswa" required>
                  <option value="">-- Pilih --</option>
                </select>
                <small class="text-muted">Siswa yang ditampilkan hanya yang belum dinilai pada tahun yang dipilih.</small>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_perusahaan">Perusahaan <span class="text-danger fw-bold">*</span></label>
                <select name="xid_perusahaan" class="form-control mb-1 select2" id="xid_perusahaan" required>
                  <option value="">-- Pilih --</option>
                  <?php
                  $query_perusahaan = mysqli_query($connection, 
                    "SELECT
                      a.id AS id_perusahaan, a.nama_perusahaan,
                      b.id AS id_posisi_penempatan, b.nama_posisi
                    FROM tbl_perusahaan AS a
                    INNER JOIN tbl_posisi_penempatan AS b
                      ON a.id = b.id_perusahaan
                    WHERE a.id = {$_SESSION['id_perusahaan']}
                    GROUP BY a.id")
                  ?>
                  <?php while ($perusahaan = mysqli_fetch_assoc($query_perusahaan)): ?>

                    <option value="<?= $perusahaan['id_perusahaan'] ?>"><?= $perusahaan['nama_perusahaan'] ?></option>

                  <?php endwhile ?>
                </select>
                <small class="text-muted">Perusahaan yang ditampilkan hanya yang memiliki posisi penempatan.</small>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_posisi_penempatan">Posisi Penempatan <span class="text-danger fw-bold">*</span></label>
                <select name="xid_posisi_penempatan" class="form-control mb-1 select2" id="xid_posisi_penempatan" required>
                  <option value="">-- Pilih --</option>
                </select>
                <small class="text-muted">Pilih perusahaan terlebih dahulu!</small>
              </div>

            </div>
            <div class="modal-footer">
              <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary" id="toggle_swal_submit" type="submit">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/.modal-input-jurusan -->
    
    <?php include '_partials/script.php' ?>
    <?php include '../helpers/sweetalert2_notify.php' ?>
    
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
        
        const select2ModalInputSeleksi = $('#ModalInputSeleksi .select2');
        
        initSelect2(select2ModalInputSeleksi, {
          width: '100%',
          dropdownParent: "#ModalInputSeleksi .modal-content .modal-body"
        });


        $('.toggle_modal_ubah').on('click', function() {
          const data = $(this).data();
          
          $('#ModalInputSeleksi .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Seleksi`);
          $('#ModalInputSeleksi form').attr({action: 'seleksi_ubah.php', method: 'post'});

          $('#ModalInputSeleksi #xid_seleksi').val(data.id_seleksi);
          $('#ModalInputSeleksi #xid_tahun_seleksi').val(data.id_tahun_seleksi).trigger('change');
          $('#ModalInputSeleksi #xid_tahun_seleksi').prop('disabled', true);
          $('#ModalInputSeleksi #xid_siswa').val(data.id_siswa).trigger('change');
          $('#ModalInputSeleksi #xid_siswa').prop('disabled', true);
          $('#ModalInputSeleksi #xid_perusahaan').val(data.id_perusahaan).trigger('change');

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputSeleksi').modal('show');
        });

        
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


        $('#xid_tahun_seleksi').on('change', function() {
          const id_tahun_seleksi = $(this).val();

          if (!id_tahun_seleksi) {
            const siswaSelect = $('#xid_siswa');
            
            siswaSelect.html(null);
              
            initSelect2(siswaSelect, {
              data: [
                {id: '', text: 'Pilih tahun terlebih dahulu!'}
              ],
              width: '100%',
              dropdownParent: "#ModalInputSeleksi .modal-content .modal-body"
            });

            return;
          }

          $.ajax({
            url: 'get_siswa_belum_dinilai_by_tahun.php',
            type: 'POST',
            data: {
              id_tahun_seleksi: id_tahun_seleksi
            },
            dataType: 'JSON',
            success: function(data) {
              // Transform the data to the format that Select2 expects
              const transformedData = data.map(item => ({
                id: item.id_siswa,
                text: item.nama_siswa
              }));
              
              const siswaSelect = $('#xid_siswa');
              
              siswaSelect.html(null);
              
              initSelect2(siswaSelect, {
                data: transformedData,
                width: '100%',
                dropdownParent: "#ModalInputSeleksi .modal-content .modal-body"
              });
            },
            error: function(request, status, error) {
              console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          })
        });


        $('#xid_perusahaan').on('change', function() {
          const id_perusahaan = $(this).val();

          $.ajax({
            url: 'get_posisi_penempatan.php',
            type: 'POST',
            data: {
              id_perusahaan: id_perusahaan
            },
            dataType: 'JSON',
            success: function(data) {
              // Transform the data to the format that Select2 expects
              const transformedData = data.map(item => ({
                id: item.id_posisi_penempatan,
                text: item.nama_posisi
              }));
              
              const posisiPenempatanSelect = $('#xid_posisi_penempatan');
              
              posisiPenempatanSelect.html(null);
              
              initSelect2(posisiPenempatanSelect, {
                data: transformedData,
                width: '100%',
                dropdownParent: "#ModalInputSeleksi .modal-content .modal-body"
              });
            },
            error: function(request, status, error) {
              // console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          })
        });
        

        const formSubmitBtn = $('#toggle_swal_submit');
        const eventName = 'click';
        
        toggleSwalSubmit(formSubmitBtn, eventName);
        
      });
    </script>

  </body>

  </html>

<?php endif ?>