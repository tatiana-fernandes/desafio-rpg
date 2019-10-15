<?php

class Batalha extends BD {
    
    const FACES_DADO = 20;
    const STATUS_AGUARDANDO_OPONENTE = 'AGUARDANDO_OPONENTE';
    const STATUS_INICIATIVA = 'INICIATIVA';
    const STATUS_EM_CONFRONTO = 'EM_CONFRONTO';
    const STATUS_FINALIZADO = 'FINALIZADO';
    
    public $id;
    public $id_jogador_ganhador;
    public $total_rodadas;
    public $status;
    
    public static function nomeTabela() {
        return 'batalha';
    }

    public static function primaryKey() {
         return 'id';
    }

    public static function atributosTabela() {
         return array('id_jogador_ganhador', 'total_rodadas', 'status');
    }
    
    public static function sortearPersonagem() {
        $personagens = array('Humano', 'Orc');
        
        $sorteio = rand(0, 1);
        return $personagens[$sorteio];
    }
    
    public function getJogadores() {
        return Jogador::findAll('id_batalha=:id_batalha', array(':id_batalha' => $this->id));
    }
    
    public function getRodadas() {
        return Rodada::findAll('id_batalha=:id_batalha ORDER BY id', array(':id_batalha' => $this->id));
    }
    
    public function verificaJogador($id_jogador) {
         $jogadores = $this->getJogadores();
         $arrayIdJogadores = array();
         
            foreach ($jogadores as $jogador) {
                $arrayIdJogadores[] = $jogador->id_usuario;
            }
            
            if (in_array($id_jogador, $arrayIdJogadores)) {
                return true;
            } else {
                return false;
            }
    }
    
    public function getUltimaRodada() {
        $rodadas = $this->getRodadas();
        $index = $this->total_rodadas - 1;
        if ($rodadas && isset($rodadas[$index])) {
            return $rodadas[$index];
        } else {
            return null;
        }
    }
    
    public function atualizaTotalRodadas() {
        $this->total_rodadas += 1;
        $this->save();
    }
    
    public function verificaStatus() {
        $jogadores = $this->getJogadores();
        
        $pontuacao_vida_insuficiente = array();
        $maior_pontuacao = 0;
        $id_jogador_ganhador = 0;
        
        foreach($jogadores as $jogador) {
            if ($jogador->pontuacao_vida <= 0) {
                $pontuacao_vida_insuficiente[] = $jogador->id_usuario;
            }
            
            if ($maior_pontuacao < $jogador->pontuacao_vida) {
                $maior_pontuacao = $jogador->pontuacao_vida;
                $id_jogador_ganhador = $jogador->id_usuario;
            }
        }
        
        if (count($pontuacao_vida_insuficiente) > 0) {
            $this->id_jogador_ganhador = $id_jogador_ganhador;
            $this->status = Batalha::STATUS_FINALIZADO;
            $this->save();
        }
        
        return $this->status;
    }
}

