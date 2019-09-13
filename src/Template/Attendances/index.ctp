<div class="row">
    <div class="col-xs-10">
        <div class="box box-default">
            <div class="box-header"></div>
            <div class="box-body">
                <div class="attendances index dataTable_wrapper table-responsive">
                    <table id="dataTables-attendances" class="dataTable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><?= __('No') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Department') ?></th>
                                <th><?= __('Status') ?></th>
                                <th><?= __('In') ?></th>
                                <th><?= __('Out') ?></th>
                                <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                    <th class="actions"><?= __('Actions') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="ui-sortable">
                        <?php //print_r($attendances);?>
                        <?php $count = 0 ?>
        
                        <?php foreach ($attendances as $attendance): ?>
                            
                            <tr id="<?= $attendance['id']; ?>" class="<?= (++$count%2 ? 'odd' : 'even') ?>">
                                <td><?= $this->Number->format($count) ?></td>
                                <td>
                                    <?= $attendance['username']?>
                                </td>   
                                <td><?= $attendance['organization_name'] ?></td>
                                <td>
                                    <?php 
                                    if($attendance['status']==2){ 
                                        $color='red';
                                    }else if($attendance['status']==1){
                                        $color='green';
                                    }
                                    ?>
                                    <p style="color:<?php echo $color;?>"><?= $attendance['attendance_codes_name'] ?></p>
                                </td>
                                <td><?php if($attendance['in']!=""){
                                     $date_in=date_create($attendance['in']);
                                echo date_format($date_in,"H:i a");
                                     
                                }
                               
                                ?></td>
                                <td><?php if($attendance['out']!=""){
                                    $date_out=date_create($attendance['out']);
                                        echo date_format($date_out,"H:i a");
                                }
                                
                                ?></td>
                               
                               <?php if($userRoles->hasRole(['Master Admin','Admin','Supervisor'])) :?>
                                    <td class="actions">
                                        <div class="btn-group">
                                            <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-eye']), ['action' => 'view', $attendance->id], ['escape' => false, 'title' => __('View'), 'class' => 'btn btn-info btn-xs']) ?>
                                            <?= $this->Html->link($this->Html->tag('i', '', ['class' => 'fa fa-pencil']), ['action' => 'edit', $attendance->id], ['escape' => false, 'title' => __('Edit'), 'class' => 'btn btn-success btn-xs']) ?>
                                            <?= $this->Form->postLink($this->Html->tag('i', '', ['class' => 'fa fa-trash']), ['action' => 'delete', $attendance->id], ['escape' => false, 'title' => __('Delete'), 'class' => 'btn btn-danger btn-xs', 'confirm' => __('Are you sure you want to delete this {0}?', 'attendance')]) ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
