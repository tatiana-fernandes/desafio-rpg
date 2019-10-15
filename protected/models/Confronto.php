<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Confronto
 *
 * @author tatiana.fernandes
 */
class Confronto extends BD {
    
    public $id;
    public $id_batalha;
    public $id_rodada;
    public $id_jogador_ataque;
    public $valor_jogador_ataque;
    public $valor_dano_jogador_ataque;
    public $id_jogador_defesa;
    public $valor_jogador_defesa;
    
    
    public static function nomeTabela() {
        return 'confronto';
    }

    public static function primaryKey() {
         return 'id';
    }

    public static function atributosTabela() {
         return array('id_batalha', 'id_rodada', 'id_jogador_ataque', 'valor_jogador_ataque', 'valor_dano_jogador_ataque', 'id_jogador_defesa', 'valor_jogador_defesa');
    }
    
    public function completo() {
        if ($this->valor_jogador_ataque && $this->valor_jogador_defesa) {
            return true;
        } else {
            return false;
        }
            
    }
    
    public function atualizaPontuacaoJogadores() {
        $jogadores = Jogador::findAll('id_batalha=:id_batalha AND (id_usuario = :id_ataque OR id_usuario = :id_defesa)', array(':id_batalha' => $this->id_batalha, ':id_ataque'=> $this->id_jogador_ataque, ':id_defesa'=> $this->id_jogador_defesa));
        
        foreach($jogadores as $jogador) {
            if ($jogador->id_usuario == $this->id_jogador_ataque) {
                $jogadorAtaque = $jogador;
            } else {
                $jogadorDefesa = $jogador;
            }
        }
        
        if ($this->valor_jogador_ataque > $this->valor_jogador_defesa) {
            $jogadorDefesa->atualizaPontuacaoVida($this->valor_dano_jogador_ataque);
            $jogadorDefesa->save();
        }
    }
            
}
