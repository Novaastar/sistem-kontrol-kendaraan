
<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
$t = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
function scalar($conn,$sql){$r=mysqli_query($conn,$sql);return (int)mysqli_fetch_assoc($r)['n'];}
$total_kendaraan=scalar($conn,"SELECT COUNT(*) n FROM kendaraan");
$total_tugas=scalar($conn,"SELECT COUNT(*) n FROM penugasan WHERE tanggal='$t'");
$selesai=scalar($conn,"SELECT COUNT(*) n FROM penugasan WHERE tanggal='$t' AND status='selesai'");
$jalan=scalar($conn,"SELECT COUNT(*) n FROM penugasan WHERE tanggal='$t' AND status='berjalan'");
$ditugaskan=scalar($conn,"SELECT COUNT(*) n FROM penugasan WHERE tanggal='$t' AND status='ditugaskan'");
$q=mysqli_query($conn,"SELECT p.*,k.merk,k.nomor_polisi,d.nama driver,pr.jam_ambil FROM penugasan p JOIN kendaraan k ON k.id=p.kendaraan_id JOIN drivers d ON d.id=p.driver_id LEFT JOIN perjalanan pr ON pr.penugasan_id=p.id WHERE p.tanggal='$t' ORDER BY p.id DESC LIMIT 5");
$page_title='Dashboard'; require '../partials/header.php'; 
?>
<div class="main-wrap">
<?php require '../partials/sidebar.php'; ?>
<main class="content">
<?php require '../partials/navbar.php'; ?>
<div class="content-body">

<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h3 class="fw-bold mb-2">Dashboard</h3>
    <div class="text-secondary"><i class="bi bi-calendar3"></i> <?=e(date('d F Y', strtotime($t)))?></div>
  </div>
  <form method="GET" class="d-flex gap-2 align-items-center m-0">
    <label for="tanggal" class="text-muted small fw-bold mb-0">Lihat Tanggal:</label>
    <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm border-secondary" style="width: 150px;" value="<?=e($t)?>" onchange="this.form.submit()">
  </form>
</div>

<div class="row g-3 mb-4">
<?php foreach([['Total Armada',$total_kendaraan,'bi-car-front text-primary'],['Sudah Lengkap',$selesai,'bi-check-circle text-success'],['Belum Selesai',$jalan,'bi-arrow-repeat text-warning'],['Belum Input',$ditugaskan,'bi-x-circle text-danger']] as $s): ?>
<div class="col-md-6 col-xl-3">
  <div class="card card-stat p-3">
    <div class="d-flex align-items-center gap-3">
      <div class="icon fs-3"><i class="bi <?= $s[2] ?>"></i></div>
      <div>
        <div class="fs-4 fw-bold mb-0 lh-1"><?=$s[1]?></div>
        <div class="text-muted small mt-1"><?=$s[0]?></div>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-md-5 col-lg-4">
    <div class="card card-stat h-100">
      <div class="card-body">
        <h6 class="fw-bold mb-4">Status Kendaraan Hari Ini</h6>
        <div class="chart-container">
          <canvas id="statusChart"></canvas>
          <div class="chart-center-text"><?= $total_tugas ?></div>
        </div>
        <div class="d-flex justify-content-center gap-4 mt-4 flex-wrap">
          <div class="d-flex align-items-center gap-2 small">
            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
            <span>Sudah Lengkap</span>
          </div>
          <div class="d-flex align-items-center gap-2 small">
            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#f59e0b;flex-shrink:0"></span>
            <span>Belum Selesai</span>
          </div>
          <div class="d-flex align-items-center gap-2 small">
            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
            <span>Belum Input</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-7 col-lg-8">
    <div class="card card-stat h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Aktivitas Terbaru Hari Ini</h6>
          <a href="../laporan/" class="text-decoration-none text-danger small fw-bold">Lihat Semua</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover border-top">
            <thead>
              <tr>
                <th>Jam</th>
                <th>Driver</th>
                <th>Kendaraan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while($r=mysqli_fetch_assoc($q)): ?>
              <tr>
                <td class="text-muted"><?=e($r['jam_ambil'] ? substr($r['jam_ambil'],0,5) : '-')?></td>
                <td class="fw-bold"><?=e($r['driver'])?></td>
                <td><?=e($r['merk'])?> (<?=e($r['nomor_polisi'])?>)</td>
                <td>
                  <?php if($r['status']=='selesai'): ?><span class="badge badge-success-soft rounded-pill">Selesai</span>
                  <?php elseif($r['status']=='berjalan'): ?><span class="badge badge-warning-soft rounded-pill">Berjalan</span>
                  <?php else: ?><span class="badge badge-soft rounded-pill">Belum</span><?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php if(mysqli_num_rows($q)==0): ?>
              <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas hari ini.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
</main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var ctx = document.getElementById('statusChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Sudah Lengkap', 'Belum Selesai', 'Belum Input'],
      datasets: [{
        data: [<?= $selesai ?>, <?= $jalan ?>, <?= $ditugaskan ?>],
        backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
        borderWidth: 0,
        cutout: '75%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { enabled: true } }
    }
  });
});
</script>
<?php require '../partials/footer.php'; ?>
