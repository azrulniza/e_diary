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
                            <dt><?= __('Email') ?></dt>
                            <dd><?= h($user->email) ?></dd>
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($user->name) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($user->id) ?></dd>
                            <dt><?= __('Ic Number') ?></dt>
                            <dd><?= h($user->ic_number) ?></dd>
                            <dt><?= __('Phone') ?></dt>
                            <dd><?= h($user->phone) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
