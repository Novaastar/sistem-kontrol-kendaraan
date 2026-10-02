<?php
$current_page = basename(dirname($_SERVER['PHP_SELF']));
function is_active($p, $c) { return $p === $c ? 'active' : ''; }
?>
<aside class="sidebar d-flex flex-column">
<div class="sidebar-header d-flex justify-content-center align-items-center bg-white" style="height: 75px; border-bottom: 1px solid #e2e8f0; overflow: hidden;">
  <img src="/sistem_kontrol_kendaraan_bank_jatim/assets/img/logo.png" alt="Bank Jatim" style="width: 200px; height: auto; transform: scale(1.25);">
</div>
<div class="sidebar-menu flex-grow-1">
  <div class="side-title">MENU UTAMA</div>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/dashboard/" class="<?= is_active('dashboard', $current_page) ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/kendaraan/" class="<?= is_active('kendaraan', $current_page) ?>"><i class="bi bi-car-front"></i> Data Kendaraan</a>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/drivers/" class="<?= is_active('drivers', $current_page) ?>"><i class="bi bi-person-badge"></i> Data Driver</a>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/penugasan/" class="<?= is_active('penugasan', $current_page) ?>"><i class="bi bi-clipboard-check"></i> Penugasan Kendaraan</a>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/laporan/" class="<?= is_active('laporan', $current_page) ?>"><i class="bi bi-file-earmark-text"></i> Laporan Harian</a>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/riwayat/" class="<?= is_active('riwayat', $current_page) ?>"><i class="bi bi-clock-history"></i> Riwayat</a>
  
  <div class="side-title mt-4">SISTEM</div>
  <a href="/sistem_kontrol_kendaraan_bank_jatim/pengaturan/" class="<?= is_active('pengaturan', $current_page) ?>"><i class="bi bi-gear"></i> Pengaturan</a>
  <a href="#" id="btn-logout-trigger" data-bs-toggle="modal" data-bs-target="#modalLogout"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>
</aside>

<!-- Modal Konfirmasi Logout -->
<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
      <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #003d7a 0%, #0066cc 100%); padding: 1.5rem 1.5rem 1rem;">
        <div class="d-flex align-items-center gap-3">
          <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-box-arrow-right text-white fs-4"></i>
          </div>
          <h5 class="modal-title text-white fw-bold mb-0" id="modalLogoutLabel">Konfirmasi Logout</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Batal"></button>
      </div>
      <div class="modal-body px-4 py-3">
        <p class="text-secondary mb-0" style="font-size: 0.95rem;">Apakah Anda yakin ingin keluar dari sistem? Sesi Anda akan diakhiri.</p>
      </div>
      <div class="modal-footer border-0 px-4 pb-4 pt-1 gap-2">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
        <a href="/sistem_kontrol_kendaraan_bank_jatim/auth/logout.php" class="btn btn-danger px-4">
          <i class="bi bi-box-arrow-right me-1"></i> Ya, Logout
        </a>
      </div>
    </div>
  </div>
</div>
