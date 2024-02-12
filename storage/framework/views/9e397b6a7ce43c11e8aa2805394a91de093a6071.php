
<?php $__env->startSection('title','Forecast Data'); ?>

<?php $__env->startSection('content'); ?>
		<?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<!-- CONTENT -->
		<section id="content">
			<!-- NAVBAR -->
			<nav>
				<i class='bx bx-menu'> <i class="far fa-bars"></i> </i>
				<div class="admin-icon">
					<img src="<?php echo e(asset('images/admin.png')); ?>" alt="">
					<?php echo e(auth()->user()->email); ?>

				</div>
			</nav>
			<!-- NAVBAR -->
			<!-- MAIN -->
			<main>
				<div class="page-wrap">
					<div class="influ-strip">
						<div class="influ-search">
							<form action="<?php echo e(url('forecast')); ?>" method="POST">
								<div class="field_section">
									<label>
										<input type="text" class="field_name" id="field_name" name="field_name" placeholder="Buscar Id de la Parcela" required>
										<img src="<?php echo e(asset('images/search.png')); ?>" alt="">
									</label>
									<div class="field_name_section"></div>
								</div>
								
								<select name="" class="form-control graph-dropdown-select" id="graph_filter" >
									<option value="">-- Seleccionar --</option>
									<option value="current_month">Mes actual</option>
									<option value="next_month">Próximo mes</option>
									<option value="from_to">Fecha entre</option>
									<!-- <option value="last_month">Last Month</option>
									<option value="six_month">Six Month</option>
									<option value="current_year">Current Year</option>
									<option value="last_year">Last Year</option> -->
								</select>
								<div id="from_to" style="display:none">
									<label class="pr-0" for="datepicker">
										<input type="date" name="date" placeholder="Partir de la fecha" id="from_date" required="" onfocus="this.showPicker()">
									</label>
									<label class="pr-0" for="datepicker">
										<input type="date" name="date" placeholder="Hasta la fecha" id="to_date" required="" onfocus="this.showPicker()">
									</label>
								</div>
									<input type="button" id="get_forecast" onclick="graph_filters()" value="Enviar" class="influ-search-btn">
								<span style="cursor:pointer" data-href="<?php echo e(url('export-csv-forecast')); ?>" id="export" class="influ-search-btn" onclick ="exportTasks (event.target);"><img src="<?php echo e(asset('images/export.png')); ?>"alt="">Exportar CSV</span>
							</form>
						</div>
					</div>
					<div class="page-inner">
						<div class="page-heading">
							<h1>Proyección Humedad(%)</h1>
							<ul>
								<li><span></span> Datos Medidos</li>
								<li><span></span>Datos Proyectados</li>
							</ul>
						</div>
						<div class="page-chart">
							<canvas id="myChart" style="width:100%;"></canvas>
						</div>
					</div>
				</div>
			</main>
			<!-- MAIN -->
		</section>
		<!-- CONTENT -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


<script>
	var chart = document.getElementById("myChart").getContext("2d");
	var myChart = '';
	var curdate = new Date();
	var today = curdate.getFullYear()+'-'+curdate.getMonth()+1+'-'+curdate.getDate();
	var lastDayOfMonth = new Date(curdate.getFullYear(), curdate.getMonth()+1, 0);
	var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June","July", "Aug", "Sep", "Oct", "Nov", "Dec"];
	var xValues = [];
	for(i=1;i<=curdate.getDate()+7;i++)
	{
		xValues.push([i]+' '+monthNames[curdate.getMonth()]);
		if(i >= lastDayOfMonth.getDate())
			break;
	}
	// var xValues = [50,60,70,80,90,100,110,120,130,140,150];
	var yValues = [];
	var humidity_date = [];

	// $(document).on('click','#get_forecast',function(){
	// 	var url = "<?php echo e(url('forecast')); ?>";
	// 	var field_name = $("#field_name").val();

	// 	$.ajax({
	// 		url:url,
	// 		method:'POST',
	// 		data:{
	// 			_token: "<?php echo e(csrf_token()); ?>",
	// 			field_name:field_name
	// 		},
	// 		success:function(response)
	// 		{	
	// 			yValues = [];
	// 			var abc = JSON.parse(response);

	// 			if(abc.length == 0)
	// 			{
	// 				alert('No Data Found for your Search.');
	// 			}

	// 			$.each(abc,function(index,element){
	// 				yValues.push(Math.ceil(element.humidity));
	// 				humidity_date.push((element.humidity_date).substring(0,10));
	// 			});

	// 			function color(humidity_date)
	// 			{
	// 				if(humidity_date < today)
	// 					return 'rgba(111, 214, 255,0.9)';
	// 				if(humidity_date >= today)
	// 					return 'rgba(8, 159, 41,0.9)';
	// 			}
	// 			myChart.destroy();
	// 			myChart = new Chart(chart, {
	// 			type: "bar",
	// 			data: {
	// 					labels: xValues,
	// 					datasets: [{
	// 						backgroundColor: humidity_date.map(color),
	// 						data: yValues
	// 				}]
	// 			},
	// 			options: {
	// 					legend: {display: false},
	// 					scales: {
	// 					yAxes: [{ticks: {min: 0, max:90}}],
	// 				}
	// 			}
	// 			});
	// 		},
	// 		error:function(response)
	// 		{
	// 			alert('Something went wrong!!');
	// 		}
	// 	})
	// });

	myChart = new Chart(chart, {
		type: "line",
		data: {
			labels: xValues
		},
		options: {
			scales: {
			yAxes: [{ticks: {min: 0, max:90}}],
			}
		}
	});
