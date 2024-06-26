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

    <meta name="description" content="Data Jurusan" />
    <meta name="author" content="" />
    <title>Jurusan - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Jurusan</h1>
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
                  <i data-feather="book" class="me-2 mt-1"></i>
                  Data Jurusan
                </div>
                <button class="btn btn-sm btn-primary toggle_modal_tambah" type="button"><i data-feather="plus-circle" class="me-2"></i>Tambah Data</button>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Jurusan</th>
                      <th>Asal Pendidikan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_jurusan = mysqli_query($connection, 
                      "SELECT a.id AS id_jurusan_pendidikan, a.nama_jurusan, b.id AS id_pendidikan, b.nama_pendidikan
                      FROM tbl_jurusan_pendidikan a
                      JOIN tbl_pendidikan b
                        ON a.id_pendidikan = b.id
                      ORDER BY a.id DESC");

                    while ($jurusan = mysqli_fetch_assoc($query_jurusan)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $jurusan['nama_jurusan'] ?></td>
                        <td><?= $jurusan['nama_pendidikan'] ?></td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_jurusan_pendidikan="<?= $jurusan['id_jurusan_pendidikan'] ?>" 
                            data-id_pendidikan="<?= $jurusan['id_pendidikan'] ?>" 
                            data-nama_jurusan="<?= $jurusan['nama_jurusan'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_swal_hapus"
                            data-id_jurusan_pendidikan="<?= $jurusan['id_jurusan_pendidikan'] ?>"
                            data-id_pendidikan="<?= $jurusan['id_pendidikan'] ?>" 
                            data-nama_jurusan="<?= $jurusan['nama_jurusan'] ?>">
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
    <div class="modal fade" id="ModalInputJurusan" role="dialog" aria-labelledby="ModalInputJurusanTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputJurusanTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_jurusan_pendidikan" name="xid_jurusan_pendidikan">
              
              <div class="mb-3" bis_skin_checked="1">
                <label class="small mb-1" for="xid_pendidikan">Pendidikan</label>
                <select name="xid_pendidikan" class="form-control select2" id="xid_pendidikan" required>
                  <option value="">-- Pilih --</option>
                  <?php $query_pendidikan = mysqli_query($connection, "SELECT * FROM tbl_pendidikan WHERE nama_pendidikan NOT IN ('SD', 'SMP', 'sd', 'smp', 'tidak_sekolah')") ?>
                  <?php while ($pendidikan = mysqli_fetch_assoc($query_pendidikan)): ?>

                    <option value="<?= $pendidikan['id'] ?>"><?= $pendidikan['nama_pendidikan'] ?></option>

                  <?php endwhile ?>
                  <?php mysqli_close($connection) ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xnama_jurusan">Jurusan</label>
                <input type="text" name="xnama_jurusan" class="form-control" id="xnama_jurusan" placeholder="Enter jurusan" required />
              </div>
              
            </div>
            <div class="modal-footer">
              <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary" type="submit">Simpan</button>
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
          $('#ModalInputJurusan .modal-title').html(`<i data-feather="plus-circle" class="me-2 mt-1"></i>Tambah Jurusan`);
          $('#ModalInputJurusan form').attr({action: 'jurusan_pendidikan_tambah.php', method: 'post'});

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputJurusan').modal('show');
        });


        $('.toggle_modal_ubah').on('click', function() {
          const data = $(this).data();
          
          $('#ModalInputJurusan .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Jurusan`);
          $('#ModalInputJurusan form').attr({action: 'jurusan_pendidikan_ubah.php', method: 'post'});

          $('#ModalInputJurusan #xid_jurusan_pendidikan').val(data.id_jurusan_pendidikan);
          $('#ModalInputJurusan #xid_pendidikan').val(data.id_pendidikan).trigger('change');
          $('#ModalInputJurusan #xnama_jurusan').val(data.nama_jurusan);

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputJurusan').modal('show');
        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const id_jurusan_pendidikan   = $(this).data('id_jurusan_pendidikan');
          const nama_jurusan = $(this).data('nama_jurusan');
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `Hapus data jurusan: <strong>${nama_jurusan}?</strong>`,
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
                window.location = `jurusan_pendidikan_hapus.php?xid_jurusan_pendidikan=${id_jurusan_pendidikan}`;
              });
            }
          });
        });
        
      });
    </script>

  </body>

  </html>

<?php endif ?>