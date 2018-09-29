<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Exercicios;

use App\FuncoesAuxiliares;

use App\ParsingFormulas;

class DN extends Model
{

	//Inicia a árvore com as fórmulas do bd
    public function iniciarDN($exercicios){

    	foreach ($exercicios as $key => $value) {
    		$data[$key]['id'] = $key+1;
    		$data[$key]['idContexto'] = $key+1;
    		$data[$key]['text'] = $value;
    		$data[$key]['icon'] = '';
    		$data[$key]['suposicao'] = 0;
    		$data[$key]['parent'] = "#";
    		$contexto[] = $data[$key]['id'];
    	}
    	foreach ($exercicios as $key => $value) {
    		$data[$key]['contexto'] = $contexto;
    	}
    	return $data;
    }

    //Responsável por chamar a função desejada
    public function step($request){
    	switch ($request->step) {
    		case "supor":
    			return $this->supor($request);
    			break;
    		case "elimNot":
    			return $this->elimNot($request);
    			break;
            case "elimE":
                return $this->elimE($request);
                break;
            case "excImp":
                return $this->excImp($request);
                break;
            case "incE":
                return $this->incE($request);
                break;
            case "abs":
                return $this->abs($request);
                break;
            case "raa":
                return $this->raa($request);
                break;
            case "incImp":
                return $this->incImp($request);
                break;
            case "incNot":
                return $this->incNot($request);
                break;
    		case "incOu":
    			return $this->incOu($request);
    			break;
            case "excOu":
                return $this->excOu($request);
                break;
            case "stepOu":
                return $this->stepOu($request);
                break;
            case "elimOu":
                return $this->elimOu($request);
                break;
    		default:
    			return $this->abs($request);
    			break;
    	}
    }

    //Função para suposição
    private function supor($request){
    	$arvoreAtual = $request->atual;
		$data['text'] = Exercicios::converteSaida($request->supor);
		$data['icon'] = "true";
		$data['suposicao'] = 1;
    	
		$this->verificaDescendencia($arvoreAtual,$data);
		$this->adicionarContexto($arvoreAtual,$data);

    	return $data;
    }

    //Função para eliminação do not
    private function elimNot($request){
    	if (count($request->selecionados) != 1 ){
    		$mensagem['erro'] = "Não é permitido selecionar mais de uma linha para esta operação";
    		return $mensagem;
    	}

    	$arvoreAtual = $request->atual;

    	$selecionado = $this->verificaSelecionado($request->selecionados)[0];
    	// return $selecionado;

    	if($selecionado['conectivo'] != 'notnot'){
    		$mensagem['erro'] = "Não é possível aplicar essa operação para essa fórmula";
    		return $mensagem;
    	}

		$data['text'] = Exercicios::converteSaida($selecionado['direito']);
		$data['icon'] = "";
		$data['suposicao'] = 0;

		// return $arvoreAtual;
		$this->verificaDescendencia($arvoreAtual,$data);
		$this->adicionarContexto($arvoreAtual,$data);

		return $data;
    }

    private function incNot($request){

        $arvoreAtual = $request->atual;

        if (count($request->selecionados) != 1){
            $mensagem['erro'] = "É necessário selecionar uma linha e apenas uma linha para essa operação";
            return $mensagem;
        }

        $formula1 = explode(". ", $request->selecionados[0]['text'])[1];
        $data['text'] = Exercicios::converteSaida("(¬¬".$formula1.")");
        $data['icon'] = "";
        $data['suposicao'] = 0;

        $this->verificaDescendencia($arvoreAtual,$data);
        $this->adicionarContexto($arvoreAtual,$data);
        return $data;
    }

