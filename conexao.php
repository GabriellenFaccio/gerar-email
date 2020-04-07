<?php
	$host = "localhost";
	$user = "root";
	$pass = "";
	$dbName = "clig_exercicios";

	//criar a conexao com o banco
	$conn = mysqli_connect($host,$user,$pass,$dbName);

	if(!$conn){
		echo "Erro ao conectar ao banco de dados !";
		exit();
	}
?>