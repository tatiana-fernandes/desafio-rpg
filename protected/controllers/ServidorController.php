<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Servidor
 *
 * @author tatiana.fernandes
 */
class ServidorController {

    public function batalha($id_usuario, $id_batalha = null) {
        if ($id_batalha) {
            $this->participarBatalha($id_usuario, $id_batalha);
        } else {
            $this->iniciarBatalha($id_usuario);
        }
    }

    public function iniciarBatalha($id_usuario) {
        $batalha = new Batalha;
        $batalha->total_rodadas = 1;
        $batalha->status = Batalha::STATUS_AGUARDANDO_OPONENTE;
        $batalha->save();

        $personagem = Batalha::sortearPersonagem();

        if (strtolower($personagem) == 'humano') {
            $jogador = new Humano();
        } else {
            $jogador = new Orc;
        }

        $jogador->setPersonagem();
        $jogador->id_usuario = $id_usuario;
        $jogador->id_batalha = $batalha->id;
        $jogador->save();

        Flight::json(array(
            'status' => 200,
            'errorCode' => null,
            'data' => array(
                'mensagem' => 'Nova batalha iniciada:' . $batalha->id,
                'jogadores' => $batalha->getJogadores(),
                'batalha' => $batalha
            )
                ), 200);
    }

    public function participarBatalha($id_usuario, $id_batalha) {
        $batalha = Batalha::findByPk($id_batalha);
        
        $jogadores = $batalha->getJogadores();

        if (!$batalha->verificaJogador($id_usuario)) {
            $batalha->status = Batalha::STATUS_INICIATIVA;
            $batalha->save();
            
            if (count($jogadores) == 1) {
                if ($jogadores[0]->personagem == 'Humano') {
                    $jogador = new Orc;
                } else {
                    $jogador = new Humano();
                }

                $jogador->setPersonagem();
                $jogador->id_usuario = $id_usuario;
                $jogador->id_batalha = $batalha->id;
                $jogador->save();

                $data = array(
                    'mensagem' => 'Você é um ' . $jogador->personagem,
                    'jogadores' => $batalha->getJogadores(),
                    'batalha' => $batalha
                );
            } else {

                $data = array(
                    'mensagem' => "ERRO quantidade de participantes",
                    'jogadores' => null,
                    'batalha' => null
                );
            }
        } else {
            $data = array(
                'mensagem' => 'Você já faz parte desta batalha',
                'jogadores' => $batalha->getJogadores(),
                'batalha' => $batalha
            );
        }

        if (isset($data)) {
            Flight::json(array(
                'status' => 200,
                'errorCode' => null,
                'data' => $data
                    ), 200);
        }
    }

    public function rodada($id_batalha, $id_usuario) {
        $batalha = Batalha::findByPk($id_batalha);
        $jogadores = $batalha->getJogadores();
        $jogadorAtual = Jogador::findByPk($id_usuario);

        $valor_sorteado = rand(1, Batalha::FACES_DADO);

        if (count($jogadores) == 2) {

            if ($batalha->verificaJogador($id_usuario)) {

                $ultimaRodada = $batalha->getUltimaRodada();
                if ($ultimaRodada && !$ultimaRodada->verificaEmpate()) {
                    $rodada = $ultimaRodada;

                    if (!$rodada->verificaJogadorJogou($id_usuario)) {
                        $rodada->id_jogador_2 = $id_usuario;
                        $rodada->valor_jogador_2 = $jogadorAtual->iniciativa($valor_sorteado);
                        $rodada->save();

                        $mensagem = "Número sorteado:" . $valor_sorteado . " - Cálculo:" . $valor_sorteado . " + " . $jogadorAtual::AGILIDADE . " = " . ($jogadorAtual->iniciativa($valor_sorteado));
                    } else {
                        $mensagem = "Você já jogou. Espere sua vez.";
                    }
                } else {
                    if ($ultimaRodada) {
                        $rodada = $ultimaRodada;
                        $rodada->limparValores();
                    } else {
                        $rodada = new Rodada;
                    }

                    $rodada->id_batalha = $id_batalha;
                    $rodada->id_jogador_1 = $id_usuario;
                    $rodada->valor_jogador_1 = $jogadorAtual->iniciativa($valor_sorteado);
                    $rodada->save();

                    $mensagem = "Número sorteado:" . $valor_sorteado . " - Cálculo:" . $valor_sorteado . " + " . $jogadorAtual::AGILIDADE . " = " . ($jogadorAtual->iniciativa($valor_sorteado)).". ";
                }

                if ($rodada->verificaEmpate()) {
                    $mensagem .= "Empate. Sorteie novamente";
                } else {
                    if ($rodada->verificaJogadoresJogaram()) {
                        $batalha->status = Batalha::STATUS_EM_CONFRONTO;
                        $batalha->save();
                    }
                }
            } else {
                $mensagem = "ERRO jogador não participa da batalha";
                $jogadores = null;
                $batalha = null;
                $rodada = null;
            }
        } else {
            $mensagem = "ERRO quantidade de participantes";
            $jogadores = null;
            $batalha = null;
            $rodada = null;
        }


        if ($mensagem) {
            Flight::json(array(
                'status' => 200,
                'errorCode' => null,
                'data' => array(
                    'mensagem' => $mensagem,
                    'jogadores' => $jogadores,
                    'batalha' => $batalha,
                    'rodada' => $rodada,
                ),
             ), 200);
        }
    }

