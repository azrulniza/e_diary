<div class="row">
    <div class="col-xs-10">
        <div class="designations form">
            <?= $this->Form->create($userleaves, ['role' => 'form','enctype'=>'multipart/form-data']) ?>
            <div class="box box-deafult">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __('Change Card Status') ?></h3>
                </div>
                <div class="box-body">
                    <?php //print_r($user_card);?>
                    <?php //echo $masuk;?>
                    
                        
                             <table id="addTable" class="table-condensed" style="table-layout: auto;">
                                <tbody>

                                    <tr>
                                        <td align="left"><b><?php echo __('Name');?></b></td>
                                        <td>    
                                           <?php echo $user_card->user['name'];?>
                                        </td>   
                                    </tr>

                                    <tr>
                                        <td align="left"><b><?php echo __('Clock In');?></b></td>
                                        <td>    
                                           <?php //echo $user_card->cdate;
                                            //$t = strtotime($user_card->cdate);
                                            //echo date('d-m-Y , H:i A',$t);
                                           echo date_format($user_card->cdate,"d-m-Y , H:i a");
                                           ?>
                                        </td>   
                                    </tr>

                                    <tr>
                                        <td align="left"><b><?php echo __('Card Status');?></b></td>
                                        <td>    
                                        
                                           <b style="color:<?php echo $user_card->card['name']?>"><span class="fa fa-square"></span></b> <?= $user_card->card['name'] ?>
                                        </td>   
                                    </tr>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Change Card Color');?></label></td>
                                        <td>
                                          <?php echo $this->Form->input('card_color', ['required'=>true,'label'=>false,'options' => $cards, 'empty' => __('-- Please Select --'), 'class' => 'form-control','style'=>'width:350px;', 'id'=>'leavetype', 'value'=>$user_card->card['id']]); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left"><label for="date"><?php echo $this->Form->label('Remark');?><a style='color:red'>*</a></label></td>
                                        <td>
                                          <?php echo $this->Form->input('remark', ['required'=>true,'type' => 'textarea','label'=>false,'class' => 'form-control', 'placeholder' => __('Enter ...')]); ?>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        
                       
                    
                </div>
                <div class="box-footer">
                    <?= $this->Form->input('card', ['type'=>'hidden', 'class' => 'form-control', 'value' => $user_card['id']]); ?>
                    <?= $this->Form->input('user', ['type'=>'hidden', 'class' => 'form-control', 'value' => $user_card['user_id']]); ?>
                    <?= $this->Form->button(__('Submit'), ['class'=>'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), ['controller' => 'attendances', 'action'=>'card'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
