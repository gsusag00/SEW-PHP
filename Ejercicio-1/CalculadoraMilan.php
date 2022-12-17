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
    if(isset($_POST['punto'])) $calc->punto();
    if(isset($_POST['='])) $calc->igual();
    if(isset($_POST['M+'])) $calc->m_plus();
    
    $_SESSION['calculadora'] = $calc;
}

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
    <title>Calculdora Milan</title>
    <!-- añadir el elemento link de enlace a laa hoja de estilo dentro del <head> del documento html -->
    <link rel='stylesheet' type='text/css' href='CalculadoraMilan.css'/>
</head>

<body>
    <!-- Calculadora-->
    <section>
        <h1> Calculadora </h1>
        <form action='#' method='post' name='CalculadoraMilan'>
            <label for='pantalla'>Resultado</label>
            <input type='text' name='pantalla' id='pantalla' value='" . $_SESSION['calculadora']->getScr() . "' readonly/>
            <input type='submit' value='C' name='C' /> 
            <input type='submit' value='CE' name='CE' /> 
            <input type='submit' value='+/-' name='+/-' /> 
            <input type='submit' value='√' name='√' /> 
            <input type='submit' value='%' name='%'/> 

            <input type='submit' value='7' name='7'/> 
            <input type='submit' value='8' name='8'/> 
            <input type='submit' value='9' name='9'/> 
            <input type='submit' value='*' name='*'/> 
            <input type='submit' value='/' name='/'/> 
            
            <input type='submit' value='4' name='4'/> 
            <input type='submit' value='5' name='5'/> 
            <input type='submit' value='6' name='6'/> 
            <input type='submit' value='-' name='-'/> 
            <input type='submit' value='MRC' name='MRC'/> 
            
            <input type='submit' value='1' name='1'/> 
            <input type='submit' value='2' name='2'/> 
            <input type='submit' value='3' name='3'/> 
            <input type='submit' value='+' name='+'/> 
            <input type='submit' value='M-' name='M-'/> 
            
            <input type='submit' value='0' name='0'/> 
            <input type='submit' value='.' name='punto'/> 
            <input type='submit' value='=' name='='/> 
            <input type='submit' value='M+' name='M+'/> 
        </form>
    </section>
</body>

</html>";
?>