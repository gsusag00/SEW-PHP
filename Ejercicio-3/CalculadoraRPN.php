<?php

session_start();

class CalculadoraMilan {
    protected $scr;
    protected $mem;
    protected $op;
    protected $lastnum;

    public function __construct() {
        $this->scr="";
        $this->mem="";
        $this->op="";
        $this->lastnum="";
    }

    public function numeros($val) {
        if(!empty($this->lastnum)) {
            $this->lastnum = "";
            $this->scr = "";
            $this->op = "";
        }
        $this->scr .= $val;
    }

    protected function updateScreen() {
        //Metodo para "debugear"
        echo "Valor de pantalla" . $this->scr;
    }

    public function suma() {
        $this->operation('+');
    }

    public function resta() {
        $this->operation('-');
    }

    public function mult() {
        $this->operation('*');
    }

    public function division() {
        $this->operation('/');
    }

    public function operation($val) {
        if(!empty($this->lastnum)) {
            $this->lastnum = "";
            $this->op = "";
        }
        if(!empty($this->op)) {
            $exp = explode($this->op,$this->scr);
            if(count($exp) >= 2) {
                $this->scr = $this->doEval($this->scr);
            } else {
                $this->scr = substr_replace($this->scr,"",-1);
            }
        }
        $this->op = $val;
        $this->scr .= $this->op;
    }

    public function cpress() {
        $this->cepress();
        $this->mem = 0;
    }

    public function cepress() {
        $this->scr = "";
        $this->op = "";
        $this->lastnum = "";
    }

    public function changeSign() {
        if(!empty($this->op) && empty($this->lastnum)) {
            $exp = explode($this->op, $this->scr);
            if(count($exp) == 3) {
                $this->scr = $exp[0] . $this->op . ltrim($exp[2],"-");
            }
            if(!empty($exp[1])) {
                if(str_contains($exp[1],'-')) {
                    $this->scr = $exp[0] . $this->op . ltrim($exp[1],'-');
                } else {
                    $this->scr = $exp[0] . $this->op . '-' . $exp[1];
                }
            }
        }
        else {
            if(str_contains($this->scr,'-')) {
                $this->scr = ltrim($this->scr,"-");
            } else {
                $this->scr = "-".$this->scr;
            }
        }
    }

    public function sqrt() {
        if(!empty($this->lastnum)) {
            $this->scr = 'sqrt(' . $this->scr . ')';
            $this->op = "";
            $this->lastnum = "";
        }
        else if(!empty($this->op)) {
            $exp = explode($this->op, $this->scr);
            if(count($exp) == 3) {
                //Si es un numero negativo y es una resta.
                $this->scr = $exp[0] . $this->op . 'sqrt(-' . $exp[2] . ')';
            }
            if(!empty($exp[1])) {
                //Operacion normal
                $this->scr = $exp[0] . $this->op . 'sqrt(' . $exp[1] . ')';
            }
        } else {
            $this->scr = 'sqrt(' . $this->scr . ')';
        }
    }

    public function percentage() {
        if(!empty($this->op)) {
            $exp = explode($this->op, $this->scr);
            if(count($exp)) {
                if(!empty($exp[2])) {
                    if($this->op == '+' || $this->op == '-') {
                        $this->scr = $this->doEval($exp[0] . $this->op . '(' . $exp[0] . '/ 100) * ' . $exp[2]);
                    } else {
                        $this->scr = $this->doEval($exp[0] . $this->op . '(' . $exp[2] . '/ 100)');
                    }
                }
            }
            if(!empty($exp[1])) {
                if($this->op == '+' || $this->op == '-') {
                    $this->scr = $this->doEval($exp[0] . $this->op . '(' . $exp[0] . '/ 100) * ' . $exp[1]);
                } else {
                    $this->scr = $this->doEval($exp[0] . $this->op . '(' . $exp[1] . '/ 100)');
                }
            }
        } else {
            $this->scr = $this->doEval($this->scr . '/100');
        }
    }

    public function mrc() {
        if(!empty($this->op)) {
            $exp = explode($this->op,$this->scr);
            $this->scr = $exp[0] . $this->op . $this->mem;
        } else {
            $this->scr = $this->mem;
        }
    }

    public function m_minus() {
        $this->scr = $this->doEval($this->scr);
        $this->mem -= $this->scr;
        $this->op = "";
        $this->lastnum = "";
    }

