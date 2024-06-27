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
                <input class="form-control ps-0 pointer" id="litepickerRangePlugin" value="Tanggal: <?= date('d M Y') ?>" />
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
                      <th>Keterangan</th>
                      <th>Aksi</th>
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
                          <?php if (!$pengumuman['keterangan_seleksi']): ?>

                            <small class="fw-bold text-muted">Belum Di-input</small>
                            
                          <?php elseif ($pengumuman['keterangan_seleksi'] === 'lolos'): ?>

                            <small class="fw-bold text-success">Lolos</small>
                              
                          <?php else: ?>
                            
                            <small class="fw-bold text-danger">Tidak Lolos</small>
                            
                          <?php endif ?>
                        </td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_tooltip toggle_modal_tambah_or_ubah" title="Input Keterangan"
                            data-id_seleksi="<?= $pengumuman['id_seleksi'] ?>"
                            data-tahun="<?= $pengumuman['tahun'] ?>"
                            data-nama_siswa="<?= $pengumuman['nama_siswa'] ?>"
                            data-nama_posisi="<?= $pengumuman['nama_posisi'] ?>"
                            data-nama_perusahaan="<?= $pengumuman['nama_perusahaan'] ?>"
                            data-keterangan_seleksi="<?= $pengumuman['keterangan_seleksi'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>

                          <?php if (!$pengumuman['keterangan_seleksi']): ?>
                          
                            <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_tooltip disabled" title="Reset Pengumuman">
                              <i class="fa fa-repeat"></i>
                            </button>

                          <?php else: ?>

                            <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_tooltip toggle_swal_hapus" title="Reset Pengumuman"
                              data-id_pengumuman="<?= $pengumuman['id_pengumuman'] ?>" 
                              data-nama_siswa="<?= $pengumuman['nama_siswa'] ?>"
                              data-tahun="<?= $pengumuman['tahun'] ?>">
                              <i class="fa fa-repeat"></i>
                            </button>
                          
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
    
    <!--============================= MODAL INPUT JURUSAN =============================-->
    <div class="modal fade" id="ModalInputPengumuman" tabindex="-1" role="dialog" aria-labelledby="ModalInputPengumumanTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputPengumumanTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" id="xid_seleksi" name="xid_seleksi">
              
              <div class="mb-3">
                <label class="small mb-1" for="xtahun">Tahun Penilaian</label>
                <input type="number" name="xtahun" min="0" max="100" class="form-control mb-1" id="xtahun" placeholder="Enter tahun seleksi" disabled />
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xnama_siswa">Siswa</label>
                <input type="text" name="xnama_siswa" class="form-control mb-1" id="xnama_siswa" placeholder="Enter siswa" disabled />
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xnama_posisi">Posisi Penempatan</label>
                <input type="text" name="xnama_posisi" class="form-control mb-1" id="xnama_posisi" placeholder="Enter posisi penempatan" disabled />
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xnama_perusahaan">Perusahaan</label>
                <input type="text" name="xnama_perusahaan" class="form-control mb-1" id="xnama_perusahaan" placeholder="Enter nama perusahaan" disabled />
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xketerangan_seleksi">Keterangan <span class="text-danger fw-bold">*</span></label>
                <select name="xketerangan_seleksi" class="form-control mb-1 select2" id="xketerangan_seleksi" required>
                  <option value="">-- Pilih --</option>
                  <option value="lolos">Lolos</option>
                  <option value="tidak_lolos">Tidak Lolos</option>
                </select>
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


        $('.toggle_modal_tambah_or_ubah').on('click', function() {
          const data = $(this).data();
          
          $('#ModalInputPengumuman .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Pengumuman`);
          $('#ModalInputPengumuman form').attr({action: 'pengumuman_tambah_or_ubah.php', method: 'post'});

          $('#ModalInputPengumuman #xid_seleksi').val(data.id_seleksi);
          $('#ModalInputPengumuman #xtahun').val(data.tahun);
          $('#ModalInputPengumuman #xnama_siswa').val(data.nama_siswa);
          $('#ModalInputPengumuman #xnama_posisi').val(data.nama_posisi);
          $('#ModalInputPengumuman #xnama_perusahaan').val(data.nama_perusahaan);
          $('#ModalInputPengumuman #xketerangan_seleksi').val(data.keterangan_seleksi).trigger('change');

          // Re-init all feather icons
          feather.replace();
          
          $('#ModalInputPengumuman').modal('show');
        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const id_pengumuman = $(this).data('id_pengumuman');
          const nama_siswa    = $(this).data('nama_siswa');
          const tahun         = $(this).data('tahun');
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `<div class="mb-1">Reset keterangan pengumuman:</div><strong>${nama_siswa} (${tahun})?</strong>`,
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
                window.location = `pengumuman_hapus.php?xid_pengumuman=${id_pengumuman}`;
              });
            }
          });
        });
        
      });
    </script>

  </body>

  </html>

<?php endif ?>