<?php
require_once 'config.php';
require_once 'utils/MessageFormatter.php';
require_once 'utils/SessionManager.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

SessionManager::init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = $_POST['prompt'] ?? '';
    
    if (empty($userMessage)) {
        echo json_encode(['error' => 'No message provided']);
        exit();
    }

    SessionManager::addMessage('user', htmlspecialchars($userMessage));

    $knowledge_base = json_encode([
        ["Subjek" => "Statistik Demografi dan Sosial", "Kategori" => "Kependudukan dan Migrasi", "Keterangan" => "mencakup pekerjaan dalam statistik populasi dan demografis, topik seperti demografi, statistik vital, struktur dan pertumbuhan populasi, proyeksi demografis, keluarga dan rumah tangga (perkawinan, perceraian, ukuran rumah tangga), migrasi, pengungsi, dan pencari suaka.", "Link" => "https://siakkab.bps.go.id/id/statistics-table?subject=519"],
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Tenaga Kerja",
        "Keterangan": "mencakup statistik tentang angkatan kerja, pasar tenaga kerja, lapangan kerja dan pengangguran; topik yang lebih rinci meliputi populasi yang aktif secara ekonomi, kondisi tenaga kerja, kesehatan dan keselamatan di tempat kerja (kecelakaan di tempat kerja, cedera dan penyakit akibat kerja, masalah kesehatan terkait pekerjaan), waktu kerja dan kondisi kerja lainnya, pemogokan dan larangan bekerja, lowongan pekerjaan, penciptaan lapangan kerja.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=520"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Pendidikan",
        "Keterangan": "termasuk partisipasi pendidikan, buta huruf, lembaga dan sistem pendidikan, sumber daya manusia dan keuangan yang diinvestasikan dalam pendidikan, pembelajaran seumur hidup, pelatihan kejuruan dan pembelajaran orang dewasa, dampak pendidikan, penilaian kinerja siswa, dll.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=521"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Kesehatan",
        "Keterangan": "mencakup aktivitas statistik terkait kesehatan dan kematian, termasuk topik seperti harapan hidup, status kesehatan, kesehatan dan keselamatan, penentu kesehatan (termasuk gaya hidup, nutrisi, merokok, penyalahgunaan alkohol), sumber daya dan pengeluaran kesehatan, sistem perawatan kesehatan, morbiditas dan mortalitas (termasuk kematian bayi dan anak), masuk rumah sakit, penyebab penyakit dan kematian, penyakit tertentu (misalnya AIDS), kecacatan, konsumsi dan penjualan farmasi, tenaga kesehatan, remunerasi profesi kesehatan, status kesehatan lingkungan, ketidaksetaraan kesehatan, neraca kesehatan.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=522"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Konsumsi dan Pendapatan",
        "Keterangan": "mencakup statistik pendapatan dan pengeluaran rumah tangga dari sudut pandang rumah tangga (semua jenis pendapatan dan pengeluaran), termasuk topik seperti distribusi pendapatan, pendapatan dalam bentuk natura, transfer pendapatan yang diterima dan dibayarkan, pengukuran kemiskinan berbasis pendapatan atau pengeluaran, perlindungan konsumen, pola konsumsi, barang konsumsi dan barang tahan lama, kekayaan dan utang rumah tangga.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=523"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Perlindungan Sosial",
        "Keterangan": "berurusan dengan statistik tentang langkah-langkah untuk melindungi orang dari risiko pendapatan yang tidak memadai terkait dengan pengangguran, kesehatan yang buruk, cacat, usia tua, tanggung jawab orang tua, atau pendapatan yang tidak memadai setelah kehilangan pasangan atau orang tua, dll., termasuk statistik tentang penerima pensiun, skema jaminan sosial, pengeluaran perlindungan sosial, dll.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=524"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Pemukiman dan Perumahan",
        "Keterangan": "mencakup kegiatan statistik tentang perumahan, tempat tinggal, dan pemukiman manusia.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=525"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Hukum dan Kriminal",
        "Keterangan": "kegiatan termasuk kejahatan, hukuman, pengoperasian sistem peradilan pidana, keadilan, keselamatan, korban, tingkat pembersihan, populasi penjara, produksi obat-obatan terlarang, perdagangan dan penggunaan, dll.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=526"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Budaya",
        "Keterangan": "statistik yang berhubungan dengan kegiatan budaya dalam masyarakat, seperti teater, bioskop, museum, perpustakaan, media massa, produksi buku, olahraga, dll., termasuk pengeluaran dan pembiayaan budaya.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=527"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Aktivitas Politik dan Komunitas Lainnya",
        "Keterangan": "statistik jumlah pemilih, partisipasi dalam kegiatan politik dan masyarakat lainnya, keanggotaan serikat pekerja, dialog sosial, masyarakat sipil, modal sosial, dll.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=528"
    },
    {
        "Subjek": "Statistik Demografi dan Sosial",
        "Kategori": "Penggunaan Waktu",
        "Keterangan": "statistik tentang penggunaan waktu oleh individu, seringkali terkait dengan keseimbangan kehidupan kerja (rekonsiliasi tanggung jawab keluarga dan pekerjaan berbayar); pekerjaan yang tidak dibayar.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=529"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Statistik Makroekonomi",
        "Keterangan": "semua kegiatan yang berkaitan dengan statistik ekonomi secara luas pada tingkat makro, atau berbeda dari Neraca Nasional, baik tahunan, triwulanan, atau bulanan. Contohnya adalah database ekonomi makro yang menggabungkan akun nasional dan indikator ekonomi makro lainnya seperti Indikator Ekonomi Utama (OECD), dll.; kecenderungan bisnis dan survei pendapat konsumen, pertumbuhan ekonomi, stabilitas dan penyesuaian struktural, indikator siklus, statistik untuk analisis siklus bisnis.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=530"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Neraca Ekonomi",
        "Keterangan": "mencakup pekerjaan pada Neraca Ekonomi dengan harga saat ini dan konstan, berurusan dengan topik-topik seperti implementasi Sistem Neraca Ekonomi, Produk Domestik Bruto (PDB), Pendapatan Nasional Bruto (PDB), perekonomian non-observed dan informal, pengukuran modal, tabel input-output, neraca, dll.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=531"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Statistik Bisnis",
        "Keterangan": "statistik ekonomi luas tentang kegiatan perusahaan, mencakup pekerjaan pada statistik ekonomi di berbagai sektor, berkaitan dengan topik seperti statistik kegiatan ekonomi perusahaan, demografi bisnis, investasi bisnis, layanan bisnis, permintaan layanan, kinerja industri, kelompok perusahaan , produksi industri, komoditas, struktur penjualan dan jasa, hasil industri jasa, lembaga nirlaba.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=532"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Statistik sektoral",
        "Keterangan": "kegiatan statistik yang berhubungan dengan salah satu cabang industri atau layanan tertentu yang disebutkan pada tingkat klasifikasi tiga digit.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=533"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Keuangan Pemerintah, Fiskal dan Statistik Sektor Publik",
        "Keterangan": "semua statistik yang terkait dengan sektor pemerintah, termasuk utang dan defisit, pendapatan dan pengeluaran, rekening sektor pemerintah, pemerintah pusat, tarif dan pendapatan pajak, sistem pajak dan manfaat, pembiayaan pensiun negara dan skema jaminan sosial negara lainnya, pekerjaan sektor publik.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=534"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Perdagangan Internasional dan Neraca Pembayaran",
        "Keterangan": "berurusan dengan statistik semua transaksi lintas batas yang dicatat dalam neraca pembayaran, termasuk topik seperti perdagangan barang dan jasa, posisi dan utang eksternal, investasi asing langsung, perdagangan afiliasi asing, tarif, akses pasar, bantuan luar negeri , bantuan pembangunan, aliran sumber daya ke negara-negara berkembang.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=535"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Harga-Harga",
        "Keterangan": "mencakup semua aktivitas statistik yang berhubungan dengan harga, termasuk Paritas Daya Beli (PPP) dan perbandingan PDB internasional, mencakup topik seperti Indeks Harga Konsumen (CPI), inflasi, Indeks Harga Produsen (PPI), indeks harga untuk produk dan layanan tertentu ( misalnya produk Teknologi Informasi dan Komunikasi).",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=536"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Biaya Tenaga Kerja",
        "Keterangan": "kegiatan statistik tentang biaya tenaga kerja, penghasilan dan upah, baik untuk statistik struktural maupun jangka pendek.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=537"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Ilmu Pengetahuan, Teknologi, dan Inovasi",
        "Keterangan": "termasuk Penelitian dan Pengembangan (R&D), inovasi, paten, sumber daya manusia (dalam sains, teknologi, dan inovasi), industri teknologi tinggi dan layanan berbasis pengetahuan, bioteknologi, pembiayaan R&D, dan inovasi.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=538"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Pertanian, Kehutanan, Perikanan",
        "Keterangan": "mencakup semua statistik terkait pertanian, kehutanan, dan perikanan, mis. statistik moneter pertanian (akun ekonomi pertanian), struktur pertanian (struktur pertanian), perdagangan produk pertanian, input tenaga kerja pertanian, produksi tanaman dan hewan, komoditas pertanian, statistik agroindustri (termasuk produksi dan keamanan pangan), pertanian organik dan pangan organik , pengeluaran pemerintah untuk pertanian, perikanan dan kehutanan, tabel sumber dan penggunaan produk, statistik hasil hutan dan hutan, penilaian sumber daya hutan dan kebakaran hutan, perdagangan hasil hutan, perikanan.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=557"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Energi",
        "Keterangan": "pasokan energi, penggunaan energi, keseimbangan energi, keamanan pasokan, pasar energi, perdagangan energi, efisiensi energi, sumber energi terbarukan, pengeluaran pemerintah untuk energi.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=558"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Pertambangan, Manufaktur, Konstruksi",
        "Keterangan": "statistik kegiatan industri tertentu, mis. baja, galangan kapal, dan konstruksi, memperdagangkan produk khusus yang terkait dengan pertambangan, manufaktur, dan konstruksi.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=559"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Transportasi",
        "Keterangan": "mencakup statistik semua moda transportasi (udara, kereta api, jalan raya, perairan darat, laut), termasuk topik seperti infrastruktur transportasi, peralatan, arus lalu lintas, mobilitas pribadi, keselamatan, konsumsi energi, perusahaan transportasi, angkutan penumpang dan barang, transportasi tren sektor, kecelakaan lalu lintas jalan.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=560"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Pariwisata",
        "Keterangan": "mencakup statistik mengenai aktivitas pengunjung (seperti kedatangan/keberangkatan, menginap semalam, pengeluaran, tujuan kunjungan, dll.) yang terkait dengan berbagai bentuk pariwisata (inbound, domestik dan outbound), kegiatan industri pariwisata dan infrastruktur, ketenagakerjaan dan akun satelit pariwisata.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=561"
    },
    {
        "Subjek": "Statistik Ekonomi",
        "Kategori": "Perbankan, Asuransi dan Finansial",
        "Keterangan": "statistik uang, perbankan dan pasar keuangan, termasuk rekening keuangan, jumlah uang beredar, suku bunga, nilai tukar, indikator pasar saham, sekuritas, profitabilitas bank, asuransi sektor swasta dan statistik dana pensiun, Indikator Kesehatan Keuangan.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=562"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Lingkungan",
        "Keterangan": "termasuk topik seperti iklim, perubahan iklim (termasuk pengukuran aspek sosial ekonomi dari dampak perubahan iklim, kerentanan dan adaptasi), keanekaragaman hayati, lingkungan dan kesehatan, sumber daya alam, tanah, air, udara, lanskap, limbah, pengeluaran lingkungan, pengeluaran untuk perlindungan lingkungan, neraca lingkungan, indikator agri-lingkungan, tekanan lingkungan, dampak lingkungan dari industri, transportasi, energi dll., pemantauan lingkungan, analisis aliran material, indikator decoupling lingkungan, polusi, ekosistem, penggunaan dan tutupan lahan, perlindungan lingkungan, kawasan lindung nasional.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=539"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Statistik Regional dan Statistik Area Kecil",
        "Keterangan": "kegiatan yang berkaitan dengan statistik regional dan statistik yang mengacu pada wilayah sub-nasional atau wilayah berdasarkan unit administrasi, statistik perkotaan dan pedesaan, pembangunan pedesaan, akun regional, tipologi regional, dan kesenjangan regional.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=540"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Statistik dan Indikator Multi-Domain",
        "Keterangan": "berurusan dengan pekerjaan konseptual atau data berdasarkan pendekatan tematik khusus untuk keluaran yang melintasi beberapa bidang studi ekonomi, sosial atau lingkungan.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=541"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Buku Tahunan dan Ringkasan Sejenis",
        "Keterangan": "publikasi statistik multi-domain, basis data, dan produk data lainnya tanpa fokus tematik atau berorientasi masalah tertentu.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=542"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Kondisi Tempat Tinggal, Kemiskinan, dan Permasalahan Sosial Lintas Sektor",
        "Keterangan": "termasuk pekerjaan pada metode multidimensi untuk mengukur kemiskinan, kondisi kehidupan dalam arti luas, inklusi/eksklusi sosial, indikator sosial, dan situasi sosial.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=563"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Gender dan Kelompok Populasi Khusus",
        "Keterangan": "kondisi kehidupan dan peran mereka dalam masyarakat: perbandingan laki-laki/perempuan dan situasi kelompok populasi khusus seperti anak-anak, remaja, perempuan, lanjut usia, orang cacat, kelompok minoritas, dll.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=564"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Masyarakat Informasi",
        "Keterangan": "statistik yang memungkinkan untuk menilai penggunaan dan dampak teknologi informasi dan komunikasi pada masyarakat, termasuk akses dan penggunaan TIK (termasuk Internet), pengeluaran dan investasi TIK, infrastruktur TIK, jaringan telekomunikasi, komunikasi elektronik, e-government, perdagangan elektronik , e-learning, penetrasi broadband, layanan TIK, tarif komunikasi, infrastruktur jaringan, pendapatan, pengeluaran dan investasi operator, indikator Internet, perdagangan peralatan telekomunikasi.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=565"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Globalisasi",
        "Keterangan": "berurusan dengan mengukur aktivitas ekonomi perusahaan multinasional, serta dengan upaya untuk mengukur globalisasi melalui berbagai komponen dari bidang studi lainnya.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=566"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Indikator Millenium Development Goals (MDGs)",
        "Keterangan": "mengerjakan serangkaian indikator untuk memantau pencapaian Tujuan Pembangunan Milenium yang disepakati di KTT Milenium PBB.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=567"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Perkembangan Berkelanjutan",
        "Keterangan": "mengerjakan indikator dan kerangka kerja untuk memantau dimensi ekonomi, sosial dan lingkungan dari pembangunan berkelanjutan.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=568"
    },
    {
        "Subjek": "Statistik Lingkungan Hidup dan Multi-domain",
        "Kategori": "Kewiraswastaan",
        "Keterangan": "pengukuran determinan, kinerja dan dampak kegiatan kewirausahaan orang dan organisasi.",
        "Link": "https://siakkab.bps.go.id/id/statistics-table?subject=569"
    }
    ]);

    $history = [
        [
            "role" => "user",
            "parts" => [["text" => "Anda adalah program AI yang dikembangkan untuk membantu pengguna data bps kabupaten siak mencari data dan informasi statistik. Nama anda adalah Dara, Data Assistant and Response AI BPS Kabupaten Siak. Anda adalah petugas yang ahli statistik dan siap membantu setiap pengguna data dengan layanan terbaik."]]
        ],
        [
            "role" => "model",
            "parts" => [["text" => "Halo! Saya Dara, Data Assistant and Response AI BPS Kabupaten Siak. Saya siap membantu Anda menemukan data dan informasi statistik yang Anda butuhkan dari BPS Kabupaten Siak. Sebagai petugas ahli statistik, saya siap memberikan layanan terbaik."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Setiap pengguna mengakses layanan berikan sapaan berikut: Halo, Saya Dara, Data Assistant and Response AI BPS Kabupaten Siak. Saya siap membantu Anda dalam mencari data dan informasi statistik seputar BPS Kabupaten Siak. Apakah ada yang dapat saya bantu?"]]
        ],
        [
            "role" => "user", 
            "parts" => [["text" => "Saat pengguna menanyakan layanan yang tersedia, berikan informasi: Anda dapat menanyakan data dan informasi statistik seputar Kabupaten Siak. Saya siap membantu kebutuhan informasi anda. Anda juga dapat bertanya dan berkonsultasi secara langsung di Pusat Layanan Terpadu (PST) BPS Kabupaten Siak pada jam layanan berikut ini: Senin - Kamis pada Pukul 08.00-15.00 dan Jum'at pada Pukul 08.00-15.30. Alamat: Kompleks Perkantoran Sei Betung, Kp. Rempak, Siak. https://maps.app.goo.gl/GnQnqp5VnexdNNqG6"]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Anda adalah data assistant yang berintegritas, berhati-hati saat memberikan jawaban data dan interpretasinya secara langsung. Jangan memberikan data palsu. Meskipun demikian, anda tidak boleh menjawab dengan tidak tahu, berikan jawaban yang profesional."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Setiap memberikan jawaban sertakan disclaimer: Sebagai Data Assistant AI, saya dapat membuat kesalahan. Mohon periksa kembali informasi penting."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Nama kepala BPS Kabupaten Siak saat ini adalah Prayudho Bagus Jatmiko. Berikan informasi tersebut ketika ada yang bertanya. Jika ditanya tentang kepala lainnya, jawab secara profesional bahwa informasi terbatas."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Jika ditanya informasi pribadi pegawai BPS Kabupaten Siak, jawab sopan bahwa tidak dapat memberikan informasi pribadi dan arahkan untuk datang langsung ke kantor."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Berikut informasi media sosial BPS Kabupaten Siak:
Instagram: http://s.bps.go.id/instagrambpssiak
Facebook: http://s.bps.go.id/facebookbpssiak
YouTube: http://s.bps.go.id/youtubebpssiak
Website: https://siakkab.bps.go.id
Email: bps1405@bps.go.id
WA: 085183111405
Alamat: http://s.bps.go.id/alamatbpssiak"]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Anda tidak diizinkan memberikan jawaban selain informasi yang sudah diberikan. Tidak boleh mengarang nama dan data. Jika tidak tahu sampaikan secara profesional dan arahkan ke web BPS atau kunjungan langsung."]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Berikut adalah daftar data dan kategori yang tersedia: " . $knowledge_base]]
        ],
        [
            "role" => "user",
            "parts" => [["text" => "Apabila pengguna menanyakan berapa angka suatu data, anda harus mencari sinonim judul data yang diminta pada 'kategori' yang ada pada daftar pengetahuan, jika sudah menemukan berikan link yang bersesuaian dengan kategori tersebut. Anda juga dapat memberikan penjelasan tambahan mengenai data tersebut secara umum."]]
        ]
    ];

    $requestData = [
        'contents' => array_merge($history, [
            [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ]
        ]),
        'generationConfig' => [
            'maxOutputTokens' => 800,
            'temperature' => 0.7
        ]
    ];

    $ch = curl_init();
    $fullUrl = API_BASE_URL . '?key=' . GEMINI_API_KEY;
    
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        $errorNo = curl_errno($ch);
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("CURL Error ($errorNo): $error");
        error_log("Verbose information: " . $verboseLog);
        $aiResponse = "Maaf, terjadi kesalahan dalam memproses permintaan Anda. Error: " . $error;
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            error_log("HTTP Error: $httpCode, Response: $response");
            $aiResponse = "Maaf, terjadi kesalahan dalam memproses permintaan Anda. HTTP Code: " . $httpCode;
        } else {
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON Decode Error: " . json_last_error_msg());
                $aiResponse = "Maaf, terjadi kesalahan dalam memproses format response.";
            } else {
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';
            }
        }
    }
    
    $formattedResponse = AIMessageFormatter::format($aiResponse);
    SessionManager::addMessage('ai', $formattedResponse);
    
    curl_close($ch);
    fclose($verbose);
    
    echo json_encode(['response' => $formattedResponse]);
    exit();
}

echo json_encode(['error' => 'Invalid request method']);
exit();
?>