    private function incOu($request){

        $arvoreAtual = $request->atual;

        if (count($request->selecionados) != 1){
            $mensagem['erro'] = "É necessário selecionar uma linha e apenas uma linha para essa operação";
            return $mensagem;
        }

        if($request->incluir == ""){
            $mensagem['erro'] = "É necessário digitar uma fórmula para que seja incluída no ou";
            return $mensagem;
        }

        $formula1 = explode(". ", $request->selecionados[0]['text'])[1];
        $formula2 = $request->incluir;
        $data['text'] = Exercicios::converteSaida("(".$formula1."ou".$formula2.")");
        $data['icon'] = "";
        $data['suposicao'] = 0;

        $this->verificaDescendencia($arvoreAtual,$data);
        $this->adicionarContexto($arvoreAtual,$data);
        return $data;
    }

    private function excOu($request){

        $arvoreAtual = $request->atual;

        if (count($request->selecionados) != 1){
            $mensagem['erro'] = "É necessário selecionar uma linha e apenas uma linha para essa operação";
            return $mensagem;
        }

        $selecionado = $this->verificaSelecionado($request->selecionados)[0];


        if($selecionado['conectivo'] != 'ou'){
            $mensagem['erro'] = "Não é possivel aplicar a operação para essa fórmula";
            return $mensagem;
        }


        
        $data[0]['text'] = Exercicios::converteSaida($selecionado['esquerdo']);
        $data[0]['icon'] = "true";
        $data[0]['suposicao'] = 1;
        $data[0]['excOu'] = 1;

        $data[1]['text'] = Exercicios::converteSaida($selecionado['direito']);
        $data[1]['icon'] = "true";
        $data[1]['suposicao'] = 1;
        $data[1]['excOu'] = 1;
        

        $this->verificaDescendencia($arvoreAtual,$data[0]);
        $this->adicionarContexto($arvoreAtual,$data[0]);
        $data[1]['parent'] = $data[0]['parent'];
        $data[1]['idContexto'] = $data[0]['idContexto']+1;
        return $data;
    }

    private function elimE($request){
        if (count($request->selecionados) != 1 ){
            $mensagem['erro'] = "Não é permitido selecionar mais de uma linha para esta operação";
            return $mensagem;
        }

        $arvoreAtual = $request->atual;

        $selecionado = $this->verificaSelecionado($request->selecionados)[0];
        // return $selecionado;

        if($selecionado['conectivo'] != 'e'){
            $mensagem['erro'] = "Não é possível aplicar essa operação para essa fórmula";
            return $mensagem;
        }

        $selecionado = $this->verificaSelecionado($request->selecionados)[0];

        $data[0]['text'] = Exercicios::converteSaida($selecionado['esquerdo']);
        $data[0]['icon'] = "";
        $data[0]['suposicao'] = 0;

        $data[1]['text'] = Exercicios::converteSaida($selecionado['direito']);
        $data[1]['icon'] = "";
        $data[1]['suposicao'] = 0;

        $this->verificaDescendencia($arvoreAtual,$data[0]);
        $this->adicionarContexto($arvoreAtual,$data[0]);
        $this->adicionaArvoreAtual($arvoreAtual,$data[0]);

        $this->verificaDescendencia($arvoreAtual,$data[1]);
        $this->adicionarContexto($arvoreAtual,$data[1]);

        return $data;
        
    }

