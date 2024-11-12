<?php

require_once 'Database.php';
require_once 'validator.php';

class Misc{
    private Database $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllMisc(){
        $result = $this->db->query("SELECT * FROM miscelaneo");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMiscById($id){
        $result = $this->db->query("SELECT * from miscelaneo where id=?", [$id]);
        return $result->fetch_assoc();
    }

    public function createMisc($concepto, $precio){
        $data = ["concepto"=>$concepto, "precio"=>$precio];
        $dataSaneado = Validator::sanear($data);

        $conceptoSaneado = $dataSaneado['concepto'];

        $this->db->query("INSERT INTO miscelaneo(concepto,precio) values (?,?)",[$conceptoSaneado,$precio]);
        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];
    }

    public function updateMisc($id, $concepto,$precio){
        $data = ['id' => $id, "concepto"=>$concepto, "precio"=>$precio];
        $dataSaneado = Validator::sanear($data);

        $conceptoSaneado = $dataSaneado['concepto'];

        $result = $this->db->query('SELECT id from miscelaneo where id=?',[$id]);
        if($result->num_rows == 0){
            return ["miscelaneo"=>"Error, no existe"];
        }

        $this->db->query("UPDATE miscelaneo set concepto = ?, precio=? where id=?",[$concepto, $precio,$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }

    function deleteMisc($id){
        $this->db->query("DELETE FROM miscelaneo where id=?",[$id]);
        return $this->db->query('SELECT ROW_COUNT() as affected')->fetch_assoc()['affected'];
    }
}