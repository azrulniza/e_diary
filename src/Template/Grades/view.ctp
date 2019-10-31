<div class="row">
    <div class="col-xs-10">
        <div class="grades view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-grade"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($grade->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('Name') ?></dt>
                            <dd><?= h($grade->name) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $this->Number->format($grade->status) ?></dd>
                            <dt><?= __('Cdate') ?></dt>
                            <dd><?= h($grade->cdate) ?></dd>
                            <dt><?= __('Mdate') ?></dt>
                            <dd><?= h($grade->mdate) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