    public function m_plus() {
        $this->scr = $this->doEval($this->scr);
        $this->mem += $this->scr;
        $this->op = "";
        $this->lastnum = "";
    }

    protected function doEval($expression) { 
        $val = "";
        try {
            $val=eval("return $expression ;"); 
        }
        catch (Error $e) {
            $val = "Syntax Error";
        }  
        catch(Exception $e){
            $val = "Syntax Error";
        }
        return $val;
    }

    public function igual() {
        if(!empty($this->lastnum)) {
            $this->scr .= $this->op .  $this->lastnum;
        } else {
            if(!empty($this->op)) {
                $exp = explode($this->op, $this->scr);
                if(count($exp) == 3) {
                    $this->lastnum = $exp[2];
                } else {
                    $this->lastnum = $exp[1];
                }
            }
        }
        try {
            $res=eval("return $this->scr ;"); 
            $this->scr = $res;
        }
        catch (Error $e) {
            $this->scr = "Syntax Error";
        }  
        catch(Exception $e){
            $this->scr = "Syntax Error";
        }
    }

    public function punto() {
        $this->scr .= ".";
    }

    public function getScr() {
        return $this->scr;
    }
}

class CalculadoraCientifica extends CalculadoraMilan {

    protected $second;
    protected $hyp;
    protected $deg;
    protected $arr = array('+','-','*','/','%','e+','**','**(1/');
    protected $left;

    public function __construct() {
        parent::__construct();
        $this->second=false;
        $this->hyp=false;
        $this->deg='DEG';
        $this->left = "";
    }

    public function igual() {
        if(!empty($this->lastnum)) {
            $this->scr .= $this->op .  $this->lastnum;
        } else {
            if(!empty($this->op)) {
                $alt = substr($this->scr,strlen($this->left));
                $exp = explode($this->op,$alt);
                if(count($exp) == 3) {
                    $this->lastnum = $exp[2];
                } else {
                    $this->lastnum = $exp[1];
                }
            }
        }
        try {
            $res=eval("return $this->scr ;"); 
            $this->scr = $res;
        }
        catch (Error $e) {
            $this->scr = "Syntax Error";
        }  
        catch(Exception $e){
            $this->scr = "Syntax Error";
        }
    }

    public function numeros($val) {
        if(!empty($this->lastnum)) {
            $this->lastnum = "";
            $this->scr = "";
            $this->op = "";
            $this->left = "";
        }
        $this->scr .= $val;
    }

    public function operation($val) {
        if(!empty($this->lastnum)) {
            $this->lastnum = "";
            $this->op = "";
        }
        if(!empty($this->op)) {
            $last = substr($this->scr,-1);
            if(in_array($last,$this->arr)) {
                $this->scr = substr_replace($this->scr,"",-1);
            }
        }
        $this->op = $val;
        $this->left = $this->scr;
        $this->scr .= $this->op;
    }

    public function deg() {
        if($this->deg == 'DEG') {
            $this->deg = 'RAD';
        } else if($this->deg == 'RAD') {
            $this->deg = 'GRAD';
        } else {
            $this->deg = 'DEG';
        }
    }

    public function getDeg() {
        return $this->deg;
    }

    public function hyp() {
        $this->hyp = !$this->hyp;
    }

    public function getHyp() {
        return $this->hyp;
    }

    public function f_e() {
        if(str_contains($this->scr,'e')) {
            $this->scr = (float) $this->scr;
        } else {
            $this->scr = sprintf("%.e", $this->scr);
        }
    }

    public function mc() {
        $this->mem = 0;
    }

    public function mr() {
        parent::mrc();
    }

    public function ms() {
        if(empty($this->op)) {
            $this->mem = $this->scr;
        } else {
            $exp = explode($this->op,$this->scr);
            if(count($exp) == 3) {
                $this->mem = $exp[2];
            } else {
                $this-> mem = $exp[1];
            }
        }
    }

    public function second() {
        $this->second = !$this->second;
    }

    public function getSecond() {
        return $this->second;
    }

    public function pi() {
        $var = pi();
        if($this->second) {
            $var = exp(1);
        }
        if(!empty($this->op)) {
            $exp = explode($this->op, $this->scr);
            $this->scr = $exp[0] . $this->op . $var;
        } else {
            $this->scr = $var;
        }
    }

