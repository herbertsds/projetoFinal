function f_GabTableaux(exercicio) {
	var myData = {
		'exercicio' : exercicio
	}; // parseInt(numExercicio)
	console.log(myData);

	$
			.ajax({

				url : 'http://127.0.0.1:8000/api/tableaux/',
				type : 'GET',
				callback : '?',
				data : myData,
				datatype : 'application/json',
				success : function(obj) {
					obj = JSON.parse(obj);
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

					console.log(retorno2);
				},
			});

}
function f_abreGabTab() {
	var win = window.open('gabTableaux.html?exercicioBuscado=' + numExercicio);

}
function f_LimpaTableaux() {
	$("#t_divFormulas").empty();

}