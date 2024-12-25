# Sistem Manajemen Aplikasi Berbasis Website

Aplikasi ini adalah sistem manajemen berbasis website yang dikembangkan menggunakan PHP native. Aplikasi ini memiliki fitur login, register, dashboard admin, profil user, top-up saldo, transfer antar pengguna, serta fitur chat antar pengguna. 

## 📑 Fitur Utama

1. **Autentikasi Pengguna**
   - Login untuk Admin dan User.
   - Register pengguna baru.

2. **Dashboard Admin**
   - Admin langsung diarahkan ke dashboard setelah login.
   - Admin dapat memonitor semua aktivitas pengguna, seperti transaksi top-up, transfer.

3. **Profil Pengguna**
   - User langsung diarahkan ke halaman profil mereka setelah login.
   - Menampilkan informasi saldo, riwayat transaksi, dan detail akun.

4. **Top-Up Saldo**
   - Pengguna dapat melakukan top-up saldo mereka melalui halaman yang disediakan.

5. **Transfer Saldo**
   - Transfer saldo antar pengguna dengan validasi akun penerima.

6. **Chat Antar Pengguna**
   - Fitur chat real-time untuk komunikasi antar pengguna.

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP Native
- **Frontend:** HTML, CSS, dan JavaScript
- **Database:** MySQL
- **Library:** Bootsrap

## 🗂️ Struktur Direktori

├── auth/
  └──signup.php
├── chat/
  └──chat.php
├── images/
  └──# Gambar yang digunakan di aplikasi
├── js/
  └──# File JavaScript untuk fungsionalitas
├── style/
  └──# File CSS untuk styling
├── topup/
  └──topup.php
└──index.php # Halaman Utama



## 🔧 Instalasi dan Penggunaan

1. **Clone repositori ini**:
   ```bash
   git clone https://github.com/mrnurihsann/eterno.git

2. **Konfigurasi Database**:
   - Impor file database SQL yang disediakan (database.sql) ke MySQL.
   - Ubah konfigurasi koneksi database di config/database.php sesuai pengaturan lokal Anda.

3. **Jalankan di Server Lokal**:
   - Pastikan Anda memiliki server lokal seperti XAMPP atau WAMP.
   - Letakkan file proyek di dalam folder htdocs atau direktori server lokal Anda.
   - Akses aplikasi melalui http://localhost/namaprojek.

🚀 **Fitur yang Akan Datang**
   - Implementasi fitur notifikasi transaksi.
   - Sistem laporan untuk admin.
   - Desain antarmuka yang lebih responsif.
   - Log chat dashboard admin

🤝 Kontribusi
Kami menerima kontribusi dari komunitas. Jika Anda memiliki ide atau menemukan bug, silakan buat issue atau kirimkan pull request.


Form Login
![image](https://github.com/user-attachments/assets/87d54e3d-aa0c-457a-a279-478a21da36c5)

Form Register
![image](https://github.com/user-attachments/assets/96b51349-0575-4c66-aef0-31eef8e4ae97)

Dashboard admin
![image](https://github.com/user-attachments/assets/a9b5da6c-93b8-441e-bf0c-dd548512d514)
![image](https://github.com/user-attachments/assets/936ad391-4cc1-4d73-99a3-291dc8568987)
![image](https://github.com/user-attachments/assets/5c6012fb-f270-4134-bf36-4a0e8e68d728)

Profile user
![image](https://github.com/user-attachments/assets/fbb99a7a-888c-4dd3-92a6-10295798d36f)

Fitur chat
![image](https://github.com/user-attachments/assets/e2206ea4-a982-48da-85c3-d299f85753f2)

Fitur Topup
![image](https://github.com/user-attachments/assets/3e7daac1-ae3f-4d24-af68-3df6a4020bcb)

Fitur Transfer
![image](https://github.com/user-attachments/assets/f829c192-2a37-4a67-b6b4-ec9241399ad4)