    public function del() {
        $this->scr = substr_replace($this->scr,"",-1);
    }

    public function xsquare() {
        $var = 2;
        if($this->second) {
            $var = 3;
        }
        if(!empty($this->op)) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $this->scr = $this->left . $this->op . pow($exp[1],$var);
        } else {
            $this->scr = pow($this->scr,$var);
        }
    }

    public function sqrt() {
        $var = 2;
        if($this->second) {
            $var = 3;
        }
        if(!empty($this->op)) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $this->scr = $exp[0] . $this->op . pow($exp[1],1/$var);
        } else {
            $this->scr = pow($this->scr,1/$var);
        }
    }

    public function degreeToRad($val) {
        if ($this->deg == 'DEG') {
            return $val * pi() / 180;
        } else if ($this->deg === 'GRAD') {
            return $val * pi() / 200;
        }
        return $val;
    }

    public function radToDegree($val) {
        if ($this->deg == 'DEG') {
            return $val * 180 / pi();
        } else if ($this->deg === 'GRAD') {
            return $val * 200 / pi();
        }
        return $val;
    }

    public function sin() {
        $var = 'sin';
        if($this->second && $this->hyp) {
            $var = 'asinh';
        } else if ($this->second) {
            $var = 'asin';
        } else if ($this->hyp) {
            $var = 'sinh';
        }
        if($this->op) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $right = $exp[1] + 0;
            if(!$this->second) {
                $right = $this->degreeToRad($right);
            }
            $right = $var($right);
            if($this->second) {
                $right = $this->radToDegree($right);
            }
            $this->scr = $this->left . $this->op . $right;
        } else {
            $right = $this->scr;
            if(!$this->second) {
                $right = $this->degreeToRad($right);
            }
            $right = $var($right);
            if($this->second) {
                $right = $this->radToDegree($right);
            }
            $this->scr = $right;
        }
    }

    public function cos() {
        $var = 'cos';
        if($this->second && $this->hyp) {
            $var = 'acosh';
        } else if ($this->second) {
            $var = 'acos';
        } else if ($this->hyp) {
            $var = 'cosh';
        }
        if($this->op) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $right = $exp[1] + 0;
            if(!$this->second) {
                $right = $this->degreeToRad($right);
            }
            $right = $var($right);
            if($this->second) {
                $right = $this->radToDegree($right);
            }
            $this->scr = $this->left . $this->op . $right;
        } else {
            $right = $this->scr;
            if(!$this->second) {
                $right = $this->degreeToRad($right);
            }
            $right = $var($right);
            if($this->second) {
                $right = $this->radToDegree($right);
            }
            $this->scr = $right;
        }
    }

    public function tan() {
        $var = 'tan';
        if($this->second && $this->hyp) {
            $var = 'atanh';
        } else if ($this->second) {
            $var = 'atan';
        } else if ($this->hyp) {
            $var = 'tanh';
        }
        if($this->op) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $right = $exp[1] + 0;
            if(!$this->second) {
                $right = $this->degreeToRad($right);
            }
            $right = $var($right);
            if($this->second) {
                $right = $this->radToDegree($right);
            }
            $this->scr = $this->left . $this->op . $right;
        } else {
            $right = $this->scr;
            if(!$this->second) {
                $right = $this->degreeToRad($right);
            }
            $right = $var($right);
            if($this->second) {
                $right = $this->radToDegree($right);
            }
            $this->scr = $right;
        }
    }

    public function mod() {
        $this->operation('%');
    }

    public function l_parentesis() {
        $this->scr .= '(';
    }

    public function r_parentesis() {
        $this->scr .= ')';
    }

    public function exp() {
        $this->operation('e+');
    }

    public function xtoy() {
        if($this->second) {
            $this->operation('**(1/');
        } else {
            $this->operation('**');
        }
    }

    public function tentox() {
        $var = 10;
        if($this->second) {
            $var = 2;
        }
        if(!empty($this->op)) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $this->scr = $this->left . $this->op . 'pow(2,' . $exp[1] . ')';
        } else {
            $this->scr = 'pow(' . $var . ',' . $this->scr . ')';
        }
    }

    public function log() {
        $var = 'log10';
        if($this->second) {
            $var = 'log';
        }
        if(!empty($this->op)) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $this->scr = $this->left . $this->op . $var($exp[1]);
        } else {
            $this->scr = $var($this->scr);
        }
    }

    protected function doFact($var) {
        $entero = (int)$var;
        $num = 1;
        for ($i = 1; $i <= $entero; $i++){ 
            $num *= $i; 
        } 
        return $num;
    }

    public function fact() {
        if(!empty($this->op)) {
            $alt = substr($this->scr,strlen($this->left));
            $exp = explode($this->op,$alt);
            $this->scr = $this->left . $this->op . $this->doFact($exp[1]);
        } else {
            $this->scr = $this->doFact($this->scr);
        }
    }

}

