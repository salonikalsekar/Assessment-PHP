function getRowsFromDB(){

	$.ajax({
		url: "app/controllers/requestController.php",
		type: "GET",
		dataType: "html",
		success: function(html){
			$('#myTable > tbody').html(html);
		}
	});
}