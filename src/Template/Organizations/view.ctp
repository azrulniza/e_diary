<div class="row">
    <div class="col-xs-12">
        <div class="organizations view info-box">
            <div class="box box-default">
                <span class="">
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
                            <dt><?= __('Description') ?></dt>
                            <dd><?= h($organization->description) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $organization->status == 1 ? __('Active') : __('Disable'); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
