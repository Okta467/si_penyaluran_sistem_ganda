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

    <meta name="description" content="Data Siswa" />
    <meta name="author" content="" />
    <title>Siswa - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Siswa</h1>
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
                  <i data-feather="user" class="me-2 mt-1"></i>
                  Data Siswa
                </div>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama/NISN</th>
                      <th class="text-center">JK</th>
                      <th>Kelas</th>
                      <th>No. Telepon</th>
                      <th>Email</th>
                      <th>Ubah Profil <br>/ Detail Siswa</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $no = 1;
                    $query_siswa = mysqli_query($connection, 
                      "SELECT
                        a.id AS id_siswa, a.nisn, a.nama_siswa, a.jk, a.alamat, a.tmp_lahir, a.tgl_lahir, a.no_telp, a.email,
                        b.id AS id_kelas, b.nama_kelas,
                        c.id AS id_wali_kelas, c.nama_guru AS nama_wali_kelas,
                        f.id AS id_pengguna, f.username, f.hak_akses
                      FROM tbl_siswa AS a
                      LEFT JOIN tbl_kelas AS b
                        ON b.id = a.id_kelas
                      LEFT JOIN tbl_guru AS c
                        ON c.id = b.id_wali_kelas
                      LEFT JOIN tbl_pengguna AS f
                        ON f.id = a.id_pengguna
                      WHERE a.id = {$_SESSION['id_siswa']}");

                    while ($siswa = mysqli_fetch_assoc($query_siswa)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <?= htmlspecialchars($siswa['nama_siswa']) ?>
                          <?= "<br><small class='text-muted'>({$siswa['nisn']})</small>" ?>
                        </td>
                        <td><div class="text-center"><?= ucfirst($siswa['jk']) ?></div></td>
                        <td><?= $siswa['nama_kelas'] ?></td>
                        <td><?= $siswa['no_telp'] ?></td>
                        <td><?= $siswa['email'] ?></td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah" type="button"
                            data-id_siswa="<?= $siswa['id_siswa'] ?>" 
                            data-nama_siswa="<?= $siswa['nama_siswa'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_detail_siswa" type="button"
                            data-id_siswa="<?= $siswa['id_siswa'] ?>"
                            data-nama_siswa="<?= $siswa['nama_siswa'] ?>"
                            data-username="<?= $siswa['username'] ?>"
                            data-hak_akses="<?= $siswa['hak_akses'] ?>"
                            data-alamat="<?= $siswa['alamat'] ?>"
                            data-tmp_lahir="<?= $siswa['tmp_lahir'] ?>"
                            data-tgl_lahir="<?= $siswa['tgl_lahir'] ?>">
                            <i class="fa fa-list"></i>
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
    
    <!--============================= MODAL DETAIL SISWA =============================-->
    <div class="modal fade" id="ModalDetailSiswa" tabindex="-1" role="dialog" aria-labelledby="ModalDetailSiswa" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width: 600px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i data-feather="info" class="me-2"></i>Detail Siswa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            
            <div class="p-4">
              <h4><i data-feather="star" class="me-2"></i>Siswa</h4>
              <p class="mb-0 xnama_siswa"></p>
            </div>
            
            <div class="p-4">
              <h4><i data-feather="key" class="me-2"></i>Username</h4>
              <p class="mb-0 xusername"></p>
            </div>
            
            <div class="p-4">
              <h4><i data-feather="key" class="me-2"></i>Hak Akses</h4>
              <p class="mb-0 xhak_akses"></p>
            </div>
            
            <div class="p-4">
              <h4><i data-feather="home" class="me-2"></i>Alamat</h4>
              <p class="mb-0 xalamat"></p>
            </div>
            
            <div class="p-4">
              <h4><i data-feather="gift" class="me-2"></i>Tempat, Tanggal Lahir</h4>
              <p class="mb-0 xtmp_tgl_lahir"></p>
            </div>
          
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
    <!--/.modal-detail-siswa -->
      
    <!--============================= MODAL INPUT SISWA =============================-->
    <div class="modal fade" id="ModalInputSiswa" tabindex="-1" role="dialog" aria-labelledby="ModalInputSiswaTitle" aria-hidden="true" data-focus="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputSiswaTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
    
              <input type="hidden" name="xid_siswa" id="xid_siswa" required>
              <input type="hidden" name="xid_pengguna" id="xid_pengguna" required>
    
              <div class="mb-3">
                <label class="small mb-1" for="xnisn">NISN (10 Digit)</label>
                <input class="form-control xnisn" id="xnisn" type="text" name="xnisn" minlength="10" maxlength="10" placeholder="Enter nisn" disabled>
              </div>
    
              <div class="mb-3">
                <label class="small mb-1" for="xnama_siswa">Nama Siswa</label>
                <input class="form-control" id="xnama_siswa" type="text" name="xnama_siswa" placeholder="Enter nama Siswa" disabled>
              </div>
    
              <div class="mb-3">
                <label class="small mb-1" for="xid_kelas">Kelas</label>
                <select name="xid_kelas" class="form-control select2" id="xid_kelas" disabled>
                  <option value="">-- Pilih --</option>
                  <?php $query_kelas = mysqli_query($connection, "SELECT * FROM tbl_kelas ORDER BY nama_kelas ASC") ?>
                  <?php while ($kelas = mysqli_fetch_assoc($query_kelas)): ?>

                    <option value="<?= $kelas['id'] ?>"><?= $kelas['nama_kelas'] ?></option>

                  <?php endwhile ?>
                </select>
              </div>
    
              <div class="mb-3">
                <label class="small mb-1" for="xpassword">Password <span class="text-danger fw-bold">*</span></label>
                <div class="input-group input-group-joined mb-1">
                  <input class="form-control mb-1" id="xpassword" type="password" name="xpassword" placeholder="Enter password" autocomplete="new-password" required>
                  <button class="input-group-text" id="xpassword_toggle" type="button"><i class="fa-regular fa-eye"></i></button>
                </div>
                <small class="text-muted" id="xpassword_help"></small>
              </div>
    
    
              <div class="row gx-3">
    
                <div class="col-md-6">
                  <div class="form-check form-check-solid mb-3">
                    <input class="form-check-input" id="xjk_l" type="radio" name="xjk" value="l" checked disabled>
                    <label class="form-check-label" for="xjk_l">Laki-laki</label>
                  </div>
                </div>
    
                <div class="col-md-6">
                  <div class="form-check form-check-solid mb-3">
                    <input class="form-check-input" id="xjk_p" type="radio" name="xjk" value="p" disabled>
                    <label class="form-check-label" for="xjk_p">Perempuan</label>
                  </div>
                </div>
    
              </div>
    
    
              <div class="mb-3">
                <label class="small mb-1" for="xalamat">Alamat</label>
                <input class="form-control" id="xalamat" type="text" name="xalamat" placeholder="Enter alamat" required>
              </div>
    
    
              <div class="row gx-3">
    
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="small mb-1" for="xtmp_lahir">Tempat Lahir</label>
                    <input class="form-control" id="xtmp_lahir" type="text" name="xtmp_lahir" placeholder="Enter tempat lahir" required>
                  </div>
                </div>
    
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="small mb-1" for="xtgl_lahir">Tanggal Lahir</label>
                    <input class="form-control" id="xtgl_lahir" type="date" name="xtgl_lahir" required>
                  </div>
                </div>
    
              </div>
    
              
              <div class="mb-3">
                <label class="small mb-1" for="xno_telp">No. Telp</label>
                <input class="form-control" id="xno_telp" type="text" name="xno_telp" min="0" maxlength="16" placeholder="Enter no telp" required>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xemail">Email <span class="text-danger fw-bold">*</span></label>
                <input class="form-control" id="xemail" type="email" name="xemail" aria-describedby="emailHelp" placeholder="Enter email address" required>
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
    <!--/.modal-input-siswa -->
    
    <?php include '_partials/script.php' ?>
    <?php include '../helpers/sweetalert2_notify.php' ?>

    <!-- PAGE SCRIPT -->
    <script>
        let password = document.getElementById('xpassword');
        let passwordConfirm = document.getElementById('xpassword_confirm');
    
        let passwordToggle = document.getElementById('xpassword_toggle');
        let passwordConfirmToggle = document.getElementById('xpassword_confirm_toggle');
    
        let passwordHelp = document.getElementById('xpassword_help');
        let passwordConfirmHelp = document.getElementById('xpassword_confirm_help');
        
        passwordToggle.addEventListener('click', function() {
          initTogglePassword(password, passwordToggle);
        });
    </script>

    <script>
      $(document).ready(function() {

        const selectKelas = $('#xid_kelas');
        initSelect2(selectKelas, {
          width: '100%',
          dropdownParent: "#ModalInputSiswa .modal-content .modal-body"
        });

        $('.toggle_modal_detail_siswa').tooltip({
          title: 'Alamat, Hak Akses Akun, dan Tempat, Tanggal Lahir',
          delay: {
            show: 1000,
            hide: 100
          }
        });

        
        $('.toggle_modal_ubah').on('click', function() {
          const id_siswa   = $(this).data('id_siswa');
          const nama_siswa = $(this).data('nama_siswa');
          
          $('#ModalInputSiswa .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Siswa`);
          $('#ModalInputSiswa form').attr({action: 'profil_ubah.php', method: 'post'});
          $('#ModalInputSiswa #xpassword').attr('required', false);
          $('#ModalInputSiswa #xpassword_help').html('Kosongkan jika tidak ingin ubah password.');
        
          $.ajax({
            url: 'get_siswa.php',
            method: 'POST',
            dataType: 'JSON',
            data: {
              'id_siswa': id_siswa
            },
            success: function(data) {
              $('#ModalInputSiswa #xid_siswa').val(data[0].id_siswa);
              $('#ModalInputSiswa #xid_pengguna').val(data[0].id_pengguna);
              $('#ModalInputSiswa #xnisn').val(data[0].nisn);
              $('#ModalInputSiswa #xnama_siswa').val(data[0].nama_siswa);
              $('#ModalInputSiswa #xid_kelas').val(data[0].id_kelas).trigger('change');
              $(`#ModalInputSiswa input[name="xjk"][value="${data[0].jk}"]`).prop('checked', true)
              $('#ModalInputSiswa #xalamat').val(data[0].alamat);
              $('#ModalInputSiswa #xtmp_lahir').val(data[0].tmp_lahir);
              $('#ModalInputSiswa #xtgl_lahir').val(data[0].tgl_lahir);
              $('#ModalInputSiswa #xno_telp').val(data[0].no_telp);
              $('#ModalInputSiswa #xemail').val(data[0].email);
              
              $('#ModalInputSiswa').modal('show');
            },
            error: function(request, status, error) {
              // console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          })
          
          $('#ModalInputSiswa #xid_siswa').val(id_siswa);
          $('#ModalInputSiswa #xnama_siswa').val(nama_siswa);
        
          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputSiswa').modal('show');
        });

        
        $('.toggle_modal_detail_siswa').on('click', function() {
          const data = $(this).data();
        
          $('#ModalDetailSiswa .xnama_siswa').html(data.nama_siswa);
          $('#ModalDetailSiswa .xusername').html(data.username || 'Tidak ada (akun belum dibuat)');
          $('#ModalDetailSiswa .xhak_akses').html(data.hak_akses || 'Tidak ada (akun belum dibuat)');
          $('#ModalDetailSiswa .xalamat').html(data.alamat);
          $('#ModalDetailSiswa .xtmp_tgl_lahir').html(`${data.tmp_lahir}, ${moment(data.tgl_lahir).format("DD MMMM YYYY")}`);
        
          $('#ModalDetailSiswa').modal('show');
        });
        

        const formSubmitBtn = $('#toggle_swal_submit');
        const eventName = 'click';
        
        toggleSwalSubmit(formSubmitBtn, eventName);
        
      });
    </script>

  </body>

  </html>

<?php endif ?>