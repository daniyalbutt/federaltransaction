

(function($) {
    /* "use strict" */
	
 var dlabChartlist = function(){
	
	var screenWidth = $(window).width();	
	
	var pieChart = function(){
		if(jQuery('#pieChart').length > 0 ){
			//doughut chart
			var one = jQuery('#pieChart').data('one');
			var two = jQuery('#pieChart').data('two');
			var three = jQuery('#pieChart').data('three');
			
			const pieChart = document.getElementById("pieChart").getContext('2d');
			// pieChart.height = 100;
			new Chart(pieChart, {
				type: 'doughnut',
				data: {
					weight: 5,	
					defaultFontFamily: 'Poppins',
					datasets: [{
						data: [one, three, two],
						borderWidth: 0, 
						borderColor: "rgba(255,255,255,1)",
						backgroundColor: [
							"#8df05f",
							"#ff4b4b",
							"#e3e3e3"
						],
						hoverBackgroundColor: [
							"#8df05f",
							"#ff4b4b",
							"#e3e3e3"
						]

					}],
				},
				options: {
					weight: 1,	
					 cutout: 50,
					responsive: true,
					aspectRatio:5,
					maintainAspectRatio: false
				}
			});
		}
	}
 
	/* Function ============ */
		return {
			init:function(){
			},
			
			
			load:function(){
				
				pieChart();
				// lineChart();
			},
			
			resize:function(){
			}
		}
	
	}();

	
		
	jQuery(window).on('load',function(){
		setTimeout(function(){
			dlabChartlist.load();
		}, 1000); 
		
	});

     

})(jQuery);