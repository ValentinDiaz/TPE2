<?php

class discoModel {

    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;'.'dbname=db_tpe2;charset=utf8', 'root', '');
    }



    public function getAllDiscos($prior, $condition){
        $query =$this-> db->prepare( "SELECT * FROM disco a INNER JOIN generos b 
        ON a.id_genero = b.id_genero  ORDER BY ". $condition." ".$prior);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDisco($id){
        $query =$this-> db->prepare( "SELECT * FROM disco a INNER JOIN generos b 
        ON a.id_genero = b.id_genero WHERE a.id=?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function removeDisco($id){
        $query = $this->db->prepare("DELETE FROM disco WHERE id = ?");
        $query->execute([$id]);
    }

    public function addDisco($titulo, $anio, $artista, $id_genero){
        $query = $this->db->prepare("INSERT INTO disco (titulo, anio, artista, id_genero) VALUES (?, ?, ?, ?)");
        $query->execute([$titulo, $anio, $artista, $id_genero]);

        return $this->db->lastInsertId();
    }

    public function editDisco(  $titulo, $anio, $artista, $id_genero,$id){  
      $query = $this->db->prepare("UPDATE disco SET titulo = ?, anio = ?, artista = ?, id_genero = ? WHERE id= ?");
      $query->execute([ $titulo, $anio, $artista,  $id_genero, $id]);
        
    }













}    
