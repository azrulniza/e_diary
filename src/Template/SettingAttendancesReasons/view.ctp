<div class="row">
    <div class="col-xs-10">
        <div class="settingAttendancesReasons view info-box">
            <div class="box box-default">
                <span class="info-box-icon bg-default">
                    <i class="fa fa-settingAttendancesReason"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($settingAttendancesReason->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($settingAttendancesReason->name) ?></dd>
                            <dt><?= __('Description') ?></dt>
                            <dd><?= h($settingAttendancesReason->description) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $settingAttendancesReason->status == 1 ? __('ACTIVE') : __('DISABLE'); ?></dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
