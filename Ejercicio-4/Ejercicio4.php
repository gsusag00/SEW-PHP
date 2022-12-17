<?php

session_start();

class CopperPrice {
    
    protected $iscall;
    protected $apiKey;
    protected $curr;
    protected $date;
    protected $url;
    protected $price;

    public function __construct() {
        $this->url = "https://metals-api.com/api/";
        $this->apiKey = "ajse4s0ek31j5xa91wht9sajwo024m6iygoes8ta70rwbz5sj447x50y4j8t";
        $this->iscall = false;
        $this->curr = "";
        $this->date = "";
    }

    public function printRes() {
        if($this->iscall) {
            $this->iscall = false;
            $str = "<h2>Estos son los datos sobre el precio del cobre.</h2>";
            $fecha = $this->date;
            $sym = "€";
            if($this->curr === "USD") {
                $sym = "$";
            } else if ($this->curr === "GBP") {
                $sym = "£";
            }
            if($fecha === 'latest') {
                $fecha = date("Y-m-d");
            }
            $str.= "<p>El precio del cobre es a " . $fecha . ": " . $this->price . " " . $sym . "/onza</p>";
            return $str;
        }
    }

    public function currentDate() {
        return date("Y-m-d");
    }

    public function getCopperPrice($curr,$date) {
        $this->curr = $curr;
        if(empty($date)) {
            $this->date = 'latest';
        } else {
            $this->date = $date;
        }
        $this->doRequest();
        $this->iscall = true;
    }

    public function doRequest() {
        $toreq = $this->url . $this->date . "?access_key=" . $this->apiKey . "&base=" . $this->curr . "&symbols=XCU";
        $ch = curl_init($toreq);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $rates = json_decode($json, true);
        $this->price = $rates['rates']['XCU'];
    }

}

if(!isset($_SESSION['cop'])) {
    $cop = new CopperPrice();
    $_SESSION['cop'] = $cop;
}

if(count($_POST) > 0) {
    $cop = $_SESSION['cop'];
    $curr = $_POST['currency'];
    if(isset($_POST['date'])) $date = $_POST['date'];
    if(isset($_POST['data'])) $cop->getCopperPrice($curr,$date);

    $_SESSION['cop'] = $cop;
}

echo "
<!DOCTYPE html>

<html lang='es'/>

<head>
    <meta charset='UTF-8'/>
    <!-- Metadatos de los documentos HTML5 -->
    <meta name='author' content='Jesús Alonso Gárcia'/>
    <!-- Definición de la ventana grafica -->
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <!-- Titulo de la página -->
    <title>Ejercicio 4</title>
    <!-- añadir el elemento link de enlace a la hoja de estilo dentro del <head> del documento html -->
    <link rel='stylesheet' type='text/css' href='Ejercicio4.css'/>
</head>

<body>
    <h1>Precio del cobre</h1>
    <section>
        <form action='#' method='post' name='PrecioCobre'> 
            <label for='fecha'>Escoge la fecha: </label>
            <input type='date' name ='date' id='fecha' max='";
            echo $_SESSION['cop']->currentDate();
            echo "'>
            <label for='currency'> Escoge la divisa: </label>
            <select id='currency' name='currency'>
                <option value='EUR'> Euros </option>
                <option value='USD'> Dolar </option>
                <option value='GBP'> Libra </option>
            </select>
            <input type='submit' value='Mirar el precio del cobre' name='data'/>
        </form>";
    echo $_SESSION['cop']->printRes();
    echo "</section>
    </body>

</html>
";

?>