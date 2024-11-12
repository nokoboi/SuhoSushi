<?php

require_once 'Database.php';
require_once 'validator.php';

class Mesa{
    private Database $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllMesas(){
        $result = $this->db->query("SELECT * FROM mesas");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMesaById($id){
        $result = $this->db->query("SELECT * from mesas where id=?", [$id]);
        return $result->fetch_assoc();
    }

    public function createMesa($numeroMesa){
        $data = ['numero_mesa' => $numeroMesa];
        //$numMesaSaneado = Validator::sanear($data['numero_mesa']);

        $this->db->query("INSERT INTO mesas(numero_mesa) values (?)",[$numeroMesa]);
        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];
    }

    public function updateMesa($id, $numMesa){
        $data = ['numero_mesa' => $numMesa];
        //$numMesaSaneado = Validator::sanear($data['numero_mesa']);

        $result = $this->db->query('SELECT id from mesas where id=?',[$id]);
        if($result->num_rows == 0){
            return ["mesa"=>"la mesa no existe"];
        }

        $this->db->query("UPDATE mesa set numero_mesa = ? where id=?",[$numMesa,$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }

    function deleteMesa($id){
        $this->db->query("DELETE FROM mesas where id=?",[$id]);
        return $this->db->query('SELECT ROW_COUNT() as affected')->fetch_assoc()['affected'];
    }
}