</script>
<script>
	function exportTasks(_this) {
		let _url = $(_this).data('href');
		window.location.href = _url;
	}
</script>

<script>
	// $(document).on('change','#graph_filter',function(){
		// var filter_value = $(this).val();
		// var field_name = $("#field_name").val();
		// var url = "graph_filter";

		// if(filter_value != '')
		// {
		// 	$.ajax({
		// 		url:url,
		// 		method:'POST',
		// 		data:{
		// 			_token: "<?php echo e(csrf_token()); ?>",
		// 			filter_value:filter_value,
		// 			field_name:field_name
		// 		},
		// 		success:function(response)
		// 		{
		// 			yValues = [];
		// 			var abc = JSON.parse(response);
					
		// 			if(abc.data.length == 0)
		// 			{
		// 				alert('No Data Found for your Search.');
		// 			}
					
		// 			xValues = abc.x_values;
		// 			var y_data = abc.data;
		// 			$.each(y_data,function(index,element){
		// 				if(abc.y_values.length == 0)
		// 				yValues.push(Math.ceil(element.humidity));
						
		// 				humidity_date.push((element.humidity_date).substring(0,10));
		// 			});
		// 			if(abc.y_values.length > 0)
		// 			{
		// 				$.each(abc.y_values,function(index,element){
		// 					yValues.push(Math.ceil(element));
		// 					// humidity_date.push((element.humidity_date).substring(0,10));
		// 				});
		// 			}
					
					
		// 			function color(humidity_date)
		// 			{
		// 				if(humidity_date < today)
		// 				return 'rgba(111, 214, 255,0.9)';
		// 				if(humidity_date >= today)
		// 				return 'rgba(8, 159, 41,0.9)';
		// 			}
		// 			myChart.destroy();
		// 			myChart = new Chart(chart, {
		// 				type: "bar",
		// 				data: {
		// 					labels: xValues,
		// 					datasets: [{
		// 						backgroundColor: humidity_date.map(color),
		// 						data: yValues
		// 					}]
		// 				},
		// 				options: {
		// 					legend: {display: false},
		// 					scales: {
		// 						yAxes: [{ticks: {min: 0, max:90}}],
		// 					}
		// 				}
		// 			});
		// 		}
		// 	})
		
		// }
	// })

	// bar graph 08_feb commmented
	// function graph_filters()
	// {
	// 	$('.field_name_section').html('');
	// 	var filter_value = $("#graph_filter").val();
	// 	var field_name = $("#field_name").val();
	// 	var url = "graph_filter";
	// 	var is_forecast = [];
	// 	if(filter_value != '')
	// 	{
	// 		$.ajax({
	// 			url:url,
	// 			method:'POST',
	// 			data:{
	// 				_token: "<?php echo e(csrf_token()); ?>",
	// 				filter_value:filter_value,
	// 				field_name:field_name
	// 			},
	// 			success:function(response)
	// 			{
	// 				yValues = [];
	// 				var abc = JSON.parse(response);
					
	// 				if(abc.data.length == 0)
	// 				{
	// 					alert('No Data Found for your Search.');
	// 				}
					
	// 				xValues = abc.x_values;
	// 				var y_data = abc.data;
	// 				$.each(y_data,function(index,element){
	// 					if(abc.y_values.length == 0)
	// 					yValues.push(Math.ceil(element.humidity));
						
	// 					humidity_date.push((element.humidity_date).substring(0,10));
	// 					is_forecast.push(element.is_forecast);
	// 				});
	// 				if(abc.y_values.length > 0)
	// 				{
	// 					$.each(abc.y_values,function(index,element){
	// 						yValues.push(Math.ceil(element));
	// 						// humidity_date.push((element.humidity_date).substring(0,10));
	// 					});
	// 				}
					
					
	// 				// function color(humidity_date)
	// 				// {
	// 				// 	if(humidity_date < today)
	// 				// 	return 'rgba(111, 214, 255,0.9)';
	// 				// 	if(humidity_date >= today)
	// 				// 	return 'rgba(8, 159, 41,0.9)';
	// 				// }
	// 				function color(is_forecast)
	// 				{
	// 					if(is_forecast == 0)
	// 					return 'rgba(111, 214, 255,0.9)';
	// 					if(is_forecast == 1)
	// 					return 'rgba(8, 159, 41,0.9)';
	// 				}
	// 				myChart.destroy();
	// 				myChart = new Chart(chart, {
	// 					type: "bar",
	// 					data: {
	// 						labels: xValues,
	// 						datasets: [{
	// 							backgroundColor: is_forecast.map(color),
	// 							data: yValues
	// 						}]
	// 					},
	// 					options: {
	// 						legend: {display: false},
	// 						scales: {
	// 							yAxes: [{ticks: {min: 0, max:90}}],
	// 						}
	// 					}
	// 				});
	// 			}
	// 		})
	// 	}
	// }

	$(document).on('change','#graph_filter',function(){
		if($(this).val() == 'from_to')
			$('#from_to').show();
		else
		{
			$("#from_date").val('');
			$("#to_date").val('');
			$('#from_to').hide();
		}
	})

	function graph_filters()
	{
		$('.field_name_section').html('');
		var filter_value = $("#graph_filter").val();
		var field_name = $("#field_name").val();
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var url = "graph_filter";
		var is_forecast = [];
		if(filter_value != '' || (from_date != '' && to_date != ''))
		{
			$.ajax({
				url:url,
				method:'POST',
				data:{
					_token: "<?php echo e(csrf_token()); ?>",
					filter_value:filter_value,
					field_name:field_name,
					from_date:from_date,
					to_date:to_date
				},
				success:function(response)
				{
					yValues = [];
					var abc = JSON.parse(response);
					
					if(abc.data.length == 0)
					{
						alert('No Data Found for your Search.');
					}
					is_forecast = [];
					xValues = abc.x_values;
					var y_data = abc.data;
					$.each(y_data,function(index,element){
						if(abc.y_values.length == 0)
						{
							yValues.push(Math.ceil(element.humidity));
							is_forecast.push(element.is_forecast);

						}
						
						humidity_date.push((element.humidity_date).substring(0,10));
						// is_forecast.push(element.is_forecast);
					});
					if(abc.y_values.length > 0)
					{
						$.each(abc.y_values,function(index,element){
							yValues.push(Math.ceil(element));
							// humidity_date.push((element.humidity_date).substring(0,10));
						});
						$.each(abc.is_forecast,function(index,element){
							is_forecast.push(element);
							// humidity_date.push((element.humidity_date).substring(0,10));
						});
					}
					// function color(humidity_date)
					// {
					// 	if(humidity_date < today)
					// 	return 'rgba(111, 214, 255,0.9)';
					// 	if(humidity_date >= today)
					// 	return 'rgba(8, 159, 41,0.9)';
					// }
					function color(is_forecast)
					{
						if(is_forecast == 0)
						return 'rgba(111, 214, 255,0.9)';
						if(is_forecast == 1)
						return 'rgba(8, 159, 41,0.9)';
					}
					myChart.destroy();
					myChart = new Chart(chart, {
						type: "bar",
						data: {
							labels: xValues,
							datasets: [{
								backgroundColor: is_forecast.map(color),
								data: yValues
							}]
						},
						options: {
							legend: {display: false},
							scales: {
								yAxes: [{ticks: {min: 0, max:90}}],
							}
						}
					});
				}
			})
		}
	}



	$(document).on('keyup','.field_name',function(){
			var search = $(this).val();
			$.ajax({
				url:"<?php echo e(url('field_names')); ?>",
				data:{search:search},
				success:function(response){
					$('.field_name_section').html('');
					$.each(JSON.parse(response),function(index,element){
						var field_name_section = "<p class='select_field_name'>"+element+"</p>";
						$('.field_name_section').append(field_name_section);
					});	
				}
			})
		
		});


		$(document).on('click','.select_field_name',function(){
			$(".field_name").val($(this).html());
			$('.field_name_section').html('');
		});

	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/forecast-data.blade.php ENDPATH**/ ?>