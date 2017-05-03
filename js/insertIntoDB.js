function insertIntoDB(ASIN, Title, MPN, Price){
	
	$.ajax({
		url: "app/controllers/requestController.php",
		type: "POST",
		data: 'ASIN='+ASIN+'&Title='+Title+'&MPN='+MPN+'&Price='+Price,
		success: function(result){
			if(result=='true'){
				getRowsFromDB();
				$('#myTable2').hide();
				$('#insertBtn').hide();
				$('#resultRow').html();
			}
			else if(result=='false'){
				alert('This ASIN is already in the database.');
				$('#myTable2').hide();
				$('#insertBtn').hide();
				$('#resultRow').html();
				$('#ASIN').focus();
			}
			else if(result=='length'){
				alert('ASIN has to be 10 characters in length.');
				$('#myTable2').hide();
				$('#insertBtn').hide();
				$('#resultRow').html();
				$('#ASIN').focus();
			}
			else{
				alert('Something went wrong!');
				$('#myTable2').hide();
				$('#insertBtn').hide();
				$('#resultRow').html();
				$('#ASIN').focus();
			}
		}
	});
}