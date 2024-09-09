<?php

// cURL ile siteden veri çekme
function getKuranData($sureNo) {
    $url = "https://ornek-kuran-meali-sitesi.com/sure/" . $sureNo;  // Örnek URL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;  // Çekilen HTML verisi
}

// HTML içeriğini ayetler şeklinde ayrıştırma
function parseAyetler($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    
    // Ayetleri içeren div'leri bul (örneğin, 'ayet' class'ı ile işaretlenmiş olabilir)
    $nodes = $xpath->query("//div[@class='ayet']");
    
    $ayetler = [];
    foreach ($nodes as $node) {
        $ayetler[] = trim($node->textContent);
    }
    
    return $ayetler;
}

// Anahtar kelime ile ayetlerde arama
function searchAyetByKeyword($keyword, $ayetler) {
    $results = [];
    foreach ($ayetler as $ayet) {
        if (strpos($ayet, $keyword) !== false) {
            $results[] = $ayet;
        }
    }
    return $results;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Kur'an-ı Kerim Arama Motoru</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Kur'an-ı Kerim'de Kelime Arama</h2>
        <form method="POST" class="d-flex justify-content-center mt-4">
            <input type="text" name="keyword" class="form-control w-50" placeholder="Aranacak kelimeyi yazın" required>
            <button type="submit" name="search" class="btn btn-primary ml-2">Ara</button>
        </form>

        <div class="mt-5">
            <?php
            // Arama yapıldığında
            if (isset($_POST['search'])) {
                $keyword = $_POST['keyword'];
                $sureNo = 1;  // Örnek olarak ilk sureyi alıyoruz, bu dinamik olabilir
                $html = getKuranData($sureNo);  // Siteden veri çek
                $ayetler = parseAyetler($html);  // HTML'den ayetleri ayrıştır
                $results = searchAyetByKeyword($keyword, $ayetler);  // Kelimeye göre arama yap

                if (!empty($results)) {
                    echo "<h4>Arama Sonuçları:</h4>";
                    foreach ($results as $result) {
                        echo "<p>$result</p>";  // Arama sonuçlarını ekrana bas
                    }
                } else {
                    echo "<p>Sonuç bulunamadı.</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>