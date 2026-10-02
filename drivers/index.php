<?php
require_once '../config/koneksi.php';require_once '../config/auth.php';require_admin();
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = "";
if ($search !== '') {
    $esc_search = mysqli_real_escape_string($conn, $search);
    $where = " WHERE d.nama LIKE '%$esc_search%' OR u.username LIKE '%$esc_search%' ";
}
$q=mysqli_query($conn,"SELECT d.*,u.username FROM drivers d LEFT JOIN users u ON u.id=d.user_id $where ORDER BY d.id DESC");$page_title='Data Driver';require '../partials/header.php';?>
<div class="main-wrap">
<?php require '../partials/sidebar.php';?>
<main class="content">
<?php require '../partials/navbar.php';?>
<div class="content-body">
<div class="d-flex justify-content-between align-items-end mb-4">
  <div>
    <h3 class="fw-bold mb-1">Data Driver</h3>
    <p class="text-muted mb-0">Kelola data driver yang bertugas</p>
  </div>
  <form method="GET" class="d-flex align-items-center gap-2 m-0">
    <div class="input-group">
      <input type="text" class="form-control bg-light border-0" name="q" placeholder="Cari nama / NIP..." value="<?= e($search) ?>" style="max-width: 250px;">
      <button class="btn btn-light" type="submit" style="border: 1px solid #dee2e6; border-left: none;"><i class="bi bi-search"></i></button>
    </div>
    <a class="btn btn-bj text-nowrap" href="tambah.php"><i class="bi bi-plus-lg"></i> Tambah Driver</a>
  </form>
</div>
<div class="card card-stat">
  <div class="card-body p-0 table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="ps-4">No</th>
          <th>NIP/Username</th>
          <th>Nama Driver</th>
          <th>No HP</th>
          <th>Status</th>
          <th class="pe-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $n=1;while($r=mysqli_fetch_assoc($q)):?>
        <tr>
          <td class="ps-4 text-muted align-middle"><?=$n++?></td>
          <td class="align-middle"><span class="badge bg-light border text-dark fw-bold"><?=e($r['username']?:'-')?></span></td>
          <td class="fw-bold align-middle"><?=e($r['nama'])?></td>
          <td><?=e($r['no_hp']?:'-')?></td>
          <td class="align-middle">
            <?php if($r['status']=='aktif'): ?><span class="badge badge-success-soft rounded-pill">Aktif</span>
            <?php else: ?><span class="badge badge-danger-soft rounded-pill">Tidak Aktif</span><?php endif; ?>
          </td>
          <td class="pe-4 align-middle text-nowrap">
            <a class="btn btn-sm btn-light" href="edit.php?id=<?=$r['id']?>" title="Edit Data"><i class="bi bi-pencil"></i></a>
            <a class="btn btn-sm btn-light text-warning" href="reset_password.php?id=<?=$r['id']?>" title="Reset Password"><i class="bi bi-key"></i></a>
            <a data-confirm="Hapus driver ini?" class="btn btn-sm btn-light text-danger" href="hapus.php?id=<?=$r['id']?>" onclick="return confirm('Yakin ingin menghapus driver ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
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
