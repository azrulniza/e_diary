<div class="row">
    <div class="col-xs-12">
        <div class="settingEmails view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-settingEmail"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($settingEmail->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
							<div align="center">
	                            <h3><?= __('Template: ').$settingEmail->name ?></h3>
							</div>
							<b><h3><?= __('Email Template - English') ?></h3></b>
								<blockquote><dl class="dl-horizontal">
									<dt><?= __('Subject') ?></dt>
									<dd><?= $this->Text->autoParagraph(h($settingEmail->en_subject)); ?></dd>
		                            <dt><?= __('Body') ?></dt>
									<dd><?= $this->Text->autoParagraph(h($settingEmail->en_body)); ?></dd>
							</dl></blockquote><br>
							<b><h3><?= __('Email Template - Malay') ?></h3></b>
								<blockquote><dl class="dl-horizontal">
									<dt><?= __('Subject') ?></dt>
									<dd><?= $this->Text->autoParagraph(h($settingEmail->my_subject)); ?></dd>
									<dt><?= __('Body') ?></dt>
									<dd><?= $this->Text->autoParagraph(h($settingEmail->my_body)); ?></dd>
							</dl></blockquote><br>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
