<?php

session_start();

class MundialManager {

    protected $servername;
    protected $username;
    protected $passwd;
    protected $dbname;
    protected $mensaje;

    public function __construct() {
        $this->servername = "localhost";
        $this->username = "DBUSER2022";
        $this->passwd = "DBPSWD2022";
        $this->dbname = "mundiales";
    }

    public function insertarMundial($nombre,$dueño) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        $id = $connection->query("SELECT * 
        FROM mundial;")->num_rows + 1;

        $query = $connection->prepare("INSERT INTO mundial(
            mundial_id,
            nombre,
            dueño)
            VALUES(?,?,?)");

        $query->bind_param('sss',
            $id,
            $nombre,
            $dueño
        );
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

    public function insertarCircuito($circuito,$lugar,$longitud) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        $id = $connection->query("SELECT * 
        FROM carrera;")->num_rows + 1;

        $query = $connection->prepare("INSERT INTO carrera(
            carrera_id,
            nombre,
            localizacion,
            longitud)
            VALUES(?,?,?,?)");

        $query->bind_param('sssi',
            $id,
            $circuito,
            $lugar,
            $longitud
        );
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

    public function insertarEscuderia($escuderia,$origen) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        $id = $connection->query("SELECT * 
        FROM escuderia;")->num_rows + 1;

        $query = $connection->prepare("INSERT INTO escuderia(
            escuderia_id,
            nombre,
            origen)
            VALUES(?,?,?)");

        $query->bind_param('sss',
            $id,
            $escuderia,
            $origen
        );
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

    public function buscar($nombre,$entidad){
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        $sql = "SELECT * FROM " . $entidad . " WHERE nombre =?";
        $query = $connection->prepare($sql);

        $query->bind_param('s',
            $nombre);
        $query->execute();
        $res = $query->get_result();
        if($res->num_rows > 0) {
            $fila = $res->fetch_array();
            $this->mensaje = "<h2>Datos de " . $nombre . "</h2>
            <ul>"
            . $this->printData($fila,$entidad) . "</ul>";
        } else {
            $this->mensaje = "<p>No se ha encontrado nada</p>";
        }
        $query->close();
        $connection->close();
    }

    private function printData($data,$entidad) {
        $str = "";
        if($entidad == 'mundial') {
            $str .= '<li> Nombre: ' . $data['nombre'] . '</li>
            <li>Dueño: ' . $data['dueño'] . '</li>';
        } else if ($entidad == 'carrera') {
            $str .= '<li> Nombre: ' . $data['nombre'] . '</li>
            <li>Localizacion: ' . $data['localizacion'] . '</li>
            <li>Longitud: ' . $data['longitud'] . ' metros</li>';
        } else {
            $str .= '<li> Nombre: ' . $data['nombre'] . '</li>
            <li>Origen : ' . $data['origen'] . '</li>';
        }
        return $str;
    } 

    public function assignEscuderia($mundial,$escuderia) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = $connection->prepare("SELECT * 
            FROM mundial WHERE nombre=?");

        $query->bind_param('s',
            $mundial);
        $query->execute();
        $res = $query->get_result();
        $id_mundial = $res->fetch_array()['mundial_id'];
        $query = $connection->prepare("SELECT * 
            FROM escuderia WHERE nombre=?");
        $query->bind_param('s',
            $escuderia);
        $query->execute();
        $res = $query->get_result();
        $id_escuderia = $res->fetch_array()['escuderia_id'];
        $query = $connection->prepare("INSERT INTO compiteen(
            mundial,
            escuderia)
            VALUES(?,?)");

        $query->bind_param('ss',
            $id_mundial,
            $id_escuderia
        );
        try {
            $query->execute();
            if($query) {
                $this->mensaje = "<p>Se ha asignado la carrera correctamente</p>";
            } else {
                $this->mensaje = "<p>Ha surgido un error al insertar datos</p>";
            }
        } catch (mysqli_sql_exception $e) {
            $this->mensaje = "<p>Ha surgido un error: " . $e->getMessage() . "</p>";
        }
    }

    public function assignCircuito($mundial,$circuito) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = $connection->prepare("SELECT * 
            FROM mundial WHERE nombre=?");

        $query->bind_param('s',
            $mundial);
        $query->execute();
        $res = $query->get_result();
        $id_mundial = $res->fetch_array()['mundial_id'];
        $query = $connection->prepare("SELECT * 
            FROM carrera WHERE nombre=?");
        $query->bind_param('s',
            $circuito);
        $query->execute();
        $res = $query->get_result();
        $id_circuito = $res->fetch_array()['carrera_id'];
        $query = $connection->prepare("INSERT INTO circuitosmundial(
            mundial,
            circuito)
            VALUES(?,?)");

        $query->bind_param('ss',
            $id_mundial,
            $id_circuito
        );
        try {
            $query->execute();
            if($query) {
                $this->mensaje = "<p>Se ha asignado la carrera correctamente</p>";
            } else {
                $this->mensaje = "<p>Ha surgido un error al insertar datos</p>";
            }
        } catch (mysqli_sql_exception $e) {
            $this->mensaje = "<p>Ha surgido un error: " . $e->getMessage() . "</p>";
        }
    }

    private function findId($nombre,$entidad) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        $query = $connection->prepare("SELECT * 
            FROM " . $entidad . 
            " WHERE nombre=?");

        $query->bind_param('s',
            $nombre);
        $id = null;
        try {
            $query->execute();
            if($entidad == 'mundial') {
                $id = $query->get_result()->fetch_array()['mundial_id'];
            } else if ($entidad == 'carrera') {
                $id = $query->get_result()->fetch_array()['carrera_id'];
            } else {
                $id = $query->get_result()->fetch_array()['escuderia_id'];
            }
            $query->close();
            $connection->close();
            return $id;
        } catch (mysqli_sql_exception $e) {
            $this->mensaje = "<p>Ha surgido un error: " . $e->getMessage() . "</p>";
        }
    }

    private function deleteAssociation($id,$entidad) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }
        try {
            if($entidad == 'mundial') {
                $query = $connection->prepare("DELETE FROM compiteen WHERE mundial=?");

                $query->bind_param('s',$id);
                $query->execute();
                $query = $connection->prepare("DELETE FROM circuitosmundial WHERE mundial=?");

                $query->bind_param('s',$id);
                $query->execute();
            } else if ($entidad == 'carrera') {
                $query = $connection->prepare("DELETE FROM circuitosmundial WHERE circuito=?");

                $query->bind_param('s',$id);
                $query->execute();
            } else {
                $query = $connection->prepare("DELETE FROM compiteen WHERE escuderia=?");

                $query->bind_param('s',$id);
                $query->execute();
            }
            $query->close();
            $connection->close();
        } catch (mysqli_sql_exception $e) {
            $this->mensaje = "<p>Ha surgido un error: " . $e->getMessage() . "</p>";
        }
    }

    public function eliminar($nombre,$entidad) {
        $connection = new mysqli($this->servername, $this->username, $this->passwd, $this->dbname);

        if($connection->connect_error) {
            $this->mensaje = "Fallo de conexion: " . $connection->connect_error;
        }

        
        $id = $this->findId($nombre,$entidad);
        $this->deleteAssociation($id, $entidad);
        $query = $connection->prepare("DELETE FROM " . $entidad . 
            " WHERE nombre=?");

        $query->bind_param('s',
            $nombre);
        $query->execute();
        if($query) {
            $this->mensaje = "<p>Se ha eliminado " . $entidad . " : " . $nombre . " correctamente</p>";
        } else {
            $query->close();
            $connection->close();
            $this->mensaje = "<p>Se ha producido un error al eliminar el " . $entidad . " " . $nombre . "</p>";
        }
    }

    public function getMessage() {
        return $this->mensaje;
    }

    public function setMessage($message) {
        $this->mensaje = $message;
    }

}


if (!isset($_SESSION['mm'])) {


    $mm = new MundialManager();
    $_SESSION['mm'] = $mm;

}

if (count($_POST) > 0) {

    $mm = $_SESSION['mm'];
    if(isset($_POST['addMundial'])) {
        $nombre = $_POST['nombre'];
        $dueño = $_POST['dueño'];
        if(!empty($nombre) && !empty($dueño)) {
            $mm->insertarMundial($nombre,$dueño);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }

    if(isset($_POST['addCircuito'])) {
        $circuito = $_POST['circuito'];
        $lugar = $_POST['lugar'];
        $longitud = $_POST['longitud'];
        if(!empty($circuito) && !empty($lugar)) {
            $mm->insertarCircuito($circuito,$lugar,$longitud);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }

    if(isset($_POST['addEscuderia'])) {
        $escuderia = $_POST['escuderia'];
        $origen = $_POST['origen'];
        if(!empty($escuderia) && !empty($origen)) {
            $mm->insertarEscuderia($escuderia,$origen);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }
    if(isset($_POST['buscar'])) {
        $nombre = $_POST['id'];
        $entity = $_POST['entity'];
        if(!empty($nombre)) {
            $mm->buscar($nombre, $entity);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }
    if(isset($_POST['assignEscuderia'])) {
        $mundial = $_POST['idNomMund'];
        $circuito = $_POST['nomEsc'];
        if(!empty($mundial) && !empty($circuito)) {
            $mm->assignEscuderia($mundial, $circuito);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }
    if(isset($_POST['assignCircuito'])) {
        $mundial = $_POST['idNomMundial'];
        $circuito = $_POST['nomCir'];
        if(!empty($mundial) && !empty($circuito)) {
            $mm->assignCircuito($mundial, $circuito);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }
    if(isset($_POST['eliminar'])) {
        $nombre = $_POST['todelete'];
        $entity = $_POST['entityDel'];
        if(!empty($nombre)) {
            $mm->eliminar($nombre, $entity);
        } else {
            $mm->setMessage("<p>Todos los campos tienen que estar rellenos</p>");
        }
    }

}

echo "<!DOCTYPE HTML>

<html lang='es'>

<head>
    <!-- Datos que describen el documento -->

    <meta charset='UTF-8' />
    <title>Ejercicio 7</title>
    <link rel='stylesheet' type='text/css' href='Ejercicio7.css' />
    

    <meta name='author' content='Jesús Alonso García' />
    <meta name='description' content='Ejercicio 7' />
    <meta name ='viewport' content ='width=device-width, initial scale=1.0' />
    
</head>
<body>

    <h1>Mundial Manager</h1>
    <nav>
        <ul>
            <li> <a accesskey='m' href='#AñadirMundial' tabindex='1'> Añadir Mundial </a></li>
            <li> <a accesskey='c' href='#AñadirCircuito' tabindex='2'> Añadir circuito </a></li>
            <li> <a accesskey='e' href='#AñadirEscuderia' tabindex='3'> Añadir escuderia </a></li>
            <li> <a accesskey='b' href='#buscar' tabindex='4'> Busqueda </a></li>
            <li> <a accesskey='s' href='#escuderia2mundial' tabindex='5'> Inscribir escuderia </a></li>
            <li> <a accesskey='i' href='#circuito2mundial' tabindex='6'> Inscribir circuito </a></li>
            <li> <a accesskey='l' href='#eliminar' tabindex='7'> Eliminar elementos </a></li>
        </ul>
    </nav>
    <section>
        <h2>Menu:</h2>

            <h3 id='AñadirMundial'>Añadir mundial</h3>
                <form action='#' method='post'>
                <label for='nombre'>Nombre del mundial :</label>
                <input type='text' name='nombre' id='nombre'/>

                <label for='dueño'>Dueño:</label>
                <input type='text' name='dueño' id='dueño'/>
                <input type='submit' name='addMundial' value='Añadir Mundial'/>
            </form>

            <h3 id='AñadirCircuito'>Añadir circuito</h3>
            <form action='#' method='post'>
                <label for='circuito'>Nombre del circuito :</label>
                <input type='text' name='circuito' id='circuito'/>

                <label for='lugar'>Localización:</label>
                <input type='text' name='lugar' id='lugar'/>
                
                <label for='longitud'>Longitud:</label>
                <input type='number' min='0' name='longitud' id='longitud'/>
                <input type='submit' name='addCircuito' value='Añadir Circuito'/>
            </form>

            <h3 id='AñadirEscuderia'>Añadir Escuderia</h3>
            <form action='#' method='post'>
                <label for='escuderia'>Nombre de la escuderia:</label>
                <input type='text' name='escuderia' id='escuderia'/>

                <label for='origen'>Origen:</label>
                <input type='text' name='origen' id='origen'/>
                <input type='submit' name='addEscuderia' value='Añadir Escuderia'/>
            </form>
            
            <h3 id='buscar'>Buscar datos por nombre</h3>
            <form action='#' method='post'>
            <label for='idToMod'>Nombre :</label>
            <input type='text' name='id' id='idToMod'/>
            <label for='entity'>¿Sobre que quieres buscar?: </label>
            <select id='entity' name='entity'>
                <option value='escuderia'> Escuderia </option>
                <option value='mundial'> Mundial </option>
                <option value='carrera'> Circuito </option>
            </select>
            <input type='submit' name='buscar' value='Buscar'/>
            </form>
            
            <h3 id='escuderia2mundial'>Añade una escuderia a un mundial</h3>
            <form action='#' method='post'>
                <label for='idNomMund'>Nombre del mundial :</label>
                <input type='text' name='idNomMund' id='idNomMund'/>

                <label for='nomEsc'>Nombre de la escuderia:</label>
                <input type='text' name='nomEsc' id='nomEsc'/>
                <input type='submit' name='assignEscuderia' value='Añadir Escuderia al mundial'/>
            </form>

            <h3 id='circuito2mundial'>Añade un circuito a un mundial</h3>
            <form action='#' method='post'>
                <label for='id'>Nombre Mundial :</label>
                <input type='text' name='idNomMundial' id='id'/>

                <label for='nomCir'>Nombre circuito:</label>
                <input type='text' name='nomCir' id='nomCir'/>
                
                <input type='submit' name='assignCircuito' value='Añadir Circuito al mundial'/>
            </form>

            <h3 id='eliminar'>Eliminar datos por nombre</h3>
            <form action='#' method='post'>
                <label for='nameToDelete'>Nombre de lo que quieres eliminar:</label>
                <input type='text' name='todelete' id='nameToDelete'/>
                <label for='entityDel'>¿Sobre que quieres buscar?: </label>
                <select id='entityDel' name='entityDel'>
                <option value='escuderia'> Escuderia </option>
                <option value='mundial'> Mundial </option>
                <option value='carrera'> Circuito </option>
            </select>
            <input type='submit' name='eliminar' value='Eliminar'/>
        </form>
        </section>" . 
        $_SESSION['mm']->getMessage() . "
    </body>

</html>";

?>