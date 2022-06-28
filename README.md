# Kuliah Web 2022
Ini merupakan respository aplikasi web PHP backend. Project ini merupakan aplikasi API (Application Programming Interface) untuk digunakan dari aplikasi API consumer. Untuk menggunakan web ini juga bisa menggunakan API client seperti Postman

## Live demo web app
Frontend application [https//kuliah-web-2022.pages.dev](https//kuliah-web-2022.pages.dev)

Backend application [https://kuliahweb.riaudevops.id](https://kuliahweb.riaudevops.id)

## Source code
Frontend menggunakan VueJS [Sourcecode frontend](https://github.com/pizaini/kuliah-web-2022)

Backend menggunakan PHP [Sourcecode backend](https://github.com/pizaini/kuliah-web-backend-2022)

## Cara menggunakan
1. Clone project ini
2. Pastikan PHP Composer telah terinstall dan jalankan command `composer install`
3. Import database.sql
4. Akses app melalui API client application misalnya Postman

## Apa yang ada diproject ini
1. Simple URL Routing
    PHP routing sederhana menggunakan include file. Sehingga URL yang dihasilkan menjadi `/buku/index`, `/buku/all`. Lihat pengaturan file .htaccess
2. Autentikasi
   Belum diimplementasikan
3. Database
   Data diambil dari database
4. Cache
    Belum diimplementasikan
5. Composer
    Menggunakan manajemen library pihak ketiga
6. Tanpa framework
    Secara garis besar, project ini menggunakan core function PHP secara langsung seperti PHP PDO, query database, autentikasi dan hal lainnya.

## API endpoints
Berikut adalah API endpoint dari project ini

| Endpoint                   | Method | Keterangan            |
|----------------------------|--------|-----------------------|
| /buku atau /buku/index     | GET    | List buku             |
| /buku/create               | POST   | Add buku              |
| /buku/update               | PATCH  | Update buku by ISBN   |
| /buku/delete               | DELETE | Delete buku by ISBN   |
| /kategori atau /kategori/index | GET    | List kategori         |
| /kategori/create               | POST   | Add kategori          |
| /kategori/update               | PATCH  | Update kategori by ID |
| /kategori/delete               | DELETE | Delete kategori by ID |