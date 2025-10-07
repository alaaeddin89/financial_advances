"use strict";

// Shared Colors Definition
const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

// Class definition
function generateBubbleData(baseval, count, yrange) {
    var i = 0;
    var series = [];
    while (i < count) {
      var x = Math.floor(Math.random() * (750 - 1 + 1)) + 1;;
      var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;
      var z = Math.floor(Math.random() * (75 - 15 + 1)) + 15;
  
      series.push([x, y, z]);
      baseval += 86400000;
      i++;
    }
    return series;
  }

function generateData(count, yrange) {
    var i = 0;
    var series = [];
    while (i < count) {
        var x = 'w' + (i + 1).toString();
        var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;

        series.push({
            x: x,
            y: y
        });
        i++;
    }
    return series;
}

var KTApexChartsDemo = function () {
	// Private functions
	
	var _demo2 = function () {
		const apexChart = "#chart_2";
		var options = {
			series: [{
				name: 'series1',
				data: [31, 40, 28, 51, 42, 109, 100]
			}, {
				name: 'series2',
				data: [11, 32, 45, 32, 34, 52, 41]
			}],
			chart: {
				height: 350,
				type: 'area'
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				curve: 'smooth'
			},
			xaxis: {
				type: 'datetime',
				categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
			},
			tooltip: {
				x: {
					format: 'dd/MM/yy HH:mm'
				},
			},
			colors: [primary, success]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo3 = function () {
		const apexChart = "#chart_3";
		var options = {
			series: [{
				name: 'Net Profit',
				data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
			}, {
				name: 'Revenue',
				data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
			}, {
				name: 'Free Cash Flow',
				data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
			}],
			chart: {
				type: 'bar',
				height: 350
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '55%',
					endingShape: 'rounded'
				},
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			xaxis: {
				categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
			},
			yaxis: {
				title: {
					text: '$ (thousands)'
				}
			},
			fill: {
				opacity: 1
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return "$ " + val + " thousands"
					}
				}
			},
			colors: [primary, success, warning]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo4 = function () {
		const apexChart = "#chart_4";
		var options = {
			series: [{
				name: 'Marine Sprite',
				data: [44, 55, 41, 37, 22, 43, 21]
			}, {
				name: 'Striking Calf',
				data: [53, 32, 33, 52, 13, 43, 32]
			}, {
				name: 'Tank Picture',
				data: [12, 17, 11, 9, 15, 11, 20]
			}, {
				name: 'Bucket Slope',
				data: [9, 7, 5, 8, 6, 9, 4]
			}, {
				name: 'Reborn Kid',
				data: [25, 12, 19, 32, 25, 24, 10]
			}],
			chart: {
				type: 'bar',
				height: 350,
				stacked: true,
			},
			plotOptions: {
				bar: {
					horizontal: true,
				},
			},
			stroke: {
				width: 1,
				colors: ['#fff']
			},
			title: {
				text: 'Fiction Books Sales'
			},
			xaxis: {
				categories: [2008, 2009, 2010, 2011, 2012, 2013, 2014],
				labels: {
					formatter: function (val) {
						return val + "K"
					}
				}
			},
			yaxis: {
				title: {
					text: undefined
				},
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return val + "K"
					}
				}
			},
			fill: {
				opacity: 1
			},
			legend: {
				position: 'top',
				horizontalAlign: 'left',
				offsetX: 40
			},
			colors: [primary, success, warning, danger, info]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo5 = function () {
		const apexChart = "#chart_5";
		var options = {
			series: [{
				name: 'Income',
				type: 'column',
				data: [1.4, 2, 2.5, 1.5, 2.5, 2.8, 3.8, 4.6]
			}, {
				name: 'Cashflow',
				type: 'column',
				data: [1.1, 3, 3.1, 4, 4.1, 4.9, 6.5, 8.5]
			}, {
				name: 'Revenue',
				type: 'line',
				data: [20, 29, 37, 36, 44, 45, 50, 58]
			}],
			chart: {
				height: 350,
				type: 'line',
				stacked: false
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				width: [1, 1, 4]
			},
			title: {
				text: 'XYZ - Stock Analysis (2009 - 2016)',
				align: 'left',
				offsetX: 110
			},
			xaxis: {
				categories: [2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016],
			},
			yaxis: [
				{
					axisTicks: {
						show: true,
					},
					axisBorder: {
						show: true,
						color: primary
					},
					labels: {
						style: {
							colors: primary,
						}
					},
					title: {
						text: "Income (thousand crores)",
						style: {
							color: primary,
						}
					},
					tooltip: {
						enabled: true
					}
				},
				{
					seriesName: 'Income',
					opposite: true,
					axisTicks: {
						show: true,
					},
					axisBorder: {
						show: true,
						color: success
					},
					labels: {
						style: {
							colors: success,
						}
					},
					title: {
						text: "Operating Cashflow (thousand crores)",
						style: {
							color: success,
						}
					},
				},
				{
					seriesName: 'Revenue',
					opposite: true,
					axisTicks: {
						show: true,
					},
					axisBorder: {
						show: true,
						color: warning
					},
					labels: {
						style: {
							colors: warning,
						},
					},
					title: {
						text: "Revenue (thousand crores)",
						style: {
							color: warning,
						}
					}
				},
			],
			tooltip: {
				fixed: {
					enabled: true,
					position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
					offsetY: 30,
					offsetX: 60
				},
			},
			legend: {
				horizontalAlign: 'left',
				offsetX: 40
			}
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo6 = function () {
		const apexChart = "#chart_6";
		var options = {
			series: [
				{
					data: [
						{
							x: 'Analysis',
							y: [
								new Date('2019-02-27').getTime(),
								new Date('2019-03-04').getTime()
							],
							fillColor: primary
						},
						{
							x: 'Design',
							y: [
								new Date('2019-03-04').getTime(),
								new Date('2019-03-08').getTime()
							],
							fillColor: success
						},
						{
							x: 'Coding',
							y: [
								new Date('2019-03-07').getTime(),
								new Date('2019-03-10').getTime()
							],
							fillColor: info
						},
						{
							x: 'Testing',
							y: [
								new Date('2019-03-08').getTime(),
								new Date('2019-03-12').getTime()
							],
							fillColor: warning
						},
						{
							x: 'Deployment',
							y: [
								new Date('2019-03-12').getTime(),
								new Date('2019-03-17').getTime()
							],
							fillColor: danger
						}
					]
				}
			],
			chart: {
				height: 350,
				type: 'rangeBar'
			},
			plotOptions: {
				bar: {
					horizontal: true,
					distributed: true,
					dataLabels: {
						hideOverflowingLabels: false
					}
				}
			},
			dataLabels: {
				enabled: true,
				formatter: function (val, opts) {
					var label = opts.w.globals.labels[opts.dataPointIndex]
					var a = moment(val[0])
					var b = moment(val[1])
					var diff = b.diff(a, 'days')
					return label + ': ' + diff + (diff > 1 ? ' days' : ' day')
				},
				style: {
					colors: ['#f3f4f5', '#fff']
				}
			},
			xaxis: {
				type: 'datetime'
			},
			yaxis: {
				show: false
			},
			grid: {
				row: {
					colors: ['#f3f4f5', '#fff'],
					opacity: 1
				}
			}
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	

	var _demo8 = function () {
		const apexChart = "#chart_8";
		var options = {
			series: [{
				name: 'Bubble1',
				data: generateBubbleData(new Date('11 Feb 2017 GMT').getTime(), 20, {
				  min: 10,
				  max: 60
				})
			  },
			  {
				name: 'Bubble2',
				data: generateBubbleData(new Date('11 Feb 2017 GMT').getTime(), 20, {
				  min: 10,
				  max: 60
				})
			  },
			  {
				name: 'Bubble3',
				data: generateBubbleData(new Date('11 Feb 2017 GMT').getTime(), 20, {
				  min: 10,
				  max: 60
				})
			  },
			  {
				name: 'Bubble4',
				data: generateBubbleData(new Date('11 Feb 2017 GMT').getTime(), 20, {
				  min: 10,
				  max: 60
				})
			  }],
			chart: {
				height: 350,
				type: 'bubble',
			},
			dataLabels: {
				enabled: false
			},
			fill: {
				opacity: 0.8
			},
			xaxis: {
				tickAmount: 12,
				type: 'category',
			},
			yaxis: {
				max: 70
			},
			colors: [primary, success, warning, danger]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo9 = function () {
		const apexChart = "#chart_9";
		var options = {
			series: [{
				name: "SAMPLE A",
				data: [
					[16.4, 5.4], [21.7, 2], [25.4, 3], [19, 2], [10.9, 1], [13.6, 3.2], [10.9, 7.4], [10.9, 0], [10.9, 8.2], [16.4, 0], [16.4, 1.8], [13.6, 0.3], [13.6, 0], [29.9, 0], [27.1, 2.3], [16.4, 0], [13.6, 3.7], [10.9, 5.2], [16.4, 6.5], [10.9, 0], [24.5, 7.1], [10.9, 0], [8.1, 4.7], [19, 0], [21.7, 1.8], [27.1, 0], [24.5, 0], [27.1, 0], [29.9, 1.5], [27.1, 0.8], [22.1, 2]]
			}, {
				name: "SAMPLE B",
				data: [
					[36.4, 13.4], [1.7, 11], [5.4, 8], [9, 17], [1.9, 4], [3.6, 12.2], [1.9, 14.4], [1.9, 9], [1.9, 13.2], [1.4, 7], [6.4, 8.8], [3.6, 4.3], [1.6, 10], [9.9, 2], [7.1, 15], [1.4, 0], [3.6, 13.7], [1.9, 15.2], [6.4, 16.5], [0.9, 10], [4.5, 17.1], [10.9, 10], [0.1, 14.7], [9, 10], [12.7, 11.8], [2.1, 10], [2.5, 10], [27.1, 10], [2.9, 11.5], [7.1, 10.8], [2.1, 12]]
			}, {
				name: "SAMPLE C",
				data: [
					[21.7, 3], [23.6, 3.5], [24.6, 3], [29.9, 3], [21.7, 20], [23, 2], [10.9, 3], [28, 4], [27.1, 0.3], [16.4, 4], [13.6, 0], [19, 5], [22.4, 3], [24.5, 3], [32.6, 3], [27.1, 4], [29.6, 6], [31.6, 8], [21.6, 5], [20.9, 4], [22.4, 0], [32.6, 10.3], [29.7, 20.8], [24.5, 0.8], [21.4, 0], [21.7, 6.9], [28.6, 7.7], [15.4, 0], [18.1, 0], [33.4, 0], [16.4, 0]]
			}],
			chart: {
				height: 350,
				type: 'scatter',
				zoom: {
					enabled: true,
					type: 'xy'
				}
			},
			xaxis: {
				tickAmount: 10,
				labels: {
					formatter: function (val) {
						return parseFloat(val).toFixed(1)
					}
				}
			},
			yaxis: {
				tickAmount: 7
			},
			colors: [primary, success, warning]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo10 = function () {
		const apexChart = "#chart_10";
		var options = {
			series: [{
				name: 'Jan',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Feb',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Mar',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Apr',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'May',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Jun',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Jul',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Aug',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			},
			{
				name: 'Sep',
				data: generateData(20, {
					min: -30,
					max: 55
				})
			}
			],
			chart: {
				height: 350,
				type: 'heatmap',
			},
			plotOptions: {
				heatmap: {
					shadeIntensity: 0.5,

					colorScale: {
						ranges: [{
							from: -30,
							to: 5,
							name: 'low',
							color: success
						},
						{
							from: 6,
							to: 20,
							name: 'medium',
							color: primary
						},
						{
							from: 21,
							to: 45,
							name: 'high',
							color: warning
						},
						{
							from: 46,
							to: 55,
							name: 'extreme',
							color: danger
						}
						]
					}
				}
			},
			dataLabels: {
				enabled: false
			},
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo11 = function () {
		const apexChart = "#chart_11";
		var options = {
			series: [44, 55, 41, 17, 15],
			chart: {
				width: 380,
				type: 'donut',
			},
			responsive: [{
				breakpoint: 480,
				options: {
					chart: {
						width: 200
					},
					legend: {
						position: 'bottom'
					}
				}
			}],
			colors: [primary, success, warning, danger, info]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	

	var _demo13 = function () {
		const apexChart = "#chart_13";
		var options = {
			series: [44, 55, 67, 83],
			chart: {
				height: 350,
				type: 'radialBar',
			},
			plotOptions: {
				radialBar: {
					dataLabels: {
						name: {
							fontSize: '22px',
						},
						value: {
							fontSize: '16px',
						},
						total: {
							show: true,
							label: 'Total',
							formatter: function (w) {
								// By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
								return 249
							}
						}
					}
				}
			},
			labels: ['Apples', 'Oranges', 'Bananas', 'Berries'],
			colors: [primary, success, warning, danger]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	var _demo14 = function () {
		const apexChart = "#chart_14";
		var options = {
			series: [{
				name: 'Series 1',
				data: [80, 50, 30, 40, 100, 20],
			}, {
				name: 'Series 2',
				data: [20, 30, 40, 80, 20, 80],
			}, {
				name: 'Series 3',
				data: [44, 76, 78, 13, 43, 10],
			}],
			chart: {
				height: 350,
				type: 'radar',
				dropShadow: {
					enabled: true,
					blur: 1,
					left: 1,
					top: 1
				}
			},
			stroke: {
				width: 0
			},
			fill: {
				opacity: 0.4
			},
			markers: {
				size: 0
			},
			xaxis: {
				categories: ['2011', '2012', '2013', '2014', '2015', '2016']
			},
			colors: [primary, success, warning]
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

	return {
		// public functions
		init: function () {
			_demo1();
		//	_demo2();
		//	_demo3();
		//	_demo4();
		//	_demo5();
		//	_demo6();
		//	_demo7();
		//	_demo8();
		//	_demo9();
		//	_demo10();
		//	_demo11();
			_demo12();
		//	_demo13();
		//	_demo14();
		}
	};
}();

jQuery(document).ready(function () {
	KTApexChartsDemo.init();
});
