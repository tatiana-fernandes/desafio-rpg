<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cliente
 * 
 * A ideia é simular um serviço cliente 
 * 
 *
 * @author tatiana.fernandes
 */
class ClienteController {
    
   public function criarUsuario() {
       
   }

   public function index() {
       
       $id_usuario = null;
       $data = Flight::request()->data->getData();
        
        if (isset($data['id_usuario'])) {
            $id_usuario = $data['id_usuario'];
        }
        
       Flight::render('index', array('id_usuario'=>$id_usuario));
   }

   public function jogar($id_usuario, $id_batalha) {
       Flight::render('jogar', array('id_usuario'=>$id_usuario, 'id_batalha' => $id_batalha));
   }
   
    
}
