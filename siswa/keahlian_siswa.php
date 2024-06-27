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

    <meta name="description" content="Data Keahlian Siswa" />
    <meta name="author" content="" />
    <title>Keahlian Siswa - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Keahlian Siswa</h1>
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
                  <i data-feather="star" class="me-2 mt-1"></i>
                  Data Keahlian Siswa
                </div>
                <button class="btn btn-sm btn-primary toggle_modal_tambah" type="button"><i data-feather="plus-circle" class="me-2"></i>Tambah Data</button>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Siswa</th>
                      <th>Keahlian Siswa</th>
                      <th>Berkas</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_keahlian_siswa = mysqli_query($connection, 
                      "SELECT
                        d.id AS id_keahlian_siswa, d.nama_keahlian, d.file_keahlian,
                        a.id AS id_siswa, a.nisn, a.nama_siswa, a.jk, a.alamat, a.tmp_lahir, a.tgl_lahir, a.no_telp, a.email,
                        b.id AS id_kelas, b.nama_kelas,
                        c.id AS id_wali_kelas, c.nama_guru AS nama_wali_kelas,
                        f.id AS id_pengguna, f.username, f.hak_akses
                      FROM tbl_keahlian_siswa AS d
                      INNER JOIN tbl_siswa AS a
                        ON a.id = d.id_siswa
                      LEFT JOIN tbl_kelas AS b
                        ON b.id = a.id_kelas
                      LEFT JOIN tbl_guru AS c
                        ON c.id = b.id_wali_kelas
                      LEFT JOIN tbl_pengguna AS f
                        ON f.id = a.id_pengguna
                      WHERE d.id_siswa = {$_SESSION['id_siswa']}");

                    while ($keahlian_siswa = mysqli_fetch_assoc($query_keahlian_siswa)):
                      $path_file_keahlian_siswa = base_url_return('assets/uploads/file_keahlian_siswa/');
                      $file_keahlian = $keahlian_siswa['file_keahlian'] ?? null;
                      $link_file_keahlian = $path_file_keahlian_siswa . $file_keahlian;
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <?= htmlspecialchars($keahlian_siswa['nama_siswa']) ?>
                          <?= "<br><small class='text-muted'>({$keahlian_siswa['nisn']})</small>" ?>
                        </td>
                        <td><?= $keahlian_siswa['nama_keahlian'] ?></td>
                        <td>
                          <?php if ($file_keahlian): ?>

                            <a class="btn btn-xs rounded-pill bg-purple-soft text-purple" href="<?= $link_file_keahlian ?>" target="_blank">
                              <i data-feather="eye" class="me-1"></i>Preview
                            </a>

                            <a class="btn btn-xs rounded-pill bg-blue-soft text-blue" href="<?= $link_file_keahlian ?>" download>
                              <i data-feather="download-cloud" class="me-1"></i>Download
                            </a>
                            
                          <?php else: ?>
                            
                            <small class="text-muted">Tidak ada</small>
                            
                          <?php endif ?>
                        </td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_keahlian_siswa="<?= $keahlian_siswa['id_keahlian_siswa'] ?>" 
                            data-nama_keahlian="<?= $keahlian_siswa['nama_keahlian'] ?>"
                            data-file_keahlian="<?= $keahlian_siswa['file_keahlian'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_swal_hapus"
                            data-id_keahlian_siswa="<?= $keahlian_siswa['id_keahlian_siswa'] ?>" 
                            data-id_siswa="<?= $keahlian_siswa['id_siswa'] ?>" 
                            data-nama_keahlian="<?= $keahlian_siswa['nama_keahlian'] ?>">
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
    <div class="modal fade" id="ModalInputKompetensiSiswa" tabindex="-1" role="dialog" aria-labelledby="ModalInputKompetensiSiswaTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputKompetensiSiswaTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_keahlian_siswa" name="xid_keahlian_siswa">
              <input type="hidden" id="xfile_keahlian_old" name="xfile_keahlian_old">
              
              <div class="mb-3">
                <label class="small mb-1" for="xid_siswa">Siswa <span class="text-danger fw-bold">*</span></label>
                <select name="xid_siswa" class="form-control select2" id="xid_siswa" required>
                  <?php $query_siswa = mysqli_query($connection, "SELECT * FROM tbl_siswa WHERE id = {$_SESSION['id_siswa']}") ?>
                  <?php while ($keahlian_siswa = mysqli_fetch_assoc($query_siswa)): ?>
          
                    <option value="<?= $keahlian_siswa['id'] ?>" selected><?= $keahlian_siswa['nama_siswa'] ?></option>
          
                  <?php endwhile ?>
                </select>
              </div>
            
              <div class="mb-3">
                <label class="small mb-1" for="xnama_keahlian">Nama Kompetensi <span class="text-danger fw-bold">*</span></label>
                <input type="text" name="xnama_keahlian" class="form-control" id="xnama_keahlian" placeholder="Enter nama keahlian_siswa" required />
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xfile_keahlian">File <span class="text-danger fw-bold">*</span></label>
                <input type="file" name="xfile_keahlian" class="form-control dropify xfile_keahlian" id="xfile_keahlian" required
                  data-height="100"
                  data-max-file-size="200K"
                  data-allowed-file-extensions="pdf">
                <div class="d-flex flex-column">
                  <small class="text-muted mt-1" id="xfile_keahlian_help">*) File <span class="text-danger">.pdf</span> dengan maks. <span class="text-danger">200KB</span></small>
                  <small class="my-2 mb-4" id="xfile_keahlian_help2">Jangan unggah jika tidak ingin mengubah file keahlian siswa!</small>
                </div>
                <div class="small" id="xfile_keahlian_old_container">
                  File saat ini:
                  <a class="btn btn-link p-0" id="xfile_keahlian_old_preview" href="#" target="_blank">Preview</a>
                  <a class="btn btn-link p-0" id="xfile_keahlian_old_download" href="#" download>Download</a>
                </div>
              </div>

            </div>
            <div class="modal-footer">
              <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary" type="submit" id="toggle_swal_submit">Simpan</button>
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
        
        $('.dropify').dropify({
          messages: {
            'default': 'Drag and drop a file here or click',
            'replace': 'Drag and drop or click to replace',
            'remove':  'Remove',
            'error':   'Ooops, something wrong happended.'
          },
          error: {
            'fileSize': 'Ukuran berkas maksimal ({{ value }}).',
            'minWidth': 'The image width is too small ({{ value }}}px min).',
            'maxWidth': 'The image width is too big ({{ value }}}px max).',
            'minHeight': 'The image height is too small ({{ value }}}px min).',
            'maxHeight': 'The image height is too big ({{ value }}px max).',
            'imageFormat': 'The image format is not allowed ({{ value }} only).',
            'fileExtension': 'Ekstensi file hanya boleh ({{ value }}).'
          }
        });


        $('.toggle_modal_tambah').on('click', function() {
          $('#ModalInputKompetensiSiswa .modal-title').html(`<i data-feather="plus-circle" class="me-2 mt-1"></i>Tambah Keahlian Siswa`);
          $('#ModalInputKompetensiSiswa form').attr({action: 'keahlian_siswa_tambah.php', method: 'post', enctype: 'multipart/form-data'});
          
          $('#ModalInputKompetensiSiswa #xfile_keahlian').prop('required', true);
          $('#ModalInputKompetensiSiswa #xfile_keahlian_required_label').show();

          $('#ModalInputKompetensiSiswa #xfile_keahlian_old_container').hide();
          $('#ModalInputKompetensiSiswa #xfile_keahlian_help2').hide();

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputKompetensiSiswa').modal('show');
        });


        $('.toggle_modal_ubah').on('click', function() {
          const id_keahlian_siswa = $(this).data('id_keahlian_siswa');
          
          $('#ModalInputKompetensiSiswa .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Keahlian Siswa`);
          $('#ModalInputKompetensiSiswa form').attr({action: 'keahlian_siswa_ubah.php', method: 'post', enctype: 'multipart/form-data'});

          $.ajax({
            url: 'get_keahlian_siswa.php',
            type: 'POST',
            data: {
              id_keahlian_siswa: id_keahlian_siswa
            },
            dataType: 'JSON',
            success: function(data) {
              $('#ModalInputKompetensiSiswa #xid_keahlian_siswa').val(data.id_keahlian_siswa);
              $('#ModalInputKompetensiSiswa #xid_siswa').val(data.id_siswa).trigger('change');
              $('#ModalInputKompetensiSiswa #xnama_keahlian').val(data.nama_keahlian);
              
              const file_keahlian_old_path = "<?= base_url_return('assets/uploads/file_keahlian_siswa/') ?>";
              const file_keahlian_old = file_keahlian_old_path + data.file_keahlian;
              
              $('#ModalInputKompetensiSiswa #xfile_keahlian_old').val(data.file_keahlian);
              $('#ModalInputKompetensiSiswa #xfile_keahlian_old_preview').attr('href', file_keahlian_old);
              $('#ModalInputKompetensiSiswa #xfile_keahlian_old_download').attr('href', file_keahlian_old);
              $('#ModalInputKompetensiSiswa #xfile_keahlian_help2').show();
              
              $('#ModalInputKompetensiSiswa #xfile_keahlian').prop('required', false);
              $('#ModalInputKompetensiSiswa #xfile_keahlian_required_label').hide();
              
              !data.file_keahlian
                ? $('#ModalInputKompetensiSiswa #xfile_keahlian_old_container').hide()
                : $('#ModalInputKompetensiSiswa #xfile_keahlian_old_container').show();
    
              // Re-init all feather icons
              feather.replace();
              
              $('#ModalInputKompetensiSiswa').modal('show');
            },
            error: function(requrest, statut, error) {
              // console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          });
        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const id_keahlian_siswa = $(this).data('id_keahlian_siswa');
          const nama_keahlian     = $(this).data('nama_keahlian');
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `Hapus data siswa: <strong>${nama_keahlian}?</strong>`,
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
                window.location = `keahlian_siswa_hapus.php?xid_keahlian_siswa=${id_keahlian_siswa}`;
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