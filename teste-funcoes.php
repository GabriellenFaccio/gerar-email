<?php 
	

	function nomeMinusculo($nome){ return strtolower($nome); }
	function nomeMaiusculo($nome){ return strtoupper($nome); }
	function nome3Letras($nome){ return substr($nome, 0,3); }
	function primeiroNome($nome){ return strstr($nome, ' ', true); }
	function ultimoNome($nome){ return strrchr($nome, ' '); }
	function nomeUnderline($nome){ return str_replace(" ","_",$nome); }
	function nomeMinusculoUnder($nome){ return str_replace(" ","_",strtolower($nome)); }
	function nomeEmailPF($nome){
		$nomeLetra = substr($nome, 0,1);
		$nomeLetra .= ltrim(strrchr($nome, ' '));
		$nomeLetra .= "@virgos.com.br";
		return strtolower($nomeLetra);
	}

	function nomeEmailPJ($nome){
		$primeiroNome = strstr($nome, ' ', true);
		$primeiroNome .= "@virgos.com.br";
		return strtolower($primeiroNome);
	}

	function nomeEmailIgual($nome,$nome2){
		$email = $nome . $nome2 . "@virgos.com.br";
		return strtolower($email);
	}

	function chamaSelect($nome,$sit,$conn){
		$nomeAchado = " ";
		$email = " ";
		$selectNome = "SELECT nome FROM exercicio1 where nome like '%$nome%'";

		$nomeBanco = mysqli_query($conn,$selectNome) or die("Erro");

		while($dado = mysqli_fetch_array($nomeBanco))
				$nomeAchado = $dado['nome'];

		$nomePartes = explode(" ", $nome);
		$nomeAchadoPartes = explode(" ",$nomeAchado);
		

		if($nomePartes[0] == $nomeAchadoPartes[0]){//1° nome = 
			if($nomePartes[1] == $nomeAchadoPartes[1]){//2° nome =
				if($nomePartes[2] == $nomeAchadoPartes[2]){//3° nome

					$email = nomeEmailIgual($nomePartes[0],substr($nomePartes[1], 0,3), substr($nomePartes[2], 0,3));
					cadastraBanco($nome,$sit,$email,$conn);
					return $email;

				}else{
					$email = nomeEmailIgual($nomePartes[0],substr($nomePartes[1], 0,3), " ");
					cadastraBanco($nome,$sit,$email,$conn);
					return email;
				}
			}else{
				$email = nomeEmailIgual($nomePartes[0], " "," ");
				cadastraBanco($nome,$sit,$email,$conn);
				return email;
			}
	    }elseif($nome != $nomeAchado){ // não cadastrado nome igual
	   		if($sit == "PF"){
				cadastraBanco($nome,$sit,nomeEmailIgual(substr($nome, 0,1),strrchr($nome," "), " "),$conn);
	   		}else{

	   		}
	    }
	}

	function cadastraBanco($nome,$sit,$email,$conn){
		$select = "SELECT nome FROM exercicio1 WHERE nome LIKE '%".$nome."%'";

		$nomeAchado = mysqli_query($conn,$select) or die("Erro ao tentar consultar");


	
		if($nomeAchado != $nome){
			$sql = "INSERT INTO exercicio1(nome,situacao,email) VALUES ('$nome','$sit','$email')";

			mysqli_query($conn,$sql) or die("Erro ao tentar cadastrar registro !");
		}
	}

	function abrirArquivo($nomeArq,$conn){
			if(($handle = fopen("testeNomes.csv", "r")) !== FALSE){ 
			//$cabecalho = fgetcsv($handle,0,",");
			while ($line = fgetcsv($handle, 1000, ",")) {
				if($line[0] == "nome"){
					continue;
				}
				//print_r($line);
				//die();

				$registro = array_combine(['nome'], $line);


				//print_r($registro[0]);

				if(strstr($registro['nome'], "LTDA"." ", true) or strstr($registro['nome'],"ltda"." ",true)){

					$situacao = "PJ";
					echo $registro['nome'].PHP_EOL." : ".nomeEmailPJ($registro['nome'])."<br>";
				
					chamaSelect($registro['nome'],$situacao, $conn);

					
				}elseif(strstr($registro['nome'],"ME"." ",true) or strstr($registro['nome'],"me"." ",true)){ 
					$situacao = "PJ";
					echo $registro['nome'].PHP_EOL." : ".nomeEmailPJ($registro['nome'])."<br>";
				
					chamaSelect($registro['nome'],$situacao, $conn);


				}else{ 

					$situacao = "PF";
					echo $registro['nome'].PHP_EOL." : ".nomeEmailPF($registro['nome'])."<br>";
					
					chamaSelect($registro['nome'],$situacao, $conn);
					
				}
			}
			
			fclose($handle);
		}
}
?>