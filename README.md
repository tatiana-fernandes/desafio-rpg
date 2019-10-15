# desafio-rpg
Desafio RPG

O projeto tem por objetivo simular uma batalha medieval de um jogo de RPG em turnos.
A ideia é um duelo entre dois jogadores, em que um é um Humano e o outro é um Orc.

### Estrutura do Projeto
Foi utilizado o __framework Flight__ para implementação do sistema.
O projeto segue o padrão MVC, organizando seus arquivos dentro do diretório __protected__.
No arquivo __protected/data/db.sql__, encontram-se os comandos para criação das tabelas utilizadas. 

### Informações
No arquivo __index.php__, é necessário informar o endereço para acessar o projeto e as informações para conexão com a base de dados.

Para controle das etapas da Batalha, foram criados alguns status:
* __AGUARDANDO_OPONENTE__ - quando a batalha é iniciada, o jogador que inicia fica aguardando um segundo jogador. 
* __INICIATIVA__ - antes de iniciar uma rodada, verifica-se quem será o primeiro a jogar. Esta etapa foi chamada de Iniciativa.
* __EM_CONFRONTO__ - cada confronto é composto por um ataque e uma defesa. Cada Rodada tem 2 confrontos.
* __FINALIZADO__ - quando a pontuação de vida de um dos jogadores chegar a 0 ou menos. 


### Instruções
Para facilitar a simulação do duelo, não foram utilizadas variáveis de sessão. Assim, não é necessário utilizar mais de um navegador ou outro recurso para testar 2 jogadores simultâneos.

Os jogadores são identificados apenas com um número inteiro(que passa a ser considerado o ID do usuário) o qual é informado quando o usuário entra no sistema.
No passo seguinte, ele escolhe iniciar uma nova Batalha ou participar de alguma já disponível.

Assim que a partida é iniciada, sorteia-se o personagem do jogador e são exibidos na tela todos os valores calculados pelo sistema. 
 
