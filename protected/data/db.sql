-- Usuários do Sistema
/*CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `login` varchar(20) NOT NULL,
  `senha` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ;
*/

-- Informações sobre a batalha
CREATE TABLE IF NOT EXISTS `batalha` (
    id int(11) NOT NULL AUTO_INCREMENT,
    id_jogador_ganhador int(11),
    total_rodadas int(11),
    status varchar(20),
    PRIMARY KEY (`id`)
) ;


-- Informações sobre os jogadores da batalha
CREATE TABLE IF NOT EXISTS `jogador` (
    id int(11) NOT NULL AUTO_INCREMENT,
    id_batalha int(11),
    id_usuario int(11),
    personagem varchar(20),  
    pontuacao_vida int(11),
    PRIMARY KEY (`id`)
) ;


-- Informações sobre cada rodada (assim que os 2 jogadores realizam seus ataques, uma nova rodada começa)
CREATE TABLE IF NOT EXISTS `rodada` (
    id int(11) NOT NULL AUTO_INCREMENT,
    id_batalha int(11),
    id_jogador_1 int(11),
    valor_jogador_1 int(11),
    id_jogador_2 int(11),
    valor_jogador_2 int(11),
    PRIMARY KEY (`id`)
) ;


-- Informações de cada confronto
CREATE TABLE IF NOT EXISTS `confronto` (
    id int(11) NOT NULL AUTO_INCREMENT,
    id_batalha int(11),
    id_rodada int(11),
    id_jogador_ataque int(11),
    valor_jogador_ataque int(11),
    valor_dano_jogador_ataque int(11),
    id_jogador_defesa int(11),
    valor_jogador_defesa int(11),
    PRIMARY KEY (`id`)
) ;
