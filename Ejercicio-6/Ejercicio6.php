<?php

session_start();

class BaseDatos
{

    protected $servername;
    protected $username;
    protected $passwd;
    protected $dbname;
    protected $tablename;
    protected $mensaje;

    public function __construct() {
        $this->servername = "localhost";
        $this->username = "DBUSER2022";
        $this->passwd = "DBPSWD2022";
        $this->dbname = "sew_php";
        $this->tablename = "pruebasusabilidad";
    }

    public function createBD() {
        $connection = new mysqli($this->servername, $this->username, $this->passwd);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        $query = "CREATE DATABASE IF NOT EXISTS " . $this->dbname;
        if ($connection->query($query) === TRUE) {
            $this->mensaje = "<p>La base de datos " . $this->dbname . " se creo correctamente</p>";
        } else {
            $this->mensaje = "<p>Ha ocurrido un error creando la base de datos: " . $connection->error . "</p>";
        }
        $connection->close();
    }

    public function createTable() {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        $query = "CREATE TABLE IF NOT EXISTS " . $this->tablename . " (
            `ID_DNI` varchar(255) PRIMARY KEY,
            `NOMBRE` varchar(255) NOT NULL,
            `APELLIDOS` varchar(255) NOT NULL,
            `EMAIL` varchar(255) NOT NULL,
            `TELEFONO` varchar(255) NOT NULL,
            `EDAD` int(255) NOT NULL,
            `SEXO` varchar(255) NOT NULL,
            `NIVEL` int(255) NOT NULL,
            `TIEMPO` int(255) NOT NULL,
            `CORRECTO` tinyint(1) NOT NULL,
            `COMENTARIOS` varchar(255) NOT NULL,
            `PROPUESTAS` varchar(255) NOT NULL,
            `VALORACION` int(255) NOT NULL);";
        if ($connection->query($query) === TRUE) {
            $this->mensaje = "<p>La base de datos " . $this->tablename . " se creo correctamente</p>";
        } else {
            $this->mensaje = "<p>Ha ocurrido un error creando la base de datos: " . $connection->error . "</p>";
        }
        $connection->close();
    }

    public function insertarDatos($id,$nombre,$apellidos,$email,$telefono,$edad,$sexo,$nivel,$tiempo,$correcto,$comentarios,$propuestas,$valoracion) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = $connection->prepare("INSERT INTO " . $this->tablename . "(
            ID_DNI,
            NOMBRE,
            APELLIDOS,
            EMAIL,
            TELEFONO,
            EDAD,
            SEXO,
            NIVEL,
            TIEMPO,
            CORRECTO,
            COMENTARIOS,
            PROPUESTAS,
            VALORACION)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $query->bind_param('sssssisiiissi',
            $id,
            $nombre,
            $apellidos,
            $email,
            $telefono,
            $edad,
            $sexo,
            $nivel,
            $tiempo,
            $correcto,
            $comentarios,
            $propuestas,
            $valoracion);
        try {
            $query->execute();
            if($query) {
                $this->mensaje = "<p>Datos insertados</p>";
            } else {
                $this->mensaje = "<p>Ha surgido un error al insertar datos</p>";
            }
        } catch (mysqli_sql_exception $e) {
            $this->mensaje = "<p>Ha surgido un error: " . $e->getMessage() . "</p>";
        }
        $query->close();
        $connection->close();
    }

    public function buscar($id) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = $connection->prepare("SELECT * 
            FROM " . $this->tablename . 
            " WHERE ID_DNI=?");

        $query->bind_param('s',
            $id);
        $query->execute();
        $res = $query->get_result();
        if($res->num_rows == 1) {
            $pers = mysqli_fetch_assoc($res);
            $this->mensaje = "<h2>Datos encontrados</h2> 
            <ul>
                <li>DNI: " . $pers['ID_DNI'] . "</li>
                <li>Nombre: " . $pers['NOMBRE'] . "</li>
                <li>Apellidos: " . $pers['APELLIDOS'] . "</li>
                <li>Email: " . $pers['EMAIL'] . "</li>
                <li>Telefono: " . $pers['TELEFONO'] . "</li>
                <li>Edad: " . $pers['EDAD'] . "</li>
                <li>Sexo: " . $pers['SEXO'] . "</li>
                <li>Nivel: " . $pers['NIVEL'] . "</li>
                <li>Tiempo: " . $pers['TIEMPO'] . "</li>
                <li>Correcto: " . ($pers['CORRECTO'] == 1? "SI" : "NO") . "</li>
                <li>Comentarios: " . $pers['COMENTARIOS'] . "</li>
                <li>Propuestas: " . $pers['PROPUESTAS'] . "</li>
                <li>Nota: " . $pers['VALORACION'] . "</li>
            </ul>";
        } else {
            $this->mensaje = "<p>No se ha encontrado nada</p>";
        }
        $query->close();
        $connection->close();
    }

    private function findId($id) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = $connection->prepare("SELECT * 
            FROM " . $this->tablename . 
            " WHERE ID_DNI=?");

        $query->bind_param('s',
            $id);
        $query->execute();
        $res = $query->get_result();
        if($res->num_rows == 1) {
            $query->close();
            $connection->close();
            return true;
        }
        $query->close();
        $connection->close();
        return false;
    }

    public function modificar($id,$datos,$columns,$tipos) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        if (!$this->findId($id)) {
            $this->mensaje = "<p>No existe persona con este ID.</p>";
            $connection->close();
            return;
        }

        $query = "UPDATE " . $this->tablename . " SET ";
        for ($i = 0; $i < sizeof($columns); $i++) {
            $query .= $columns[$i] . "= ?";

            if ($i != sizeof($columns) - 1) {
                $query .= ", ";
            }
        }

        $query .= " WHERE ID_DNI = ?";

        $tipos .= "s"; //tipo del id

        array_push($datos,$id);

        $preparedStatement = $connection->prepare($query);

        $preparedStatement->bind_param($tipos, ...$datos);

        $preparedStatement->execute();

        if ($preparedStatement) {
            $this->mensaje = "<p>Datos modificados correctamente.</p>";
        }
        else {
            $this->mensaje = "<p>Error modificando los datos... $query</p>";
        }
        $preparedStatement->close();
        $connection->close();
    }

    public function eliminar($id) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        if (!$this->findId($id)) {
            $this->mensaje = "<p>No existe persona con este ID.</p>";
            $connection->close();
            return;
        }

        $query = $connection->prepare("DELETE FROM " . $this->tablename . 
            " WHERE ID_DNI=?");

        $query->bind_param('s',
            $id);
        $query->execute();
        if($query) {
            $query->close();
            $connection->close();
            $this->mensaje = "<p>El usuario: " . $id . " se ha eliminado correctamente</p>";
        } else {
            $connection->close();
            $this->mensaje = "<p>Se ha producido un error al eliminar el usuario " . $id . "</p>";
        }
    }

    public function generarInforme() {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = "SELECT * 
            FROM " . $this->tablename . ";";
        $res = $connection->query($query);
        $totalrows = $res->num_rows;
        $totalage = 0;
        $totalComplete = 0;
        $totallevel = 0;
        $totaltime = 0;
        $totalScore = 0;
        $males = 0;
        if($res) {
            
            while ($fila = $res->fetch_array()) {
                $totalage += $fila['EDAD'];
                $totalComplete += $fila['CORRECTO'];
                $totallevel += $fila['NIVEL'];
                $totaltime += $fila['TIEMPO'];
                if($fila['SEXO'] == 'Masculino') {
                    $males++;
                }
                $totalScore += $fila['VALORACION'];
            }

            $meanage = $totalage / $totalrows;
            $meancomplete = ($totalComplete / $totalrows) * 100;
            $meanlevel = $totallevel / $totalrows;
            $meantime = $totaltime / $totalrows;
            $meanscore = $totalScore / $totalrows;
            $malespct = ($males / $totalrows) * 100;

            $this->mensaje = "<h2>Resultados del informe</h2>
            <ul>
                <li>Edad media: " . $meanage . " años</li>
                <li>Porcentaje hombres: " . $malespct . "%</li>
                <li>Porcentaje mujeres: " . 100 - $malespct . "%</li>
                <li>Nivel medio: " . $meanlevel . "</li>
                <li>Tiempo medio: " . $meantime . " segundos</li>
                <li>Usuarios que han realizado la tarea correctamente: " . $meancomplete . "%</li>
                <li>Valoración media de la tarea: " . $meanscore . " puntos sobre 10</li>
            </ul>";
        }
        else {
            $this->mensaje = "<p>Ha ocurrido un error al generar el informe.</p>";
        }
        $connection->close();
    }

    public function getMessage() {
        return $this->mensaje;
    }

    public function generarCSV() {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = "SELECT * 
            FROM " . $this->tablename . ";";
        $res = $connection->query($query);
        ob_clean();
        ob_start();

        $file = fopen("php://output", 'w');
        $sqlfields = mysqli_fetch_fields($res);
        $fields = array();
        foreach($sqlfields as $field) {
            array_push($fields, $field->name);
        }

        header("Content-Disposition: attachment; filename=pruebasUsabilidad.csv");
        header("Content-Type: text/csv");

        fputcsv($file, $fields);
        while($row = $res->fetch_assoc()) {
            fputcsv($file,$row);
        }

        fpassthru($file);

        $res->close();
        $connection->close();
        if(fclose($file)) {
            $this->mensaje = "<p>El archivo csv se a generado correctamente</p>";
        } else {
            $this->mensaje = "<p>Ha surgido un error al generar el archivo CSV</p>";
        }

        exit();
    }

    public function cargarCSV() {
        $name = $_FILES['file']['tmp_name'];
        $arr = array_map('str_getcsv', file($name));
        array_shift($arr);
        foreach($arr as $row) {
            $this->insertarDatos($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12]);
        }
    }

}




