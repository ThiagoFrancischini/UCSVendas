<?php
class Endereco {
    private $id;
    private $rua;
    private $numero;
    private $complemento;
    private $bairro;
    private $cep;
    private $cidade;
    private $estado;

    public function __construct($id = null, $rua = "", $numero = "", $complemento = "", $bairro = "", $cep = "", $cidade = "", $estado = "") {
        $this->id = $id;
        $this->rua = $rua;
        $this->numero = $numero;
        $this->complemento = $complemento;
        $this->bairro = $bairro;
        $this->cep = $cep;
        $this->cidade = $cidade;
        $this->estado = $estado;
    }

    public function getId() { return $this->id; }
    public function getRua() { return $this->rua; }
    public function getNumero() { return $this->numero; }
    public function getComplemento() { return $this->complemento; }

    public function setId($id) { $this->id = $id; }
    public function setRua($rua) { $this->rua = $rua; }
}
?>