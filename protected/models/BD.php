<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BD
 *
 * @author tatiana.fernandes
 */


class BD {

    public static function nomeTabela() {
        
    }

    public static function primaryKey() {
        
    }

    public static function atributosTabela() {
        
    }

    public function insert() {
        $atributos = $this->atributosTabela();

        $sql = "INSERT INTO " . $this->nomeTabela() . "(" . implode(",", $atributos) . ") VALUES (:" . implode(", :", $atributos) . ")";

        $arrayValues = array();
        foreach ($atributos as $atributo) {
            $arrayValues[":" . $atributo] = $this->$atributo;
        }

        $db = Flight::db();

        if ($db->prepare($sql)->execute($arrayValues)) {
            $this->{$this->primaryKey()} = $db->lastInsertId();
            return true;
        } else {
            return false;
        }
    }

    public function update() {
        $atributos = $this->atributosTabela();

        $arrayAtributos = $atributos;
        array_walk($arrayAtributos, function (&$item, $key) {
            $item = "$item=:$item";
        });

        $sql = "UPDATE " . $this->nomeTabela() . " SET " . implode(", ", $arrayAtributos) . " WHERE " . $this->primaryKey() . "=" . $this->{$this->primaryKey()};

        $arrayValues = array();
        foreach ($atributos as $atributo) {
            $arrayValues[":" . $atributo] = $this->$atributo;
        }

        $db = Flight::db();

        if ($db->prepare($sql)->execute($arrayValues)) {
            return true;
        } else {
            return false;
        }
    }

    public function save() {
        $pk = $this->primaryKey();

        if (!$this->$pk)
            return $this->insert();
        else
            return $this->update();
    }

    public static function populateRecord($atributos) {
        $class = get_called_class();
        $model = new $class;

        foreach ($atributos as $nome => $valor)
            $model->$nome = $valor;

        return $model;
    }
    
    public static function populateRecords($data) {
        $models = array();

        if ($data) {
            foreach ($data as $attributes)
                $models[] = static::populateRecord($attributes);
        } 
        return $models;
    }

    public static function findByPk($pk) {
        $atributos = static::atributosTabela(); 
        
        $sql = "SELECT id, " . implode(", ", $atributos) . " FROM " . static::nomeTabela() . " WHERE " . static::primaryKey() . "=" . $pk;
       
        $db = Flight::db();
        $prepare = $db->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetch(PDO::FETCH_ASSOC);
 
        return static::populateRecord($result);
    }
    
    public static function findAll($condition = null, $values = null) {
        $atributos = static::atributosTabela(); 
        
        $sql = "SELECT id, " . implode(", ", $atributos) . " FROM " . static::nomeTabela();
        $sql .= $condition ? " WHERE " . $condition: '';
       
        $db = Flight::db();
        $prepare = $db->prepare($sql);
        $prepare->execute($values);
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
 
        return static::populateRecords($result);
        
    }

}
