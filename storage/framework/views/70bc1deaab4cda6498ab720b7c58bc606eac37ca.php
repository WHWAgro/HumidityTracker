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
                            <div id="search_field" class="field_section">
                                <label>
                                    <input type="text" class="field_name" id="field_name" name="field_name"
                                           placeholder="Buscar Id de la Parcela" required>
                                    <img src="<?php echo e(asset('images/search.png')); ?>" alt="">
                                </label>
                                <div class="field_name_section"></div>
                            </div>

                            <select name="" class="form-control graph-dropdown-select" id="graph_filter">
                                <option value="">-- Seleccionar --</option>
                                <option value="current_month">Mes actual</option>
                                <option value="next_month">Próximo mes</option>
                                <option value="from_to">Fecha entre</option>
                            </select>
                            <div id="from_to" style="display:none">
                                <label class="pr-0" for="datepicker">
                                    <input type="date" name="date" placeholder="Partir de la fecha" id="from_date"
                                           required="" onfocus="this.showPicker()">
                                </label>
                                <label class="pr-0" for="datepicker">
                                    <input type="date" name="date" placeholder="Hasta la fecha" id="to_date" required=""
                                           onfocus="this.showPicker()">
                                </label>
                            </div>
                            <input type="button" id="get_forecast" onclick="graph_filters()" value="Enviar"
                                   class="influ-search-btn">
                            <div style="gap:12px" class="d-flex">
								<span style="cursor:pointer;display:none;" data-href="<?php echo e(url('export-csv-forecast')); ?>" id="export_forecast"
                                      class="influ-search-btn" onclick="exportTasks(event.target);"><img
                                            src="<?php echo e(asset('images/export.png')); ?>" alt="">Exportar CSV</span>

								<span style="cursor:pointer" data-href="<?php echo e(url('export-csv-resume')); ?>" id="export_resume"
								  class="influ-search-btn" onclick="exportTasks(event.target);"><img
										src="<?php echo e(asset('images/export.png')); ?>" alt="">Exportar CSV</span>

                                <span style="cursor:pointer;" id="mode" class="influ-search-btn" onclick="toggleMode()">Modo Resumen</span>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="graph" class="page-inner">
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
                <table id="data_table" class="table" style="display:none;">
                    <thead>
                    <tr>
                        <th scope="col">Nombre Parcela</th>
                        <th scope="col">Código Parcela</th>
                        <th scope="col">Comuna</th>
                        <th scope="col">Humedad Objetivo</th>
                        <th scope="col">Humedad 15 días</th>
                        <th scope="col">Fecha Humedad Objetivo</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script type="importmap">
        {
          "imports": {
            "radash": "https://unpkg.com/radash@11.0.0/dist/esm/index.mjs"
          }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <script>
			let currentMode = "Modo Resumen";

			function toggleMode() {
				const modeButton = document.getElementById("mode");
				const graph = document.getElementById("graph");
				const table = document.getElementById("data_table");
				const sendButton = document.getElementById("get_forecast");
				const searchField = document.getElementById("search_field");
				const filter = document.getElementById("graph_filter");
				const exportResumeButton = document.getElementById("export_resume");
				const exportForecastButton = document.getElementById("export_forecast");

				if (currentMode === "Modo Resumen") {
					graph.style.display = "none";
					sendButton.style.visibility = "hidden";
					searchField.style.visibility = "hidden";
					filter.style.visibility = "hidden";
					table.style.display = "block";
					exportResumeButton.style.display = "block";
					exportForecastButton.style.display = "none";

				} else {
					graph.style.display = "block";
					table.style.display = "none";
					sendButton.style.visibility = "visible";
					searchField.style.visibility = "visible";
					filter.style.visibility = "visible";
					exportForecastButton.style.display = "block";
					exportResumeButton.style.display = "none";
				}

				if (modeButton.innerHTML === "Modo Resumen") {
					modeButton.innerHTML = "Modo Detalle";
					currentMode = "Modo Detalle";
				} else {
					modeButton.innerHTML = "Modo Resumen";
					currentMode = "Modo Resumen";
				}
			}
    </script>

    <script>
			var chart = document.getElementById("myChart").getContext("2d");
			var myChart = '';
			var curdate = new Date();
			var today = curdate.getFullYear() + '-' + curdate.getMonth() + 1 + '-' + curdate.getDate();
			var lastDayOfMonth = new Date(curdate.getFullYear(), curdate.getMonth() + 1, 0);
			var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
			var xValues = [];
			for (i = 1; i <= curdate.getDate() + 7; i++) {
				xValues.push([i] + ' ' + monthNames[curdate.getMonth()]);
				if (i >= lastDayOfMonth.getDate())
					break;
			}
			var yValues = [];
			var humidity_date = [];

			myChart = new Chart(chart, {
				type: "line",
				data: {
					labels: xValues
				},
				options: {
					scales:
						{
							yAxes: [{ticks: {min: 0, max: 90}}],
						}
				}
			})
			;
    </script>

    <script>
			function exportTasks(_this) {
				let _url = $(_this).data('href');
				window.location.href = _url;
			}
    </script>
    <script>
			$.ajax({
				url: "resume",
				method: 'GET',
				success: function (response) {
					const data = JSON.parse(response).data;
					const tableBody = document.getElementById('data_table').getElementsByTagName('tbody')[0];
					data?.forEach(item => {
						const row = document.createElement('tr');

						const fieldNameCell = document.createElement('td');
						fieldNameCell.textContent = item.field_name ?? "N/A";

						const fieldCodeCell = document.createElement('td');
						fieldCodeCell.textContent = item.field_codei ?? "N/A";

						const comunaCell = document.createElement('td');
						comunaCell.textContent = item.comuna ?? "N/A"


						const humidityObjectiveCell = document.createElement('td');
						humidityObjectiveCell.textContent = item.target_humidity ?? "N/A";

						const humidity15DaysCell = document.createElement('td');
						humidity15DaysCell.textContent = item.humidity_15_days ?? 'N/A';

						const humidityDateCell = document.createElement('td');
						const date = new Date(item.date_target_humidity) ?? "N/A";
						const year = date.getFullYear();
						const month = (date.getMonth() + 1).toString().padStart(2, '0');
						const day = date.getDate().toString().padStart(2, '0');
						humidityDateCell.textContent = `${year}-${month}-${day}`;

						row.appendChild(fieldNameCell);
						row.appendChild(fieldCodeCell);
						row.appendChild(comunaCell);
						row.appendChild(humidityObjectiveCell);
						row.appendChild(humidity15DaysCell);
						row.appendChild(humidityDateCell);

						tableBody.appendChild(row);
					});
				}
			});

    </script>

    <script>
			$(document).on('change', '#graph_filter', function () {
				if ($(this).val() == 'from_to')
					$('#from_to').show();
				else {
					$("#from_date").val('');
					$("#to_date").val('');
					$('#from_to').hide();
				}
			})

			function graph_filters() {
				$('.field_name_section').html('');
				var filter_value = $("#graph_filter").val();
				var field_name = $("#field_name").val();
				var from_date = $("#from_date").val();
				var to_date = $("#to_date").val();
				var url = "graph_filter";
				var is_forecast = [];
				if (filter_value != '' || (from_date != '' && to_date != '')) {
					$.ajax({
						url: url,
						method: 'POST',
						data: {
							_token: "<?php echo e(csrf_token()); ?>",
							filter_value: filter_value,
							field_name: field_name,
							from_date: from_date,
							to_date: to_date
						},
						success: function (response) {
							yValues = [];
							var abc = JSON.parse(response);

							if (abc.data.length == 0) {
								alert('No Data Found for your Search.');
							}

							is_forecast = [];
							xValues = abc.x_values;
							var y_data = abc.data;
							$.each(y_data, function (index, element) {
								if (abc.y_values.length == 0) {
									yValues.push(Math.ceil(element.humidity));
									is_forecast.push(element.is_forecast);

								}

								humidity_date.push((element.humidity_date).substring(0, 10));
							});
							if (abc.y_values.length > 0) {
								$.each(abc.y_values, function (index, element) {
									yValues.push(Math.ceil(element));
								});
								$.each(abc.is_forecast, function (index, element) {
									is_forecast.push(element);
								});
							}

							function color(is_forecast) {
								if (is_forecast == 0)
									return 'rgba(111, 214, 255,0.9)';
								if (is_forecast == 1)
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
										yAxes: [{ticks: {min: 0, max: 90}}],
									}
								}
							});
						}
					})
				}
			}

			$(document).on('keyup', '.field_name', function () {
				var search = $(this).val();
				$.ajax({
					url: "<?php echo e(url('field_names')); ?>",
					data: {search: search},
					success: function (response) {
						$('.field_name_section').html('');
						$.each(JSON.parse(response), function (index, element) {
							var field_name_section = "<p class='select_field_name'>" + element + "</p>";
							$('.field_name_section').append(field_name_section);
						});
					}
				})

			});

			$(document).on('click', '.select_field_name', function () {
				$(".field_name").val($(this).html());
				$('.field_name_section').html('');
			});
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/cristobal/Repos/WHW/HumidityTracker/resources/views/forecast-data.blade.php ENDPATH**/ ?>