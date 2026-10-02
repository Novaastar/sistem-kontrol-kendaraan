<?php
require_once '../config/koneksi.php';require_once '../config/auth.php';require_admin();
$tanggal=$_GET['tanggal']??date('Y-m-d');
$stmt=mysqli_prepare($conn,"SELECT p.*,k.merk,k.nomor_polisi,d.nama driver,pr.id perjalanan_id,pr.jam_ambil,pr.km_awal,pr.jam_pulang,pr.km_akhir,pr.keterangan FROM penugasan p JOIN kendaraan k ON k.id=p.kendaraan_id JOIN drivers d ON d.id=p.driver_id LEFT JOIN perjalanan pr ON pr.penugasan_id=p.id WHERE p.tanggal=? ORDER BY p.id");mysqli_stmt_bind_param($stmt,'s',$tanggal);mysqli_stmt_execute($stmt);$rows=mysqli_stmt_get_result($stmt);
$page_title='Laporan Harian';require '../partials/header.php';?>
<div class="main-wrap">
<?php require '../partials/sidebar.php';?>
<main class="content">
<?php require '../partials/navbar.php';?>
<div class="content-body">
<div class="d-flex justify-content-between align-items-end mb-4 no-print">
  <div>
    <h3 class="fw-bold mb-1">Laporan Harian</h3>
    <p class="text-muted mb-0">Rekap kontrol kendaraan per tanggal</p>
  </div>
  <form class="d-flex gap-2">
    <input class="form-control" type="date" name="tanggal" value="<?=e($tanggal)?>">
    <button class="btn btn-bj">Tampilkan</button>
  </form>
</div>
<div class="card card-stat mb-4">
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover" id="tbl-laporan">
      <thead>
        <tr>
          <th class="ps-4">No</th>
          <th>Kendaraan</th>
          <th>Plat Nomor</th>
          <th>Driver</th>
          <th>Jam Ambil</th>
          <th>KM Awal</th>
          <th>Jam Pulang</th>
          <th>KM Akhir</th>
          <th>Keterangan</th>
          <th class="pe-4 no-print">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $n=1; while($r=mysqli_fetch_assoc($rows)):?>
        <tr id="row-<?=$r['id']?>">
          <td class="ps-4 text-muted align-middle"><?=$n++?></td>
          <td class="fw-bold align-middle"><?=e($r['merk'])?></td>
          <td class="align-middle"><span class="badge bg-light border text-dark fw-bold"><?=e($r['nomor_polisi'])?></span></td>
          <td class="align-middle"><?=e($r['driver'])?></td>
          <td class="align-middle cell-jam-ambil"><?=e($r['jam_ambil']?:'-')?></td>
          <td class="align-middle cell-km-awal"><?=e($r['km_awal']?:'-')?></td>
          <td class="align-middle cell-jam-pulang"><?=e($r['jam_pulang']?:'-')?></td>
          <td class="align-middle cell-km-akhir"><?=e($r['km_akhir']?:'-')?></td>
          <td class="align-middle cell-keterangan text-muted small"><?=e($r['keterangan']?:'-')?></td>
          <td class="pe-4 align-middle no-print">
            <button class="btn btn-sm btn-light btn-edit-perjalanan"
              title="Edit data perjalanan"
              data-penugasan-id="<?=$r['id']?>"
              data-label="<?=e($r['merk']).' - '.e($r['nomor_polisi']).' ('.e($r['driver']).')'?>"
              data-jam-ambil="<?=e($r['jam_ambil']??'')?>"
              data-km-awal="<?=e($r['km_awal']??'')?>"
              data-jam-pulang="<?=e($r['jam_pulang']??'')?>"
              data-km-akhir="<?=e($r['km_akhir']??'')?>"
              data-keterangan="<?=e($r['keterangan']??'')?>">
              <i class="bi bi-pencil"></i>
            </button>
          </td>
        </tr>
      <?php endwhile;?>
      <?php if(mysqli_num_rows($rows)==0):?>
        <tr><td colspan="10" class="text-center py-4 text-muted">Tidak ada data penugasan pada tanggal ini.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="d-flex gap-2 no-print">
  <a target="_blank" class="btn btn-success" href="cetak.php?tanggal=<?=e($tanggal)?>"><i class="bi bi-file-pdf"></i> Cetak Laporan PDF</a>
  <a class="btn btn-outline-secondary" href="#"><i class="bi bi-file-excel"></i> Export Excel</a>
</div>
</div>
</main>
</div>

<!-- ═══════════════════════════════════════════
     MODAL EDIT PERJALANAN
