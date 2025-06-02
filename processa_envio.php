<?php

require "./library/PHPMailer/Exception.php";
require "./library/PHPMailer/OAuth.php";
require "./library/PHPMailer/PHPMailer.php";
require "./library/PHPMailer/POP3.php";
require "./library/PHPMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

if ($mensagem->mensagemValida()) {
    echo 'Mensagem é válida.';
} else {
    echo 'Mensagem não é válida';
}