class CalculadoraRPN extends CalculadoraCientifica {

    protected $pila;

    public function __construct() {
        parent::__construct();
        $this->pila = new Pila();
    }

    public function enter() {
        $currScr = parent::getScr();
        $this->pila->push($currScr);
        $this->scr = "";
        $this->getTa();
    }

    public function cpress() {
        parent::cpress();
        $this->pila->clear();
    }

    public function getTa() {
        return $this->pila->print();
    }

    public function cos() {
        if(!empty($this->scr)) {
            parent::cos();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::cos();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function sin() {
        if(!empty($this->scr)) {
            parent::sin();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::sin();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function tan() {
        if(!empty($this->scr)) {
            parent::tan();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::tan();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function xsquare() {
        if(!empty($this->scr)) {
            parent::xsquare();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::xsquare();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function sqrt() {
        if(!empty($this->scr)) {
            parent::sqrt();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::sqrt();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function tentox() {
        if(!empty($this->scr)) {
            parent::tentox();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::tentox();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function log() {
        if(!empty($this->scr)) {
            parent::log();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::log();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function fact() {
        if(!empty($this->scr)) {
            parent::fact();
            $this->pila->push($this->scr);
            $this->scr = "";
        } else {
            $this->scr = $this->pila->pop();
            parent::fact();
            $this->pila->push($this->scr);
            $this->scr = "";
        }
    }

    public function operation($val) {
        if($this->pila->getSize() >= 2) {
            $op2 = $this->pila->pop();
            $op1 = $this->pila->pop();
            $res = 0;
            if($val == '+') {
                $res = $op1 + $op2;
            } else if($val == '-') {
                $res = $op1 - $op2;
            } else if($val == '*') {
                $res = $op1 * $op2;
            } else {
                $res = $op1 / $op2;
            }
            $this->pila->push($res);
        }
    }

    public function mod() {
        if($this->pila->getSize() >= 2) {
            $op2 = $this->pila->pop();
            $op1 = $this->pila->pop();
            $res = $op1 % $op2;
            $this->pila->push($res);
        }
    }

    public function exp() {
        if($this->pila->getSize() >= 2) {
            $op2 = $this->pila->pop();
            $op1 = $this->pila->pop();
            $res = $op1 *  pow(10,$op2);
            $this->pila->push($res);
        }
    }

    public function xtoy() {
        if($this->pila->getSize() >= 2) {
            if($this->second) {
                $op2 = $this->pila->pop();
                $op1 = $this->pila->pop();
                $res = pow($op1,1/$op2);
                $this->pila->push($res);
            } else {
                $op2 = $this->pila->pop();
                $op1 = $this->pila->pop();
                $res = pow($op1,$op2);
                $this->pila->push($res);
            }
        }
    }

}

class Pila {

    protected $pila;

    public function __construct() {
        $this->pila = array();
    }

    public function push($elemento) {
        array_push($this->pila,$elemento);
    }

    public function pop():mixed {
        return array_pop($this->pila);
    }

    public function getSize() {
        return count($this->pila);
    }

    public function clear() {
        $this->pila = array();
    }

    public function ver() {
        print_r($this->pila);
    }

    public function print() {
        $toPrint = "";
        $pos = count($this->pila);
        for($i = $pos - 1; $i >= 0; $i-- ) {
            $toPrint .= $this->printLine($i,$pos);
            $pos--;
        }
        return $toPrint;
    }

    public function printLine($item,$pos) {
        $realpos = count($this->pila) - 1 - $item;
        return "\n" . $pos . ":\t" . $this->pila[$item];
    }

}

if(!isset($_SESSION['calculadora'])) {
    $calculadoraRPN = new CalculadoraRPN();
    $_SESSION['calculadora']  = $calculadoraRPN;
}

if(count($_POST) > 0) {
    
    $calc = $_SESSION['calculadora'];
    if(isset($_POST['DEG'])) $calc->deg();
    if(isset($_POST['HYP'])) $calc->hyp();
    if(isset($_POST['F-E'])) $calc->f_e();

    if(isset($_POST['MC'])) $calc->mc();
    if(isset($_POST['MR'])) $calc->mr();
    if(isset($_POST['M+'])) $calc->m_plus();
    if(isset($_POST['M-'])) $calc->m_minus();
    if(isset($_POST['MS'])) $calc->ms();

    if(isset($_POST['2nd']))$calc->second();
    if(isset($_POST['π'])) $calc->pi();
    if(isset($_POST['C'])) $calc->cpress();
    if(isset($_POST['CE'])) $calc->cepress();
    if(isset($_POST['Del'])) $calc->del();

    if(isset($_POST['x^2'])) $calc->xsquare();
    if(isset($_POST['sin'])) $calc->sin();
    if(isset($_POST['cos'])) $calc->cos();
    if(isset($_POST['tan'])) $calc->tan();
    if(isset($_POST['mod'])) $calc->mod();

    if(isset($_POST['√'])) $calc->sqrt();
    if(isset($_POST['('])) $calc->l_parentesis();
    if(isset($_POST[')'])) $calc->r_parentesis();
    if(isset($_POST['exp'])) $calc->exp();
    if(isset($_POST['/'])) $calc->division();
    
    if(isset($_POST['x^y'])) $calc->xtoy();
    if(isset($_POST['7'])) $calc->numeros(7);
    if(isset($_POST['8'])) $calc->numeros(8);
    if(isset($_POST['9'])) $calc->numeros(9);
    if(isset($_POST['*'])) $calc->mult();

    if(isset($_POST['10^x'])) $calc->tentox();
    if(isset($_POST['4'])) $calc->numeros(4);
    if(isset($_POST['5'])) $calc->numeros(5);
    if(isset($_POST['6'])) $calc->numeros(6);
    if(isset($_POST['-'])) $calc->resta();

    if(isset($_POST['log'])) $calc->log();
    if(isset($_POST['1'])) $calc->numeros(1);
    if(isset($_POST['2'])) $calc->numeros(2);
    if(isset($_POST['3'])) $calc->numeros(3);
    if(isset($_POST['+'])) $calc->suma();

    if(isset($_POST['n!'])) $calc->fact();
    if(isset($_POST['+/-'])) $calc->changeSign();
    if(isset($_POST['0'])) $calc->numeros(0);
    if(isset($_POST['punto'])) $calc->punto();
    if(isset($_POST['Enter'])) $calc->enter();
    
    $_SESSION['calculadora'] = $calc;
}

$calc = $_SESSION['calculadora'];
echo"
<!DOCTYPE html>

<html lang='es'/>

<head>
    <meta charset='UTF-8'/>
    <!-- Metadatos de los documentos HTML5 -->
    <meta name='author' content='Jesús Alonso Gárcia'/>
    <!-- Definición de la ventana grafica -->
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <!-- Titulo de la página -->
    <title>Calculdora RPN</title>
    <!-- añadir el elemento link de enlace a laa hoja de estilo dentro del <head> del documento html -->
    <link rel='stylesheet' type='text/css' href='CalculadoraRPN.css'/>
</head>

<body>
    <!-- Calculadora-->
    <section>
        <h1> Calculadora </h1>
        <form action='#' method='post' name='CalculadoraRPN'>
            <label for='pila'>Values</label>
            <textarea id='pila' readonly >" .
            $calc->getTa() .
            "</textarea>
            <label for='pantalla'>Resultado</label>
            <input type='text' name='pantalla' id='pantalla' value='" . $calc->getScr() . "' readonly/>
            <input type='submit' ";
            if($calc->getDeg() == 'DEG') {
                echo "value='DEG'";
            } else if ($calc->getDeg() == 'RAD') {
                echo "value='RAD'";
            }  else {
                echo "value='GRAD'";
            }
            echo " name='DEG'/> 
            <input type='submit' value='HYP' name='HYP'/> 
            <input type='submit' value='F-E' name='F-E'/>

            
            <input type='submit' value='MC' name='MC'/> 
            <input type='submit' value='MR' name='MR'/> 
            <input type='submit' value='M+' name='M+'/> 
            <input type='submit' value='M-' name='M-'/>
            <input type='submit' value='MS' name='MS'/>";
            
            if($calc->getSecond()) {
                echo "<input type='submit' value='2nd' name='2nd'/> 
                <input type='submit' value='e' name='π'/> 
                <input type='submit' value='C' name='C'/> 
                <input type='submit' value='CE' name='CE'/> 
                <input type='submit' value='Del' name='Del'/>

                <input type='submit' value='x^3' name='x^2'/>";
                if($calc->getHyp()) {
                    echo "<input type='submit' value='asinh' name='sin'/> 
                    <input type='submit' value='acosh' name='cos'/> 
                    <input type='submit' value='atanh' name='tan'/>"; 
                } else {
                    echo "<input type='submit' value='asin' name='sin'/> 
                    <input type='submit' value='acos' name='cos'/> 
                    <input type='submit' value='atan' name='tan'/>";
                }

                echo "<input type='submit' value='mod' name='mod'/>

                <input type='submit' value='3√' name='√'/>
                <input type='submit' value='exp' name='exp'/>
                <input type='submit' value='Enter' name='Enter'/>  
                
                <input type='submit' value='y√x' name='x^y'/> 
                <input type='submit' value='7' name='7'/> 
                <input type='submit' value='8' name='8'/> 
                <input type='submit' value='9' name='9'/> 
                <input type='submit' value='/' name='/'/> 
                
                <input type='submit' value='2^x' name='10^x'/> 
                <input type='submit' value='4' name='4'/> 
                <input type='submit' value='5' name='5'/> 
                <input type='submit' value='6' name='6'/> 
                <input type='submit' value='*' name='*'/> 
                
                <input type='submit' value='ln' name='log'/> 
                <input type='submit' value='1' name='1'/> 
                <input type='submit' value='2' name='2'/> 
                <input type='submit' value='3' name='3'/> 
                <input type='submit' value='-' name='-'/> 
                
                <input type='submit' value='n!' name='n!'/> 
                <input type='submit' value='+/-' name='+/-'/> 
                <input type='submit' value='0' name='0'/> 
                <input type='submit' value='.' name='punto'/> 
                <input type='submit' value='+' name='+'/>";
            } else {
                echo "<input type='submit' value='2nd' name='2nd'/> 
                <input type='submit' value='π' name='π'/> 
                <input type='submit' value='C' name='C'/> 
                <input type='submit' value='CE' name='CE'/> 
                <input type='submit' value='Del' name='Del'/>

                <input type='submit' value='x^2' name='x^2'/> ";
                if($calc->getHyp()) {
                    echo "<input type='submit' value='sinh' name='sin'/> 
                    <input type='submit' value='cosh' name='cos'/> 
                    <input type='submit' value='tanh' name='tan'/>"; 
                } else {
                    echo "<input type='submit' value='sin' name='sin'/> 
                    <input type='submit' value='cos' name='cos'/> 
                    <input type='submit' value='tan' name='tan'/>";
                }
                echo "<input type='submit' value='mod' name='mod'/>

                <input type='submit' value='√' name='√'/>
                <input type='submit' value='exp' name='exp'/>
                <input type='submit' value='Enter' name='Enter'/> 
                
                <input type='submit' value='x^y' name='x^y'/> 
                <input type='submit' value='7' name='7'/> 
                <input type='submit' value='8' name='8'/> 
                <input type='submit' value='9' name='9'/> 
                <input type='submit' value='/' name='/'/> 
                
                <input type='submit' value='10^x' name='10^x'/> 
                <input type='submit' value='4' name='4'/> 
                <input type='submit' value='5' name='5'/> 
                <input type='submit' value='6' name='6'/> 
                <input type='submit' value='*' name='*'/> 
                
                <input type='submit' value='log' name='log'/> 
                <input type='submit' value='1' name='1'/> 
                <input type='submit' value='2' name='2'/> 
                <input type='submit' value='3' name='3'/> 
                <input type='submit' value='-' name='-'/> 
                
                <input type='submit' value='n!' name='n!'/> 
                <input type='submit' value='+/-' name='+/-'/> 
                <input type='submit' value='0' name='0'/> 
                <input type='submit' value='.' name='punto'/> 
                <input type='submit' value='+' name='+'/>" ;
            }

        echo "</form>
    </section>
</body>

</html>";
?>