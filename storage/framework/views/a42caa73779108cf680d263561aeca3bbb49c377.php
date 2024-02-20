
<?php $__env->startSection('title','Check Data'); ?>
<?php $__env->startSection('css'); ?>
<style>
	#map {
       height: 360px;
       width: 100%;
       overflow: hidden;
       float: left;
       border: thin solid #333;
       }
	   a[href^="http://maps.google.com/maps"]{display:none !important}
		a[href^="https://maps.google.com/maps"]{display:none !important}

		.gmnoprint a, .gmnoprint span, .gm-style-cc {
			display:none;
		}
		.gmnoprint div {
			background:none !important;
		}
		.gm-svpc, .gmnoprint, .gm-fullscreen-control{
			display:none !important;
		}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<!-- CONTENT -->
		<section id="content">
			<!-- NAVBAR -->
			<nav>
				<i class='bx bx-menu'> <i class="far fa-bars"></i> </i>
				<div class="admin-icon">
					<img src="<?php echo e(asset('images/admin.png')); ?>"alt="">
					<?php echo e(auth()->user()->email); ?>

				</div>
			</nav>
			<!-- NAVBAR -->
			<!-- MAIN -->
			<main>
				<div class="page-wrap">
					<div class="influ-strip">
						<div class="influ-search">
							<form action="<?php echo e(url('forecast_data')); ?>"method="POST">
								<?php echo csrf_field(); ?>
								<div class="field_section">
									<label>
										<input type="text" class="field_name" name="field_name" placeholder="Buscar Id de la Parcela" value="<?php echo e($requested_field_name ?? old('field_name')); ?>" required="">
										<img src="<?php echo e(asset('images/search.png')); ?>"alt="">
									</label>
									<div class="field_name_section"></div>
								</div>
								<label class="pr-0" for="datepicker">
									<input type="date" name="date" placeholder="<?php echo e($requested_date == '' ? 'dd-mm-yyyy' : date('d-m-Y',strtotime($requested_date))); ?>" id="datepicker" required="" onfocus="this.showPicker()">
								</label>
								<input type="submit"value="Enviar"class="influ-search-btn">
								<!-- <a href=""class="influ-search-btn"><img src="<?php echo e(asset('images/export.png')); ?>"alt="">Export CSV</a> -->
								<span style="cursor:pointer" data-href="<?php echo e(url('export-csv')); ?>" id="export" class="influ-search-btn" onclick ="exportTasks (event.target);"><img src="<?php echo e(asset('images/export.png')); ?>"alt="">Exportar CSV</span>
							</form>
						</div>
					</div>
					<div class="page-inner">
						<div class="page-heading">
							<h1>Revisar Mediciones</h1>
							<p>Humedad (30%) <span></span>  Humedad (90%)</p>
						</div>
						<div class="page-map">
							<!-- <p>Select field to see the data.</p> -->
							<p><?php echo e(date('M d Y')); ?></p>
							<div class="icon">
								<div class="tooltip-box">
									<a class="show-modal" data-toggle="modal" data-target="#myModal"><img src="<?php echo e(asset('images/tooltip-edit.png')); ?>" alt=""></a>
									<h1>Humidity Level</h1>
									<span>99.05%</span>
									<span>52.86%</span>
								</div>
							</div>
							    <div id="map"></div>
							<!-- <img src="<?php echo e(asset('images/data-map.jpg')); ?>" alt=""> -->
						</div>
						<div class="page-table table-responsive">
							<div class="table-responsive">
								<table>
									<tr>
										<th>Fecha</th>
										<th>Nombre Parcela</th>
										<th>Código Parcela</th>
										<th>Alias</th>
										<th>Razón Social</th>
										<th>Latitud</th>
										<th>Longitud</th>
										<th>Humedad</th>
										<th>Acción</th>
									</tr>
									<?php 
										$locations = [];
										$a = [];
									?>
									<?php if(count($field_data) == 0): ?>
										<tr><td colspan="9">No Data Found</td></tr>
									<?php endif; ?>
									<?php $__currentLoopData = $field_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php 
											$field_humidity = substr($field->humidity,0,2);
											$color_name = "";
											
											if($field_humidity <= 30)
												$color_name = "Vector";
											elseif($field_humidity > 30 && $field_humidity <= 35)
												$color_name = "Vector-5";
											elseif($field_humidity > 35 && $field_humidity <= 40)
												$color_name = "Vector-2";
											elseif($field_humidity > 40 && $field_humidity <= 45)
												$color_name = "Vector-6";
											elseif($field_humidity > 45 && $field_humidity <= 50)
												$color_name = "Vector-1";
											elseif($field_humidity > 50 && $field_humidity <= 53)
												$color_name = "Vector-13";
											elseif($field_humidity > 53 && $field_humidity <= 55)
												$color_name = "Vector-12";
											elseif($field_humidity > 55 && $field_humidity <= 60)
												$color_name = "Vector-4";
											elseif($field_humidity > 60 && $field_humidity <= 70)
												$color_name = "Vector-7";
											elseif($field_humidity > 70 && $field_humidity <= 75)
												$color_name = "Vector-11";
											elseif($field_humidity > 75 && $field_humidity <= 80)
												$color_name = "Vector-8";
											elseif($field_humidity > 80 && $field_humidity <= 85)
												$color_name = "Vector-9";
											elseif($field_humidity > 85 && $field_humidity <= 90)
												$color_name = "Vector-10";
											else 
												$color_name = "Vector-3";
											
											$color = "images/map_icons/".$color_name.".png";
											$locations[] = [$field->humidity,$field->latitude,$field->longitude,$color,$field->map_file];
											if(in_array($field->latitude,array_column($locations,1)) && in_array($field->longitude,array_column($locations,2)) )
											{
												$a[$field->latitude][] = $field->humidity;
											}
										?>
									<tr id="humidity_<?php echo e($field->id); ?>">
										<td><?php echo e(date("M d,Y",strtotime($field->humidity_date))); ?></td>
										<td><?php echo e($field->field_name); ?></td>
										<td><?php echo e($field->field_code); ?></td>
										<td><?php echo e($field->field_alias); ?></td>
										<td><?php echo e($field->razon_social); ?> </td>
										<td><?php echo e($field->latitude); ?> °N</td>
										<td><?php echo e($field->longitude); ?>°W</td>
										<td class="humidity_update"><?php echo e($field->humidity); ?>%</td>
										<td>
											<a href="javascript:void(0)" class="show-modal" data-id="<?php echo e($field->id); ?>"><img src="<?php echo e(asset('images/edit.png')); ?>" alt=""></a>
											<?php if(auth()->user()->id == $field['user_id']): ?>
												<a href="javascript:void(0)" class="delete_humidity" data-id="<?php echo e($field->id); ?>"><img src="<?php echo e(asset('images/delete.png')); ?>" alt=""></a>
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php
									$c_i = 0;
										foreach($locations as $locat)
										{
											if(in_array(array_key_exists($locat[1],$a),$locat))
											{
												$locations[$c_i][0] = $a[$locat[1]];
											}
											$c_i++;
										}
										
										if($default_map != '')
										{
											$locations[0][0] = 'abc';
											$locations[0][0] = 'abc';
											$locations[0][0] = 'abc';
											$locations[0][4] = $default_map->map_file;
										}
										
										$encoded = json_encode($locations);
										?>
								</table>
							</div>
						</div>
					</div>
				</div>
			</main>
			<!-- MAIN -->
		</section>
		<!-- CONTENT -->
		<!-- EDIT-POPUP -->
		<div class="modal fade" id="myModal"tabindex="-1"role="dialog"aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-dialog-edit"role="document">
				<div class="modal-content clearfix">
					<div class="modal-heading">
						<button type="button"class="close close-btn"data-dismiss="modal"aria-label="Close">
						<span aria-hidden="true">×</span>
						</button>
						<h1>Edit Humidity</h1>
					</div>
					<div class="modal-body">
						<!-- <div class="edit-in">
							<form>
								<label>
									Humidity
									<input class="edit-in_txt" type="text" value="60.00%">
								</label>
								<input class="edit-in-btn" type="submit"value="Save">
							</form>
						</div> -->
					</div>
				</div>
			</div>
		</div>
		<!-- EDIT-POPUP -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
	
	<script>
		$(document).on('submit','#update_humidity',function(e){
			e.preventDefault();

			var humidity = $(this).find('.humidity').val();
			var field_id = $(this).find('.field_id').val();
			var url = "<?php echo e(url('update_humidity')); ?>";

			$.ajax({
				url:url,
				method:"POST",
				data:{
					_token:"<?php echo e(csrf_token()); ?>",
					field_id:field_id,
					humidity:humidity,
				},
				success:function(response)
				{
					$("#myModal .modal-body").html('');
					$("#myModal").modal('hide');
					$("#humidity_"+field_id+" .humidity_update").html(humidity+'%');
					alert(response.success);
				},
				error:function(response)
				{
					alert("Something Went wrong");
				}
			})
		});


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
		})


		$(document).on('click','.delete_humidity',function(){
			var field_id = $(this).data('id');
			var url = "<?php echo e(url('delete_data')); ?>";

			$.ajax({
				url:url,
				method:"POST",
				data:{
					_token:"<?php echo e(csrf_token()); ?>",
					field_id:field_id
				},
				success:function(response)
				{
					$("#humidity_"+field_id).hide();
				},
				error:function(response)
				{
					alert("Something Went wrong");
				}
			})
		});
	</script>

	<script>
		$(document).on('click','.show-modal', function()
		{
			var field_id = $(this).data('id');
			var url = "<?php echo e(url('edit_humidity')); ?>";
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$.ajax({
				url:url,
				method:'GET',
				data:{
					field_id:field_id
				},
				success:function(response)
				{
					$("#myModal .modal-body").html(response);
					$("#myModal").modal('show');
				},
				error:function(response)
				{
					alert('Something went wrong!!');
				}
			})
		});
	</script>
	<script>
		function exportTasks(_this) {
			let _url = $(_this).data('href');
			window.location.href = _url;
		}
	</script>

