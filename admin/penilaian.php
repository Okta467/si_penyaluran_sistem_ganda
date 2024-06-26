<?php
include '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah admin?
if (!isAccessAllowed('admin')) :
  session_destroy();
  echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
else :
  include_once '../config/connection.php';
?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <?php include '_partials/head.php' ?>

    <meta name="description" content="Data Penilaian" />
    <meta name="author" content="" />
    <title>Penilaian - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Penilaian</h1>
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
                  <i data-feather="edit-2" class="me-2 mt-1"></i>
                  Data Penilaian
                </div>
                <button class="btn btn-sm btn-primary toggle_modal_tambah" type="button"><i data-feather="plus-circle" class="me-2"></i>Tambah Data</button>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Tahun</th>
                      <th>Siswa</th>
                      <th>Nilai Prestasi</th>
                      <th>Nilai Keahlian</th>
                      <th>File Prestasi</th>
                      <th>File Keahlian</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_penilaian = mysqli_query($connection, 
                      "SELECT
                        a.id AS id_penilaian, a.nilai_keahlian, a.nilai_prestasi,
                        b.id AS id_tahun_penilaian, b.tahun,
                        c.id AS id_siswa, c.nisn, c.nama_siswa, c.jk, c.alamat, c.tmp_lahir, c.tgl_lahir, c.no_telp, c.email,
                        d.id AS id_prestasi_siswa, d.nama_prestasi, d.file_prestasi,
                        e.id AS id_keahlian_siswa, e.nama_keahlian, e.file_keahlian
                      FROM tbl_penilaian_seleksi AS a
                      INNER JOIN tbl_tahun_penilaian AS b
                        ON b.id = a.id_tahun_penilaian
                      INNER JOIN tbl_siswa AS c
                        ON c.id = a.id_siswa
                      LEFT JOIN tbl_prestasi_siswa AS d
                        ON c.id = d.id_siswa
                      LEFT JOIN tbl_keahlian_siswa AS e
                        ON c.id = e.id_siswa
                      ORDER BY a.id DESC");

                    while ($penilaian = mysqli_fetch_assoc($query_penilaian)):
                      $link_file_prestasi = !$penilaian['file_prestasi'] 
                        ? null 
                        : base_url_return('assets/uploads/file_prestasi_siswa/') . $penilaian['file_prestasi'];

                      $link_file_keahlian = !$penilaian['file_keahlian'] 
                        ? null 
                        : base_url_return('assets/uploads/file_keahlian_siswa/') . $penilaian['file_keahlian'];
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $penilaian['tahun'] ?></td>
                        <td><?= $penilaian['nama_siswa'] ?></td>
                        <td><?= $penilaian['nilai_prestasi'] ?></td>
                        <td><?= $penilaian['nilai_keahlian'] ?></td>
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
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_penilaian="<?= $penilaian['id_penilaian'] ?>" 
                            data-id_tahun_penilaian="<?= $penilaian['id_tahun_penilaian'] ?>"
                            data-nilai_prestasi="<?= $penilaian['nilai_prestasi'] ?>"
                            data-nilai_keahlian="<?= $penilaian['nilai_keahlian'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_swal_hapus"
                            data-id_penilaian="<?= $penilaian['id_penilaian'] ?>" 
                            data-nama_siswa="<?= $penilaian['nama_siswa'] ?>"
                            data-tahun="<?= $penilaian['tahun'] ?>">
                            <i class="fa fa-trash-can"></i>
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
    
    <!--============================= MODAL INPUT JURUSAN =============================-->
    <div class="modal fade" id="ModalInputPenilaian" tabindex="-1" role="dialog" aria-labelledby="ModalInputPenilaianTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputPenilaianTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_penilaian" name="xid_penilaian">
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_tahun_penilaian">Tahun Penilaian <span class="text-danger fw-bold">*</span></label>
                <select name="xid_tahun_penilaian" class="form-control mb-1 select2" id="xid_tahun_penilaian" required>
                  <option value="">-- Pilih --</option>
                  <?php $query_tahun_penilaian = mysqli_query($connection, "SELECT * FROM tbl_tahun_penilaian ORDER BY tahun DESC") ?>
                  <?php while ($tahun_penilaian = mysqli_fetch_assoc($query_tahun_penilaian)): ?>
          
                    <option value="<?= $tahun_penilaian['id'] ?>"><?= $tahun_penilaian['tahun'] ?></option>
          
                  <?php endwhile ?>
                </select>
                <small class="text-muted">Pilih tahun terlebih dahulu untuk melihat daftar siswa.</small>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_siswa">Siswa <span class="text-danger fw-bold">*</span></label>
                <select name="xid_siswa" class="form-control mb-1 select2" id="xid_siswa" required>
                  <option value="">-- Pilih --</option>
                </select>
                <small class="text-muted">Siswa yang muncul hanya yang belum dinilai pada tahun yang dipilih.</small>
              </div>
            
              <div class="mb-3">
                <label class="small mb-1" for="xnilai_prestasi">Nilai Prestasi <span class="text-danger fw-bold">*</span></label>
                <input type="number" name="xnilai_prestasi" min="0" max="100" class="form-control mb-1" id="xnilai_prestasi" placeholder="Enter nilai penilaian" required />
              </div>
            
              <div class="mb-3">
                <label class="small mb-1" for="xnilai_keahlian">Nilai Keahlian <span class="text-danger fw-bold">*</span></label>
                <input type="number" name="xnilai_keahlian" min="0" max="100" class="form-control mb-1" id="xnilai_keahlian" placeholder="Enter nilai penilaian" required />
              </div>

            </div>
            <div class="modal-footer">
              <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary" id="toggle_swa_submit" type="submit">Simpan</button>
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

        $('.toggle_modal_tambah').on('click', function() {
          $('#ModalInputPenilaian .modal-title').html(`<i data-feather="plus-circle" class="me-2 mt-1"></i>Tambah Penilaian`);
          $('#ModalInputPenilaian form').attr({action: 'penilaian_tambah.php', method: 'post'});

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPenilaian').modal('show');
        });


        $('.toggle_modal_ubah').on('click', function() {
          const data = $(this).data();
          
          $('#ModalInputPenilaian .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Penilaian`);
          $('#ModalInputPenilaian form').attr({action: 'penilaian_ubah.php', method: 'post'});

          $('#ModalInputPenilaian #xid_penilaian').val(data.id_penilaian);
          $('#ModalInputPenilaian #xid_tahun_penilaian').val(data.id_tahun_penilaian).trigger('change');
          $('#ModalInputPenilaian #xnilai_prestasi').val(data.nilai_prestasi);
          $('#ModalInputPenilaian #xnilai_keahlian').val(data.nilai_keahlian);

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPenilaian').modal('show');
        });


        $('#xid_tahun_penilaian').on('change', function() {
          const id_tahun_penilaian = $(this).val();

          if (!id_tahun_penilaian) {
            const siswaSelect = $('#xid_siswa');
            
            siswaSelect.html(null);
              
            initSelect2(siswaSelect, {
              data: [
                {id: '', text: 'Pilih tahun terlebih dahulu!'}
              ],
              width: '100%',
              dropdownParent: "#ModalInputPenilaian .modal-content .modal-body"
            });

            return;
          }

          $.ajax({
            url: 'get_siswa_belum_dinilai_by_tahun.php',
            type: 'POST',
            data: {
              id_tahun_penilaian: id_tahun_penilaian
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
                dropdownParent: "#ModalInputPenilaian .modal-content .modal-body"
              })
            },
            error: function(request, status, error) {
              // console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          })
        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const id_penilaian = $(this).data('id_penilaian');
          const nama_siswa   = $(this).data('nama_siswa');
          const tahun        = $(this).data('tahun');
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `Hapus data penilaian: <strong>${nama_siswa} (${tahun})?</strong>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, konfirmasi!"
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                title: "Tindakan Dikonfirmasi!",
                text: "Halaman akan di-reload untuk memproses.",
                icon: "success",
                timer: 3000
              }).then(() => {
                window.location = `penilaian_hapus.php?xid_penilaian=${id_penilaian}`;
              });
            }
          });
        });
        

        const formSubmitBtn = $('#toggle_swal_submit');
        const eventName = 'click';
        
        toggleSwalSubmit(formSubmitBtn, eventName);
        
      });
    </script>

  </body>

  </html>

<?php endif ?>