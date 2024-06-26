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

    <meta name="description" content="Data Pendidikan" />
    <meta name="author" content="" />
    <title>Pendidikan - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Pendidikan</h1>
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
                  Data Pendidikan
                </div>
                <button class="btn btn-sm btn-primary toggle_modal_tambah" type="button"><i data-feather="plus-circle" class="me-2"></i>Tambah Data</button>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Pendidikan</th>
                      <th>Daftar Jurusan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    $query_pendidikan = mysqli_query($connection, 
                      "SELECT 
                        a.id AS id_pendidikan, a.nama_pendidikan, IFNULL(b.jml_jurusan, 0) AS jml_jurusan
                      FROM tbl_pendidikan AS a
                      LEFT JOIN (SELECT id_pendidikan, COUNT(id) jml_jurusan FROM `tbl_jurusan_pendidikan` GROUP BY id_pendidikan) AS b
                        ON a.id = b.id_pendidikan
                      ORDER BY a.id DESC");

                    while ($pendidikan = mysqli_fetch_assoc($query_pendidikan)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $pendidikan['nama_pendidikan'] ?></td>
                        <td>
                          <button type="button" class="btn btn-xs rounded-pill btn-outline-primary toggle_daftar_jurusan" data-id_pendidikan="<?= $pendidikan['id_pendidikan'] ?>">
                            <i data-feather="list" class="me-1"></i>
                            Daftar Jurusan
                            <span class="btn btn-sm rounded-pill btn-outline-primary py-0 px-2 ms-1"><?= $pendidikan['jml_jurusan'] ?></button>
                          </button>
                        </td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_pendidikan="<?= $pendidikan['id_pendidikan'] ?>" 
                            data-nama_pendidikan="<?= $pendidikan['nama_pendidikan'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_swal_hapus"
                            data-id_pendidikan="<?= $pendidikan['id_pendidikan'] ?>" 
                            data-nama_pendidikan="<?= $pendidikan['nama_pendidikan'] ?>">
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
    
    <!--============================= MODAL DAFTAR JURUSAN =============================-->
    <div class="modal fade" id="ModalDaftarJurusan" tabindex="-1" role="dialog" aria-labelledby="ModalDaftarJurusanTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalDaftarJurusanTitle"><i data-feather="book" class="me-2 mt-1"></i>Daftar Jurusan</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <table class="table table-striped" id="tabel_daftar_jurusan">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Pendidikan</th>
                    <th>Jurusan</th>
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
    <!--/.modal-daftar-jurusan -->
    
    <!--============================= MODAL INPUT JURUSAN =============================-->
    <div class="modal fade" id="ModalInputPendidikan" tabindex="-1" role="dialog" aria-labelledby="ModalInputPendidikanTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputPendidikanTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_pendidikan" name="xid_pendidikan">
            
              <div class="mb-3">
                <label class="small mb-1" for="xnama_pendidikan">Pendidikan</label>
                <input type="text" name="xnama_pendidikan" class="form-control" id="xnama_pendidikan" placeholder="Enter pendidikan" required />
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
        let tableDaftarJurusan = document.getElementById("tabel_daftar_jurusan");

        if (tableDaftarJurusan) {
          var datatableDaftarJurusan = new simpleDatatables.DataTable(tableDaftarJurusan, {
            fixedHeader: true,
            pageLength: 5,
            lengthMenu: [
              [3, 5, 10, 25, 50, 100],
              [3, 5, 10, 25, 50, 100],
            ]
          });
        }
        
        $('.toggle_modal_tambah').on('click', function() {
          $('#ModalInputPendidikan .modal-title').html(`<i data-feather="plus-circle" class="me-2 mt-1"></i>Tambah Pendidikan`);
          $('#ModalInputPendidikan form').attr({action: 'pendidikan_tambah.php', method: 'post'});

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPendidikan').modal('show');
        });


        $('.toggle_modal_ubah').on('click', function() {
          const id_pendidikan   = $(this).data('id_pendidikan');
          const nama_pendidikan = $(this).data('nama_pendidikan');
          
          $('#ModalInputPendidikan .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Pendidikan`);
          $('#ModalInputPendidikan form').attr({action: 'pendidikan_ubah.php', method: 'post'});

          $('#ModalInputPendidikan #xid_pendidikan').val(id_pendidikan);
          $('#ModalInputPendidikan #xnama_pendidikan').val(nama_pendidikan);

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPendidikan').modal('show');
        });

        
        $('.toggle_daftar_jurusan').on('click', function() {
          const id_pendidikan = $(this).data('id_pendidikan');
        
          $.ajax({
            url: 'get_jurusan_pendidikan.php',
            method: 'POST',
            dataType: 'JSON',
            data: {
              'id_pendidikan': id_pendidikan
            },
            success: function(data) {
              // add datatables row
              let i = 1;
              let rowsData = [];
              
              for (key in data) {
                rowsData.push([i++, data[key]['nama_pendidikan'], data[key]['nama_jurusan']]);
              }

              datatableDaftarJurusan.destroy();
              datatableDaftarJurusan.init();
              datatableDaftarJurusan.insert({
                data: rowsData
              });
              
              $('#ModalDaftarJurusan').modal('show');
            },
            error: function(request, status, error) {
              // console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          })
        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const id_pendidikan   = $(this).data('id_pendidikan');
          const nama_pendidikan = $(this).data('nama_pendidikan');
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `Hapus data pendidikan: <strong>${nama_pendidikan}?</strong>`,
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
                window.location = `pendidikan_hapus.php?xid_pendidikan=${id_pendidikan}`;
              });
            }
          });
        });
        
      });
    </script>

  </body>

  </html>

<?php endif ?>