    private function excImp($request){
    	if (count($request->selecionados) != 2 ){
    		$mensagem['erro'] = "É necessário selecionar duas linhas para esta operação";
    		return $mensagem;
    	}

        $arvoreAtual = $request->atual;

    	$selecionado = $this->verificaSelecionado($request->selecionados);

    	if($selecionado[0]['conectivo'] != 'implica' && $selecionado[1]['conectivo'] != 'implica'){
    		$mensagem['erro'] = "Não é possível aplicar essa operação para essas fórmulas";
    		return $mensagem;
    	}
        if(!$this->verificaContexto($arvoreAtual,$request->selecionados)){
            $mensagem['erro'] = "As fórmulas não fazem parte do mesmo contexto";
            return $mensagem;
        }

        if($selecionado[0]['conectivo'] == 'implica'){
            $indexImplica = 0;
            $indexEliminar = 1;
        }
        else{
            $indexImplica = 1;
            $indexEliminar = 0;
        }

        //Volta a fórmula selecionada para eliminar o implica para o formato de string
        $selecionado[$indexEliminar] = explode(". ",$request->selecionados[$indexEliminar]['text'])[1];

        if(Exercicios::converteSaida($selecionado[$indexImplica]['esquerdo']) != Exercicios::converteSaida($selecionado[$indexEliminar])){
            $mensagem['erro'] = "Não é possível realizar a exclusão do implica com a fórmula selecionada";
            $mensagem['erro'] = $selecionado[$indexEliminar];
            return $mensagem;
        }


        else{
            $data['text'] = Exercicios::converteSaida($selecionado[$indexImplica]['direito']);
            $data['icon'] = "";
            $data['suposicao'] = 0;
            $this->verificaDescendencia($arvoreAtual,$data);
            $this->adicionarContexto($arvoreAtual,$data);
            return $data;
        }

        // $selecionado = $this->verificaSelecionado($request->selecionados)[0];

        // $data[0]['text'] = Exercicios::converteSaida($selecionado['esquerdo']);
        // $data[0]['icon'] = "";
        // $data[0]['suposicao'] = 0;

        // $data[1]['text'] = Exercicios::converteSaida($selecionado['direito']);
        // $data[1]['icon'] = "";
        // $data[1]['suposicao'] = 0;

        // $this->verificaDescendencia($arvoreAtual,$data[0]);
        // $this->adicionarContexto($arvoreAtual,$data[0]);
        // $this->adicionaArvoreAtual($arvoreAtual,$data[0]);

        // $this->verificaDescendencia($arvoreAtual,$data[1]);
        // $this->adicionarContexto($arvoreAtual,$data[1]);

        // return $data;
    	
    }

    private function incE($request){
        // return $request;
        if (count($request->selecionados) != 2 ){
            $mensagem['erro'] = "É necessário selecionar duas linhas para essa operação";
            return $mensagem;
        }

        $arvoreAtual = $request->atual;

        if(!$this->verificaContexto($arvoreAtual,$request->selecionados)){
            $mensagem['erro'] = "As fórmulas não fazem parte do mesmo contexto";
            return $mensagem;
        }

        $formula1 = explode(". ", $request->selecionados[0]['text'])[1];
        if(strlen($formula1) == 3)
            $formula1 = $formula1[1];
        $formula2 = explode(". ", $request->selecionados[1]['text'])[1];
        if(strlen($formula2) == 3)
            $formula2 = $formula2[1];

        if($formula1 > $formula2){
            $data['text'] = Exercicios::converteSaida("($formula2"."e$formula1)");
        }else{
            $data['text'] = Exercicios::converteSaida("($formula1"."e$formula2)");
        }
        
        $data['icon'] = "";
        $data['suposicao'] = 0;

        // return $selecionado;


        $this->verificaDescendencia($arvoreAtual,$data);
        $this->adicionarContexto($arvoreAtual,$data);

        return $data;
        
    }

    private function abs($request){
        if (count($request->selecionados) != 1 && count($request->selecionados) != 2){
            $mensagem['erro'] = "Para essa operação devem ser selecionadas uma ou duas linhas";
            return $mensagem;
        }
        
        $arvoreAtual = $request->atual;

        if(count($request->selecionados) == 2){
            if(!$this->verificaContexto($arvoreAtual,$request->selecionados)){
                $mensagem['erro'] = "As fórmulas não fazem parte do mesmo contexto";
                return $mensagem;
            }
        }


        foreach ($arvoreAtual as $key => $value) {
            if($value['id'] == $request->selecionados[0]['parent']){
                if(array_key_exists("excOu", $value)){
                    $mensagem['erro'] = "Não é possível aplicar a operação. Exclusão do ou aberta";
                    return $mensagem;
                }
                break;
            }
        }

        $selecionado = $this->verificaSelecionado($request->selecionados)[0];
        // return $selecionado;

        if($selecionado['conectivo'] != 'e'){
            $mensagem['erro'] = "Não é possível aplicar essa operação para essa fórmula";
            return $mensagem;
        }
        
        // return $selecionado;

        //String de maior tamanho
        $formula1 = strlen($selecionado['esquerdo']) > strlen($selecionado['direito']) ? $selecionado['esquerdo']:$selecionado['direito'];

        //String de menor tamanho
        $formula2 = strlen($selecionado['esquerdo']) < strlen($selecionado['direito']) ? $selecionado['esquerdo']:$selecionado['direito'];

        if("(not".$formula2.")" != $formula1){
            $mensagem['erro'] = "Não é possível declarar absurdo com as fórmulas selecionadas";
            return $mensagem;
        }

        $data['text'] = "⊥";
        $data['icon'] = "";
        $data['suposicao'] = 0;

        // return $selecionado;


        $this->verificaDescendencia($arvoreAtual,$data);
        $this->adicionarContexto($arvoreAtual,$data);

        return $data;
        
    }

