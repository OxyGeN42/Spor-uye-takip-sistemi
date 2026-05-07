<?php
require_once __DIR__ . '/includes/config.php';

// AJAX istekleri için session kontrolü
if (empty($_SESSION['logged_in'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Oturum süresi doldu, sayfayı yenileyin.']);
    exit;
}

// Action hem POST hem GET'ten gelebilir
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Her yanıttan önce JSON header garantile
header('Content-Type: application/json; charset=utf-8');

// ── ÜYELER ──────────────────────────────────────────────────

if ($action === 'uye_listesi') {
    $q     = '%' . trim($_GET['q'] ?? '') . '%';
    $durum = $_GET['durum'] ?? '';
    $brans = $_GET['brans'] ?? '';

    $sql  = 'SELECT * FROM uyeler WHERE (ad_soyad LIKE ? OR tc LIKE ? OR telefon LIKE ?)';
    $args = [$q, $q, $q];

    if ($brans) { $sql .= ' AND brans = ?'; $args[] = $brans; }
    $sql .= ' ORDER BY ad_soyad';

    $rows = db()->prepare($sql);
    $rows->execute($args);
    $uyeler = $rows->fetchAll();

    // Durum hesapla
    $today = new DateTime(date('Y-m-d'));
    foreach ($uyeler as &$m) {
        $kayit = new DateTime($m['kayit_tarihi']);
        $bitis = (clone $kayit)->modify("+{$m['sure']} months");
        $m['bitis_tarihi'] = $bitis->format('Y-m-d');
        $fark = (int) $today->diff($bitis)->format('%r%a');
        if ($fark < 0)      $m['durum'] = 'BİTTİ';
        elseif ($fark <= 5) $m['durum'] = 'AZ KALDI';
        else                $m['durum'] = 'AKTİF';
        $m['fark'] = $fark;
    }
    unset($m);

    // Durum filtresi
    if ($durum) $uyeler = array_values(array_filter($uyeler, fn($u) => $u['durum'] === $durum));

    jsonResponse(['ok' => true, 'data' => $uyeler]);
}

if ($action === 'uye_ekle' || $action === 'uye_guncelle') {
    $id       = intval($_POST['id'] ?? 0);
    $tc       = trim($_POST['tc'] ?? '');
    $ad_soyad = trim($_POST['ad_soyad'] ?? '');
    $telefon  = trim($_POST['telefon'] ?? '');
    $dogum    = $_POST['dogum_tarihi'] ?? null ?: null;
    $brans    = $_POST['brans'] ?? 'Kickboks';
    $kayit    = $_POST['kayit_tarihi'] ?? date('Y-m-d');
    $sure     = max(1, intval($_POST['sure'] ?? 1));

    if (!$ad_soyad) jsonResponse(['ok' => false, 'msg' => 'Ad Soyad zorunlu!'], 400);

    $allowed = ['Kickboks','Boks','Taekwondo','PT'];
    if (!in_array($brans, $allowed)) $brans = 'Kickboks';

    if ($action === 'uye_ekle') {
        $stmt = db()->prepare('INSERT INTO uyeler (tc,ad_soyad,telefon,dogum_tarihi,brans,kayit_tarihi,sure) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$tc,$ad_soyad,$telefon,$dogum,$brans,$kayit,$sure]);
        jsonResponse(['ok' => true, 'id' => db()->lastInsertId()]);
    } else {
        $stmt = db()->prepare('UPDATE uyeler SET tc=?,ad_soyad=?,telefon=?,dogum_tarihi=?,brans=?,kayit_tarihi=?,sure=? WHERE id=?');
        $stmt->execute([$tc,$ad_soyad,$telefon,$dogum,$brans,$kayit,$sure,$id]);
        jsonResponse(['ok' => true]);
    }
}

if ($action === 'uye_uzat') {
    $id  = intval($_POST['id'] ?? 0);
    $ay  = intval($_POST['ay'] ?? 1);
    $row = db()->prepare('SELECT sure, kayit_tarihi FROM uyeler WHERE id=?');
    $row->execute([$id]);
    $m   = $row->fetch();
    if (!$m) jsonResponse(['ok' => false, 'msg' => 'Üye bulunamadı'], 404);

    $today  = date('Y-m-d');
    $kayit  = new DateTime($m['kayit_tarihi']);
    $bitis  = (clone $kayit)->modify("+{$m['sure']} months");
    $bugun  = new DateTime($today);

    if ($bitis < $bugun) {
        // Bittiyse bugünden yeniden başlat
        db()->prepare('UPDATE uyeler SET kayit_tarihi=?, sure=? WHERE id=?')->execute([$today, $ay, $id]);
    } else {
        db()->prepare('UPDATE uyeler SET sure=sure+? WHERE id=?')->execute([$ay, $id]);
    }
    jsonResponse(['ok' => true]);
}

if ($action === 'uye_sil') {
    $id = intval($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM uyeler WHERE id=?')->execute([$id]);
    jsonResponse(['ok' => true]);
}

// ── ÖDEMELER ────────────────────────────────────────────────

if ($action === 'odeme_listesi') {
    $stmt = db()->query(
        'SELECT o.*, u.ad_soyad, u.brans FROM odemeler o
         LEFT JOIN uyeler u ON u.id = o.uye_id
         ORDER BY o.tarih DESC, o.id DESC'
    );
    jsonResponse(['ok' => true, 'data' => $stmt->fetchAll()]);
}

if ($action === 'odeme_ekle') {
    $uye_id = intval($_POST['uye_id'] ?? 0);
    $tutar  = floatval($_POST['tutar'] ?? 0);
    $not    = trim($_POST['not'] ?? '');
    $tarih  = $_POST['tarih'] ?? date('Y-m-d');

    if (!$uye_id || $tutar <= 0) jsonResponse(['ok' => false, 'msg' => 'Geçersiz veri'], 400);

    db()->prepare('INSERT INTO odemeler (uye_id,tutar,`not`,tarih) VALUES (?,?,?,?)')->execute([$uye_id,$tutar,$not,$tarih]);
    jsonResponse(['ok' => true]);
}

if ($action === 'odeme_sil') {
    $id = intval($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM odemeler WHERE id=?')->execute([$id]);
    jsonResponse(['ok' => true]);
}

// ── İSTATİSTİKLER ───────────────────────────────────────────

if ($action === 'istatistik') {
    $today = date('Y-m-d');
    $month = date('Y-m');

    $rows = db()->query('SELECT kayit_tarihi, sure FROM uyeler')->fetchAll();
    $aktif = $azKaldi = $bitti = 0;
    $bugun = new DateTime($today);
    foreach ($rows as $r) {
        $kayit = new DateTime($r['kayit_tarihi']);
        $bitis = (clone $kayit)->modify("+{$r['sure']} months");
        $fark  = (int) $bugun->diff($bitis)->format('%r%a');
        if ($fark < 0)      $bitti++;
        elseif ($fark <= 5) $azKaldi++;
        else                $aktif++;
    }

    $totalCiro = db()->query('SELECT COALESCE(SUM(tutar),0) FROM odemeler')->fetchColumn();
    $aylikCiro = db()->query("SELECT COALESCE(SUM(tutar),0) FROM odemeler WHERE DATE_FORMAT(tarih,'%Y-%m')='$month'")->fetchColumn();

    $branslar = db()->query("SELECT brans, COUNT(*) as cnt FROM uyeler GROUP BY brans")->fetchAll();

    jsonResponse([
        'ok'        => true,
        'toplam'    => count($rows),
        'aktif'     => $aktif,
        'az_kaldi'  => $azKaldi,
        'bitti'     => $bitti,
        'total_ciro'=> floatval($totalCiro),
        'aylik_ciro'=> floatval($aylikCiro),
        'branslar'  => $branslar,
    ]);
}

jsonResponse(['ok' => false, 'msg' => 'Geçersiz işlem'], 400);
