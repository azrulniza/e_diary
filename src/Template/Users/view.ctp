<div class="row">
    <div class="col-xs-12">
        <div class="users view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-user"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($user->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('E-mail') ?></dt>
                            <dd><?= h($user->email) ?></dd>
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($user->name) ?></dd>
                            <dt><?= __('Date Created') ?></dt>
                            <dd><?= h($user->date_created) ?></dd>
                            <dt><?= __('Date Modified') ?></dt>
                            <dd><?= h($user->date_modified) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
