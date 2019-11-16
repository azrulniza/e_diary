<?php
$this->Html->script('dashboard');

?>
<script type="text/javascript">
	$( document ).ready(function() {
		$('#listdepartment').change(function(){
			var id = $(this).val();
			$.ajax({
				type : "POST",
				//url  : '<?php echo $this->Basepath->getBasePath() ; ?>' + 'dashboards/getUsers' + '?id=' + id, //pass query string to server
				url  : '<?php echo $this->Basepath->getBasePath() ; ?>' + 'dashboards/getDetails' + '?id=' + id, //pass query string to server
				success: function(data){
						data = JSON.parse(data);
						console.log(data);
						$("#listuser").empty();
						$('#listuser').append($('<option value><?php __("-- All --") ?></option>'));
													
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
            	<?php if($userRoles->hasRole(['Master Admin']) OR $userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])){?>
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
				
				<!------------------------DEPARTMENT GRAPH START-------------------------->
				<?php 	
					//for normal 
				
					foreach ($inTimeDeptresult as $index => $value){					
						$index1 = $index + 1;
						$curentTime = date("H.i",strtotime($value['cdate']));
						$curentTime1 = ltrim($curentTime,0);
						$curentTime2 = number_format((float)$curentTime1,2,'.','');
						$arr_implodeinTimeDeptData[] = '['.$curentTime2.','.$index1.']';
					}			
					$implodeinTimeDeptData = implode(", ",$arr_implodeinTimeDeptData);
					//echo $implodeinTimeDeptData = implode(", ",ltrim($arr_implodeinTimeDeptData,0));
				
				?>
				<?php echo $this->Html->script('/js/loader'); ?>
				<div class="col-md-6 col-sm-4 col-xs-8">
					<!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
					
					<div id="chart_div" style="width: 550px; height: 300px;"></div>
					<script>
						google.charts.load('current', {packages: ['corechart', 'line']});
						google.charts.setOnLoadCallback(drawLineColors);

						function drawLineColors() {
							  var data = new google.visualization.DataTable();
							  data.addColumn('number', '<?php echo __('Time In');?>');
							  data.addColumn('number', '<?php echo __('Staff');?>');
							  data.addRows([<?php echo $implodeinTimeDeptData;?>]);

							  var options = {
								hAxis: {
								  title: '<?php echo __('Hour');?>',
								   minValue: 6.0, 
								   maxValue: 12.0
								  
								},
								vAxis: {
								  title: '<?php echo __('Total Staff');?>',
								  minValue: 0,
								  maxValue: 50
								},
								colors: ['#a52714', '#097138']
							  };

							  var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
							  chart.draw(data, options);
							}
					</script>
				</div>

				
				<div class="col-md-6 col-sm-4 col-xs-8">
						
					<!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
					<script type="text/javascript">
					  google.charts.load("current", {packages:["corechart"]});
					  google.charts.setOnLoadCallback(drawChart);
					  function drawChart() {
						var data = google.visualization.arrayToDataTable([
						  ['Task', 'Hours per Day'],
						  ['<?php echo __('Late');?>',     <?php echo $total_late?>],
						  ['<?php echo __('Absent');?>',      <?php echo $staff_absent;?>],
						  ['<?php echo __('Working');?>',  <?php echo $staff_working;?>]
						]);

						var options = {
							colors: ['#f39c12','#dd4b39','#00a65a'],
							title: '<?php echo __('Today Attendance Summary');?>',
						        pieHole: 0.4,
						};

						var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
						chart.draw(data, options);
					  }
					</script>
					<div id="donutchart" style="width: 700px; height: 300px;"></div>
				</div>
				<br clear="all"><br><br>
				<!------------------------DEPARTMENT GRAPH END-------------------------->

				
	           
            	<?php } ?>

 				<h4><?php echo __('Month Summary');?></h4><hr/>
				<?= $this->Form->create('dashboards',['action' => '/index','type' => 'GET','class' => 'form-horizontal']) ?>
				<table id="addTable" class="table-condensed" style="table-layout: auto;">
                    <tbody>
                        <tr>
                           
                            <?php if($userRoles->hasRole(['Master Admin']) OR $userRoles->hasRole(['Supervisor']) OR $userRoles->hasRole(['Admin'])) :?>
                            <td align="left"><label for="staff"><?php echo $this->Form->label(__('Staffs'));?></label></td>
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

						<div class="col-md-3 col-sm-4 col-xs-8">
							  <div style="pointer-events: none;" class="small-box bg-danger">
								<div class="inner">
								  <h3><?php echo $user_total_normal_month ?></h3>

								  <p><?= __('NORMAL ATTENDANCE')?></p>
								</div>
								<div class="icon">
								  <i class="ion ion-information-circled"></i>
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

			<!--DEPARTMENT GRAPH--->
				<div class="col-md-10 col-sm-4 col-xs-8">
					<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>-->
					<?php echo $this->Html->script('/js/Chart.min'); ?>
					<!--<script type="text/javascript" src="jscript/graph.js"></script>-->

					<canvas id="bar-chart" width="300" height="90"></canvas>
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
								label: '<?php echo __('Late');?>',
								data: dataPack1,
											backgroundColor: "#CC0000",
											hoverBackgroundColor: "#FF3232",
											hoverBorderWidth: 0
							},
							{
								label: '<?php echo __('Normal');?>',
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
								min: 0,
								max: 31,
								gridLines: { display: false },

								}],
							  yAxes: [{ 
								stacked: true, 
								ticks: {
										max : 35
										//callback: function(value) { return numberWithCommas(value); },
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
			</div>
        </div>
    </div>
</div>