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
    public $status = ['codigo_status' => null, 'descricao_status' => null];

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
    header('Location: index.php');
}

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
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

    $mensagem->status['codigo_status'] = 1;
    $mensagem->status['descricao_status'] = 'A mensagem foi enviada com sucesso!';
    
} catch (Exception $e) {
    $mensagem->status['codigo_status'] = 2;
    $mensagem->status['descricao_status'] = "Não foi possível enviar este e-mail. Tente novamente mais tarde. <br>Detalhes do erro: {$mail->ErrorInfo}";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Send Mail</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
		</div>
        <div class="row">
            <div class="col-md-12">
                <? if ($mensagem->status['codigo_status'] == 1) { ?>
                    <div class="container">
                        <h1 class="display-4 text-success">Sucesso!</h1>
                        <p><?= $mensagem->status['descricao_status']; ?></p>
                        <a href="index.php" class="btn btn-success mt-5 text-white">Voltar</a>
                    </div>
                <? } ?>
                <? if ($mensagem->status['codigo_status'] == 2) { ?>
                    <div class="container">
                        <h1 class="display-4 text-danger">Ops! :\</h1>
                        <p><?= $mensagem->status['descricao_status']; ?></p>
                        <a href="index.php" class="btn btn-danger mt-5 text-white">Voltar</a>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</body>
</html>