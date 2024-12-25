// Menyimpan referensi ke elemen dengan class "theme-toggler" yang bertindak sebagai tombol pengubah tema
const themeToggler = document.querySelector(".theme-toggler");
// Menyimpan referensi ke elemen <body> yang akan diubah temanya
const body = document.body;

// Mengecek apakah sebelumnya ada preferensi tema yang disimpan di localStorage
if (localStorage.getItem("theme") === "dark") {
  // Jika tema yang disimpan adalah "dark", maka hapus class "light-theme" dan tambahkan "dark-theme"
  body.classList.remove("light-theme");
  body.classList.add("dark-theme");

  // Mengubah tampilan indikator tema pada tombol, menandakan bahwa tema gelap aktif
  themeToggler.querySelector("span:nth-child(1)").classList.remove("active"); // Menghilangkan tanda aktif pada ikon tema terang
  themeToggler.querySelector("span:nth-child(2)").classList.add("active"); // Menambahkan tanda aktif pada ikon tema gelap
} else {
  // Jika tema yang disimpan bukan "dark", berarti tema terang (light) yang aktif
  body.classList.add("light-theme");
  body.classList.remove("dark-theme");

  // Mengubah tampilan indikator tema pada tombol, menandakan bahwa tema terang aktif
  themeToggler.querySelector("span:nth-child(1)").classList.add("active"); // Menambahkan tanda aktif pada ikon tema terang
  themeToggler.querySelector("span:nth-child(2)").classList.remove("active"); // Menghilangkan tanda aktif pada ikon tema gelap
}

// Menambahkan event listener pada tombol untuk mengganti tema ketika diklik
themeToggler.onclick = () => {
  // Mengubah class pada <body> untuk menukar antara tema gelap dan terang
  body.classList.toggle("dark-theme");
  body.classList.toggle("light-theme");

  // Mengecek apakah tema gelap sekarang aktif
  if (body.classList.contains("dark-theme")) {
    // Jika tema gelap yang aktif, simpan preferensi "dark" di localStorage
    localStorage.setItem("theme", "dark");
    // Mengubah tampilan indikator tombol
    themeToggler.querySelector("span:nth-child(1)").classList.remove("active"); // Menandakan ikon tema terang tidak aktif
    themeToggler.querySelector("span:nth-child(2)").classList.add("active"); // Menandakan ikon tema gelap aktif
  } else {
    // Jika tema terang yang aktif, simpan preferensi "light" di localStorage
    localStorage.setItem("theme", "light");
    // Mengubah tampilan indikator tombol
    themeToggler.querySelector("span:nth-child(1)").classList.add("active"); // Menandakan ikon tema terang aktif
    themeToggler.querySelector("span:nth-child(2)").classList.remove("active"); // Menandakan ikon tema gelap tidak aktif
  }
};
