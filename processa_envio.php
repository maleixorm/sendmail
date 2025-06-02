<?php

require "./library/PHPMailer/Exception.php";
require "./library/PHPMailer/OAuth.php";
require "./library/PHPMailer/PHPMailer.php";
require "./library/PHPMailer/POP3.php";
require "./library/PHPMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mensagem {
    private $para;
    private $assunto;
    private $mensagem;

    function __construct($para, $assunto, $mensagem)
    {
        $this->para = $para;
        $this->assunto = $assunto;
        $this->mensagem = $mensagem;
    }

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function mensagemValida() {
        if (empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
            return false;
        }
        return true;
    }
}

$mensagem = new Mensagem($_POST['para'], $_POST['assunto'], $_POST['mensagem']);

if (!$mensagem->mensagemValida()) {
    echo 'Mensagem não é válida.';
    die();
}

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.dominio.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'seuemail@dominio.com';                     //SMTP username
    $mail->Password   = 'suasenha';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('seuemail@dominio.com', 'Nome do Remetente');
    $mail->addAddress($mensagem->__get('para'));     //Add a recipient
    // $mail->addAddress('segundoemail@dominio.com');               //Name is optional
    // $mail->addReplyTo('remetente@dominio.com', 'Informações');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $mensagem->__get('assunto');
    $mail->Body    = $mensagem->__get('mensagem');
    $mail->AltBody = 'Utilize um client que tenha suporte a HTML para visualizar essa mensagem!';

    $mail->send();
    echo 'A mensagem foi enviada com sucesso!';
} catch (Exception $e) {
    echo "Não foi possível enviar este e-mail. Tente novamente mais tarde.";
    echo "<br>Detalhes do erro: {$mail->ErrorInfo}";
}