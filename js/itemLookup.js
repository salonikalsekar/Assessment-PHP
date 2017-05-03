function itemLookup(asin){

	if(asin!==''){
		$.ajax({
			url: "app/controllers/requestController.php",
			data: 'ASIN='+asin,
			type: "GET",
			dataType: "json",
			success: function(data){

				if(data.Exists=='true'){
					$('#resultRow').html(
					'<td id="td_ASIN">'+data.ASIN+'</td><td id="td_Title">'+data.Title+
					'</td><td id="td_MPN">'+data.MPN+'</td><td id="td_Price">'+data.Price+'</td>');
				
					$('#myTable2').show();
					$('#insertBtn').show();
				}
				else{
					$('#myTable2').hide();
					$('#insertBtn').hide();
					$('#resultRow').html();
					alert('A product with this ASIN is not found');
				}
			}
		});
	}
	else{
		alert('No!');
	}
}