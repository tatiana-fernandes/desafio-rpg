<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Jogador
 *
 * @author tatiana.fernandes
 */
abstract class Jogador extends BD {

    const PONTUACAO_INICIAL_VIDA = null;
    const ARMA_ATAQUE = null;
    const ARMA_DEFESA = null;
    const FACES_DADO_DANO = null;
    const FORCA = null;
    const AGILIDADE = null;
    
    public $id;
    public $id_batalha;
    public $id_usuario;
    public $personagem;
    public $pontuacao_vida;
    
    public $valor_dado_dano;
    
    public $arma_ataque;
    public $arma_defesa;
    public $faces_dado_dano;

    public function __construct() {
        $this->arma_ataque = static::ARMA_ATAQUE;
        $this->arma_defesa = static::ARMA_DEFESA;
        $this->faces_dado_dano = static::FACES_DADO_DANO;
    }
    
     public static function nomeTabela() {
        return 'jogador';
    }

    public static function primaryKey() {
         return 'id';
    }

    public static function atributosTabela() {
         return array('id_batalha', 'id_usuario', 'personagem', 'pontuacao_vida');
    }
    
    public static function populateRecord($atributos) {
        
        if ($atributos['personagem'] == 'Humano') {
            $model = new Humano;
        } else {
            $model = new Orc;
        }

        foreach ($atributos as $nome => $valor)
            $model->$nome = $valor;

        return $model;
    }
    
    public function setPersonagem() {
        $this->personagem = get_called_class();  
        $this->pontuacao_vida = static::PONTUACAO_INICIAL_VIDA;
    
  }
    
    public function iniciativa($valorDado) {
        return $valorDado + static::AGILIDADE;
    }

    public function ataque($valorDado) {
        $this->valor_dado_dano = rand(1, static::FACES_DADO_DANO);
        return $valorDado + static::AGILIDADE + static::ARMA_ATAQUE;
    }

    public function defesa($valorDado) {
        $this->valor_dado_dano = rand(1, static::FACES_DADO_DANO);
        return $valorDado + static::AGILIDADE + static::ARMA_DEFESA;
    }

    public function calculoDanoRealizado() {
        return $this->valor_dado_dano + static::FORCA;
    }

    public function atualizaPontuacaoVida($valorDanoAdquirido) {
        $this->pontuacao_vida -= $valorDanoAdquirido;
    }

}
