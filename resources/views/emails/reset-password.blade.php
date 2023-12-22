<x-mail::message>
# Ubah Kata Sandi

Kami mendengar bahwa Anda kehilangan kata sandi {{ config('app.name') }} Anda. Maaf tentang itu!
Tapi jangan khawatir! Anda dapat menggunakan tombol berikut untuk mengatur ulang kata sandi Anda:

<x-mail::button :url="$url">
Ubah Kata Sandi
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
