<?php
session_start();
require_once 'dbconexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $secretKey = "6LcZ0V0rAAAAABd4Ofxkg8jldtUqQD-22-lpmTDf"; 
    $responseKey = $_POST['g-recaptcha-response'] ?? '';
    $userIP = $_SERVER['REMOTE_ADDR'];

    if (!$responseKey) {
        echo json_encode(['exito' => false, 'mensaje' => 'Por favor completa el reCAPTCHA.']);
        exit();
    }

    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $secretKey,
        'response' => $responseKey,
        'remoteip' => $userIP
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess || !$captchaSuccess->success) {
        echo json_encode(['exito' => false, 'mensaje' => '❌ Verifica que no eres un robot.']);
        exit();
    }

    $nombre = trim($_POST["nombre"] ?? '');
    $correo = trim($_POST["correo"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($nombre) || empty($correo) || empty($password)) {
        echo json_encode(['exito' => false, 'mensaje' => '⚠️ Todos los campos son obligatorios.']);
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['exito' => false, 'mensaje' => '⚠️ El correo no es válido.']);
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
        $stmt = $cnnPDO->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':password', $passwordHash);

        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'carnicerialablanca31@gmail.com'; // tu correo real
                $mail->Password = 't g i c q z r q e m v s g r t z';  // tu app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('saucedodamaris804@gmail.com', 'VIAJES CHIQUITA');
                $mail->addAddress($correo);

                $mail->isHTML(true);
                $mail->Subject = 'Bienvenido a Agencia de Viajes';
                $mail->Body = '
                    <h3>¡Hola ' . htmlspecialchars($nombre) . ' 👋!</h3>
                    <p>Tu registro en <strong>Agencia de Viajes</strong> se ha completado con éxito.</p>
                    <p>Gracias por elegirnos. Ya puedes iniciar sesión con tu cuenta.</p>
                    <p><a href="http://localhost/unidad3/login.php">Haz clic aquí para iniciar sesión</a></p>
                ';

                $mail->send();
                echo json_encode(['exito' => true, 'mensaje' => '✅ Registro exitoso y correo enviado.']);
            } catch (Exception $e) {
                echo json_encode(['exito' => true, 'mensaje' => '✅ Registro exitoso, pero fallo al enviar el correo: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['exito' => false, 'mensaje' => '❌ Error al registrar usuario.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['exito' => false, 'mensaje' => '❌ Error de base de datos: ' . $e->getMessage()]);
    }
    exit();
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}
?>
