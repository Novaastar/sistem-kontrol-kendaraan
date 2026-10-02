<?php
require_once '../config/koneksi.php'; require_once '../config/auth.php'; require_admin();
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "";
if ($search !== '') {
    $esc_search = mysqli_real_escape_string($conn, $search);
    $where = " WHERE merk LIKE '%$esc_search%' OR nomor_polisi LIKE '%$esc_search%' ";
}
$q=mysqli_query($conn,"SELECT * FROM kendaraan $where ORDER BY id DESC"); $page_title='Data Kendaraan';
require '../partials/header.php'; ?>
<div class="main-wrap">
<?php require '../partials/sidebar.php'; ?>
<main class="content">
<?php require '../partials/navbar.php'; ?>
<div class="content-body">
<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h3 class="fw-bold mb-1">Data Kendaraan</h3>
    <p class="text-muted mb-0">Daftar kendaraan operasional Bank Jatim</p>
  </div>
  <form method="GET" class="d-flex align-items-center gap-2 m-0">
    <div class="input-group">
      <input type="text" class="form-control bg-light border-0" name="q" placeholder="Cari merk / plat..." value="<?= e($search) ?>" style="max-width: 250px;">
      <button class="btn btn-light" type="submit" style="border: 1px solid #dee2e6; border-left: none;"><i class="bi bi-search"></i></button>
    </div>
    <a class="btn btn-bj text-nowrap" href="tambah.php"><i class="bi bi-plus-lg"></i> Tambah Kendaraan</a>
  </form>
</div>
<div class="card card-stat">
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="ps-4">No</th>
          <th>Jenis Kendaraan</th>
          <th>Plat Nomor</th>
          <th>Tahun</th>
          <th>Status</th>
          <th class="pe-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $no=1; while($r=mysqli_fetch_assoc($q)): ?>
        <tr>
          <td class="ps-4 text-muted align-middle"><?=$no++?></td>
          <td>
            <div class="d-flex align-items-center gap-3">
              <?php if($r['foto']): ?>
                  <div class="kendaraan-img-wrap">
                    <img src="../uploads/kendaraan/<?=e($r['foto'])?>" alt="Foto" class="kendaraan-img">
                  </div>
              <?php else: ?>
                  <div class="kendaraan-img-wrap kendaraan-img-placeholder bg-light d-flex justify-content-center align-items-center text-muted border">
                    <i class="bi bi-car-front" style="font-size:1.4rem;"></i>
                  </div>
              <?php endif; ?>
              <span class="fw-bold"><?=e($r['merk'])?></span>
            </div>
          </td>
          <td class="align-middle"><span class="badge bg-light border text-dark fw-bold"><?=e($r['nomor_polisi'])?></span></td>
          <td class="align-middle"><?=e($r['tahun'] ?: '-')?></td>
          <td class="align-middle">
            <?php if($r['status']=='aktif'): ?><span class="badge badge-success-soft rounded-pill">Aktif</span>
            <?php else: ?><span class="badge badge-danger-soft rounded-pill">Tidak Aktif</span><?php endif; ?>
          </td>
          <td class="pe-4 align-middle">
            <a class="btn btn-sm btn-light" href="edit.php?id=<?=$r['id']?>" title="Edit"><i class="bi bi-pencil"></i></a> 
            <a data-confirm="Hapus kendaraan ini?" class="btn btn-sm btn-light text-danger" href="hapus.php?id=<?=$r['id']?>" title="Hapus"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</div>
</main>
</div>
<?php require '../partials/footer.php'; ?>
