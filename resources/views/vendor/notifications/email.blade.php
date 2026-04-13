<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password EduFinance</title>
<style>
  body { margin:0; padding:0; background:#f0f4f8; font-family:Arial,Helvetica,sans-serif; }
  .wrap { max-width:580px; margin:30px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1); }
  .hdr  { background:linear-gradient(135deg,#1e3a8a,#2563eb); padding:36px 32px; text-align:center; }
  .hdr-icon { font-size:52px; display:block; margin-bottom:12px; }
  .hdr h1 { margin:0; color:#fff; font-size:22px; font-weight:800; }
  .hdr p  { margin:6px 0 0; color:rgba(255,255,255,.75); font-size:13px; }
  .banner { background:#eff6ff; border-bottom:1px solid #bfdbfe; padding:13px 32px; font-size:14px; color:#1e4ed8; font-weight:700; display:flex; align-items:center; gap:6px; }
  .banner:before { content:"🔐"; font-size:16px; }
  .body { padding:32px; }
  .greeting { font-size:15px; color:#1e293b; line-height:1.6; margin-bottom:24px; }
  .reset-box { background:#eff6ff; border:2px dashed #3b82f6; border-radius:14px; padding:26px; text-align:center; margin-bottom:26px; }
  .reset-label { font-size:11px; text-transform:uppercase; letter-spacing:1.5px; color:#6b7280; margin-bottom:10px; font-weight:600; }
  .reset-link { display:inline-block; font-family:'Courier New',monospace; font-size:14px; font-weight:500; color:#1d4ed8; background:#fff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 22px; margin:4px 0 12px; word-break:break-all; max-width:100%; text-decoration:none; }
  .reset-hint { font-size:12px; color:#9ca3af; }
  .info { width:100%; border-collapse:collapse; margin-bottom:26px; background:#f8fafc; border-radius:12px; overflow:hidden; }
  .info tr { border-bottom:1px solid #e2e8f0; }
  .info tr:last-child { border-bottom:none; }
  .info td { padding:12px 16px; font-size:14px; vertical-align:middle; }
  .info td:first-child { background:#f1f5f9; color:#475569; font-weight:600; width:40%; }
  .info td:last-child { color:#0f172a; background:#fff; }
  .steps { background:#f8faff; border-radius:12px; padding:20px 22px; margin-bottom:26px; }
  .steps h3 { color:#1e3a8a; font-size:13px; text-transform:uppercase; letter-spacing:.8px; margin:0 0 14px; font-weight:800; display:flex; align-items:center; gap:6px; }
  .steps h3:before { content:"📋"; font-size:14px; }
  .step { display:flex; margin-bottom:11px; font-size:13px; }
  .step:last-child { margin-bottom:0; }
  .step-n { background:#2563eb; color:#fff; border-radius:50%; width:23px; height:23px; min-width:23px; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; margin-right:11px; margin-top:1px; }
  .step-t { color:#374151; line-height:1.5; }
  .cta { text-align:center; margin-bottom:26px; }
  .cta a { display:inline-block; background:#2563eb; color:#fff !important; text-decoration:none; padding:15px 38px; border-radius:10px; font-size:15px; font-weight:700; box-shadow:0 4px 6px rgba(37,99,235,.2); transition:all .2s; }
  .cta a:hover { background:#1d4ed8; transform:translateY(-1px); box-shadow:0 6px 8px rgba(37,99,235,.3); }
  .warn { background:#fffbeb; border-left:4px solid #f59e0b; border-radius:0 10px 10px 0; padding:13px 16px; margin-bottom:26px; }
  .warn strong { display:block; color:#92400e; font-size:13px; margin-bottom:4px; }
  .warn p { margin:0; color:#a16207; font-size:13px; line-height:1.5; }
  .expire-notice { background:#f0fdf4; border-radius:10px; padding:12px 16px; margin-bottom:20px; border:1px solid #bbf7d0; }
  .expire-notice p { margin:0; color:#166534; font-size:13px; display:flex; align-items:center; gap:8px; }
  .expire-notice p:before { content:"⏰"; font-size:14px; }
  .ftr { background:#f8fafc; border-top:1px solid #e2e8f0; padding:22px 32px; text-align:center; }
  .ftr p { margin:0 0 5px; font-size:12px; color:#9ca3af; line-height:1.6; }
  .ftr a { color:#2563eb; text-decoration:none; }
  .copy { margin-top:14px; padding-top:12px; border-top:1px solid #e2e8f0; font-size:11px; color:#d1d5db; }
  .subcopy { background:#f1f5f9; border-radius:8px; padding:12px 16px; margin-top:20px; font-size:12px; color:#475569; border:1px solid #e2e8f0; }
  .subcopy p { margin:0 0 8px; }
  .subcopy .break-all { word-break:break-all; color:#1d4ed8; font-family:monospace; font-size:11px; background:#fff; padding:6px 8px; border-radius:6px; display:inline-block; border:1px solid #cbd5e1; }
</style>
</head>
<body>
<div class="wrap">

  <div class="hdr">
    <span class="hdr-icon">🏫</span>
    <h1>EduFinance</h1>
    <p>Manajemen Keuangan Instansi Digital</p>
  </div>

  <div class="banner">🔐 Permintaan Reset Password</div>

  <div class="body">

    <p class="greeting">
      @if (!empty($greeting))
        {{ $greeting }}
      @else
        Halo, <strong>{{ $email ?? 'Pengguna' }}</strong>!
      @endif
      <br>
      @foreach ($introLines as $line)
        {{ $line }}
      @endforeach
    </p>

    @isset($actionText)
    <div class="reset-box">
      <div class="reset-label">🔑 TOMBOL RESET PASSWORD</div>
      <div class="cta" style="margin-bottom:10px;">
        <a href="{{ $actionUrl }}">{{ $actionText }} →</a>
      </div>
      <div class="reset-hint">Klik tombol di atas untuk mereset password Anda</div>
    </div>
    @endisset

    <div class="expire-notice">
      <p>Link reset password ini akan kedaluwarsa dalam 60 menit</p>
    </div>

    <table class="info">
      <tr><td>📧 Email Akun</td><td>{{ $email ?? $notifiable->email ?? 'Email terdaftar' }}</td></tr>
      <tr><td>⏱️ Waktu Permintaan</td><td>{{ now()->format('d M Y H:i') }} WIB</td></tr>
      <tr><td>🌐 IP Address</td><td>{{ request()->ip() ?? 'Unknown' }}</td></tr>
    </table>

    <div class="steps">
      <h3>📋 Langkah Reset Password</h3>
      <div class="step"><div class="step-n">1</div><div class="step-t">Klik tombol <strong>Reset Password</strong> di atas</div></div>
      <div class="step"><div class="step-n">2</div><div class="step-t">Anda akan diarahkan ke halaman reset password</div></div>
      <div class="step"><div class="step-n">3</div><div class="step-t">Masukkan password baru Anda (min. 8 karakter)</div></div>
      <div class="step"><div class="step-n">4</div><div class="step-t">Konfirmasi password baru dan simpan perubahan</div></div>
      <div class="step"><div class="step-n">5</div><div class="step-t">Login dengan password baru Anda 🎉</div></div>
    </div>

    <div class="warn">
      <strong>⚠️ Perhatian</strong>
      <p>Jika Anda tidak meminta reset password, abaikan email ini dan pastikan akun Anda tetap aman.</p>
    </div>

    @foreach ($outroLines as $line)
    <p style="font-size:13px; color:#64748b; margin-bottom:10px;">{{ $line }}</p>
    @endforeach

    @isset($actionText)
    <div class="subcopy">
      <p>Jika tombol "{{ $actionText }}" tidak berfungsi, salin dan tempel URL berikut ke browser Anda:</p>
      <span class="break-all">{{ $actionUrl }}</span>
    </div>
    @endisset

  </div>

  <div class="ftr">
    <p>Email ini dikirim otomatis oleh sistem EduFinance.</p>
    <p>Bantuan: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a></p>
    @if (!empty($salutation))
      <p>{{ $salutation }}</p>
    @else
      <p>Regards,<br>EduFinance</p>
    @endif
    <div class="copy">© {{ date('Y') }} EduFinance. Semua hak dilindungi.</div>
  </div>

</div>
</body>
</html>