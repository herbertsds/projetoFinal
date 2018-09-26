function f_GabTab_lpo(exercicio) {
	var myData = {
		'exercicio' : exercicio
	}; // parseInt(numExercicio)
	console.log(myData);

	$
			.ajax({

				url : 'http://127.0.0.1:8000/api/tableauxLPO/',
				type : 'GET',
				callback : '?',
				data : myData,
				datatype : 'application/json',
				success : function(resposta) {
					obj = resposta[0];
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
									'<div  class="form-check"  id="s_divNovasFormulas"><div id="s_content"><div id="s_main"><ul id="s_organisation"></ul></div></div></div>');
					$('#iframe').contents().find("#s_organisation").append(obj);
					$('#iframe').contents().find("#s_organisation").orgChart({
						container : $('#iframe').contents().find("#s_main")
					});

				},
				error : function(retorno2) {

					console.log(retorno2.responseText);
				},
			});

}
function f_abreGabTab_lpo() {
	var win = window.open('gabTableaux_lpo.html?exercicioBuscado='
			+ numExercicio);

}
function f_LimpaTableaux_lpo() {

	$("#t_lpo_divFormulas").empty();

}