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

    <meta name="description" content="Data Posisi Penempatan" />
    <meta name="author" content="" />
    <title>Posisi Penempatan - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Posisi Penempatan</h1>
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
                  <i data-feather="briefcase" class="me-2 mt-1"></i>
                  Data Posisi Penempatan
                </div>
                <button class="btn btn-sm btn-primary toggle_modal_tambah" type="button"><i data-feather="plus-circle" class="me-2"></i>Tambah Data</button>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Posisi</th>
                      <th>Perusahaan</th>
                      <th>Jenis Perusahaan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_posisi_penempatan = mysqli_query($connection, 
                      "SELECT
                        a.id AS id_posisi_penempatan, a.nama_posisi,
                        b.id AS id_perusahaan, b.nama_perusahaan, b.alamat_perusahaan,
                        c.id AS id_jenis_perusahaan, c.nama_jenis
                      FROM tbl_posisi_penempatan AS a
                      JOIN tbl_perusahaan AS b
                        ON b.id = a.id_perusahaan
                      LEFT JOIN tbl_jenis_perusahaan AS c
                        ON c.id = b.id_jenis_perusahaan
                      ORDER BY a.id DESC");

                    while ($posisi_penempatan = mysqli_fetch_assoc($query_posisi_penempatan)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $posisi_penempatan['nama_posisi'] ?></td>
                        <td><?= $posisi_penempatan['nama_perusahaan'] ?></td>
                        <td><?= $posisi_penempatan['nama_jenis'] ?></td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_posisi_penempatan="<?= $posisi_penempatan['id_posisi_penempatan'] ?>" 
                            data-id_perusahaan="<?= $posisi_penempatan['id_perusahaan'] ?>" 
                            data-nama_posisi="<?= $posisi_penempatan['nama_posisi'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_swal_hapus"
                            data-id_posisi_penempatan="<?= $posisi_penempatan['id_posisi_penempatan'] ?>" 
                            data-nama_posisi="<?= $posisi_penempatan['nama_posisi'] ?>">
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
    <div class="modal fade" id="ModalInputPosisiPenempatan" tabindex="-1" role="dialog" aria-labelledby="ModalInputPosisiTitlePenempatan" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputPosisiTitlePenempatan">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_posisi_penempatan" name="xid_posisi_penempatan">
            
              <div class="mb-3">
                <label class="small mb-1" for="xnama_posisi">Posisi Penempatan <span class="fw-bold text-danger">*</span></label>
                <input type="text" name="xnama_posisi" class="form-control" id="xnama_posisi" placeholder="Enter posisi_penempatan" required />
              </div>
              
              <div class="mb-3" bis_skin_checked="1">
                <label class="small mb-1" for="xid_perusahaan">Perusahaan <span class="fw-bold text-danger">*</span></label>
                <select name="xid_perusahaan" class="form-control select2" id="xid_perusahaan" required>
                  <option value="">-- Pilih --</option>
                  <?php $query_perusahaan = mysqli_query($connection, "SELECT * FROM tbl_perusahaan ORDER BY nama_perusahaan ASC") ?>
                  <?php while ($perusahaan = mysqli_fetch_assoc($query_perusahaan)): ?>

                    <option value="<?= $perusahaan['id'] ?>"><?= $perusahaan['nama_perusahaan'] ?></option>

                  <?php endwhile ?>
                </select>
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
          $('#ModalInputPosisiPenempatan .modal-title').html(`<i data-feather="plus-circle" class="me-2 mt-1"></i>Tambah Posisi Penempatan`);
          $('#ModalInputPosisiPenempatan form').attr({action: 'posisi_penempatan_tambah.php', method: 'post'});

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPosisiPenempatan').modal('show');
        });


        $('.toggle_modal_ubah').on('click', function() {
          const id_posisi_penempatan   = $(this).data('id_posisi_penempatan');
          const id_perusahaan   = $(this).data('id_perusahaan');
          const nama_posisi = $(this).data('nama_posisi');
          
          $('#ModalInputPosisiPenempatan .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Posisi Penempatan`);
          $('#ModalInputPosisiPenempatan form').attr({action: 'posisi_penempatan_ubah.php', method: 'post'});

          $('#ModalInputPosisiPenempatan #xid_posisi_penempatan').val(id_posisi_penempatan);
          $('#ModalInputPosisiPenempatan #xid_perusahaan').val(id_perusahaan).trigger('change');
          $('#ModalInputPosisiPenempatan #xnama_posisi').val(nama_posisi);

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPosisiPenempatan').modal('show');
        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const id_posisi_penempatan   = $(this).data('id_posisi_penempatan');
          const nama_posisi = $(this).data('nama_posisi');
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `Hapus data posisi_penempatan: <strong>${nama_posisi}?</strong>`,
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
                window.location = `posisi_penempatan_hapus.php?xid_posisi_penempatan=${id_posisi_penempatan}`;
              });
            }
          });
        });
        
      });
    </script>

  </body>

  </html>

<?php endif ?>