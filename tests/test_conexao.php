<?php
include_once(__DIR__ . '/dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$conn = $factory->getConnection();

if ($conn) {
    echo "Conexao com PostgreSQL bem-sucedida!\n";
} else {
    echo "Falha na conexao com PostgreSQL.\n";
}