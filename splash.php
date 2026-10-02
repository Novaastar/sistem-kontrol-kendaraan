<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bank Jatim</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: #ffffff;
    width: 100vw;
    height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Inter, system-ui, sans-serif;
  }

  /* ── Logo ── */
  .splash-logo {
    position: relative;
    z-index: 10;
    opacity: 0;
    animation: logoFadeIn 0.9s ease-out 0.35s forwards;
  }
  .splash-logo img {
    width: 200px;
    height: auto;
  }

  /* ── Ornamen pojok kanan atas ── */
  .ornament-top-right {
    position: fixed;
    top: 0;
    right: 0;
    width: 160px;
    height: 160px;
    transform: translateX(55px);
    opacity: 0;
    animation: slideFromRight 0.75s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.1s forwards;
  }

  /* ── Ornamen pojok kiri bawah ── */
  .ornament-bottom-left {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 160px;
    height: 160px;
    transform: translateX(-55px);
    opacity: 0;
    animation: slideFromLeft 0.75s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.1s forwards;
  }

  /* ── Keyframes ── */
  @keyframes logoFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  @keyframes slideFromRight {
    from { transform: translateX(55px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
  }
  @keyframes slideFromLeft {
    from { transform: translateX(-55px); opacity: 0; }
    to   { transform: translateX(0);     opacity: 1; }
  }

  /* ── Fade out seluruh halaman sebelum redirect ── */
  body.fade-out {
    animation: pageFadeOut 0.4s ease-in forwards;
  }
  @keyframes pageFadeOut {
    from { opacity: 1; }
    to   { opacity: 0; }
  }
</style>
</head>
<body>

  <!-- Ornamen pojok kanan atas: slide dari kanan ke kiri -->
  <div class="ornament-top-right">
    <svg viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g opacity="0.88">
        <!-- Kotak ukiran sudut kanan atas -->
        <rect x="108" y="8"  width="38" height="38" rx="4" fill="none" stroke="#b8960c" stroke-width="2.2"/>
        <rect x="115" y="15" width="24" height="24" rx="3" fill="none" stroke="#b8960c" stroke-width="1.5"/>
        <rect x="121" y="21" width="12" height="12" rx="2" fill="#b8960c" opacity="0.22"/>
        <line x1="108" y1="27" x2="146" y2="27" stroke="#b8960c" stroke-width="0.9" opacity="0.45"/>
        <line x1="127" y1="8"  x2="127" y2="46" stroke="#b8960c" stroke-width="0.9" opacity="0.45"/>

        <!-- Sulur lengkung utama vertikal -->
        <path d="M148 2 C148 2 130 6 118 18 C106 30 104 52 104 52"
              stroke="#6b7c2d" stroke-width="2.4" fill="none" stroke-linecap="round"/>
        <path d="M158 10 C158 10 136 14 122 30 C108 46 106 70 106 70"
              stroke="#6b7c2d" stroke-width="1.7" fill="none" stroke-linecap="round" opacity="0.65"/>

        <!-- Percabangan sulur kecil atas -->
        <path d="M118 18 C118 18 112 13 108 7"  stroke="#6b7c2d" stroke-width="1.4" fill="none" stroke-linecap="round" opacity="0.65"/>
        <path d="M122 30 C122 30 115 22 110 13" stroke="#6b7c2d" stroke-width="1.2" fill="none" stroke-linecap="round" opacity="0.55"/>
        <circle cx="108" cy="7"  r="2.8" fill="#b8960c" opacity="0.55"/>
        <circle cx="110" cy="13" r="2"   fill="#6b7c2d" opacity="0.48"/>

        <!-- Medali / lingkaran beruntai -->
        <circle cx="130" cy="58" r="7"   fill="none" stroke="#b8960c" stroke-width="1.8"/>
        <circle cx="130" cy="58" r="3.5" fill="#b8960c" opacity="0.28"/>
        <circle cx="147" cy="73" r="5"   fill="none" stroke="#b8960c" stroke-width="1.5"/>
        <circle cx="147" cy="73" r="2.5" fill="#b8960c" opacity="0.22"/>
        <path d="M130 65 Q138 68 147 67" stroke="#b8960c" stroke-width="1.2" fill="none" stroke-linecap="round"/>

        <!-- Sulur vertikal turun kiri -->
        <path d="M104 52 C104 52 100 76 102 100 C104 122 112 140 112 140"
              stroke="#6b7c2d" stroke-width="1.9" fill="none" stroke-linecap="round" opacity="0.58"/>
        <path d="M106 70 C106 70 103 90 105 112"
              stroke="#6b7c2d" stroke-width="1.3" fill="none" stroke-linecap="round" opacity="0.42"/>

        <!-- Percabangan sulur kiri -->
        <path d="M102 80 Q95 76 90 70"  stroke="#6b7c2d" stroke-width="1.2" fill="none" stroke-linecap="round" opacity="0.48"/>
        <circle cx="90"  cy="70" r="2.4" fill="#b8960c" opacity="0.42"/>
        <path d="M103 97 Q96 94 92 88"  stroke="#6b7c2d" stroke-width="1.1" fill="none" stroke-linecap="round" opacity="0.42"/>

        <!-- Kotak ukiran bawah -->
        <rect x="110" y="118" width="30" height="30" rx="3" fill="none" stroke="#b8960c" stroke-width="1.8"/>
        <rect x="116" y="124" width="18" height="18" rx="2" fill="none" stroke="#b8960c" stroke-width="1.2"/>
        <rect x="121" y="129" width="8"  height="8"  rx="1" fill="#b8960c" opacity="0.22"/>
        <line x1="110" y1="133" x2="140" y2="133" stroke="#b8960c" stroke-width="0.8" opacity="0.45"/>
        <line x1="125" y1="118" x2="125" y2="148" stroke="#b8960c" stroke-width="0.8" opacity="0.45"/>

        <!-- Aksen merah -->
        <circle cx="150" cy="48"  r="4"   fill="none" stroke="#c0392b" stroke-width="1.4" opacity="0.55"/>
        <circle cx="150" cy="48"  r="1.8" fill="#c0392b" opacity="0.38"/>
        <circle cx="156" cy="100" r="3.5" fill="none" stroke="#c0392b" stroke-width="1.2" opacity="0.48"/>
        <circle cx="156" cy="100" r="1.5" fill="#c0392b" opacity="0.32"/>
      </g>
    </svg>
  </div>

  <!-- Logo tengah -->
  <div class="splash-logo">
    <img src="assets/img/logo.png" alt="Bank Jatim">
  </div>

  <!-- Ornamen pojok kiri bawah: slide dari kiri ke kanan -->
  <div class="ornament-bottom-left">
    <svg viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g opacity="0.88">
        <!-- Kotak ukiran kiri bawah -->
        <rect x="14" y="114" width="38" height="38" rx="4" fill="none" stroke="#b8960c" stroke-width="2.2"/>
        <rect x="21" y="121" width="24" height="24" rx="3" fill="none" stroke="#b8960c" stroke-width="1.5"/>
        <rect x="27" y="127" width="12" height="12" rx="2" fill="#b8960c" opacity="0.22"/>
        <line x1="14" y1="133" x2="52" y2="133" stroke="#b8960c" stroke-width="0.9" opacity="0.45"/>
        <line x1="33" y1="114" x2="33" y2="152" stroke="#b8960c" stroke-width="0.9" opacity="0.45"/>

        <!-- Sulur lengkung utama vertikal -->
        <path d="M12 158 C12 158 30 154 42 142 C54 130 56 108 56 108"
              stroke="#6b7c2d" stroke-width="2.4" fill="none" stroke-linecap="round"/>
        <path d="M2 150 C2 150 24 146 38 130 C52 114 54 90 54 90"
              stroke="#6b7c2d" stroke-width="1.7" fill="none" stroke-linecap="round" opacity="0.65"/>

        <!-- Percabangan sulur kecil bawah -->
        <path d="M42 142 C42 142 48 147 52 153" stroke="#6b7c2d" stroke-width="1.4" fill="none" stroke-linecap="round" opacity="0.65"/>
        <path d="M38 130 C38 130 45 138 50 147" stroke="#6b7c2d" stroke-width="1.2" fill="none" stroke-linecap="round" opacity="0.55"/>
        <circle cx="52"  cy="153" r="2.8" fill="#b8960c" opacity="0.55"/>
        <circle cx="50"  cy="147" r="2"   fill="#6b7c2d" opacity="0.48"/>

        <!-- Medali / lingkaran beruntai -->
        <circle cx="30" cy="102" r="7"   fill="none" stroke="#b8960c" stroke-width="1.8"/>
        <circle cx="30" cy="102" r="3.5" fill="#b8960c" opacity="0.28"/>
        <circle cx="13" cy="87"  r="5"   fill="none" stroke="#b8960c" stroke-width="1.5"/>
        <circle cx="13" cy="87"  r="2.5" fill="#b8960c" opacity="0.22"/>
        <path d="M30 95 Q22 92 13 93" stroke="#b8960c" stroke-width="1.2" fill="none" stroke-linecap="round"/>

        <!-- Sulur vertikal naik kanan -->
        <path d="M56 108 C56 108 60 84 58 60 C56 38 48 20 48 20"
              stroke="#6b7c2d" stroke-width="1.9" fill="none" stroke-linecap="round" opacity="0.58"/>
        <path d="M54 90 C54 90 57 70 55 48"
              stroke="#6b7c2d" stroke-width="1.3" fill="none" stroke-linecap="round" opacity="0.42"/>

        <!-- Percabangan sulur kanan -->
        <path d="M58 80 Q66 84 70 90"  stroke="#6b7c2d" stroke-width="1.2" fill="none" stroke-linecap="round" opacity="0.48"/>
        <circle cx="70" cy="90" r="2.4" fill="#b8960c" opacity="0.42"/>
        <path d="M57 63 Q64 66 68 72"  stroke="#6b7c2d" stroke-width="1.1" fill="none" stroke-linecap="round" opacity="0.42"/>

        <!-- Kotak ukiran atas -->
        <rect x="18" y="12" width="30" height="30" rx="3" fill="none" stroke="#b8960c" stroke-width="1.8"/>
        <rect x="24" y="18" width="18" height="18" rx="2" fill="none" stroke="#b8960c" stroke-width="1.2"/>
        <rect x="29" y="23" width="8"  height="8"  rx="1" fill="#b8960c" opacity="0.22"/>
        <line x1="18" y1="27" x2="48" y2="27" stroke="#b8960c" stroke-width="0.8" opacity="0.45"/>
        <line x1="33" y1="12" x2="33" y2="42" stroke="#b8960c" stroke-width="0.8" opacity="0.45"/>

        <!-- Aksen merah -->
        <circle cx="10"  cy="112" r="4"   fill="none" stroke="#c0392b" stroke-width="1.4" opacity="0.55"/>
        <circle cx="10"  cy="112" r="1.8" fill="#c0392b" opacity="0.38"/>
        <circle cx="4"   cy="60"  r="3.5" fill="none" stroke="#c0392b" stroke-width="1.2" opacity="0.48"/>
        <circle cx="4"   cy="60"  r="1.5" fill="#c0392b" opacity="0.32"/>
      </g>
    </svg>
  </div>

</body>
<script>
  // Setelah animasi selesai (~2.1 detik), fade out halaman lalu redirect
  setTimeout(function () {
    document.body.classList.add('fade-out');
    setTimeout(function () {
      window.location.href = 'index.php';
    }, 400);
  }, 2100);
</script>
</html>
