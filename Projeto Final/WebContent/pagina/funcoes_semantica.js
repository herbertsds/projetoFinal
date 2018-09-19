var dominios = 0;
var vet_dominio = [];
var dominioAdicionado = "";
var relacoes = 0;
var vet_relacoes=[];
var relacoesAdicionadas = "";
function f_GabSemantica(exercicio) {
	var myData = {
		'exercicio' : exercicio
	};
	console.log(' enviando : ' + myData);

	$
			.ajax({

				url : 'http://127.0.0.1:8000/api/semantica/',
				type : 'GET',
				callback : '?',
				data : myData,
				datatype : 'application/json',
				success : function(obj) {
					console.log(obj);
					$('#iframe')
							.contents()
							.find('head')
							.append(
									'    <link rel="stylesheet" href="jquery.orgchart.css"/>');
					$('#iframe').css('height', $(window).height() + 'px');

					$('#iframe')
							.contents()
							.find('body')
							.append(
									'<div name="divArvSem" class="form-check"  id="s_divNovasFormulas"><div id="s_content"><div id="s_main"><ul id="s_organisation"></ul></div></div></div>');
					$('#iframe').contents().find("#s_organisation").append(obj[0]);
					$('#iframe').contents().find("#s_organisation").orgChart({
						container : $('#iframe').contents().find("#s_main")
					});
					jQuery.each(obj[1],function() {
						//console.log(this);
						
						if(this['valor'] == true){
							console.log("verde");
						}
						else {console.log("vermelho");}
						
					});
				},
				error : function(retorno2) {

					console.log(retorno2);
				},
			});

}

function f_abreGabSem() {
	var win = window.open('gabSemantica.html?exercicioBuscado=' + numExercicio);

}

function f_AddDominio(){
	dominios++;
	vet_dominio.push( $('#s_Dominio').val().replace(/\s/gi, ''));
	dominioAdicionado = $('#s_Dominio').val().replace(/\s/gi, '');

	$('#dominioAdicionado').append(
			"<br/>" + dominios + ": " + dominioAdicionado);
	$('#s_Dominio').val("");
	$("#buttonRemoverDominio").removeAttr("style");

}

function f_RemoverDominio() {
	if (dominios > 0) {
		vet_dominio.splice(-1, 1);
		dominios--;

		$('#dominioAdicionado').html(function(_, html) {
			return html.split(/<br\s*\/?>/i).slice(0, -1).join('<br>')
		});
		if (dominios == 0) {
			$("#buttonRemoverDominio").attr("style", 'display:none');

		}
	}
	f_LimpaDominio();

}

function f_LimpaDominio(){
	
	dominioAdicionado = "";
	
}


function f_AddRelacao(){
	relacoes++;
	vet_relacoes.push( $('#s_Relacoes').val().replace(/\s/gi, ''));
	relacaoAdicionada = $('#s_Relacoes').val().replace(/\s/gi, '');

	$('#relacoesAdicionadas').append(
			"<br/>" + relacoes + ": " + relacaoAdicionada);
	$('#s_Relacoes').val("");
	$("#buttonRemoverRelacao").removeAttr("style");

}

function f_RemoverRelacao(){
	if (relacoes > 0) {
		vet_relacoes.splice(-1, 1);
		relacoes--;

		$('#relacoesAdicionadas').html(function(_, html) {
			return html.split(/<br\s*\/?>/i).slice(0, -1).join('<br>')
		});
		if (relacoes == 0) {
			$("#buttonRemoverRelacao").attr("style", 'display:none');

		}
	}
	f_LimpaRelacao();
}

function f_LimpaRelacao(){
	
	relacaoAdicionada = "";
	
}


function f_LimpaSemantica() {
	$("#s_divFormulas").empty();

}