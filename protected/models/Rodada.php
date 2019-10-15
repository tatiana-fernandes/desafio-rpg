<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rodada
 *
 * @author tatiana.fernandes
 */
class Rodada extends BD  {
    
    public $id;
    public $id_batalha;
    public $id_jogador_1;
    public $valor_jogador_1;
    public $id_jogador_2;
    public $valor_jogador_2;
    
    public static function nomeTabela() {
        return 'rodada';
    }

    public static function primaryKey() {
         return 'id';
    }

    public static function atributosTabela() {
         return array('id_batalha', 'id_jogador_1', 'valor_jogador_1', 'id_jogador_2', 'valor_jogador_2' );
    }
    
    public function getConfrontos() {
        return Confronto::findAll('id_rodada=:id_rodada', array(':id_rodada'=>$this->id));
    }
    
    public function getUltimoConfronto() {
        $confrontos = $this->getConfrontos();
        $qtd_confrontos = count($confrontos);
        
        if ($qtd_confrontos) {
            return $confrontos[$qtd_confrontos - 1];
        } else {
            return null;
        }
    }
    
    public function getJogadorIniciaConfronto1() {
        if ($this->valor_jogador_1 && $this->valor_jogador_2) {
            if ($this->valor_jogador_1 > $this->valor_jogador_2) {
                return $this->id_jogador_1;
            } else {
                return $this->id_jogador_2;
            }
        } else {
            return null;
        }
    }
    
    public function getJogadorIniciaConfronto2() {
        if ($this->valor_jogador_1 && $this->valor_jogador_2) {
            if ($this->valor_jogador_1 > $this->valor_jogador_2) {
                return $this->id_jogador_2;
            } else {
                return $this->id_jogador_1;
            }
        } else {
            return null;
        }
    }
    
    public function verificaEmpate() {
        if ($this->valor_jogador_1 && $this->valor_jogador_2 && $this->valor_jogador_1 == $this->valor_jogador_2) {
            return true;
        } else {
            return false;
        }
    }
    
    public function verificaJogadorJogou($id_usuario) {
        if ($this->id_jogador_1 == $id_usuario || $this->id_jogador_2 == $id_usuario) {
            return true;
        } else {
            return false;
        }
    }
    
    public function verificaJogadoresJogaram() {
        if ($this->valor_jogador_1  && $this->valor_jogador_2) {
            return true;
        } else {
            return false;
        }
    }
    
    public function limparValores() {
        $this->id_jogador_1 = null;
        $this->valor_jogador_1 = null;
        $this->id_jogador_2 = null;
        $this->valor_jogador_2 = null;
    }
}
