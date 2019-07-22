$(document).ready(function () {
	// Chart.plugins.register({
	// 		beforeRender: function (chart) {
  //       console.log(chart.data.datasets);
	//
	// 			if (chart.config.options.showAllTooltips) {
	// 				// create an array of tooltips
	// 				// we can't use the chart tooltip because there is only one tooltip per chart
	// 				chart.pluginTooltips = [];
	// 				chart.config.data.datasets.forEach(function (dataset, i) {
  //           // console.log(dataset);
	//
	// 					chart.getDatasetMeta(i).data.forEach(function (sector, j) {
	// 						chart.pluginTooltips.push(new Chart.Tooltip({
	// 							_chart: chart.chart,
	// 							_chartInstance: chart,
	// 							_data: chart.data,
	// 							_options: chart.options.tooltips,
	// 							_active: [sector]
	// 						}, chart));
	// 					});
	// 				});
	//
	// 				// turn off normal tooltips
	// 				chart.options.tooltips.enabled = false;
	// 			}
	// 		},
	//
	// 		afterDraw: function (chart, easing) {
	// 			if (chart.config.options.showAllTooltips) {
	// 				// we don't want the permanent tooltips to animate, so don't do anything till the animation runs atleast once
	// 				if (!chart.allTooltipsOnce) {
	// 					if (easing !== 1)
	// 						return;
	// 					chart.allTooltipsOnce = true;
	// 				}
	//
	// 				// turn on tooltips
	// 				chart.options.tooltips.enabled = true;
	// 				Chart.helpers.each(chart.pluginTooltips, function (tooltip) {
  //           // This line checks if the item is visible to display the tooltip
  //           // console.log(tooltip);
  //         	if(!tooltip._active[0].hidden){
  //             tooltip.initialize();
  //             tooltip.update();
  //             // we don't actually need this since we are not animating tooltips
  //             tooltip.pivot();
  //             tooltip.transition(easing).draw();
  //           }
	// 				});
	// 				chart.options.tooltips.enabled = false;
	// 			}
	// 		}
	// 	});

    // console.log(Chart.defaults.global.tooltips);

    var ctx = document.getElementById('chart').getContext('2d');
        var gradientFill = ctx.createLinearGradient(100, 0, 100, 600);
        gradientFill.addColorStop(0, "rgba(29, 94, 62, 0.95)");
        gradientFill.addColorStop(1, "rgba(45, 54, 49, 0.2)");

        // var data = [
        //   {
        //     backgroundColor: null,
        //     borderColor: "#fff",
        //     data: [
        //       "426.48",
        //       "426.48",
        //       "426.48",
        //       "426.48",
        //       "431.56",
        //       "430.63"
        //     ],
        //     fill: true,
        //     label: "Еуро",
        //     pointBackgroundColor: "#1C8048",
        //     pointRadius: 5,
        //     pointStrokeColor: "#9DB86D"
        //   },
        //   {
        //     backgroundColor: null,
        //     borderColor: "#fff",
        //     data: [
        //       "428.48",
        //       "430.48",
        //       "427.48",
        //       "426.48",
        //       "425.56",
        //       "432.63"
        //     ],
        //     fill: true,
        //     label: "Еуро2",
        //     pointBackgroundColor: "#1C8048",
        //     pointRadius: 5,
        //     pointStrokeColor: "#9DB86D"
        //   }
        // ];
        var data = window.sharedData.rates.datasets;

        Object.keys(data).forEach(function(key){ data[key]['backgroundColor'] = gradientFill });

        var config = {
          type: 'line',
          data: {
            labels: window.sharedData.rates.labels,
            datasets: data
          },
          options: {
            legend: {
              // display: false
            },
            showAllTooltips: true,
            responsive: true,
            title: {
              display: true,
              text: 'Крафик изменения курсов валют'
            },
            tooltips: {
              enabled: true,
              mode: 'index',
              intersect: true,
              yAlign: 'bottom',
              xAlign: 'center'
              // custom: customTooltips
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

        var chart = new Chart(ctx, config);
});
