<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Attendance $attendance
 */
?>
<script type="text/javascript">
    $( document ).ready(function() {
        function startTime() {
          var today = new Date();
          var h = today.getHours();
          var m = today.getMinutes();
          var s = today.getSeconds();
          m = checkTime(m);
          s = checkTime(s);
          document.getElementById('txt').innerHTML =
          h + ":" + m + ":" + s;
          var t = setTimeout(startTime, 1000);
          console.log(h + ":" + m + ":" + s);
        }
        function checkTime(i) {
          if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
          return i;
        }
        startTime();
    });
</script>
<div class="row">
    <div class="col-xs-12 col-lg-10">
        <div class="attendances form">
            <?= $this->Form->create($attendance, ['role' => 'form']) ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <?php //print_r($has_attend);?>
                    <?php if($has_attend->status==1){ $action="out"?>
                        <?php $date = strtotime($has_attend->cdate);
                            $clock_in_time=date('H:i:s', $date);
                            $cur_time = date('H:i:s');
                            
                            $startTime = new DateTime($cur_time);
                            $endTime = new DateTime($clock_in_time);
                            $duration = $startTime->diff($endTime); //$duration is a DateInterval object
                            $hour=$duration->format("%H");
                            $minute=$duration->format("%I");
                            $second=$duration->format("%S");
                        ?>

                        <h3 class="box-title"><?= __('Clock Out') ?></h3>
                    <?php } else if($has_attend->status==2 OR $user->id>0){ $action="in"?>
                        <h3 class="box-title"><?= __('Clock In') ?></h3>
                    <?php } ?>
                    
                </div>
                <div class="box-body">
                    <h4 align="center"> 
                        <?php
                             echo __("Hi") ." ". $user_pic->name;
                        ?>
                    </h4>
                    <div align="center">
                        <?php if($action=="out"){?>

                            <?php echo __("Click to clock out for ") ?><b><?php echo $user->name;?></b>
                            <h5><?php echo __("Clocked in for ") . $hour. " ".__("Hour"). " " . $minute ." ".__("Minute") . " " . $second . " " .__("Second");?></h5>
                        <?php }else if($action=="in"){?>
                            <?php echo __("Get started today! Click to clock in for ") ?><b><?php echo $user->name;?></b>
                        <?php }?>
                    </div><br/> 
                    <div align="center" class="well">
                        <h4><b><?php echo $today_date; ?></b></h4>
                        <h4><b id="txt"></b></h4>
                    </div>
                   
                    <div align="center" class="form-group">
                        <?php
                        echo $this->Form->input('user_id', ['type'=>'hidden','value' => $user->id]);
                        echo $this->Form->input('action', ['type'=>'hidden','value' => $action]);
                        echo $this->Form->input('reason', ['style'=>'width:50%;','required'=>true,'id'=>'reason','options' => $SettingAttendancesReasons, 'class' => 'form-control','empty' => __('--Please Select--')]);
                        echo $this->Form->input('remark', ['style'=>'width:50%;','type'=>'textarea','class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        /*echo $this->Form->input('attendance_code_id', ['options' => $attendanceCodes, 'class' => 'form-control']);
                        echo $this->Form->input('ip_address', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('gps_lat', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('gps_lng', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('pic', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('cdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('mdate', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('status', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);
                        echo $this->Form->input('biometric', ['class' => 'form-control', 'placeholder' => __('Enter ...')]);*/
                        ?>
                    </div>
                </div>
                <div class="box-footer">
                    
                    <?php if($has_attend->status==1){?>
                         <?= $this->Form->button(__('Clock Out'), ['class'=>'btn btn-block btn-danger']) ?>
                    <?php }else if($has_attend->status==2 OR $user->id>0){?>
                        <?= $this->Form->button(__('Clock In'), ['class'=>'btn btn-block btn-success']) ?>
                    <?php } ?>
                    
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

