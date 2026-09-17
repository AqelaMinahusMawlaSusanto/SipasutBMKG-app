// Tombol mata/kunci untuk menampilkan & menyembunyikan password
document.querySelectorAll('[data-toggle="password"]').forEach(function (tombol) {
  tombol.addEventListener('click', function () {
    var input = tombol.closest('.control').querySelector('input');
    var tampil = input.type === 'password';
    input.type = tampil ? 'text' : 'password';
    tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
    input.focus();
  });
});
