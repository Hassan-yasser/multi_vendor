<x-guest-layout>
  <p class="mb-3">
    شكرًا للتسجيل. رجاءًا تأكد من بريدك عبر الرابط الذي أرسلناه. إن لم يصلك البريد يمكنك طلب رابط جديد.
  </p>

  @if (session('status') == 'verification-link-sent')
    <div class="alert alert-success small mb-3">تم إرسال رابط تحقق جديد إلى بريدك.</div>
  @endif

  <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-3">
    <form method="POST" action="{{ route('verification.send') }}">
      @csrf
      <button type="submit" class="btn btn-primary">إعادة إرسال رابط التحقق</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-link">تسجيل الخروج</button>
    </form>
  </div>
</x-guest-layout>
