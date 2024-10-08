<div class="row">
    <div class="col-xs-10">
        <div class="designations view info-box">
            <div class="box box-default">
                <span class="">
                    <i class="fa fa-designation"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($designation->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($designation->name) ?></dd>
                            <dt><?= __('Department') ?></dt>
                            <dd><?= $designation->has('organization') ? $this->Html->link($designation->organization->name, ['controller' => 'Organizations', 'action' => 'view', $designation->organization->id]) : '' ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
