<?php
//debug($assigned);
//debug($assignable);
//debug($roles);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title">Roles and Permissions</h3>
        <p>Access Control List defined at /app/config/acl.ini</p>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap"><div class="row"><div class="col-sm-6"></div><div class="col-sm-6"></div></div><div class="row"><div class="col-sm-12">
                    <?=$this->Form->create() ?>
                    <table class="table table-bordered table-hover dataTable" id="example2" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Rendering engine: activate to sort column descending" aria-sort="ascending">Menu</th>
                            <?php foreach($roles as $role):?>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending"><?=$role->name?></th>
                            <?php endforeach;?>        
                        </thead>
                        <tbody>
                        <?php foreach($assignable as $menu):?>
                            <tr role="row" class="odd">
                                <td class="sorting_1"><?=$menu->label?></td>
                                <?php foreach($roles as $role):?>
                                <td><?=$this->Form->checkbox('permission.'.$menu->id.'.'.$role->id,['value'=> true,
                                    'checked'=> $authorizer->allowed([$role->id] , $menu->controller, $menu->action)]);  ?></td>
                                <?php endforeach;?>
                             
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                        
                    </table>
                 <?=$this->Form->end() ?>
                </div></div>
            </div>
    </div>
    <!-- /.box-body -->
</div>
