<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('Edit Department'), ['action' => 'edit', $department->id]) ?> </li>
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(__('Delete Department'), ['action' => 'delete', $department->id], ['confirm' => __('Are you sure you want to delete # {0}?', $department->id)]) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Departments'), ['action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Department'), ['action' => 'add']) ?> </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="departments view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-department"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($department->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($department->id) ?></dd>
                            <dt><?= __('Name') ?></dt>
                            <dd><?= $this->Number->format($department->name) ?></dd>
                            <dt><?= __('Date Created') ?></dt>
                            <dd><?= h($department->date_created) ?></dd>
                            <dt><?= __('Date Modified') ?></dt>
                            <dd><?= h($department->date_modified) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
