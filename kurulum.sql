-- ============================================================
--  FIGHT ACADEMY — Veritabanı Kurulum Dosyası
--  cPanel > phpMyAdmin'de bu dosyayı içe aktar (Import)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `fight_academy` CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE `fight_academy`;

-- Üyeler tablosu
CREATE TABLE IF NOT EXISTS `uyeler` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `tc`            VARCHAR(11)     DEFAULT NULL,
    `ad_soyad`      VARCHAR(100)    NOT NULL,
    `telefon`       VARCHAR(20)     DEFAULT NULL,
    `dogum_tarihi`  DATE            DEFAULT NULL,
    `brans`         ENUM('Kickboks','Boks','Taekwondo','PT') NOT NULL DEFAULT 'Kickboks',
    `kayit_tarihi`  DATE            NOT NULL,
    `sure`          TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Ay cinsinden süre',
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_brans` (`brans`),
    INDEX `idx_kayit` (`kayit_tarihi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Ödemeler tablosu
CREATE TABLE IF NOT EXISTS `odemeler` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `uye_id`        INT UNSIGNED    NOT NULL,
    `tutar`         DECIMAL(10,2)   NOT NULL,
    `not`           VARCHAR(255)    DEFAULT NULL,
    `tarih`         DATE            NOT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_uye` (`uye_id`),
    INDEX `idx_tarih` (`tarih`),
    CONSTRAINT `fk_odeme_uye` FOREIGN KEY (`uye_id`) REFERENCES `uyeler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Örnek veriler (isteğe bağlı, silmek istersen bu bloğu kaldır)
INSERT INTO `uyeler` (`tc`, `ad_soyad`, `telefon`, `dogum_tarihi`, `brans`, `kayit_tarihi`, `sure`) VALUES
('12345678901', 'Ahmet Yılmaz',   '05321234567', '1995-03-15', 'Kickboks',  CURDATE(), 3),
('98765432109', 'Mehmet Kara',    '05449876543', '1990-07-22', 'Boks',      DATE_SUB(CURDATE(), INTERVAL 5 MONTH), 6),
('11122233344', 'Fatma Demir',    '05559001122', '2000-11-05', 'Taekwondo', DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 1),
('55566677788', 'Ali Çelik',      '05361112233', '1988-01-30', 'PT',        CURDATE(), 3),
('22233344455', 'Zeynep Arslan',  '05271234567', '1998-06-10', 'Kickboks',  DATE_SUB(CURDATE(), INTERVAL 2 DAY), 1);
