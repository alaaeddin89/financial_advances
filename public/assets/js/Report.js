
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
		$("#part4").val(item.SERIAL_NO);
		$("#MONY").val(item.ALL_AMOUNT);
		$("#MONY_NAME").val(item.MONY_NAME);
	}
	
	function cancelDoc(id){ 
	if(!confirm('هل تريد بالتأكيد حذف السجل'))
		return
			var formData = {
					'RANTE_NO_IN': id
				};
				$.ajax({
					url: realPath + 'Account/WEB_REVERSE_ACTIVITY_PR',
					type: 'POST',
					data: formData,
					dataType: "json",
					async: true,
					success: function (data) {
						location.reload()
					},
					error: function () {
						$(".alert-success").addClass("hide");
						$(".alert-danger").removeClass('hide');
						$("#errMsg").text(data.status.msg)
						$(".loader").addClass('hide');
						//$(".form-actions").removeClass('hide');
					},
					/*cache: false,
					 contentType: false,
					 processData: false*/
				});
	}