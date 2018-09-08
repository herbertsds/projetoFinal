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

					console.log(retorno2);
				},
			});

}

function f_abreGabSem() {
	var win = window.open('gabSemantica.html?exercicioBuscado=' + numExercicio);

}
function f_LimpaSemantica() {
	$("#s_divFormulas").empty();

}