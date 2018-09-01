$(function(){
	var tree = [];
	
	var myData = {
        'formulas': ["(AimplicaC)"]
    };

    var pergunta = {
    	'pergunta': ["not(Ae(not(C)))"]
    }

	startArvore(myData);

	$("#incE").click(function(){ incE(); });
	$("#elimE").click(function(){ elimE(); });
	$("#supor").click(function(){ supor(); });
	$("#elimNot").click(function(){ elimNot(); });
	$("#excImp").click(function(){ excImp(); });
	$("#abs").click(function(){ abs(); });
});

//Carga inicial da dedução natural
function startArvore(myData){
    $.ajax({
        url: 'http://127.0.0.1:8000/api/deducaoNatural/',
        type: 'GET',
        callback: '?',
        data: myData,
        datatype: 'application/json',
        success: function(resposta) { 
	        tree = resposta;
	        var data = {};
			
	        for(chave in resposta){
	        	data['id'] = resposta[chave]['id'];
	        	data['text'] = data['id']+". "+resposta[chave]['text'];
	        	data['icon'] = resposta[chave]['icon'] == "" ? false:true;
	        	data['state'] = {"opened" : true};
	        	$('#jstree_div').jstree().create_node('#', data, 'last', function(){}, true);
	        }
        },
        error: function(erro) {
			console.log(erro.responseText);
		}
    });
}

function supor(){
	var myData = {
		"step": "supor",
		"supor": "notnot(Ae(not(C)))",
		"atual": tree
	};
	// Carga inicial da dedução natural
    $.ajax({
        url: 'http://127.0.0.1:8000/api/deducaoNatural/step/',
        type: 'GET',
        callback: '?',
        data: myData,
        datatype: 'application/json',
        success: function(resposta) { 
	        tree.push(resposta);
	        
	        var data = {};

	        data['id'] = resposta['id'];
        	data['text'] = data['id']+". "+resposta['text'];
        	data['icon'] = resposta['icon'] == "" ? false:true;
        	data['state'] = {"opened" : true};
        	$('#jstree_div').jstree().create_node(resposta['parent'], data, 'last', function(){}, true);
        },
        error: function(erro) {
			console.log(erro.responseText);
		}
    });
}

function elimNot(){
	var selecionados = $("#jstree_div").jstree("get_selected",true);
	var myData = {
		"step": "elimNot",
		"selecionados": selecionados,
		"atual": tree
	};
	// Carga inicial da dedução natural
    $.ajax({
        url: 'http://127.0.0.1:8000/api/deducaoNatural/step/',
        type: 'GET',
        callback: '?',
        data: myData,
        datatype: 'application/json',
        success: function(resposta) { 
        	console.log(resposta);
        	if("erro" in resposta){
        		// console.log(resposta['erro']);
        	}else{
        		tree.push(resposta);
	        
		        var data = {};

		        data['id'] = resposta['id'];
	        	data['text'] = data['id']+". "+resposta['text'];
	        	data['icon'] = resposta['icon'] == "" ? false:true;
	        	data['state'] = {"opened" : true};
	        	$('#jstree_div').jstree().create_node(resposta['parent'], data, 'last', function(){}, true);
        	}
	        
        },
        error: function(erro) {
			console.log(erro.responseText);
		}
    });
}

function incE(){
	var selecionados = $("#jstree_div").jstree("get_selected",true);
	var myData = {
		"step": "incE",
		"selecionados": selecionados,
		"atual": tree
	};
	// Carga inicial da dedução natural
    $.ajax({
        url: 'http://127.0.0.1:8000/api/deducaoNatural/step/',
        type: 'GET',
        callback: '?',
        data: myData,
        datatype: 'application/json',
        success: function(resposta) { 
        	if("erro" in resposta){
        		console.log(resposta);
        	}else{
        		console.log(resposta);
        		tree.push(resposta);
	        
		        var data = {};

		        data['id'] = resposta['id'];
	        	data['text'] = data['id']+". "+resposta['text'];
	        	data['icon'] = resposta['icon'] == "" ? false:true;
	        	data['state'] = {"opened" : true};
	        	$('#jstree_div').jstree().create_node(resposta['parent'], data, 'last', function(){}, true);
	        }
        },
        error: function(erro) {
			console.log(erro.responseText);
		}
    });
}

function elimE(){
	var selecionados = $("#jstree_div").jstree("get_selected",true);
	var myData = {
		"step": "elimE",
		"selecionados": selecionados,
		"atual": tree
	};
	// Carga inicial da dedução natural
    $.ajax({
        url: 'http://127.0.0.1:8000/api/deducaoNatural/step/',
        type: 'GET',
        callback: '?',
        data: myData,
        datatype: 'application/json',
        success: function(resposta) { 
        	if("erro" in resposta){
        		console.log(resposta);
        	}else{
        		// console.log(resposta);
        		
	        
		        var data = {};

		        for(chave in resposta){
		        	tree.push(resposta[chave]);
		        	console.log()
		        	data['id'] = resposta[chave]['id'];
		        	data['text'] = data['id']+". "+resposta[chave]['text'];
		        	data['icon'] = resposta[chave]['icon'] == "" ? false:true;
		        	data['state'] = {"opened" : true};
		        	$('#jstree_div').jstree().create_node(resposta[chave]["parent"], data, 'last', function(){}, true);
		        }
        	}
	        
        },
        error: function(erro) {
			console.log(erro.responseText);
		}
    });
}

function excImp(){
	var selecionados = $("#jstree_div").jstree("get_selected",true);
	var myData = {
		"step": "excImp",
		"selecionados": selecionados,
		"atual": tree
	};
	// Carga inicial da dedução natural
    $.ajax({
        url: 'http://127.0.0.1:8000/api/deducaoNatural/step/',
        type: 'GET',
        callback: '?',
        data: myData,
        datatype: 'application/json',
        success: function(resposta) { 
        	if("erro" in resposta){
        		console.log(resposta);
        	}else{
        		console.log(resposta);
        		tree.push(resposta);
	        
		        var data = {};

		        data['id'] = resposta['id'];
	        	data['text'] = data['id']+". "+resposta['text'];
	        	data['icon'] = resposta['icon'] == "" ? false:true;
	        	data['state'] = {"opened" : true};
	        	$('#jstree_div').jstree().create_node(resposta['parent'], data, 'last', function(){}, true);
        	}
	        
        },
        error: function(erro) {
			console.log(erro.responseText);
		}
    });
}