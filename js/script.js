document.addEventListener('DOMContentLoaded', function() {
    const aramaButon = document.getElementById('aramaButon');
    const aramaInput = document.getElementById('aramaInput');
    const sonucAlani = document.getElementById('sonucAlani');
    const onerilerAlani = document.getElementById('onerilerAlani');
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');

    // Kullanıcı scroll yaptıkça butonu göster/gizle
    let scrollTimeout;
    window.onscroll = function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                scrollToTopBtn.style.display = "block";
            } else {
                scrollToTopBtn.style.display = "none";
            }
        }, 100); // 100 milisaniye gecikme ekle
    };

    // Butona tıklandığında en üste kaydır
    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth' // Yumuşak kaydırma animasyonu
        });
    });

    aramaButon.addEventListener('click', function() { // Butona tıklanınca çalış
        performSearch();
    });

    aramaInput.addEventListener('keypress', function(event) { // Enter tuşuna basınca çalış
        if (event.key === 'Enter') {
            performSearch();
        }
    });
    

    //Arama input'u için input event listener (otomatik tamamlama) 
    let timeout = null; // Gecikme için değişken
    aramaInput.addEventListener('input', function() {
        clearTimeout(timeout); // Önceki zamanlayıcıyı temizle

        const arananKelime = aramaInput.value.trim();

        if (arananKelime.length < 2) { // En az 2 karakter yazılınca öneri getir
            onerilerAlani.innerHTML = '';
            onerilerAlani.classList.remove('aktif');
            sonucAlani.innerHTML = ''; // Sonuç içeriğini de temizle
            sonucAlani.style.display = 'none';
            return;
        }

        // Kullanıcı yazmayı bıraktıktan sonra 300ms bekle, sonra önerileri getir
        timeout = setTimeout(() => {
            fetch('oneriler_getir.php?kelime=' + encodeURIComponent(arananKelime))
                .then(response => response.json())
                .then(data => {
                    onerilerAlani.innerHTML = ''; // Önceki önerileri temizle
                    if (data.length > 0) {
                        data.forEach(onerilenKelime => {
                            const oneriDiv = document.createElement('div');
                            oneriDiv.textContent = onerilenKelime;
                            oneriDiv.addEventListener('click', function() {
                                aramaInput.value = onerilenKelime; // Öneriye tıklayınca input'a yaz
                                onerilerAlani.innerHTML = ''; // Önerileri gizle
                                onerilerAlani.classList.remove('aktif');
                                performSearch(); // Otomatik olarak aramayı yap
                            });
                            onerilerAlani.appendChild(oneriDiv);
                        });
                        onerilerAlani.classList.add('aktif'); // Öneri kutusunu göster
                    } else {
                        onerilerAlani.classList.remove('aktif'); // Öneri kutusunu gizle
                    }
                })
                .catch(error => {
                    console.error('Öneri getirme sırasında hata oluştu:', error);
                    onerilerAlani.classList.remove('aktif');
                });
        }, 300); // 300 milisaniye gecikme
    });

    //Input dışına tıklayınca öneri kutusunu gizle
    document.addEventListener('click', function(event) {
        if (!aramaInput.contains(event.target) && !onerilerAlani.contains(event.target)) {
            onerilerAlani.innerHTML = '';
            onerilerAlani.classList.remove('aktif');
        }
    });

    function performSearch() {
        const arananKelime = aramaInput.value.trim();
        onerilerAlani.innerHTML = '';
        onerilerAlani.classList.remove('aktif');

        // Her arama başlangıcında sonucAlani'nin stilini kaldır (beyazlığı ve çerçeveyi)
        sonucAlani.innerHTML = ''; // Önceki sonuçları temizle
        sonucAlani.style.display = 'none';

        if (arananKelime === "") {
            sonucAlani.innerHTML = '<p style="color: red;">Lütfen aranacak bir kelime girin.</p>';
            sonucAlani.style.display = 'block'; // Mesajı göstermek için görünür yap
            return;
        }

        // AJAX isteği ile PHP dosyasına kelimeyi gönder
        // PHP dosyamızın adı 'arama.php' idi ve aynı klasörde olmalı
        fetch('arama.php?kelime=' + encodeURIComponent(arananKelime))
            .then(response => {
                // Sunucudan gelen yanıtı kontrol et
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json(); // Yanıtı JSON olarak ayrıştır
            })
            .then(data => {
                // Sonuclar geldiğinde sonucAlani'nı görünür yap
                sonucAlani.style.display = 'block';

                if (data.length > 0) {
                    data.forEach(item => {
                        const kelimeDiv = document.createElement('div');
                        kelimeDiv.classList.add('kelime-entry'); // CSS için class ekle
                        kelimeDiv.innerHTML = `
                            <h2>${item.name}</h2>
                            <p>${item.description}</p>
                        `;
                        sonucAlani.appendChild(kelimeDiv);
                    });
                } else {
                    sonucAlani.innerHTML = `<p><strong>"${arananKelime}"</strong> kelimesi sözlükte bulunamadı.</p>`;
                }
            })
            .catch(error => {
                console.error('Arama sırasında bir hata oluştu:', error);
                sonucAlani.innerHTML = '<p style="color: red;">Arama sırasında bir hata oluştu. Lütfen konsolu kontrol edin.</p>';
                sonucAlani.style.display = 'block'; // Hata durumunda da görünür yap
            });

        // Arama kutusunu temizle (isteğe bağlı)
        aramaInput.value = '';
    }
});
