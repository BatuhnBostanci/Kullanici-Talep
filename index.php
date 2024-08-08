<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hata Talebi Oluştur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 150px;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group input[type="file"] {
            border: none;
        }
        .form-group .required {
            color: red;
        }
        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        textarea {
            resize: none;
        }
        #image-preview {
            display: none;
            margin-top: 15px;
        }
        #image-preview img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        #remove-image {
            display: none;
            margin-top: 10px;
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://www.tgbd.org.tr/content/upload/companies/teknoparkist-slogansiz-lo-20191101094440.jpg" alt="Teknopark İstanbul">
            <h1>HATA TALEBİ OLUŞTUR</h1>
        </div>
		
        <form id="requestForm" action="upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="department">Sorumlu Birim<span class="required">*</span>:</label>
                <select id="department" name="department" required>
                    <option value="">Lütfen birim seçiniz</option>
                    <option value="eBA">eBA</option>
                    <option value="İnternet">İnternet</option>
                    <option value="Help Desk">Help Desk</option>
                    <option value="Bölüm-4">Network</option>
                </select>
            </div>
            <div class="form-group">
                <label for="subject">Konu<span class="required">*</span>:</label>
                <textarea id="subject" name="subject" placeholder="Lütfen talebinizin konusunu yazınız." rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label for="description">Açıklama<span class="required">*</span>:</label>
                <textarea id="description" name="description" placeholder="Lütfen talebinizi açıklayınız." rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="file">Görsel (İsteğe Bağlı):</label>
                <input type="file" id="file" name="file" accept="image/png, image/jpeg">
                <div id="image-preview">
                    <img id="image-element" src="" alt="Seçilen Görsel">
                </div>
                <div id="remove-image">Görseli Kaldır</div>
            </div>
            <button type="submit" class="submit-button">Gönder</button>
        </form>
    </div>

    <script>
        const fileInput = document.getElementById('file');
        const imagePreview = document.getElementById('image-preview');
        const imageElement = document.getElementById('image-element');
        const removeImage = document.getElementById('remove-image');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileType = file.type;
                const validImageTypes = ["image/jpeg", "image/png"];
                if (!validImageTypes.includes(fileType)) {
                    alert("Yalnızca PNG ve JPG formatında dosyalar kabul edilir.");
                    fileInput.value = '';
                    imageElement.src = '';
                    imagePreview.style.display = 'none';
                    removeImage.style.display = 'none';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    imageElement.src = e.target.result;
                    imagePreview.style.display = 'block';
                    removeImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        removeImage.addEventListener('click', function() {
            fileInput.value = '';
            imageElement.src = '';
            imagePreview.style.display = 'none';
            removeImage.style.display = 'none';
        });

        document.getElementById('requestForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Form validation
            var department = document.getElementById('department').value;
            var subject = document.getElementById('subject').value;
            var description = document.getElementById('description').value;

            if(department && subject && description) {
                // Prepare form data
                const formData = new FormData();
                formData.append('department', department);
                formData.append('subject', subject);
                formData.append('description', description);
                if (fileInput.files[0]) {
                    formData.append('file', fileInput.files[0]);
                }

                // Send data to server
                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Talebiniz başarıyla gönderildi!');
                        document.getElementById('requestForm').reset();
                        imagePreview.style.display = 'none';
                        removeImage.style.display = 'none';
                    } else {
                        alert('Bir hata oluştu: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert('Bir hata oluştu. Lütfen tekrar deneyiniz.');
                });
            } else {
                alert('Lütfen gerekli tüm alanları doldurunuz.');
            }
        });

        // Allow paste image
        document.addEventListener('paste', function(event) {
            const items = (event.clipboardData || event.originalEvent.clipboardData).items;
            for (let index in items) {
                const item = items[index];
                if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
                    const file = item.getAsFile();
                    const fileType = file.type;
                    const validImageTypes = ["image/jpeg", "image/png"];
                    if (!validImageTypes.includes(fileType)) {
                        alert("Yalnızca PNG ve JPG formatında dosyalar kabul edilir.");
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageElement.src = e.target.result;
                        imagePreview.style.display = 'block';
                        removeImage.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
</body>
</html>
