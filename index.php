<?php
require 'flightPHP/flight/Flight.php';

Flight::path('protected/models');
Flight::path('protected/controllers');
Flight::set('flight.views.path', 'protected/views');

// Informar o endereço do projeto
Flight::set('baseUrl', 'http://localhost/desafio');

// Informar os dados do banco de dados
// Estrutura da base de dados em protected >> data >> db.sql
Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=desafio', 'desafio', 'desafio'));



$servidor = new ServidorController();
//Flight::route('/servidor/personagem', array($servidor, 'sortearPersonagem'));
//Flight::route('/servidor/batalha/@id_usuario(/@id_batalha)', array($servidor, 'batalha'));
//Flight::route('/servidor/rodada/@id_batalha/@id_usuario', array($servidor, 'rodada'));
//Flight::route('/servidor/confronto/@id_rodada/@id_usuario', array($servidor, 'confronto'));
Flight::route('/servidor/jogar/@id_usuario(/@id_batalha)', array($servidor, 'jogar'));
Flight::route('/servidor/consultar/@id_batalha/@id_usuario', array($servidor, 'consultar'));
Flight::route('/servidor/batalhas', array($servidor, 'listarBatalhasDisponiveis'));


// Simulação de um cliente consumindo o serviço
$cliente = new ClienteController();
Flight::route('/', array($cliente, 'index'));
Flight::route('/jogar/@id_usuario(/@id_batalha)', array($cliente, 'jogar'));

Flight::start();