if (!isset($_SESSION['bd'])) {


    $bd = new BaseDatos();
    $_SESSION['bd'] = $bd;

}

if (count($_POST) > 0) {

    $bd = $_SESSION['bd'];
    if(isset($_POST['create'])) {
        $bd->createBD();
    }
    if(isset($_POST['createTable'])) {
        $bd->createTable();
    }
    if(isset($_POST['insertarDatos'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $edad= $_POST['edad'];
        $sexo = $_POST['sexo'];
        $nivel= $_POST['nivel'];
        $tiempo= $_POST['tiempo'];
        $correcto = $_POST['select'] == 'si';
        $comentarios= $_POST['comentarios'];
        $propuestas= $_POST['propuestas'];
        $valoracion = $_POST['valoracion'];
        $bd->insertarDatos($id, $nombre, $apellidos, $email, $telefono, $edad, $sexo, $nivel, $tiempo, $correcto, $comentarios, $propuestas, $valoracion);
    }
    if(isset($_POST['modificarDatos'])) {
        $data = array();
        $columns = array();
        $tipos = "";
        $canedit = false;
        if($_POST['id']){
            $id = $_POST['id'];
            $canedit = true;
        }
        if($_POST['nombre']){
            $nombre = $_POST['nombre'];
            array_push($data, $nombre);
            array_push($columns,"NOMBRE");
            $tipos .= "s";
        }
        if($_POST['apellidos']){
            $apellidos = $_POST['apellidos'];
            array_push($data, $apellidos);
            array_push($columns,"APELLIDOS");
            $tipos .= "s";
        }
        if($_POST['email']){
            $email = $_POST['email'];
            array_push($data, $email);
            array_push($columns,"EMAIL");
            $tipos .= "s";
        }
        if($_POST['telefono']){
            $telefono = $_POST['telefono'];
            array_push($data, $telefono);
            array_push($columns,"TELEFONO");
            $tipos .= "s";
        }
        if($_POST['edad']){
            $edad= $_POST['edad'];
            array_push($data, $edad);
            array_push($columns,"EDAD");
            $tipos .= "i";
        }
        if($_POST['sexo']){
            $sexo = $_POST['sexo'];
            array_push($data, $sexo);
            array_push($columns,"SEXO");
            $tipos .= "s";
        }
        if($_POST['nivel']){
            $nivel= $_POST['nivel'];
            array_push($data, $nivel);
            array_push($columns,"NIVEL");
            $tipos .= "i";
        }
        if($_POST['tiempo']){
            $tiempo= $_POST['tiempo'];
            array_push($data, $tiempo);
            array_push($columns,"TIEMPO");
            $tipos .= "i";
        }
        if($_POST['select']){
            $correcto = $_POST['select'] == 'si';
            array_push($data, $correcto);
            array_push($columns,"CORRECTO");
            $tipos .= "i";
        }
        if($_POST['comentarios']){
            $comentarios= $_POST['comentarios'];
            array_push($data, $comentarios);
            array_push($columns,"COMENTARIOS");
            $tipos .= "s";
        }
        if($_POST['propuestas']){
            $propuestas= $_POST['propuestas'];
            array_push($data, $propuestas);
            array_push($columns,"PROPUESTAS");
            $tipos .= "s";
        }
        if($_POST['valoracion']){
            $valoracion = $_POST['valoracion'];
            array_push($data, $valoracion);
            array_push($columns,"VALORACION");
            $tipos .= "s";
        }
        if($canedit) {
            $bd->modificar($id,$data, $columns, $tipos);
        }
    }
    if(isset($_POST['buscar'])) {
        $id = $_POST['id'];
        $bd->buscar($id);
    }
    if(isset($_POST['eliminar'])) {
        $id = $_POST['id'];
        $bd->eliminar($id);
    }
    if(isset($_POST['informe'])) {
        $bd->generarInforme();
    }
    if(isset($_POST['descargar'])) {
        $bd->generarCSV();
    }
    if(isset($_POST['importar'])) {
        $bd->cargarCSV();
    }

}


echo "<!DOCTYPE HTML>

<html lang='es'/>

<head>
    <!-- Datos que describen el documento -->

    <meta charset='UTF-8' />
    <title>Ejercicio 6</title>
    <link rel='stylesheet' type='text/css' href='Ejercicio6.css' />
    

    <meta name='author' content='Jesús Alonso García' />
    <meta name='description' content='Ejercicio 6' />
    <meta name ='viewport' content ='width=device-width, initial scale=1.0' />
    
</head>
<body>

    <h1>GESTION DE BASE DE DATOS</h1>
    <nav>
        <ul>
            <li> <a accesskey='i' href='#crear' tabindex='1'> Crear base de datos </a></li>
            <li> <a accesskey='i' href='#tabla' tabindex='1'> Crear una tabla </a></li>
            <li> <a accesskey='i' href='#insertar' tabindex='1'> Insertar datos en una tabla</a></li>
            <li> <a accesskey='i' href='#buscar' tabindex='1'> Buscar en una tabla</a></li>
            <li> <a accesskey='i' href='#modificar' tabindex='1'> Modificar datos en una tabla</a></li>
            <li> <a accesskey='i' href='#eliminar' tabindex='1'> Eliminar datos de una tabla</a></li>
            <li> <a accesskey='i' href='#generar' tabindex='1'> Generar informe </a></li>
            <li> <a accesskey='i' href='#importar' tabindex='1'> Cargar datos desde un archivo CSV en una tabla de la base de datos </a></li>
            <li> <a accesskey='i' href='#descargar' tabindex='1'> Exportar datos a un archivo en formato CSV los datos de una tabla de la base de datos </a></li>
        </ul>
    </nav>
    <section>
        <h2>Menu:</h2>
        
        <h3 id='crear'>Crear base de datos</h3>
        <form action='#' method='post'>
            <input type='submit' name='create' value='Crear base de datos'/>
            </form>
            
            <h3 id='tabla'>Crear tabla</h3>
            <form action='#' method='post'>
            <input type='submit' name='createTable' value='Crear tabla'/>
            </form>

            <h3 id='insertar'>Insertar datos</h3>
            <form action='#' method='post'>
            <label for='id'>Identificador (DNI) :</label>
            <input type='text' name='id' id='id'/>

            <label for='nombre'>Nombre:</label>
            <input type='text' name='nombre' id='nombre'/>
            
            <label for='apellidos'>Apellidos:</label>
            <input type='text' name='apellidos' id='apellidos'/>
            
            <label for='email'>Email:</label>
            <input type='text' name='email' id='email'/>
            
            <label for='telefono'>Teléfono:</label>
            <input type='text' name='telefono' id='telefono'/>
            
            <label for='edad'>Edad:</label>
            <input type='number' name='edad' id='edad' min='0'/>
            
            <label for='sexo'>Sexo:</label>
            <input type='text' name='sexo' id='sexo'/>
            
            <label for='nivel'>Nivel informático (0-10):</label>
            <input type='number' name='nivel' id='nivel' min='0' max='10'/>
            
            <label for='tiempo'>Tiempo empleado (segundos):</label>
            <input type='number' name='tiempo' id='tiempo'/>
            
            <label for='select'>Realización de la tarea:</label>
            <select id='select' name='select'>
            <option value='si'>SI</option>
            <option value='no'>NO</option>
            </select>
            
            <label for='comentarios'>Comentarios:</label>
            <input type='text' name='comentarios' id='comentarios'/>
            
            <label for='propuestas'>Propuestas de mejora:</label>
            <input type='text' name='propuestas' id='propuestas'/>
            
            <label for='valoracion'>Valoración de la app (0-10):</label>
            <input type='number' name='valoracion' id='valoracion' min='0' max='10'/>
            
            <input type='submit' name='insertarDatos' value='Insertar datos en la tabla'/>
            </form>
            
            <h3 id='buscar'>Buscar datos</h3>
            <form action='#' method='post'>
            <label for='idToMod'>Identifcador de la persona a buscar:</label>
            <input type='text' name='id' id='idToMod'/>
            <input type='submit' name='buscar' value='Buscar'/>
            </form>
            
            <h3 id='modificar'>Modificar datos</h3>
            <form action='#' method='post'>
            <label for='idMod'>Identificador (DNI) :</label>
            <input type='text' name='id' id='idMod'/>
            
            <label for='nombreMod'>Nombre:</label>
            <input type='text' name='nombre' id='nombreMod'/>
            
            <label for='apellidosMod'>Apellidos:</label>
            <input type='text' name='apellidos' id='apellidosMod'/>
            
            <label for='emailMod'>Email:</label>
            <input type='text' name='email' id='emailMod'/>
            
            <label for='telefonoMod'>Teléfono:</label>
            <input type='text' name='telefono' id='telefonoMod'/>
            
            <label for='edadMod'>Edad:</label>
            <input type='number' name='edad' id='edadMod' min='0'/>
            
            <label for='sexoMod'>Sexo:</label>
            <input type='text' name='sexo' id='sexoMod'/>
            
            <label for='nivelMod'>Nivel informático (0-10):</label>
            <input type='number' name='nivel' id='nivelMod' min='0' max='10'/>
            
            <label for='tiempoMod'>Tiempo empleado (segundos):</label>
            <input type='number' name='tiempo' id='tiempoMod'/>
            
            <label for='selectMod'>Realización de la tarea:</label>
            <select id='selectMod' name='select'>
                <option value='si'>SI</option>
                <option value='no'>NO</option>
                </select>
                
                <label for='comentariosMod'>Comentarios:</label>
                <input type='text' name='comentarios' id='comentariosMod'/>
                
                <label for='propuestasMod'>Propuestas de mejora:</label>
                <input type='text' name='propuestas' id='propuestasMod'/>

                <label for='valoracionMod'>Valoración de la app (0-10):</label>
                <input type='number' name='valoracion' id='valoracionMod' min='0' max='10'/>
                
                <input type='submit' name='modificarDatos' value='Modificar datos'/>
        </form>
        
        <h3 id='eliminar'>Eliminar datos</h3>
        <form action='#' method='post'>
            <label for='idToSearch'>Identifcador de la persona a eliminar:</label>
            <input type='text' name='id' id='idToSearch'/>
            <input type='submit' name='eliminar' value='Eliminar'/>
        </form>

        <h3 id='generar'>Generar informe</h3>
        <form action='#' method='post'>
            <input type='submit' name='informe' value='Generar informe'/>
        </form>

        <h3 id='descargar'>Descargar CSV</h3>
        <form action='#' method='post'>
            <input type='submit' name='descargar' value='Descargar CSV'/>
        </form>

        <h3 id='importar'>Importar CSV</h3>
        <form action='#' method='post' enctype='multipart/form-data'>
            <label for='archivo'>Archivo a subir</label>
            <input type='file' id='archivo' name='file' accept='.csv,application/vnd.ms-excel'/>
            <input type='submit' name='importar' value='Importar CSV'/>
        </form>
    </section>" . 
    $_SESSION['bd']->getMessage() . "
</body>

</html>";


?>