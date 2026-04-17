<?php
include_once(__DIR__ . '/dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$conn = $factory->getConnection();

if ($conn) {
    echo "Conexão com PostgreSQL bem-sucedida!";
} else {
    echo "Falha na conexão com PostgreSQL.";
}
?>