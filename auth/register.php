<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Informasi Pariwisata Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .image-section {
        position: relative;
        background: url('../assets/images/collage-image.png') no-repeat center center/cover;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
    }

    /* Pop-up Konfirmasi */
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f7fafc;
        /* Warna terang */
        color: #333;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-size: 18px;
        z-index: 1000;
        animation: fadeIn 0.5s ease-out;
    }

    .popup .popup-content {
        text-align: center;
    }

    .popup img.checkmark {
        width: 60px;
        /* Sesuaikan ukuran gambar centang */
        margin-bottom: 20px;
        animation: scaleIn 1s ease-out;
    }

    /* Animasi centang hijau */
    @keyframes scaleIn {
        0% {
            transform: scale(0);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Animasi Fade In untuk pop-up */
    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translate(-50%, -60%);
        }

        100% {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    /* Overlay untuk latar belakang pop-up */
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 999;
        display: none;
    }
    </style>
</head>

<body>
    <div class="flex h-screen">
        <!-- Left Section: Register Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
            <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Buat Akun Baru</h3>

                <form action="../proses/register_proses.php" method="POST" id="registerForm">
                    <div class="mb-4">
                        <input type="text" name="username"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Username" required>
                    </div>

                    <div class="mb-4">
                        <input type="password" name="password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Password" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-all">
                        Daftar
                    </button>

                    <p class="mt-4 text-sm text-center text-gray-600">
                        Sudah punya akun?
                        <a href="login.php" class="text-blue-600 hover:underline">Masuk di sini</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Right Section: Image and Text -->
        <div class="hidden md:block w-1/2 image-section">
            <div class="image-overlay"></div>
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-48 mb-4">
                <h2 class="text-3xl font-bold uppercase text-center">Sistem Informasi Pariwisata Banjarmasin</h2>
            </div>
        </div>
    </div>

    <!-- Pop-up and Overlay -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <img src="../image/centangHijau.png" alt="Checkmark" class="checkmark animate-checkmark mx-auto">

            <p>Akun berhasil dibuat!</p>
            <button onclick="window.location.href='../auth/login.php'"
                class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">OK</button>
        </div>
    </div>

    <div id="overlay" class="overlay"></div>


    <script>
    // Menampilkan pop-up setelah berhasil registrasi
    window.onload = function() {
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        document.getElementById("popup").style.display = "block";
        document.getElementById("overlay").style.display = "block";
        <?php endif; ?>
    };
    </script>
</body>

</html>