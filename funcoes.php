<?php
	/*function nomeMinusculo($nome){ return strtolower($nome); }
	function nomeMaiusculo($nome){ return strtoupper($nome); }
	function nome3Letras($nome){ return substr($nome, 0,3); }
	function primeiroNome($nome){ return strstr($nome, ' ', true); }
	function ultimoNome($nome){ return strrchr($nome, ' '); }
	function nomeUnderline($nome){ return str_replace(" ","_",$nome); }
	function nomeMinusculoUnder($nome){ return str_replace(" ","_",strtolower($nome)); }*/

	function abrirArquivo($nomeArq,$conn,$situacao){
		if(($conteudo = fopen($nomeArq,"r")) !== FALSE){
			while($line = fgetcsv($conteudo, 1000,",")){
				if($line[0] == "nome"){
					continue;
				}

				$registro = array_combine(['nome'], $line);

				$verificacao = verificaExistencia($conn, $registro['nome']);
				//chamaSelect($registro['nome'],$situacao,$conn);

				$sit = identificaPessoa($registro['nome']);

				if($sit == "PJ")
					$email = emailPJ($registro['nome'],$conn);
				else
					$email = emailPF($registro['nome']);


				if(!$verificacao)
					cadastraBanco($registro['nome'],$sit,$email,$conn);

				mostraTela($registro['nome'], $email, $sit);

				}
			}
	}
	
	//Função que irá verificar se no banco ja tem cadastro
	function verificaExistencia($conn,$nome/*,$sit  :Para person =s but sit <>*/){
		$nomeAchado = " ";
		$select = "SELECT nome FROM exercicio1 WHERE nome LIKE '%$nome%'";
		$nomeBanco = mysqli_query($conn, $select) or die("Erro ao procurar cliente");


		//Analisando todo o "array" retornado
		while($dado = mysqli_fetch_array($nomeBanco))
				$nomeAchado = $dado['nome'];

		if($nome == $nomeAchado){
			return true;
		}else{
			return false;
		}
	}

	function mostraTela($nome,$email,$sit){echo $nome." : ".$email." : ".$sit."<br>";}

	// essa funcao serve para verificar se o nome necessario ja esta no banco
	function chamaSelect($nome,$sit,$conn){
		$nomeAchado = " ";
		$email = " ";
		$selectNome = "SELECT nome FROM exercicio1 where nome like '%$nome%'";

		$nomeBanco = mysqli_query($conn,$selectNome) or die("Erro ao consultar nome");

		//Analisando todo o "array" retornado
		while($dado = mysqli_fetch_array($nomeBanco))
				$nomeAchado = $dado['nome'];

		$nomeP = explode(" ", $nome);
		$nomeAchadoP = explode(" ", $nomeAchado);

		$finalNome = strtolower($nome);

		if($nome == $nomeAchado){ 
			$email = selectEmail_nomeIgual($nome,$conn);
			printaNaTela($nome,$email,$sit);


		}elseif(strstr($nome, "ltda",true) or strstr($nome,"me",true)){
			if($nomeP[0] == $nomeAchadoP[0]){//1° nome igual
				if($nomeP[1] == $nomeAchadoP[1]){//2° nome igual
					if($nomeP[2] != $nomeAchadoP[2]){//3° nome igual
						$email = email_nomesQuaseIguais($nome, $nomeP[0], " ", $nome[2]);

						cadastraBanco($nome, $sit,$email,$conn);
						printaNaTela($nome, $email, $sit);
					}
				}else
					$email = email_nomesQuaseIguais($nome, $nomeP[0], $nomeP[1], " ");

					cadastraBanco($nome, $sit,$email,$conn);
					printaNaTela($nome, $email, $sit);
		}
		}else{
			

		}


		/*if($nome == $nomeAchado){
			$email = selectEmail_nomeIgual($nome,$conn);
			printaNaTela($nome,$email,$sit);

		}elseif($nomeP[0] == $nomeAchadoP[0]){//1° nome igual
			if($nomeP[1] == $nomeAchadoP[1]){//2° nome igual
				if($nomeP[2] != $nomeAchadoP[2]){//3° nome igual
					$email = email_nomesQuaseIguais($nome, $nomeP[0], " ", $nome[2]);

					cadastraBanco($nome, $sit,$email,$conn);
					printaNaTela($nome, $email, $sit);
				}
			}else
				$email = email_nomesQuaseIguais($nome, $nomeP[0], $nomeP[1], " ");

				cadastraBanco($nome, $sit,$email,$conn);
				printaNaTela($nome, $email, $sit);

		}elseif($nome <> $nomeAchado){
			$email = email_nomeDif($nome);

			cadastraBanco($nome, $sit,$email,$conn);
			printaNaTela($nome,$email,$sit);
		}else{
			$email = email_nomeDif($nome);
			cadastraBanco($nome, $sit,$email,$conn);
		}*/
	}

	//Funcao que cadastra no banco apenas
	function cadastraBanco($nome,$sit,$email,$conn){
				$sql = "INSERT INTO exercicio1(nome,situacao,email) VALUES ('$nome','$sit','$email')";

				mysqli_query($conn,$sql) or die("Erro ao tentar cadastrar registro !");
		}	

	//Gerar email para pessoa FISICA
	function emailPF($nome) {
		$nomeLetra = substr($nome, 0,1);
		$nomeLetra .= ltrim(strrchr($nome, ' '));
		$nomeLetra .= "@virgos.com.br";
		return strtolower($nomeLetra);
	}

	//Gerar email para pessoa JURIDICA
	function emailPJ($nome,$conn) {

		$arrayNome = explode(" ",$nome);
		$prefixos = ['dos','das','da','do','de'];


		if(in_array($arrayNome[1], $prefixos)){
			$email = strtolower($arrayNome[0] . $arrayNome[2] . "@virgos.com.br");

		}else{
			$email = strtolower($arrayNome[0] . $arrayNome[1] . "@virgos.com.br");
		}

		$emailA = consultaEmail($email,$conn);

		if(($email == $emailA) && (in_array($arrayNome[1], $prefixos))){
			$email = strtolower($arrayNome[0] . $arrayNome[3] . "@virgos.com.br");
		}else{
			$email = strtolower($arrayNome[0] . $arrayNome[2] . "@virgos.com.br");
		}


		return $email;
	}

	// Consulta de email
	function consultaEmail($email,$conn){
		$select = "SELECT email FROM exercicio1 WHERE email LIKE '$email'";
		$banco = mysqli_query($conn, $select) or die("Não foi possivel achar email igual");

		$emailA = "";
		//Analisano todo o valor voltado do banco
		while($dado = mysqli_fetch_array($banco)){
			$emailA = $dado['email'];
		}

		return $emailA;
	}

	//funcao que indentifica o tipo de pessoa
	function identificaPessoa($nome){
		$arrayNome = explode(" ", $nome);

		$ultimoNome = strtolower(end($arrayNome));
		$empresas = ['ltda','me','sa','epp'];

		//o in_array volta bool, e o if ve qual pertence.
		return in_array($ultimoNome, $empresas) ? "PJ" : "PF";
		//var_dump($arrayNome[count($arrayNome)-1]);
	}

	function salvaArquivo($file,$caminho){
		// Move o arquivo da pasta temporaria de upload para a pasta de destino 
		if (move_uploaded_file($file["tmp_name"], "$caminho/".$file["name"])) { 
		    echo "Arquivo salvo com sucesso! <br><br>"; 
		} 
		else { 
		    echo "Erro, o arquivo nao pode ser salvo. <br><br>"; 
		}
	}
?>


