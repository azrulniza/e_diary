<div class="row">
    <div class="col-xs-12">
        <div class="organizations view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-organization"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($organization->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($organization->name) ?></dd>
                            <dt><?= __('Address') ?></dt>
                            <dd><?= h($organization->address) ?></dd>
                            <dt><?= __('Email') ?></dt>
                            <dd><?= h($organization->email) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($organization->id) ?></dd>
                            <dt><?= __('Phone') ?></dt>
                            <dd><?= $this->Number->format($organization->phone) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $organization->status == 1 ? __('ACTIVE') : __('INACTIVE'); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
