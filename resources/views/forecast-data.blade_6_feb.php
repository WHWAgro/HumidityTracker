@extends('layouts.app')
@section('title','Forecast Data')

@section('content')
		@include('layouts.sidebar')
		<!-- CONTENT -->
		<section id="content">
			<!-- NAVBAR -->
			<nav>
				<i class='bx bx-menu'> <i class="far fa-bars"></i> </i>
				<div class="admin-icon">
					<img src="{{ asset('images/admin.png') }}" alt="">
					{{ auth()->user()->email }}
				</div>
			</nav>
			<!-- NAVBAR -->
			<!-- MAIN -->
			<main>
				<div class="page-wrap">
					<div class="influ-strip">
						<div class="influ-search">
							<form action="{{ url('forecast') }}" method="POST">
								<div class="field_section">
									<label>
										<input type="text" class="field_name" id="field_name" name="field_name" placeholder="Select Field Name/ID" required>
										<img src="{{ asset('images/search.png') }}" alt="">
									</label>
									<div class="field_name_section"></div>
								</div>
								
								<select name="" class="form-control graph-dropdown-select" id="graph_filter" >
									<option value="">-- Select --</option>
									<option value="current_month">Current Month</option>
									<option value="last_month">Last Month</option>
									<option value="six_month">Six Month</option>
									<option value="current_year">Current Year</option>
									<option value="last_year">Last Year</option>
								</select>
								<input type="button" id="get_forecast" onclick="graph_filters()" value="Submit" class="influ-search-btn">
								<span style="cursor:pointer" data-href="{{ url('export-csv') }}" id="export" class="influ-search-btn" onclick ="exportTasks (event.target);"><img src="{{ asset('images/export.png') }}"alt="">Export CSV</span>
							</form>
						</div>
					</div>
					<div class="page-inner">
						<div class="page-heading">
							<h1>Humidity Level(%)</h1>
							<ul>
								<li><span></span> Past Data</li>
								<li><span></span> Forecast Data</li>
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
@endsection
@section('script')

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
	// 	var url = "{{ url('forecast') }}";
	// 	var field_name = $("#field_name").val();

	// 	$.ajax({
	// 		url:url,
	// 		method:'POST',
	// 		data:{
	// 			_token: "{{csrf_token()}}",
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
		// 			_token: "{{csrf_token()}}",
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


	function graph_filters()
	{
		$('.field_name_section').html('');
		var filter_value = $("#graph_filter").val();
		var field_name = $("#field_name").val();
		var url = "graph_filter";

		if(filter_value != '')
		{
			$.ajax({
				url:url,
				method:'POST',
				data:{
					_token: "{{csrf_token()}}",
					filter_value:filter_value,
					field_name:field_name
				},
				success:function(response)
				{
					yValues = [];
					var abc = JSON.parse(response);
					
					if(abc.data.length == 0)
					{
						alert('No Data Found for your Search.');
					}
					
					xValues = abc.x_values;
					var y_data = abc.data;
					$.each(y_data,function(index,element){
						if(abc.y_values.length == 0)
						yValues.push(Math.ceil(element.humidity));
						
						humidity_date.push((element.humidity_date).substring(0,10));
					});
					if(abc.y_values.length > 0)
					{
						$.each(abc.y_values,function(index,element){
							yValues.push(Math.ceil(element));
							// humidity_date.push((element.humidity_date).substring(0,10));
						});
					}
					
					
					function color(humidity_date)
					{
						if(humidity_date < today)
						return 'rgba(111, 214, 255,0.9)';
						if(humidity_date >= today)
						return 'rgba(8, 159, 41,0.9)';
					}
					myChart.destroy();
					myChart = new Chart(chart, {
						type: "bar",
						data: {
							labels: xValues,
							datasets: [{
								backgroundColor: humidity_date.map(color),
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
				url:"{{ url('field_names') }}",
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
@endsection