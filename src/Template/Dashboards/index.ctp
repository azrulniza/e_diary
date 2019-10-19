<?php
$this->Html->script('dashboard');

?>
<script type="text/javascript">
	$( document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			$.ajax({
				type : "POST",
				//url  : getAppVars('basepath').basePath + 'dashboards/getUsers' + '?id=' + id, //pass query string to server
				url  : getAppVars('basepath').basePath + 'dashboards/getDetails' + '?id=' + id, //pass query string to server
				success: function(data){
						data = JSON.parse(data);
						console.log(data);
						$("#listuser").empty();
						$('#listuser').append($('<option value>-- All --</option>'));
													
						$.each(data.users, function(i, p) {
							console.log(p);
							$('#listuser').append($('<option></option>').val(p.id).html(p.name));
						});
			}});
		});
	});
</script>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
            
            	<h4><?php echo __('Today Summary');?></h4><hr/>

			    <?= $this->Form->create('list',['type' => 'GET','class' => 'form-horizontal']) ?>
				<table id="addTable" class="table-condensed" style="table-layout: auto;">
                    <tbody>
                        <tr>
                            <?php if($userRoles->hasRole(['Master Admin'])) :?>
                            <td align="left"><label for="department"><?php echo $this->Form->label('Department');?></label></td>
                            <td>    
                               
                                
                                <?php echo $this->Form->input('department', ['label'=>false,'options' => $departments, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listdepartment', 'value'=>$departmentSelected]); ?>
                                 
                            </td>
                            <?php endif; ?>
                            
                        </tr>
                       <tr>
                           <td></td>
                       </tr>
                    </tbody>
                </table>
                <?= $this->Form->end() ?>

               	<div class="" id="today_summary">

					<div class="row">
						<div class="col-md-3 col-sm-6 col-xs-12">
							  <div class="small-box bg-green">
								<div class="inner">
								  <h3><?php echo $staff_working ?></h3>

								  <p><?= __('WORKING')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-checkmark-circled"></i>
								</div>
							  </div>
						</div>
						<div class="col-md-3 col-sm-4 col-xs-8">
							  <div class="small-box bg-red">
								<div class="inner">
								  <h3><?php echo $staff_absent ?></h3>

								  <p><?= __('ABSENT')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-close-circled"></i>
								</div>
							  </div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							  <div class="small-box bg-yellow">
								<div class="inner">
								  <h3><?php echo $total_late ?></h3>

								  <p><?= __('LATE ATTENDANCE')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-information-circled"></i>
								</div>
							  </div>
						</div> 

						<div class="col-md-3 col-sm-6 col-xs-12">
							  <div class="small-box bg-aqua">
								<div class="inner">
								  <h3><?php echo $staff_timeoff ?></h3>

								  <p><?= __('TIME OFF')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-clock"></i>
								</div>
							  </div>
						</div>
					
					</div>
					
					<div class="row">
						<div class="col-md-3 col-sm-6 col-xs-12">
							  <div class="small-box bg-teal">
								<div class="inner">
								  <h3><?php echo $total_pending ?></h3>

								  <p><?= __('PENDING APPROVAL')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-clock"></i>
								</div>
							  </div>
						</div>
					</div>
					
				</div>
				

				<!--DEPARTMENT GRAPH--->
				<div class="col-md-6 col-sm-4 col-xs-8">
					<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
					<script type="text/javascript" src="jscript/graph.js"></script>

					<canvas id="bar-chart" width="600" height="300"></canvas>
					<?php 
						//for late 
						for($i=1;$i<=12;$i++){
							$valueLateCanv = 0;
							foreach ($Lateresult as $index => $value){
								if($value['monthcanvas'] == $i){
									$valueLateCanv = $value['totalLate'];
								}
							}
							$arr_implodeLateData[] = $valueLateCanv;
						}
						$implodeLateData = implode(", ",$arr_implodeLateData);
						
						//for normal 
						for($i=1;$i<=12;$i++){
							$valueNormalCanv = 0;
							foreach ($Normalresult as $index => $value){
								if($value['monthcanvas'] == $i){
									$valueNormalCanv = $value['totalLate'];
								}
							}
							$arr_implodeNormalData[] = $valueNormalCanv;
						}
						$implodeNormalData = implode(", ",$arr_implodeNormalData);

					?>
					<script type="text/javascript">
					 // Return with commas in between
					  var numberWithCommas = function(x) {
						return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					  };

					var dataPack1 = [<?php echo $implodeLateData;?>];
					var dataPack2 = [<?php echo $implodeNormalData;?>];
					var dates = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];

					var bar_ctx = document.getElementById('bar-chart');

					var bar_chart = new Chart(bar_ctx, {
						type: 'bar',
						data: {
							labels: dates,
							datasets: [
							{
								label: 'Late',
								data: dataPack1,
											backgroundColor: "#512DA8",
											hoverBackgroundColor: "#7E57C2",
											hoverBorderWidth: 0
							},
							{
								label: 'Normal',
								data: dataPack2,
											backgroundColor: "#FFA000",
											hoverBackgroundColor: "#FFCA28",
											hoverBorderWidth: 0
							},
							]
						},
						options: {
								animation: {
								duration: 10,
							},
							tooltips: {
										mode: 'label',
							  callbacks: {
							  label: function(tooltipItem, data) { 
								return data.datasets[tooltipItem.datasetIndex].label + ": " + numberWithCommas(tooltipItem.yLabel);
							  }
							  }
							 },
							scales: {
							  xAxes: [{ 
								stacked: true, 
								gridLines: { display: false },
								}],
							  yAxes: [{ 
								stacked: true, 
								ticks: {
										callback: function(value) { return numberWithCommas(value); },
										}, 
								}],
							},
							legend: {display: true}
						},
						plugins: [{
						beforeInit: function (chart) {
						  chart.data.labels.forEach(function (value, index, array) {
							var a = [];
							a.push(value.slice(0, 5));
							var i = 1;
							while(value.length > (i * 5)){
								a.push(value.slice(i * 5, (i + 1) * 5));
								i++;
							}
							array[index] = a;
						  })
						}
					  }]
					   }
					);
					</script>
				</div>
				<div class="col-md-6 col-sm-4 col-xs-8">
						<?php 	
							//for normal 
							$today = date('Y-m-d');
							$last_day_month = date('t',strtotime($today));
							
							for($j=1;$j<=$last_day_month;$j++){
								$newvalue = $j - 1;
								$haveData = 0; 
								$valueIntimeCanv = '{ x: new Date(2019, '.$newvalue.', 1),';
								foreach ($inTimeresult as $index => $value){						
									if(date('d',strtotime($value['cdate'])) == $j){
										$valueIntimeCanv .= ' y: '.date('H:i:s',strtotime($value['cdate'])).'}';
										$haveData = 1; 
									} 
								}
								if ($haveData == 0){
									$valueIntimeCanv .= ' y: 0 }';
								}
								$arr_implodeinTimeData[] = $valueIntimeCanv;
							}
							$implodeinTimeData = implode(", ",$arr_implodeinTimeData);

						?>
					<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
					<script type="text/javascript">
					  google.charts.load("current", {packages:["corechart"]});
					  google.charts.setOnLoadCallback(drawChart);
					  function drawChart() {
						var data = google.visualization.arrayToDataTable([
						  ['Task', 'Hours per Day'],
						  ['Total',     <?php echo $totalStaff?>],
						  ['Absent',      <?php echo $staff_absent;?>],
						  ['Working',  <?php echo $staff_working;?>],
						  ['Leave', <?php echo $staff_timeoff;?>]
						]);

						var options = {
						  title: 'Today Summary',
						  pieHole: 0.4,
						};

						var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
						chart.draw(data, options);
					  }
					</script>
					<div id="donutchart" style="width: 700px; height: 300px;"></div>
				</div>
				<br clear="all"><br><br>
				<!--DEPARTMENT GRAPH END--->

				
	            <h4><?php echo __('Month Summary');?></h4><hr/>
				<?= $this->Form->create('dashboards',['action' => '/index','type' => 'GET','class' => 'form-horizontal']) ?>
				<table id="addTable" class="table-condensed" style="table-layout: auto;">
                    <tbody>
                        <tr>
                           
                            <?php if($userRoles->hasRole(['Master Admin']) OR $userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) :?>
                            <td align="left"><label for="staff"><?php echo $this->Form->label('Staff');?></label></td>
                            <td>                                             
                                
                                <?php echo $this->Form->input('user', ['label'=>false,'options' => $users, 'empty' => __('-- All --'), 'class' => 'form-control autosubmit','style'=>'width:250px;', 'id'=>'listuser', 'value'=>$userSelected]); ?>
                                <?php echo $this->Form->input('department', ['type'=>'hidden','class' => 'form-control', 'value' => $departmentSelected]); ?>
                                 
                            </td>
                            <?php endif; ?>
                            
                        </tr>
                       <tr>
                           <td></td>
                       </tr>
                    </tbody>
                </table>
                <?= $this->Form->end() ?>

				<div class="" id="month_summary">

					<div class="row">
						<div class="col-md-3 col-sm-6 col-xs-12">
						  <div style="pointer-events: none;" class="small-box bg-success">
							<div class="inner">
							  <h3><?php echo $total_attend_month ?></h3>

							  <p><?= __('WORKING')?></p>
							</div>
							<div class="icon">
							  <i class="ion ion-checkmark-circled"></i>
							</div>
						  </div>
						</div>
						<div class="col-md-3 col-sm-4 col-xs-8">
							  <div style="pointer-events: none;" class="small-box bg-danger">
								<div class="inner">
								  <h3><?php echo $total_absent_month ?></h3>

								  <p><?= __('ABSENT')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-close-circled"></i>
								</div>
							  </div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							  <div style="pointer-events: none;" class="small-box bg-info">
								<div class="inner">
								  <h3><?php echo $total_time_off_month ?></h3>

								  <p><?= __('TIME OFF')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-clock"></i>
								</div>
							  </div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							  <div style="pointer-events: none;" class="small-box bg-warning">
								<div class="inner">
								  <h3><?php echo $user_total_late_month ?></h3>

								  <p><?= __('LATE ATTENDANCE')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-information-circled"></i>
								</div>
							  </div>
						</div>
					</div>
				</div>


				<!--<div class="col-md-3 col-sm-6 col-xs-12">
					  <div style="pointer-events: none;" class="small-box bg-success">
						<div class="inner">
						  <h3><?php echo $staff_working ?></h3>

						  <p><?= __('WORKING')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-checkmark-circled"></i>
						</div>
					  </div>
				</div>
				<div class="col-md-3 col-sm-4 col-xs-8">
					  <div style="pointer-events: none;" class="small-box bg-danger">
						<div class="inner">
						  <h3><?php echo $staff_absent ?></h3>

						  <p><?= __('ABSENT')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-close-circled"></i>
						</div>
					  </div>
				</div>

				<div class="col-md-3 col-sm-6 col-xs-12">
					  <div style="pointer-events: none;" class="small-box bg-info">
						<div class="inner">
						  <h3><?php echo $staff_timeoff ?></h3>

						  <p><?= __('TIME OFF')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-clock"></i>
						</div>
					  </div>
				</div>

				<div class="col-md-3 col-sm-6 col-xs-12">
					  <div style="pointer-events: none;" class="small-box bg-warning">
						<div class="inner">
						  <h3><?php echo $notifications ?></h3>

						  <p><?= __('NOTIFICATION')?></p>
						</div>
						<div class="icon">
						  <i class="ion ion-information-circled"></i>
						</div>
					  </div>
				</div>-->
				
            </div>
            <div class="box-footer">
			    <table class="table table-bordered table-striped" style="table-layout:fixed">							
                    <tr> 
                        <td><h5><strong><?= __('Name') ?></strong></h5></td>
                        <td><h5><?= $user->name ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Email') ?></strong></h5></td>
                        <td><h5><?= $user->email ?></h5></td> 
                    </tr>
                    <tr> 
                        <td><h5><strong><?= __('Phone No.') ?></strong></h5></td>
                        <td><h5><?= $user->phone ?></h5></td> 
                    </tr>
                </table>
				
            </div>
        </div>
    </div>
</div>