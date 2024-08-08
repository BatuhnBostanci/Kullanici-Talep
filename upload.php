<?php
include 'config.php'; // Veritabanı bağlantısını dahil et

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department = $_POST['department'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // Dosya yükleme işlemi
    $file_path = NULL;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "uploads/"; // Dosyaların yükleneceği klasör
        $file_path = $target_dir . basename($_FILES["file"]["name"]);

        // Dosyanın var olup olmadığını kontrol edin
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Klasör oluştur
        }

        // Dosyayı belirtilen klasöre yükleyin
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $file_path)) {
            // Dosya başarıyla yüklendi
            $file_path = htmlspecialchars($file_path);
        } else {
            // Yükleme hatası
            $error_code = $_FILES['file']['error'];
            $error_message = "Dosya yükleme sırasında bir hata oluştu. Hata kodu: " . $error_code;

            switch ($error_code) {
                case UPLOAD_ERR_INI_SIZE:
                    $error_message .= " Dosya, upload_max_filesize direktifini aşıyor.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message .= " Dosya, HTML formundaki MAX_FILE_SIZE direktifini aşıyor.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message .= " Dosya kısmen yüklendi.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message .= " Hiçbir dosya yüklenmedi.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message .= " Geçici klasör eksik.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message .= " Disk'e yazma hatası.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_message .= " PHP uzantısı dosya yüklemeyi durdurdu.";
                    break;
                default:
                    $error_message .= " Bilinmeyen bir hata oluştu.";
                    break;
            }

            echo json_encode(['success' => false, 'message' => $error_message]);
            exit();
        }
    }

    // Veritabanına kaydet
    $stmt = $conn->prepare("INSERT INTO hata_talepleri (department, subject, description, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $department, $subject, $description, $file_path);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Kayıt başarıyla oluşturuldu']);
    } else {
        echo json_encode(['success' => false, 'message' => "Hata: " . $stmt->error]);
    }

    // Bağlantıyı kapat
    $stmt->close();
    $conn->close();
}
?>