<script>
      var map;
    //   var src = 'https://mec.yilstaging.com/map.kml';
	
	//   var locations = [
		// 				['Bondi Beach', -33.890542, 151.274856, 4],
		// 				['Coogee Beach', -33.923036, 151.259052, 5],
		// 				['Cronulla Beach', -34.028249, 151.157507, 3],
		// 				['Manly Beach', -33.80010128657071, 151.28747820854187, 2],
		// 				['Maroubra Beach', -33.950198, 151.259302, 1]
		// 			];
		var locations = <?php echo $encoded; ?>;
		// var src = "https://whw.yesitlabs.xyz/map_files/"+locations[0][4];
		//var src = "https://www.whwdata.cl/map_files/"+locations[0][4];
		var src = "/map_files/"+locations[0][4];
		console.log(src)
		var fileUrl = src;
		var xhr = new XMLHttpRequest();
		xhr.open('HEAD', fileUrl, false); // false makes the request synchronous
		xhr.send();
		if (xhr.status === 200) {
			console.log('File exists.');
		} else {
			console.log('File does not exist.');
		}

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: new google.maps.LatLng(-34.75345737899994,-71.03558142499998),
          zoom: 12,
          mapTypeId: 'satellite'
        });
		var infowindow = new google.maps.InfoWindow();
		var marker, i;
		for (i = 0; i < locations.length; i++) {  
				marker = new google.maps.Marker({
					position: new google.maps.LatLng(locations[i][1], locations[i][2]),
					map: map,
					icon:locations[i][3]
			});

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					// var html = "<u>Humidity level</u><br>"+locations[i][0]+" %";
					var html = "<u>Nivel de humedad</u><br>";
					$.each(locations[i][0],function(index,element){
						html = html+element+" %<br>";
					})
					infowindow.setContent(html);
					infowindow.open(map, marker);
				}
			})(marker, i));
		}

        var kmlLayer = new google.maps.KmlLayer(src, {
          suppressInfoWindows: true,
          preserveViewport: false,
          map: map
        });
      }
    </script>
    <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC9NuN_f-wESHh3kihTvpbvdrmKlTQurxw&callback=initMap">
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/javiercarrasco/Desktop/corteva/new whw/resources/views/check-data.blade.php ENDPATH**/ ?>