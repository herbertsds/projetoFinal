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
    			return $this->incE($request);
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
    	if (count($request->selecionados) > 1 ){
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

    private function elimE($request){
        if (count($request->selecionados) > 1 ){
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

        if($selecionado[$indexImplica]['esquerdo'] != $selecionado[$indexEliminar]){
            $mensagem['erro'] = "Não é possível realizar a exclusão do implica com a fórmula selecionada";
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
        if (count($request->selecionados) != 2 ){
            $mensagem['erro'] = "É necessário selecionar duas linhas para essa operação";
            return $mensagem;
        }

        if(!$this->verificaContexto($arvoreAtual,$request->selecionados)){
            $mensagem['erro'] = "As fórmulas não fazem parte do mesmo contexto";
            return $mensagem;
        }

        $arvoreAtual = $request->atual;

        $formula1 = explode(". ", $request->selecionados[0]['text'])[1];
        $formula2 = explode(". ", $request->selecionados[1]['text'])[1];

        $data['text'] = Exercicios::converteSaida("($formula1"."e$formula2)");
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

        if(count($request->selecionados) == 2){
            if(!$this->verificaContexto($arvoreAtual,$request->selecionados)){
                $mensagem['erro'] = "As fórmulas não fazem parte do mesmo contexto";
                return $mensagem;
            }
        }

        

        $arvoreAtual = $request->atual;

        $formula1 = explode(". ", $request->selecionados[0]['text'])[1];
        $formula2 = explode(". ", $request->selecionados[1]['text'])[1];

        $data['text'] = Exercicios::converteSaida("($formula1"."e$formula2)");
        $data['icon'] = "";
        $data['suposicao'] = 0;

        // return $selecionado;


        $this->verificaDescendencia($arvoreAtual,$data);
        $this->adicionarContexto($arvoreAtual,$data);

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
