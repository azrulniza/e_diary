<div class="row">
    <div class="col-xs-10">
        <div class="users view info-box">
            <div class="box box-info">
                <span class="info-box-icon" style="background:transparent">
                    <?php if($user->image != null) :?>
						<img id="prev_image" class="img-circle" style="width:100px;height:100px;" src="<?php echo $this->Url->build( '/' ).$user->image; ?>"/>
					<?php else: ?>
						<i class="fa fa-user"></i>
					<?php endif; ?>
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