    public function confronto($id_rodada, $id_usuario) {
        $rodada = Rodada::findByPk($id_rodada);
        $batalha = Batalha::findByPk($rodada->id_batalha);
        $jogadores = $batalha->getJogadores();
        $confrontos = $rodada->getConfrontos();
        $confronto = null;
        $qtd_confrontos = count($confrontos);

        if ($batalha->verificaJogador($id_usuario)) {
            $jogadorAtual = Jogador::findByPk($id_usuario);

            $valor_sorteado = rand(1, Batalha::FACES_DADO);
            $jogadorAtual->valor_dado_dano = rand(1, $jogadorAtual::FACES_DADO_DANO);

            if ($qtd_confrontos <= 2) {
                if ($qtd_confrontos == 0) {
                    if ($id_usuario == $rodada->getJogadorIniciaConfronto1()) {
                        $confronto = new Confronto;
                        $confronto->id_batalha = $rodada->id_batalha;
                        $confronto->id_rodada = $id_rodada;
                        $confronto->id_jogador_ataque = $id_usuario;
                        $confronto->valor_jogador_ataque = $jogadorAtual->ataque($valor_sorteado);
                        $confronto->valor_dano_jogador_ataque = $jogadorAtual->calculoDanoRealizado();
                        $confronto->save();

                        $mensagem = "Número sorteado:" . $valor_sorteado . " - Cálculo:" . $valor_sorteado . " + " . $jogadorAtual::AGILIDADE . " + " . $jogadorAtual::ARMA_ATAQUE . " = " . ($valor_sorteado + $jogadorAtual::AGILIDADE + $jogadorAtual::ARMA_ATAQUE) . ". ";
                        $mensagem .= "Número dano sorteado:" . $jogadorAtual->valor_dado_dano . " - Cálculo:" . $jogadorAtual->valor_dado_dano . " + " . $jogadorAtual::FORCA . " = " . $jogadorAtual->calculoDanoRealizado();
                    } else {
                        $mensagem = "ERRO espere sua vez";
                    }
                } elseif ($qtd_confrontos == 1) {
                    $confronto = $confrontos[0];

                    if ($id_usuario == $rodada->getJogadorIniciaConfronto2()) {
                        if ($confronto->completo()) {
                            $confronto = new Confronto;
                            $confronto->id_batalha = $rodada->id_batalha;
                            $confronto->id_rodada = $id_rodada;
                            $confronto->id_jogador_ataque = $id_usuario;
                            $confronto->valor_jogador_ataque = $jogadorAtual->ataque($valor_sorteado);
                            $confronto->valor_dano_jogador_ataque = $jogadorAtual->calculoDanoRealizado();
                            $confronto->save();

                            $mensagem = "Número sorteado:" . $valor_sorteado . " - Cálculo:" . $valor_sorteado . " + " . $jogadorAtual::AGILIDADE . " + " . $jogadorAtual::ARMA_ATAQUE . " = " . ($valor_sorteado + $jogadorAtual::AGILIDADE + $jogadorAtual::ARMA_ATAQUE). ". ";
                            $mensagem .= "Número dano sorteado:" . $jogadorAtual->valor_dado_dano . " - Cálculo:" . $jogadorAtual->valor_dado_dano . " + " . $jogadorAtual::FORCA . " = " . $jogadorAtual->calculoDanoRealizado();
                        } else {
                            $confronto->id_jogador_defesa = $id_usuario;
                            $confronto->valor_jogador_defesa = $jogadorAtual->defesa($valor_sorteado);
                            $confronto->save();

                            if ($confronto->completo()) {
                                $confronto->atualizaPontuacaoJogadores();
                                $status = $batalha->verificaStatus();

                                if ($status != Batalha::STATUS_FINALIZADO) {
//                                        $batalha->atualizaTotalRodadas();
//                                        $batalha->status = Batalha::STATUS_INICIATIVA;
//                                        $batalha->save();
                                }
                            }

                            $mensagem = "Número sorteado:" . $valor_sorteado . " - Cálculo:" . $valor_sorteado . " + " . $jogadorAtual::AGILIDADE . " + " . $jogadorAtual::ARMA_DEFESA . " = " . ($valor_sorteado + $jogadorAtual::AGILIDADE + $jogadorAtual::ARMA_DEFESA);
                        }
                    } else {
                        $mensagem = "ERRO espere sua vez";
                    }
                } else {
                    $confronto = $confrontos[1];

                    if (!$confronto->completo()) {
                        if ($id_usuario == $rodada->getJogadorIniciaConfronto1()) {
                            $confronto->id_jogador_defesa = $id_usuario;
                            $confronto->valor_jogador_defesa = $jogadorAtual->defesa($valor_sorteado);
                            $confronto->save();

                            if ($confronto->completo()) {
                                $confronto->atualizaPontuacaoJogadores();
                                $status = $batalha->verificaStatus();

                                if ($status != Batalha::STATUS_FINALIZADO) {
                                    $batalha->atualizaTotalRodadas();
                                    $batalha->status = Batalha::STATUS_INICIATIVA;
                                    $batalha->save();
                                }
                            }

                            $mensagem = "Número sorteado:" . $valor_sorteado . " - Cálculo:" . $valor_sorteado . " + " . $jogadorAtual::AGILIDADE . " + " . $jogadorAtual::ARMA_DEFESA . " = " . ($valor_sorteado + $jogadorAtual::AGILIDADE + $jogadorAtual::ARMA_DEFESA);
                        } else {
                            $mensagem = "ERRO você não pode jogar";
                        }
                    } else {
                        $mensagem = "ERRO confronto completo";
                    }
                }
            } else {
                $mensagem = "ERRO quantidade de confrontos da batalha";
            }
        } else {
            $mensagem = "ERRO você não participa desta batalha";
            $jogadores = null;
            $batalha = null;
            $rodada = null;
            $confronto = null;
        }

        Flight::json(array(
            'status' => 200,
            'errorCode' => null,
            'data' => array(
                'mensagem' => $mensagem,
                'jogadores' => $jogadores,
                'batalha' => $batalha,
                'rodada' => $rodada,
                'confronto' => $confronto
            )
        ), 200);
    }

