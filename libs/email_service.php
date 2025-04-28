<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mail;
    private $config;

    public function __construct() {
        require_once __DIR__ . '/../vendor/autoload.php';
        $this->config = require_once __DIR__ . '/../config/email_config.php';
        
        $this->mail = new PHPMailer(true);
        $this->setupMailer();
    }
    
    private function setupMailer() {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = $this->config['smtp_host'];
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $this->config['smtp_username'];
            $this->mail->Password   = $this->config['smtp_password'];
            $this->mail->SMTPSecure = $this->config['smtp_encryption'];
            $this->mail->Port       = $this->config['smtp_port'];
            $this->mail->CharSet    = 'UTF-8';
            
            // Default sender
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
        } catch (Exception $e) {
            throw new Exception("Kesalahan pengaturan email: " . $e->getMessage());
        }
    }
    
    public function sendVerificationEmail($to, $name, $verificationCode) {
        try {
            // Reset recipients
            $this->mail->clearAddresses();
            
            // Recipients
            $this->mail->addAddress($to, $name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = $this->config['verification_subject'];
            
            // Email body with verification code
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;'>
                <div style='text-align: center; padding: 10px 0; background-color: #4CAF50; color: white; border-radius: 5px 5px 0 0;'>
                    <h2>Verifikasi Email Anda</h2>
                </div>
                <div style='padding: 20px; background-color: #f9f9f9;'>
                    <p>Halo <strong>{$name}</strong>,</p>
                    <p>Terima kasih telah mendaftar di Trash2Cash. Untuk melanjutkan proses pendaftaran, mohon masukkan kode verifikasi berikut pada halaman pendaftaran:</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <div style='display: inline-block; padding: 10px 20px; background-color: #f0f0f0; border: 1px solid #ddd; border-radius: 5px; letter-spacing: 5px; font-size: 24px; font-weight: bold;'>
                            {$verificationCode}
                        </div>
                    </div>
                    <p>Kode ini hanya berlaku selama 15 menit. Jika Anda tidak melakukan pendaftaran, silakan abaikan email ini.</p>
                    <p>Terima kasih,</p>
                    <p><strong>Tim Trash2Cash</strong></p>
                </div>
                <div style='text-align: center; padding: 10px; font-size: 12px; color: #666;'>
                    &copy; " . date('Y') . " Trash2Cash. All rights reserved.
                </div>
            </div>";
            
            $this->mail->Body = $body;
            
            return $this->mail->send();
        } catch (Exception $e) {
            throw new Exception("Kesalahan pengiriman email: " . $e->getMessage());
        }
    }
}
?>