    private function raa($request){
        
        $arvoreAtual = $request->atual;        

        $formula1 = $request->selecionados['id'];
        // return $arvoreAtual;

        $newData = $this->reduzirAbsurdo($arvoreAtual, $formula1);

        $data['contexto'] = $newData['contexto'];
        $data['icon'] = $newData['icon'];
        $data['id'] = $newData['id'];
        $data['idContexto'] = $newData['idContexto'];
        $data['parent'] = $newData['parent'];
        $data['suposicao'] = $newData['suposicao'];
        $data['text'] = $newData['text'];

        return $data;

    }

    private function incImp($request){

        if (count($request->selecionados) != 1){
            $mensagem['erro'] = "É necessário selecionar uma linha e apenas uma linha para essa operação";
            return $mensagem;
        }

        if(filter_var($request->selecionados[0]['id'], FILTER_VALIDATE_INT)){
            $mensagem['erro'] = "Não é possível aplicar essa operação para essa fórmula";
            return $mensagem;
        }

        $arvoreAtual = $request->atual;

        foreach ($arvoreAtual as $key => $value) {
            if($value['id'] == $request->selecionados[0]['parent']){
                if(array_key_exists("excOu", $value)){
                    $mensagem['erro'] = "Não é possível aplicar a operação. Exclusão do ou aberta";
                    return $mensagem;
                }
                break;
            }
        }
        

        $formula1 = explode(". ", $request->selecionados[0]['text'])[1];
        if(strlen($formula1) == 3)
            $formula1 = $formula1[1];
        $id = $request->selecionados[0]['id'];
        // return $formula1;

        $newData = $this->incluirImplica($arvoreAtual, $formula1,$id);
        // return $newData;

        $data['contexto'] = $newData['contexto'];
        $data['icon'] = $newData['icon'];
        $data['id'] = $newData['id'];
        $data['idContexto'] = $newData['idContexto'];
        $data['parent'] = $newData['parent'];
        $data['suposicao'] = $newData['suposicao'];
        $data['text'] = $newData['text'];

        return $data;

    }

