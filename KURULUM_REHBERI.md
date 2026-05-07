# 🥊 ÖZGÜR ŞAŞMAZ FIGHT ACADEMY — Kurulum Rehberi

## Dosya Yapısı
```
fight_academy/
├── index.php          ← Ana panel (giriş yaptıktan sonra açılır)
├── login.php          ← Giriş sayfası
├── logout.php         ← Çıkış
├── api.php            ← Tüm veri işlemleri (AJAX)
├── sifre_uret.php     ← Şifre hash oluşturucu (kullandıktan sonra SİL!)
├── kurulum.sql        ← Veritabanı kurulum dosyası
└── includes/
    └── config.php     ← Ayarlar (DB bilgileri, admin şifresi)
```

---

## ✅ ADIM ADIM KURULUM

### 1. Hosting Al
**Önerilen:** Hostinger, Natro, Turhost (PHP 8+ ve MySQL desteği)
- cPanel erişimi olan herhangi bir hosting çalışır.

---

### 2. Veritabanı Oluştur
1. cPanel'e gir → **MySQL Databases** bölümüne git
2. Yeni bir veritabanı oluştur → adını not et (örn: `kullanici_fightacademy`)
3. Yeni bir kullanıcı oluştur → güçlü bir şifre ver → not et
4. Kullanıcıyı veritabanına ekle → **"All Privileges"** ver

---

### 3. SQL Tabloları Oluştur
1. cPanel → **phpMyAdmin** → Sol panelden oluşturduğun DB'yi seç
2. Üst menüden **Import** sekmesine tıkla
3. `kurulum.sql` dosyasını yükle → **Go** butonuna bas
4. Tablolar oluşturuldu! ✅

---

### 4. config.php Güncelle
`includes/config.php` dosyasını aç, şu satırları düzenle:

```php
define('DB_HOST', 'localhost');         // Genellikle localhost
define('DB_NAME', 'kullanici_fightacademy'); // Adım 2'deki DB adı
define('DB_USER', 'kullanici_dbuser');       // Adım 2'deki kullanıcı adı
define('DB_PASS', 'guclu_sifren');           // Adım 2'deki şifre

define('ADMIN_USER', 'admin');          // Giriş için kullanıcı adı
define('ADMIN_PASS', '...hash...');     // Adım 5'te üretilecek
```

---

### 5. Admin Şifresini Belirle
1. Dosyaları hostinge yükle (önce config.php hariç)
2. `sifre_uret.php` dosyasını tarayıcıda aç:
   `https://sitean.com/fight_academy/sifre_uret.php`
3. İstediğin şifreyi yaz → **Hash Üret** butonuna bas
4. Çıkan hash'i kopyala
5. `config.php` → `ADMIN_PASS` satırına yapıştır
6. **`sifre_uret.php` dosyasını sunucudan SİL!** (güvenlik)

---

### 6. Dosyaları Hostinge Yükle
- cPanel → **File Manager** → `public_html` klasörüne gir
- `fight_academy` klasörünü yükle (ya da doğrudan `public_html` içine)
- **Upload** ile tüm dosyaları yükle

**Ya da:** FileZilla gibi FTP programı kullan.

---

### 7. Test Et
`https://sitean.com/fight_academy/login.php` adresini aç.
- Kullanıcı adı: `config.php`'de yazdığın `ADMIN_USER`
- Şifre: `sifre_uret.php` ile belirlediğin şifre

---

## 🔒 Güvenlik Notları
- `sifre_uret.php` dosyasını kurulumdan sonra MUTLAKA sil
- `includes/` klasörünü `.htaccess` ile koru:
  ```apache
  # includes/.htaccess
  Deny from all
  ```
- Güçlü bir şifre kullan (en az 12 karakter)
- HTTPS sertifikası al (Hostinger dahil ücretsiz veriyor)

---

## ❓ Sık Sorulan Sorular

**"Veritabanı bağlantısı başarısız" hatası alıyorum**
→ config.php'deki DB_HOST, DB_NAME, DB_USER, DB_PASS bilgilerini kontrol et.

**cPanel'de DB adı neden farklı görünüyor?**
→ Hostingler genellikle DB adının başına kullanıcı adını ekler.
Örn: `admin` + `fightacademy` → `admin_fightacademy` olur. Bu tam adı config.php'ye yaz.

**Şifremi unuttum**
→ `sifre_uret.php`'yi tekrar yükle, yeni hash üret, config.php'yi güncelle, sonra sil.
