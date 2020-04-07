<?php
//require: da erro, encerra a execução do script
//include: apenas produz um warning que pode ser abafado com @
//include_once: tem a garantia que o arquivo nao sera incluido novamente se ele ja foi incluido antes.
	include_once("conexao.php");
	include_once("funcoes.php");
	$file = $_FILES["arquivo"];
	$caminho = "arquivoup/";

	salvaArquivo($file,$caminho);


	$situacao = "";
	$arquivo = "testeNomes.csv";


	abrirArquivo($file["name"],$conn,$situacao);

	mysqli_close($conn);

?>