<?php
require_once '../config/koneksi.php';require_once '../config/auth.php';require_admin();

if (isset($_GET['delete_id'])) {
    $did = (int)$_GET['delete_id'];
    $p = mysqli_query($conn, "SELECT kendaraan_id, status FROM penugasan WHERE id=$did");
    if ($p && $row = mysqli_fetch_assoc($p)) {
        mysqli_query($conn, "DELETE FROM penugasan WHERE id=$did");
    }
    header('Location: index.php');
    exit;
}

$filter = "";
$sel_driver = isset($_GET['driver_id']) ? (int)$_GET['driver_id'] : 0;
if ($sel_driver > 0) {
    $filter = " WHERE p.driver_id = $sel_driver ";
}

$q=mysqli_query($conn,"SELECT p.*,k.merk,k.nomor_polisi,d.nama driver,pr.jam_ambil,pr.jam_pulang,pr.km_awal,pr.km_akhir FROM penugasan p JOIN kendaraan k ON k.id=p.kendaraan_id JOIN drivers d ON d.id=p.driver_id LEFT JOIN perjalanan pr ON pr.penugasan_id=p.id $filter ORDER BY p.tanggal DESC,p.id DESC");
$drivers=mysqli_query($conn,"SELECT d.*, u.username as nip FROM drivers d LEFT JOIN users u ON d.user_id=u.id ORDER BY d.nama");
$page_title='Penugasan Kendaraan';require '../partials/header.php';?>
<div class="main-wrap">
<?php require '../partials/sidebar.php';?>
<main class="content">
<?php require '../partials/navbar.php';?>
<div class="content-body">
<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h3 class="fw-bold mb-1">Penugasan Kendaraan</h3>
    <p class="text-muted mb-0">Daftar penugasan kendaraan operasional</p>
  </div>
  <form method="GET" class="d-flex align-items-center gap-2 m-0">
    <select class="form-select bg-light" name="driver_id" onchange="this.form.submit()" style="width: auto; max-width: 250px;">
      <option value="">Pilih Driver (Semua)</option>
      <?php while($dr=mysqli_fetch_assoc($drivers)):?>
        <option value="<?=$dr['id']?>" <?= $sel_driver == $dr['id'] ? 'selected' : '' ?>><?=e($dr['nama'])?><?= $dr['nip'] ? ' - NIP: ' . e($dr['nip']) : '' ?></option>
      <?php endwhile;?>
    </select>
    <a class="btn btn-bj text-nowrap" href="tambah.php"><i class="bi bi-plus-lg"></i> Tambah Penugasan</a>
  </form>
</div>
<div class="card card-stat">
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="ps-4">No</th>
          <th>Kendaraan</th>
          <th>Plat Nomor</th>
          <th>Driver</th>
          <th>Mulai Tugas</th>
          <th>Status</th>
          <th class="pe-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $n=1; while($r=mysqli_fetch_assoc($q)):?>
        <tr>
          <td class="ps-4 text-muted"><?=$n++?></td>
          <td class="fw-bold"><?=e($r['merk'])?></td>
          <td><span class="badge bg-light border text-dark fw-bold"><?=e($r['nomor_polisi'])?></span></td>
          <td><?=e($r['driver'])?></td>
          <td><?=e(date('d M Y',strtotime($r['tanggal'])))?></td>
          <td class="align-middle">
            <?php if($r['status']=='ditugaskan' || $r['status']=='berjalan'): ?>
              <span class="badge badge-success-soft rounded-pill">Aktif</span>
            <?php else: ?>
              <span class="badge badge-danger-soft rounded-pill">Tidak Aktif</span>
            <?php endif; ?>
          </td>
          <td class="pe-4 align-middle text-nowrap">
            <a class="btn btn-sm btn-light" href="edit.php?id=<?=$r['id']?>" title="Edit"><i class="bi bi-pencil"></i> Edit</a>
            <a class="btn btn-sm btn-light text-danger" href="?delete_id=<?=$r['id']?>" onclick="return confirm('Hapus penugasan ini?')" title="Hapus"><i class="bi bi-trash"></i> Hapus</a>
          </td>
        </tr>
      <?php endwhile;?>
      </tbody>
    </table>
  </div>
</div>
</div>
</main>
</div>
<?php require '../partials/footer.php';?>
