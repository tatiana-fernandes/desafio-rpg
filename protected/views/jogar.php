<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo Flight::get('baseUrl');?>/css/app.css">
        <title>Desafio RPG</title>
        <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
        <script type="text/javascript">


        function atualizaValores(data) {
            
                    batalha = data.batalha;
                    jogadores = data.jogadores;
                    

                    
                    $("#status").html(batalha.status);
                    
                    if (batalha.status == 'INICIATIVA') {
                        $("#botao").val("Iniciativa");
                        $("#botao").removeClass("botaoJogar");
                        $("#botao").addClass("botaoIniciativa");
                        $("#tipo_jogada_0").html("");
                        $("#tipo_jogada_1").html("");
                    } else {
                        $("#botao").val("jogar");
                        $("#botao").removeClass("botaoIniciativa");
                        $("#botao").addClass("botaoJogar");
                    }

                    $("#batalha").val(batalha.id);

                    for (i = 0; i < jogadores.length; i++) {
                        if (jogadores[i].id_usuario == usuario) {
                            $("#pontuacao_0").html(jogadores[i].pontuacao_vida);
                            $("#arma_ataque_0").html(jogadores[i].arma_ataque);
                            $("#arma_defesa_0").html(jogadores[i].arma_defesa);
                            $("#faces_dado_dano_0").html(jogadores[i].faces_dado_dano);
                            $("#personagem_0").html(jogadores[i].personagem);
                        } else {
                            $("#pontuacao_1").html(jogadores[i].pontuacao_vida);
                            $("#arma_ataque_1").html(jogadores[i].arma_ataque);
                            $("#arma_defesa_1").html(jogadores[i].arma_defesa);
                            $("#faces_dado_dano_1").html(jogadores[i].faces_dado_dano);
                            $("#personagem_1").html(jogadores[i].personagem);
                        }
                    }
                    
                    if ('rodada' in data) {
                        if (data.rodada.id_jogador_1 == usuario) {
                            $("#rodada_0").html(data.rodada.valor_jogador_1);
                            $("#rodada_1").html(data.rodada.valor_jogador_2);
                        } else {
                            $("#rodada_0").html(data.rodada.valor_jogador_2);
                            $("#rodada_1").html(data.rodada.valor_jogador_1);
                        }
                        
                    }
                    
                     if ('confronto' in data) {
                        if (data.confronto.id_jogador_ataque == usuario) {
                            $("#jogada_0").html(data.confronto.valor_jogador_ataque);
                            $("#jogada_1").html(data.confronto.valor_jogador_defesa);
                            $("#dano_0").html(data.confronto.valor_dano_jogador_ataque);
                            $("#dano_1").html("-");
                            $("#tipo_jogada_0").html("ATAQUE");
                            $("#tipo_jogada_1").html("DEFESA");
                        } else {
                            $("#jogada_0").html(data.confronto.valor_jogador_defesa);
                            $("#jogada_1").html(data.confronto.valor_jogador_ataque);
                            $("#dano_0").html("-");
                            $("#dano_1").html(data.confronto.valor_dano_jogador_ataque);
                            $("#tipo_jogada_0").html("DEFESA");
                            $("#tipo_jogada_1").html("ATAQUE");
                        }
                        
                    } else {
                        $("#jogada_0").html("");
                            $("#jogada_1").html("");
                            $("#dano_0").html("");
                            $("#dano_1").html("");
                    }
        }

        function atualizaHistorico(data) {
                if ('rodada' in data) {
                        $("#historico").append("<span>INICIATIVA </span>-> ");
                        if (data.rodada.id_jogador_1 == usuario) {
                            $("#historico").append("Eu: " + data.rodada.valor_jogador_1 + "<b> | </b>");
                            $("#historico").append("Oponente: " + data.rodada.valor_jogador_2);
                        } else {
                            $("#historico").append("Eu: " + data.rodada.valor_jogador_2 + "<b> | </b>");
                            $("#historico").append("Oponente: " + data.rodada.valor_jogador_1);
                        }
                        $("#historico").append("<hr/>");
                    }
                    
                     if ('confronto' in data) {
                         $("#historico").append("<span>CONFRONTO </span>-> ");
                        if (data.confronto.id_jogador_ataque == usuario) {
                            $("#historico").append("Minha jogada: " + data.confronto.valor_jogador_ataque + "<b> | </b>");
                            $("#historico").append("Meu dano: " + data.confronto.valor_dano_jogador_ataque + "<b> | </b>");
                            $("#historico").append("Oponente jogada: " + data.confronto.valor_jogador_defesa);
                            
                        } else {
                            $("#historico").append("Minha jogada: " + data.confronto.valor_jogador_defesa + "<b> | </b>");
                            $("#historico").append("Oponente jogada: " + data.confronto.valor_jogador_ataque + "<b> | </b>");
                            $("#historico").append("Oponente dano: " + data.confronto.valor_dano_jogador_ataque);
                        }
                        $("#historico").append("<hr/>");
                        
                    }  else {
                        $("#jogada_0").html("");
                            $("#jogada_1").html("");
                            $("#dano_0").html("");
                            $("#dano_1").html("");
                    }
            }

        function jogar() {
            
            usuario = $("#usuario").val();
            
            if ($("#batalha").val().length > 0) {
                var url = "<?php echo Flight::get('baseUrl')."/servidor/jogar/"; ?>" + usuario + "/" + $("#batalha").val();
            } else {    
                var url = "<?php echo Flight::get('baseUrl')."/servidor/jogar/"; ?>" + usuario;
            }
            
            var xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    console.log(response);

                    data = response.data;
                    mensagem = data.mensagem;
                    
                    $("#mensagem").show();
                    $("#mensagem").html(mensagem);
                    
                    atualizaValores(data);
                    atualizaHistorico(data);
                }
            };
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
            
        }

        function consultar() {
            
            usuario = $("#usuario").val();
            
            if ($("#batalha").val().length > 0) {
                var url = "<?php echo Flight::get('baseUrl')."/servidor/consultar/"; ?>" + $("#batalha").val() +  "/" + usuario ;
            
            var xmlhttp = new XMLHttpRequest();
            
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    console.log(response);

                    data = response.data;
                    atualizaValores(data);
                }
            };
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
            }
        }

        
        
        setInterval(consultar, 1000);
        </script>

    </head>
    <body onload="jogar();">

        <div id="conteudo">
        
         ID Usuário:<input type="text" id="usuario" name="usuario" value="<?php echo $id_usuario; ?>"/>

         ID Batalha:<input type="text" id="batalha" name="batalha" value="<?php echo $id_batalha; ?>" /><br/><br/>
            
         <div id="mensagem" style="display:none"/></div> <br/><br/>
            
            
        <div id="containerPainel">
        
            <div id="eu" class="painel">
                
                <fieldset>
                    <legend>Eu</legend>
                    <div class="containerPontuacaoVida">
                        Vida
                        <div id="pontuacao_0" class="pontuacaoVida"></div>
                    </div> 
                    
                    <div class="containerArmaAtaque">
                        Ataque <div id="arma_ataque_0" class="armaAtaque"></div>
                    </div> 
                    <div class="containerArmaDefesa">
                        Defesa <div id="arma_defesa_0" class="armaDefesa"></div>
                    </div> 
                    <div class="containerFacesDadoDano">
                        Dado Dano <div id="faces_dado_dano_0" class="facesDadoDano"></div>
                    </div>
                    <hr>
                    <div class="containerJogada">
                        Jogada
                        <div id="jogada_0" class="jogada"></div>
                    </div> 
                    
                    <div class="containerDano">
                        Dano
                        <div id="dano_0" class="dano"></div> 
                    </div>
                    
                    <div id="tipo_jogada_0" class="tipoJogada"></div> 
                    
                </fieldset>
                    <div class="containerPersonagem">
                        <div id="personagem_0" class="personagem"></div>
                    </div>
            </div>
        
            
            
            <div id="oponente" class="painel">
                <fieldset>
                    <legend>Oponente</legend>
                    <div class="containerPontuacaoVida">
                        Vida
                        <div id="pontuacao_1" class="pontuacaoVida"></div>
                    </div> 
                    
                    <div class="containerArmaAtaque">
                        Ataque <div id="arma_ataque_1" class="armaAtaque"></div>
                    </div> 
                    <div class="containerArmaDefesa">
                        Defesa <div id="arma_defesa_1" class="armaDefesa"></div>
                    </div> 
                    <div class="containerFacesDadoDano">
                        Dado Dano <div id="faces_dado_dano_1" class="facesDadoDano"></div>
                    </div>
                    
                    <hr/>
                    
                    <div class="containerJogada">
                        Jogada
                        <div id="jogada_1" class="jogada"></div> 
                    </div>
                    
                    <div class="containerDano">
                        Dano
                        <div type="text" id="dano_1"/></div> 
                    </div>
                    
                    <div id="tipo_jogada_1" class="tipoJogada"></div> 
                </fieldset>
                <div class="containerPersonagem">
                        <div id="personagem_1" class="personagem"></div>
                </div>
                    
            </div>
            
           
            
            
            
            
              <div id="status"></div>
            
             <br/>
              <div class="containerIniciativaRodada">
                <div class="tituloIniciativaRodada">Iniciativa Rodada Atual</div>
                <div class="containerIniciativa">
                    <div id="rodada_0" class="iniciativa"></div> <br/>
                </div> X 
                <div class="containerIniciativa">
                    <div id="rodada_1" class="iniciativa"></div> <br/>
                </div>
             </div>       
        </div>
        
        <br/>
        <input type="button" id="botao" class="botaoJogar" value="jogar" onclick="jogar()">

        
            
        <div id="historico"></div>    
        </div>
    </body>
</html>