    private function elimOu($request){

        if (count($request->selecionados) != 2){
            $mensagem['erro'] = "É necessário selecionar duas linhas e apenas duas linhas para essa operação";
            return $mensagem;
        }

        $arvoreAtual = $request->atual;

        // return $this->irmaos($arvoreAtual,$request->selecionados);

        if(!$this->irmaos($arvoreAtual,$request->selecionados)){
            $mensagem['erro'] = "Não é possível aplicar essa operação para essas fórmulas";
            return $mensagem;
        }

        $formula1 = explode(". ", $request->selecionados[0]['text']);
        $formula1 = $formula1[count($formula1)-1];

        $formula2 = explode(". ", $request->selecionados[1]['text']);
        $formula2 = $formula2[count($formula2)-1];

        if($formula1 != $formula2){
            $mensagem['erro'] = "Não é possível eliminar o Ou. As fórmulas não são iguais";
            return $mensagem;
        }       

        $id = $request->selecionados[1]['id'] > $request->selecionados[0]['id'] ? $request->selecionados[1]['id']:$request->selecionados[0]['id'];
        // return $formula1;

        $newData = $this->eliminarOu($arvoreAtual, $formula1,$id);
        // return $newData;

        $data['contexto'] = $newData['contexto'];
        $data['icon'] = $newData['icon'];
        $data['id'] = $newData['id'];
        $data['idContexto'] = $newData['idContexto'];
        $data['parent'] = $newData['parent'];
        $data['suposicao'] = $newData['suposicao'];
        $data['text'] = $newData['text'];

        return $data;

    }
    private function stepOu($request){
      
        $arvoreAtual = $request->atual;

        $formula1 = $request->selecionados;
        if($request->selecionados['parent'] == "#")
            $id = $request->selecionados['idContexto']-2;
        else
            $id = $request->selecionados['parent'];
        // return $formula1;

        $newData = $this->iniciarOu($arvoreAtual, $formula1,$id);
        return $newData;

        $data['contexto'] = $newData['contexto'];
        $data['icon'] = $newData['icon'];
        $data['id'] = $newData['id'];
        $data['idContexto'] = $newData['idContexto'];
        $data['parent'] = $newData['parent'];
        $data['suposicao'] = $newData['suposicao'];
        $data['text'] = $newData['text'];

        return $data;

    }

    private function verificaContexto($arvoreAtual,$selecionados){
        $contextoMaior = array();
        foreach ($selecionados as $chave => $selecionado) {
            
            //Pegar os contextos dentro da árvore
            foreach ($arvoreAtual as $key => $value) {
                if($value['id'] == $selecionado['id']){
                    //Verifica o contexto do mais interno
                    // return count($value['contexto']) > count($contextoMaior) ? "true":"false";
                    if(count($value['contexto']) > count($contextoMaior)){
                        //Se for a primeira adição, insere o contextoMenor
                        if(count($contextoMaior) == 0)
                            $contextoMenor = $value['contexto'];
                        $contextoMaior = $value['contexto'];
                    }
                    else{
                        $contextoMenor = $value['contexto'];
                    }
                }
            }
        }

        foreach ($selecionados as $chave => $selecionado) {
            $contador = 0;
            foreach ($contextoMaior as $key => $value) {
                if($selecionado['id'] == $value)
                    $contador++;
            }

            foreach ($contextoMenor as $key => $value) {
                if($selecionado['id'] == $value)
                    $contador++;
            }
            //Verifica se alguém está nos dois contextos
            if($contador > 1)
                return "true";
        }

        return false;
    }

    private function adicionaArvoreAtual(&$arvoreAtual,$data){
        $index = count($arvoreAtual);
        $arvoreAtual[$index]['contexto'] = $data['contexto'];
        $arvoreAtual[$index]['icon'] = $data['icon'];
        $arvoreAtual[$index]['icon'] = $data['icon'];
        $arvoreAtual[$index]['id'] = $data['id'];
        $arvoreAtual[$index]['idContexto'] = $data['idContexto'];
        $arvoreAtual[$index]['parent'] = $data['parent'];
        $arvoreAtual[$index]['suposicao'] = $data['suposicao'];
        $arvoreAtual[$index]['text'] = $data['text'];
        
    }

    private function reduzirAbsurdo(&$arvoreAtual,$data){
        $index = count($arvoreAtual);
        $indicePai = substr($data, 0, -2);
        foreach ($arvoreAtual as $key => $value) {
            if($value['id'] == $indicePai){
                $arvoreAtual[$index]['contexto'] = $value['contexto'];
                array_pop($arvoreAtual[$index]['contexto']);
                $arvoreAtual[$index]['icon'] = "";
                if(filter_var($indicePai, FILTER_VALIDATE_INT))
                    $arvoreAtual[$index]['id'] = $indicePai+1;
                else{
                    $arvoreAtual[$index]['id'] = ((int)substr($indicePai, -1))+1;
                    $arvoreAtual[$index]['id'] = substr($indicePai, 0, -1).$arvoreAtual[$index]['id'];
                }
                array_push($arvoreAtual[$index]['contexto'], (string)$arvoreAtual[$index]['id']);
                $arvoreAtual[$index]['idContexto'] = $value['idContexto']+1;
                $arvoreAtual[$index]['parent'] = $value['parent'];
                $arvoreAtual[$index]['suposicao'] = 0;
                $arvoreAtual[$index]['text'] = $this->pegarInverso($value['text']);
                // return $value;
                break;
            }
        }
        return $arvoreAtual[$index];
    }

