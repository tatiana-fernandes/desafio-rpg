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


   var url = "<?php echo Flight::get('baseUrl')."/servidor/batalhas"; ?>";
   var usuario = <?php echo $id_usuario; ?>;
    
    var xmlhttp = new XMLHttpRequest();
    
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
           
            data = response.data;
            batalhas = data.batalhas;
            
            links = "";
            if (batalhas.length > 0) {
                for (i = 0; i < batalhas.length; i++ ) {
                    links += '<a href="jogar/'+ usuario +'/'+ batalhas[i] +'"  class="linksBatalha">Participar batalha '+ batalhas[i] +'</a>';
                }
            }

            $("#batalhas").html(links);

            console.log(data);
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();


</script>
</head>

<body>
<div id="conteudo">
<?php  if(!$id_usuario) { ?>
    <div id="formIdentificacao">
        <strong>IDENTIFICAÇÃO:</strong> <hr/>
        <form method="POST"> 
            Indique seu ID de usuário (É necessário ser um número inteiro):<br/>
            <input type ="text" name="id_usuario"/>
            <input type="submit" value="OK">
        </form>
    </div><br/>
    (Obs.: Isto permite a simulação de dois usuários, sem que seja necessário utilizar 2 navegadores diferentes)
<?php } else { ?> 

    
    <div id="mensagem">
        <h3>Você pode escolher entre iniciar uma nova Batalha ou participar de uma disponível</h3>
        </div>
    <br/>
    
    <div class="titulo">Nova Batalha</div>
    <a href="jogar/<?php echo $id_usuario; ?>" class="linksBatalha">Iniciar</a> <br/><br/>

    
    
    <div class="titulo">Batalhas Disponíveis</div>
    <div id="batalhas"></div>

<?php } ?>

</div>
</body>
</html>