═══════════════════════════════════════════ -->
<div class="modal fade" id="modalEditPerjalanan" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-1">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="modalEditLabel">Edit Data Perjalanan</h5>
          <p class="text-muted small mb-0" id="modal-sublabel"></p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="modal-alert" class="alert d-none mb-3 py-2 small"></div>
        <form id="form-edit-perjalanan" autocomplete="off">
          <input type="hidden" id="edit-penugasan-id" name="penugasan_id">
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Jam Ambil</label>
              <input type="time" class="form-control" id="edit-jam-ambil" name="jam_ambil">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">KM Awal</label>
              <input type="number" class="form-control" id="edit-km-awal" name="km_awal" min="0" placeholder="Contoh: 12500">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Jam Pulang</label>
              <input type="time" class="form-control" id="edit-jam-pulang" name="jam_pulang">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">KM Akhir</label>
              <input type="number" class="form-control" id="edit-km-akhir" name="km_akhir" min="0" placeholder="Contoh: 12750">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Keterangan</label>
              <textarea class="form-control" id="edit-keterangan" name="keterangan" rows="2" placeholder="Opsional..."></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-bj" id="btn-simpan-perjalanan">
          <span id="btn-simpan-text"><i class="bi bi-check-lg me-1"></i>Simpan</span>
          <span id="btn-simpan-loading" class="d-none">
            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
          </span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
window.addEventListener('load', function () {
  'use strict';

  const modal      = new bootstrap.Modal(document.getElementById('modalEditPerjalanan'));
  const alertBox   = document.getElementById('modal-alert');
  const btnSimpan  = document.getElementById('btn-simpan-perjalanan');
  let activePenugasanId = null;

  // ── Buka modal & isi field saat klik tombol edit ──
  document.querySelectorAll('.btn-edit-perjalanan').forEach(function (btn) {
    btn.addEventListener('click', function () {
      activePenugasanId = this.dataset.penugasanId;
      document.getElementById('modal-sublabel').textContent  = this.dataset.label;
      document.getElementById('edit-penugasan-id').value     = activePenugasanId;
      document.getElementById('edit-jam-ambil').value        = this.dataset.jamAmbil  || '';
      document.getElementById('edit-km-awal').value          = this.dataset.kmAwal    || '';
      document.getElementById('edit-jam-pulang').value       = this.dataset.jamPulang || '';
      document.getElementById('edit-km-akhir').value         = this.dataset.kmAkhir   || '';
      document.getElementById('edit-keterangan').value       = this.dataset.keterangan|| '';
      alertBox.className   = 'alert d-none mb-3 py-2 small';
      alertBox.textContent = '';
      modal.show();
    });
  });

  // ── Simpan via AJAX ──
  btnSimpan.addEventListener('click', function () {
    const form = document.getElementById('form-edit-perjalanan');
    const data = new FormData(form);

    document.getElementById('btn-simpan-text').classList.add('d-none');
    document.getElementById('btn-simpan-loading').classList.remove('d-none');
    btnSimpan.disabled = true;
    alertBox.className = 'alert d-none mb-3 py-2 small';

    fetch('edit_perjalanan.php', { method: 'POST', body: data })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (json.success) {
          const row       = document.getElementById('row-' + activePenugasanId);
          const jamAmbil  = document.getElementById('edit-jam-ambil').value;
          const kmAwal    = document.getElementById('edit-km-awal').value;
          const jamPulang = document.getElementById('edit-jam-pulang').value;
          const kmAkhir   = document.getElementById('edit-km-akhir').value;
          const ket       = document.getElementById('edit-keterangan').value.trim();

          row.querySelector('.cell-jam-ambil').textContent  = jamAmbil  || '-';
          row.querySelector('.cell-km-awal').textContent    = kmAwal    || '-';
          row.querySelector('.cell-jam-pulang').textContent = jamPulang || '-';
          row.querySelector('.cell-km-akhir').textContent   = kmAkhir   || '-';
          row.querySelector('.cell-keterangan').textContent = ket        || '-';

          const btn = row.querySelector('.btn-edit-perjalanan');
          btn.dataset.jamAmbil   = jamAmbil;
          btn.dataset.kmAwal     = kmAwal;
          btn.dataset.jamPulang  = jamPulang;
          btn.dataset.kmAkhir    = kmAkhir;
          btn.dataset.keterangan = ket;

          modal.hide();
          row.classList.add('table-success');
          setTimeout(function () { row.classList.remove('table-success'); }, 1800);
        } else {
          alertBox.className   = 'alert alert-danger mb-3 py-2 small';
          alertBox.textContent = json.message || 'Terjadi kesalahan.';
        }
      })
      .catch(function () {
        alertBox.className   = 'alert alert-danger mb-3 py-2 small';
        alertBox.textContent = 'Gagal terhubung ke server.';
      })
      .finally(function () {
        document.getElementById('btn-simpan-text').classList.remove('d-none');
        document.getElementById('btn-simpan-loading').classList.add('d-none');
        btnSimpan.disabled = false;
      });
  });
});
</script>
<?php require '../partials/footer.php';?>