    private function incluirImplica(&$arvoreAtual,$data,$id){
        $index = count($arvoreAtual);
        $indicePai = substr($id, 0, -2);
        // return $indicePai;
        foreach ($arvoreAtual as $key => $value) {
            if($value['id'] == $indicePai){
                $arvoreAtual[$index]['contexto'] = $value['contexto'];
                array_pop($arvoreAtual[$index]['contexto']);
                $arvoreAtual[$index]['icon'] = "";
                $arvoreAtual[$index]['id'] = $indicePai+1;
                array_push($arvoreAtual[$index]['contexto'], (string)$arvoreAtual[$index]['id']);
                $arvoreAtual[$index]['idContexto'] = $value['idContexto']+1;
                $arvoreAtual[$index]['parent'] = $value['parent'];
                $arvoreAtual[$index]['suposicao'] = 0;
                if(strlen($value['text']) == 3)
                    $value['text'] = $value['text'][1];
                $arvoreAtual[$index]['text'] = "(".$value['text']."→".$data.")";
                // return $value;
                break;
            }
        }
        return $arvoreAtual[$index];
    }

    private function eliminarOu(&$arvoreAtual,$data,$id){
        $index = count($arvoreAtual);
        $indicePai = substr($id, 0, -2);
        // return $indicePai;
        foreach ($arvoreAtual as $key => $value) {
            if($value['id'] == $indicePai){
                $arvoreAtual[$index]['contexto'] = $value['contexto'];
                array_pop($arvoreAtual[$index]['contexto']);
                $arvoreAtual[$index]['icon'] = "";
                $arvoreAtual[$index]['id'] = $indicePai+1;
                array_push($arvoreAtual[$index]['contexto'], (string)$arvoreAtual[$index]['id']);
                $arvoreAtual[$index]['idContexto'] = $value['idContexto']+2;
                $arvoreAtual[$index]['parent'] = $value['parent'];
                $arvoreAtual[$index]['suposicao'] = 0;
                $arvoreAtual[$index]['text'] = $data;
                // return $value;
                break;
            }
        }
        return $arvoreAtual[$index];
    }

    private function iniciarOu(&$arvoreAtual,$data,$id){
        $index = count($arvoreAtual);
        $indicePai = $id;
        foreach ($arvoreAtual as $key => $value) {
            if($value['id'] == $indicePai){
                $arvoreAtual[$index]['contexto'] = $value['contexto'];
                // array_pop($arvoreAtual[$index]['contexto']);
                $arvoreAtual[$index]['icon'] = "true";
                $arvoreAtual[$index]['id'] = $indicePai+2;
                array_push($arvoreAtual[$index]['contexto'], (string)$arvoreAtual[$index]['id']);
                $arvoreAtual[$index]['idContexto'] = $value['idContexto']+1;
                 $arvoreAtual[$index]['idIrmao'] = $data['idIrmao'];
                $arvoreAtual[$index]['parent'] = $value['parent'];
                $arvoreAtual[$index]['suposicao'] = 1;
                $arvoreAtual[$index]['text'] = $data['text'];
                // return $data;
                break;
            }
        }
        return $arvoreAtual[$index];
    }

    private function pegarInverso($formula){
        if($formula[0] == "("){
            // return substr($formula, 1, 4);
            if(substr($formula, 1, 4) == "¬¬")
                return "(".substr($formula, 3);
            elseif(substr($formula, 1, 3) == "¬")
                return "(".substr($formula, 3);
            else
                return "(¬".$formula.")";
        }elseif(substr($formula, 0, 4) == "¬¬")
            return "(".substr($formula, 2).")";
            // return "estou aqui";
        elseif(substr($formula, 0, 2) == "¬")
            return substr($formula, 2);
        else
            return substr($formula, 2);

    }