    public function finalizado($id_batalha, $id_usuario) {
        $batalha = Batalha::findByPk($id_batalha);

        if ($batalha->verificaJogador($id_usuario)) {
            Flight::json(array(
                'status' => 200,
                'errorCode' => null,
                'data' => array(
                    'mensagem' => 'Batalha finalizada.',
                    'jogadores' => $batalha->getJogadores(),
                    'batalha' => $batalha
                )
             ), 200);
        }
    }

    public function jogar($id_usuario, $id_batalha) {

        if ($id_batalha) {
            $batalha = Batalha::findByPk($id_batalha);
            $status = $batalha->status;

            switch ($status) {
                case Batalha::STATUS_AGUARDANDO_OPONENTE:
                    $this->batalha($id_usuario, $id_batalha);
                    break;
                case Batalha::STATUS_INICIATIVA:
                    $this->rodada($id_batalha, $id_usuario);
                    break;
                case Batalha::STATUS_EM_CONFRONTO:
                    $rodada = $batalha->getUltimaRodada();
                    $this->confronto($rodada->id, $id_usuario);
                    break;
                case Batalha::STATUS_FINALIZADO:
                    $this->finalizado($id_batalha, $id_usuario);
                    break;
                default:
                    break;
            }
        } else {
            $this->batalha($id_usuario);
        }
    }

    public function consultar($id_batalha, $id_usuario) {
        $batalha = Batalha::findByPk($id_batalha);
        $ultimaRodada = $batalha->getUltimaRodada();
        
        if ($batalha->verificaJogador($id_usuario)) {
            Flight::json(array(
                'status' => 200,
                'errorCode' => null,
                'data' => array(
                    'mensagem' => "",
                    'batalha' => $batalha,
                    'jogadores'=> $batalha->getJogadores(),
                    'rodada' => $ultimaRodada,
                    'confronto' => ($ultimaRodada) ? $ultimaRodada->getUltimoConfronto() : null
                )
             ), 200);
        } else {
            Flight::json(array(
                'status' => 200,
                'errorCode' => null,
                'data' => array(
                    'mensagem' => "ERRO",
                )
             ), 200);
        }
    }
    
    public function listarBatalhasDisponiveis() {
        
        $batalhas = Batalha::findAll('status=:status', array(':status'=> Batalha::STATUS_AGUARDANDO_OPONENTE));
        
        $lista = array();
        
        if ($batalhas) {
            foreach($batalhas as $batalha) {
                $lista[] = $batalha->id;
            }
        }
        
        Flight::json(array(
                'status' => 200,
                'errorCode' => null,
                'data' => array(
                    'batalhas' => $lista,
                )
             ), 200);
    }
}
