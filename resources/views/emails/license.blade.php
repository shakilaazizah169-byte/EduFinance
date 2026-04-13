<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kode Lisensi EduFinance</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:Arial,Helvetica,sans-serif; }
  .wrap { max-width:580px; margin:30px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1); }
  .hdr  { background:linear-gradient(135deg,#1e3a8a,#2563eb); padding:36px 32px; text-align:center; }
  .hdr-icon { font-size:52px; display:block; margin-bottom:12px; }
  .hdr h1 { margin:0; color:#fff; font-size:22px; font-weight:800; }
  .hdr p  { margin:6px 0 0; color:rgba(255,255,255,.75); font-size:13px; }
  .banner { background:#f0fdf4; border-bottom:1px solid #bbf7d0; padding:13px 32px; font-size:14px; color:#15803d; font-weight:700; }
  .body { padding:32px; }
  .greeting { font-size:15px; color:#1e293b; line-height:1.6; margin-bottom:24px; }
  .lic-box { background:#eff6ff; border:2px dashed #3b82f6; border-radius:14px; padding:26px; text-align:center; margin-bottom:26px; }
  .lic-label { font-size:11px; text-transform:uppercase; letter-spacing:1.5px; color:#6b7280; margin-bottom:10px; font-weight:600; }
  .lic-key { display:inline-block; font-family:'Courier New',monospace; font-size:26px; font-weight:800; color:#1d4ed8; letter-spacing:5px; background:#fff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 22px; margin:4px 0 12px; word-break:break-all; }
  .lic-hint { font-size:12px; color:#9ca3af; }
  .info { width:100%; border-collapse:collapse; margin-bottom:26px; }
  .info tr { border-bottom:1px solid #f1f5f9; }
  .info tr:last-child { border-bottom:none; }
  .info td { padding:9px 4px; font-size:14px; vertical-align:middle; }
  .info td:first-child { color:#6b7280; width:42%; }
  .info td:last-child { color:#111827; font-weight:700; }
  .steps { background:#f8faff; border-radius:12px; padding:20px 22px; margin-bottom:26px; }
  .steps h3 { color:#1e3a8a; font-size:13px; text-transform:uppercase; letter-spacing:.8px; margin:0 0 14px; font-weight:800; }
  .step { display:flex; margin-bottom:11px; font-size:13px; }
  .step:last-child { margin-bottom:0; }
  .step-n { background:#2563eb; color:#fff; border-radius:50%; width:23px; height:23px; min-width:23px; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; margin-right:11px; margin-top:1px; }
  .step-t { color:#374151; line-height:1.5; }
  .cta { text-align:center; margin-bottom:26px; }
  .cta a { display:inline-block; background:#2563eb; color:#fff !important; text-decoration:none; padding:15px 38px; border-radius:10px; font-size:15px; font-weight:700; }
  .warn { background:#fffbeb; border-left:4px solid #f59e0b; border-radius:0 10px 10px 0; padding:13px 16px; }
  .warn strong { display:block; color:#92400e; font-size:13px; margin-bottom:4px; }
  .warn p { margin:0; color:#a16207; font-size:13px; line-height:1.5; }
  .ftr { background:#f8fafc; border-top:1px solid #e2e8f0; padding:22px 32px; text-align:center; }
  .ftr p { margin:0 0 5px; font-size:12px; color:#9ca3af; line-height:1.6; }
  .ftr a { color:#2563eb; text-decoration:none; }
  .copy { margin-top:14px; padding-top:12px; border-top:1px solid #e2e8f0; font-size:11px; color:#d1d5db; }
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <span class="hdr-icon">🏫</span>
    <h1>EduFinance</h1>
    <p>Manajemen Keuangan Instansi Digital</p>
  </div>

  <div class="banner">✅ Pembayaran Berhasil — Kode Lisensi Siap Digunakan</div>

  <div class="body">

    <p class="greeting">
      Halo, <strong>{{ $buyerName }}</strong>!<br>
      Terima kasih telah berlangganan <strong>EduFinance</strong>.
      Berikut kode lisensi Anda yang bisa langsung dipakai untuk mendaftar.
    </p>

    <div class="lic-box">
      <div class="lic-label">🔑 Kode Lisensi Anda</div>
      <div class="lic-key">{{ $licenseKey }}</div>
      <div class="lic-hint">Salin dan simpan kode ini di tempat yang aman</div>
    </div>

    <table class="info">
      <tr><td>📦 Paket</td><td>{{ $packageLabel }}</td></tr>
      <tr><td>💰 Total</td><td>{{ $price }}</td></tr>
      <tr><td>📅 Aktif Mulai</td><td>{{ $startDate }}</td></tr>
      <tr><td>📅 Berlaku s/d</td><td>{{ $endDate }}</td></tr>
      <tr><td>🆔 Order ID</td><td style="font-family:monospace;font-size:12px;">{{ $orderId }}</td></tr>
    </table>

    <div class="steps">
      <h3>📌 Cara Mendaftar</h3>
      <div class="step"><div class="step-n">1</div><div class="step-t">Klik tombol <strong>Daftar Sekarang</strong> di bawah</div></div>
      <div class="step"><div class="step-n">2</div><div class="step-t">Masukkan <strong>kode lisensi</strong> — terverifikasi otomatis</div></div>
      <div class="step"><div class="step-n">3</div><div class="step-t">Isi nama, email sekolah, dan buat password</div></div>
      <div class="step"><div class="step-n">4</div><div class="step-t">Login dan langsung pakai! 🎉</div></div>
    </div>

    <div class="cta">
      <a href="{{ $registerUrl }}">Daftar Sekarang →</a>
    </div>

    <div class="warn">
      <strong>⚠️ Penting</strong>
      <p>Kode lisensi hanya bisa digunakan <strong>satu kali.</strong> Jangan bagikan kepada siapapun.</p>
    </div>

  </div>

  <div class="ftr">
    <p>Email ini dikirim otomatis oleh sistem EduFinance.</p>
    <p>Bantuan: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
    <div class="copy">© {{ date('Y') }} EduFinance. Semua hak dilindungi.</div>
  </div>

</div>
</body>
</html>