    private function irmaos($arvoreAtual,$selecionados){
        foreach ($selecionados as $key => $value) {

            foreach ($arvoreAtual as $chave => $valor) {
                if($valor['id'] == $value['parent']){
                    if(!array_key_exists("idIrmao", $valor)){
                        $irmao[$key] = "";
                        $pai[$key] = $valor['id'];
                    }else{
                        $irmao[$key] = $valor['idIrmao'];
                        $pai[$key] = $valor['id'];
                    }
                    
                    break;
                }
            }

        }


        if(count($irmao) == 1)
            return false;

        foreach ($irmao as $key => $value) {
            $controle = $key == 0 ? 1:0;
            if($value != ""){
                // return array($value,$controle);
                if($value == $pai[$controle]){
                    return true;
                }
            }
        }

        return false;
    }

    //Adiciona as informações de descendência do nó a ser inserido
    private function verificaDescendencia($arvoreAtual, &$data){

	if( $arvoreAtual[count($arvoreAtual)-1]['suposicao'] == 1 ){
    		$data['idContexto'] = 1;

    		$data['id'] = $arvoreAtual[count($arvoreAtual)-1]['id'].".".$data['idContexto'];
    		$data['parent'] = $arvoreAtual[count($arvoreAtual)-1]['id'];
    	}
    	else{
    		$data['idContexto'] = $arvoreAtual[count($arvoreAtual)-1]['idContexto']+1;
    		if(is_integer($arvoreAtual[count($arvoreAtual)-1]['id']))
    			$data['id'] = $arvoreAtual[count($arvoreAtual)-1]['id']+1;
    		else{
    			if($data['idContexto'] > 10)
    				$data['id'] = substr($arvoreAtual[count($arvoreAtual)-1]['id'], 0, -2).$data['idContexto'];
    			else
    				$data['id'] = substr($arvoreAtual[count($arvoreAtual)-1]['id'], 0, -1).$data['idContexto'];
    		}
    		$data['parent'] = $arvoreAtual[count($arvoreAtual)-1]['parent'];
    	}
    }

    //Adiciona todos os nós que fazem parte do contexto do nó inserido
	private function adicionarContexto($arvoreAtual, &$data){
	    $data['contexto'] = $arvoreAtual[count($arvoreAtual)-1]['contexto'];
	    $data['contexto'][] = $data['id'];

    }

    //Separa todas as fórmulas em esquerda, direita e conectivo
    private function verificaSelecionado($selecao){
    	foreach ($selecao as $key => $value) {
    		$selecionado[$key] = explode(". ", $value['text'])[1];
	    	$selecionado[$key] = Exercicios::converteSimbolosEntrada($selecionado[$key]);
	    	FuncoesAuxiliares::converteConectivoSimbolo($selecionado[$key]);
	    	$selecionado[$key] = ParsingFormulas::resolveParenteses($selecionado[$key]);

	    	// ParsingFormulas::formataFormulas($selecionado[$key]['esquerdo']);
	    	// ParsingFormulas::formataFormulas($selecionado[$key]['direito']);
	    	
	    	FuncoesAuxiliares::converteConectivoExtenso($selecionado[$key]['esquerdo']);
	    	FuncoesAuxiliares::converteConectivoExtenso($selecionado[$key]['direito']);

	    	if(substr($selecionado[$key]['esquerdo'], 0, 1) != "(")
	    		$selecionado[$key]['esquerdo'] = "(".$selecionado[$key]['esquerdo'].")";

	    	if(substr($selecionado[$key]['direito'], 0, 1) != "(")
	    		$selecionado[$key]['direito'] = "(".$selecionado[$key]['direito'].")";
    	}
    	return $selecionado;
    }
}
