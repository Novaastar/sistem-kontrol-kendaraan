<nav class="navbar navbar-light bj-navbar sticky-top">
<div class="container-fluid px-4">
  <div class="d-flex align-items-center">
    <!-- Breadcrumb or toggle can go here -->
  </div>
  <div class="d-flex align-items-center gap-3 text-secondary">
    <div class="text-end d-none d-md-block">
      <div class="fw-bold text-dark"><?= e($_SESSION['nama'] ?? 'Admin') ?></div>
      <div class="small">Administrator</div>
    </div>
    
    <?php
    $user_id_nav = $_SESSION['user_id'] ?? 0;
    $foto_nav = null;
    if ($user_id_nav) {
        $nav_res = mysqli_query($conn, "SELECT foto FROM users WHERE id=$user_id_nav");
        if ($nav_res && $row = mysqli_fetch_assoc($nav_res)) {
            $foto_nav = $row['foto'];
        }
    }
    ?>
    <div class="dropdown">
      <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <?php if ($foto_nav): ?>
            <img src="/sistem_kontrol_kendaraan_bank_jatim/uploads/profiles/<?= e($foto_nav) ?>" alt="Profil" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
        <?php else: ?>
            <i class="bi bi-person-circle fs-3 text-secondary"></i>
        <?php endif; ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end text-small" aria-labelledby="dropdownUser1">
        <li><a class="dropdown-item" href="/sistem_kontrol_kendaraan_bank_jatim/profile/"><i class="bi bi-person me-2"></i> Edit Profil</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="/sistem_kontrol_kendaraan_bank_jatim/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sign out</a></li>
      </ul>
    </div>
  </div>
</div>
</nav>
