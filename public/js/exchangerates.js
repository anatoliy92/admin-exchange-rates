$(document).ready(function($){

		if ($('#relevant').length) {
			$( "#relevant" ).datepicker({
		    changeMonth: true,
		    changeYear: true,
		    dateFormat: 'yy-mm-dd',
				minDate: new Date(),
		    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
		    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
				onSelect: function(selectedDate) {
					console.log(selectedDate);
					$("#show-datepicker > span").html(selectedDate);

					var url = $("#url-create").attr("href");
					if (url.indexOf("?")>-1) {
						url = url.substr(0,url.indexOf("?"));
					}
					$("#url-create").attr("href", url + '?date=' + selectedDate);

					$("#datepicker-container").hide();
				}
		  });
		}

		$("#show-datepicker").click(function(){
			$("#datepicker-container").show();
		});

/* Обновление media */
		$("body").on('click', '#update-nsi', function(e) {
			e.preventDefault();
			var id         = $('#section--id').val();

			$(".card-body-overlay").css({'display': 'block'});

			$.ajax({
					url: '/sections/'+ id +'/exchangerates/receiveNSI',
					type: 'POST',
					async: false,
					dataType: 'json',
					data : { _token: $('meta[name="_token"]').attr('content'), date: $('#relevant').val()},
					success: function(data) {
						if (data.errors) {
							messageError(data.errors);
						} else {
							$("#receiveNSI").html(data.html);

						}
						$(".card-body-overlay").css({'display': 'none'});
					}
			});
		});

});
