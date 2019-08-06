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
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($department->name) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($department->id) ?></dd>
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
