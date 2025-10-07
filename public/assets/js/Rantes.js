
$(document).ready( function () {
	$( ".ACCOUNT_NAME" ).autocomplete({
		source: realPath+'Account/get?type=2',
		minLength: 2,
		select: function( event, ui ) {
			$(".ac").val(ui.item.label)
			console.log(ui.item);
			fillForm(ui.item)
		}
	});
	$( ".ACCOUNT_NO" ).autocomplete({
		source: realPath+'Account/get?type=1',
		minLength: 2,
		select: function( event, ui ) {
			$(".ac").val(ui.item.label)
			console.log(ui.item);
			fillForm(ui.item)
		}
	});
});
	
	function fillForm(item){
		$("#ACCOUNT_NAME_IN").val(item.ACOUNT_NAME);
		$("#part1").val(item.TEACHER_NO);
		$("#part2").val(item.MONY);
		$("#part3").val(item.AGENT_NO);
		$("#part4").val(item.ACC_SERIAL);
		$("#PRICE_IN").val(item.PRICE);
	}