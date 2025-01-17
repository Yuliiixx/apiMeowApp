<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require 'vendor/autoload.php'; // Sesuaikan jalur ini jika perlu
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Koneksi ke database
include 'db.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Memeriksa metode permintaan
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $response = array();

    // Memeriksa apakah email sudah diatur
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Membersihkan input email
        $email = mysqli_real_escape_string($conn, $email); // Menggunakan variabel $conn yang benar

        // Memeriksa apakah email ada
        $checkEmailQuery = "SELECT * FROM tb_users WHERE email='$email'";
        $emailResult = mysqli_query($conn, $checkEmailQuery);

        if (mysqli_num_rows($emailResult) > 0) {
            // Menghasilkan OTP
            $otp = rand(1000, 9999);

            // Waktu kedaluwarsa OTP (misalnya 10 menit dari sekarang)
            $otp_expiry = date("Y-m-d H:i:s", strtotime('+10 minutes'));

            // Menyimpan OTP dan waktu kedaluwarsa dalam database
            $updateQuery = "UPDATE tb_users SET code_verification='$otp', otp_expiry='$otp_expiry' WHERE email='$email'";
            if (mysqli_query($conn, $updateQuery)) {
                // Mengirim OTP melalui email
                $mail = new PHPMailer(true);

                try {
                    // Pengaturan server
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // Setel server SMTP
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'gamelyfe05@gmail.com'; // Nama pengguna SMTP
                    $mail->Password   = 'lrlz shbs rhom ahow'; // Kata sandi SMTP
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Penerima
                    $mail->setFrom('otp@verif.com', 'Meow App');
                    $mail->addAddress($email);

                    // Konten
                    $mail->isHTML(true);
                    $mail->Subject = 'Meow App [' . $otp . ']';
                    $mail->Body    = 'Masukkan kode OTP ini untuk memverifikasi akun Anda: ' . $otp;

                    $mail->send();
                    $response['value'] = 1;
                    $response['message'] = 'OTP berhasil dikirim';
                } catch (Exception $e) {
                    $response['value'] = 0;
                    $response['message'] = 'Gagal mengirim OTP: ' . $mail->ErrorInfo;
                }
            } else {
                $response['value'] = 0;
                $response['message'] = 'Gagal menyimpan OTP: ' . mysqli_error($conn);
            }
        } else {
            $response['value'] = 0;
            $response['message'] = 'Email tidak ditemukan';
        }
    } else {
        $response['value'] = 0;
        $response['message'] = 'Parameter yang diperlukan tidak ada';
    }

    echo json_encode($response);
} else {
    $response['value'] = 0;
    $response['message'] = 'Metode permintaan tidak valid';
    echo json_encode($response);
}

mysqli_close($conn);
?>
