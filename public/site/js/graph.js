$(document).ready(function () {
	// console.log(window.sharedData.rates);
	var config = {
		type: 'line',
		data: {
			labels: window.sharedData.rates.labels,
			datasets: window.sharedData.rates.datasets
		},
		options: {
			responsive: true,
			title: {
				display: true,
				text: 'Крафик изменения курсов валют'
			},
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Дата'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Значение'
					}
				}]
			}
		}
	};

	window.onload = function() {
		var ctx = document.getElementById('canvas').getContext('2d');
		window.myLine = new Chart(ctx, config);
	};
});
