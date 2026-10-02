<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "";
if ($search !== '') {
    $esc_search = mysqli_real_escape_string($conn, $search);
    $where = " WHERE (d.nama LIKE '%$esc_search%' OR k.merk LIKE '%$esc_search%' OR k.nomor_polisi LIKE '%$esc_search%') ";
}

$q = mysqli_query($conn, "
    SELECT p.*, k.merk, k.nomor_polisi, d.nama driver, 
           pr.jam_ambil, pr.km_awal, pr.jam_pulang, pr.km_akhir, pr.keterangan 
    FROM penugasan p 
    JOIN kendaraan k ON k.id=p.kendaraan_id 
    JOIN drivers d ON d.id=p.driver_id 
    LEFT JOIN perjalanan pr ON pr.penugasan_id=p.id 
    $where 
    ORDER BY p.tanggal DESC, p.id DESC
");

$page_title='Riwayat Penugasan'; require '../partials/header.php';
?>
<div class="main-wrap">
<?php require '../partials/sidebar.php';?>
<main class="content">
<?php require '../partials/navbar.php';?>
<div class="content-body">
<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h3 class="fw-bold mb-1">Riwayat Penugasan</h3>
    <p class="text-muted mb-0">Semua riwayat perjalanan kendaraan</p>
  </div>
  <form method="GET" class="d-flex align-items-center gap-2 m-0">
    <div class="input-group">
      <input type="text" class="form-control bg-light border-0" name="q" placeholder="Cari driver/kendaraan..." value="<?= e($search) ?>" style="max-width: 250px;">
      <button class="btn btn-light" type="submit" style="border: 1px solid #dee2e6; border-left: none;"><i class="bi bi-search"></i></button>
    </div>
  </form>
</div>
<div class="card card-stat mb-4">
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="ps-4">No</th>
          <th>Tanggal</th>
          <th>Kendaraan</th>
          <th>Driver</th>
          <th>Status</th>
          <th>Jam (Keluar - Masuk)</th>
          <th class="pe-4">KM (Awal - Akhir)</th>
        </tr>
      </thead>
      <tbody>
      <?php $n=1; while($r=mysqli_fetch_assoc($q)):?>
        <tr>
          <td class="ps-4 text-muted align-middle"><?=$n++?></td>
          <td class="fw-bold align-middle"><?=e(date('d M Y',strtotime($r['tanggal'])))?></td>
          <td class="align-middle">
            <div class="fw-bold"><?=e($r['merk'])?></div>
            <div class="small"><span class="badge bg-light border text-dark"><?=e($r['nomor_polisi'])?></span></div>
          </td>
          <td class="align-middle"><?=e($r['driver'])?></td>
          <td class="align-middle">
            <?php if($r['status']=='ditugaskan' || $r['status']=='berjalan'): ?>
              <span class="badge badge-success-soft rounded-pill">Aktif</span>
            <?php else: ?>
              <span class="badge badge-danger-soft rounded-pill">Tidak Aktif</span>
            <?php endif; ?>
          </td>
          <td class="align-middle text-muted">
             <?= $r['jam_ambil'] ? substr($r['jam_ambil'],0,5) : '-' ?> <i class="bi bi-arrow-right mx-1"></i> 
             <?= $r['jam_pulang'] ? substr($r['jam_pulang'],0,5) : '-' ?>
          </td>
          <td class="pe-4 align-middle text-muted">
             <?= $r['km_awal'] ?: '-' ?> <i class="bi bi-arrow-right mx-1"></i> 
             <?= $r['km_akhir'] ?: '-' ?>
          </td>
        </tr>
      <?php endwhile;?>
      <?php if(mysqli_num_rows($q)==0):?>
        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data riwayat.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
</main>
</div>
<?php require '../partials/footer.php';?>
