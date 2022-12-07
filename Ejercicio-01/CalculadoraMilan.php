<?php

session_start();

class CalculadoraMilan {
    protected $scr;
    protected $mem;
    protected $point;
    protected $left;
    protected $right;
    protected $op;

    public function __construct() {
        this->scr = 0;
        this->left = 0;
        this->right = 0;
        this->point = false;
        this->memory = 0;
    }

    public function getScreen() {
        return $this->scr;
    }

    public function numeros($val) {
        if(this->op)
        $this->screen = $this->screen .$dig;
    }

    public function suma() {
        this->operation('+')
    }

    public function resta() {
        this->operation('-')
    }

    public function mult() {
        this->operation('*')
    }

    public function division() {
        this->operation('/');
    }

    public function operation($val) {
        this->op = $val;
    }

    public function cpress() {

    }

    public function cepress() {

    }

    public function changeSign() {

    }

    public function sqrt() {

    }

    public function percentage() {

    }

    public function mrc() {

    }

    public function m_minus() {

    }

    public function m_plus() {

    }

    public function igual() {

    }

    public function punto() {

    }
}

if(!isset($_SESSION['calculadora'])) {
    $calculadoraMilan = new CalculadoraMilan();
    $_SESSION['calculadora']  = $calculadoraMilan;
}

if(count($_POST) > 0) {
    
    $calc = $_SESSION['calculadora'];
    if(isset($_POST['C'])) $calc->cpress();
    if(isset($_POST['CE'])) $calc->cepress();
    if(isset($_POST['+/-'])) $calc->changeSign();
    if(isset($_POST['√'])) $calc->sqrt();
    if(isset($_POST['%'])) $calc->percentage();
    if(isset($_POST['7'])) $calc->numeros(7);
    if(isset($_POST['8'])) $calc->numeros(8);
    if(isset($_POST['9'])) $calc->numeros(9);
    if(isset($_POST['*'])) $calc->mult();
    if(isset($_POST['/'])) $calc->division();
    if(isset($_POST['4'])) $calc->numeros(4);
    if(isset($_POST['5'])) $calc->numeros(5);
    if(isset($_POST['6'])) $calc->numeros(6);
    if(isset($_POST['-'])) $calc->resta();
    if(isset($_POST['MRC'])) $calc->mrc();
    if(isset($_POST['1'])) $calc->numeros(1);
    if(isset($_POST['2'])) $calc->numeros(2);
    if(isset($_POST['3'])) $calc->numeros(3);
    if(isset($_POST['+'])) $calc->suma();
    if(isset($_POST['M-'])) $calc->m_minus();
    if(isset($_POST['0'])) $calc->numeros(0);
    if(isset($_POST['.'])) $calc->punto();
    if(isset($_POST['='])) $calc->igual();
    if(isset($_POST['M+'])) $calc->m_plus();
    
    $_SESSION['calculadora'] = $calc;
}

echo"
<!DOCTYPE html>

<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <!-- Metadatos de los documentos HTML5 -->
    <meta name='author' content='Jesús Alonso Gárcia'>
    <!-- Definición de la ventana grafica -->
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <!-- Titulo de la página -->
    <title>Ejercicio 3</title>
    <!-- añadir el elemento link de enlace a laa hoja de estilo dentro del <head> del documento html -->
    <link rel='stylesheet' type='text/css' href='CalculadoraMilan.css'>
    <script src='CalculadoraMilan.js'></script>
</head>

<body>
    <!-- Calculadora-->
    <section>
        <h1> Calculadora </h1>
        <form action='#' method='post' name='CalculadoraMilan'>
            <label for='pantalla'>Resultado</label>
            <input type='text' name='pantalla' id='pantalla' readonly>
            <input type='submit' value='C' name='C' > 
            <input type='submit' value='CE' name='CE' > 
            <input type='submit' value='+/-' name='+/-' > 
            <input type='submit' value='√' name='√' > 
            <input type='submit' value='%' name='%'> 

            <input type='submit' value='7' name='7'> 
            <input type='submit' value='8' name='8'> 
            <input type='submit' value='9' name='9'> 
            <input type='submit' value='*' name='*'> 
            <input type='submit' value='/' name='/'> 
            
            <input type='submit' value='4' name='4'> 
            <input type='submit' value='5' name='5'> 
            <input type='submit' value='6' name='6'> 
            <input type='submit' value='-' name='-'> 
            <input type='submit' value='MRC' name='MRC'> 
            
            <input type='submit' value='1' name='1'> 
            <input type='submit' value='2' name='2'> 
            <input type='submit' value='3' name='3'> 
            <input type='submit' value='+' name='+'> 
            <input type='submit' value='M-' name='M-'> 
            
            <input type='submit' value='0' name='0'> 
            <input type='submit' value='.' name='.'> 
            <input type='submit' value='=' name='='> 
            <input type='submit' value='M+' name='M+'> 
        </form>
    </section>
</body>

</html>";
>