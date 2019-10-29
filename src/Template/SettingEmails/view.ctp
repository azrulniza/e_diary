<div class="row">
    <div class="col-xs-10">
        <div class="settingEmails view info-box">
            <div class="box box-deafult">
                <span class="">
                    <i class="fa fa-settingEmail"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($settingEmail->name) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <div align="center">
                                <h3><?= __('Template: ').$settingEmailEnglish->name ?></h3>
                            </div>
                            <b><h3><?= __('Email Template - English') ?></h3></b>
                                <blockquote><dl class="dl-horizontal">
                                    <dt><?= __('Subject') ?></dt>
                                    <dd><?= $this->Text->autoParagraph(h($settingEmailEnglish->subject)); ?></dd>
                                    <dt><?= __('Body') ?></dt>
                                    <dd><?= $this->Text->autoParagraph(h($settingEmailEnglish->body)); ?></dd>
                            </dl></blockquote><br>
                            <b><h3><?= __('Email Template - Malay') ?></h3></b>
                                <blockquote><dl class="dl-horizontal">
                                    <dt><?= __('Subject') ?></dt>
                                    <dd><?= $this->Text->autoParagraph(h($settingEmailMalay->subject)); ?></dd>
                                    <dt><?= __('Body') ?></dt>
                                    <dd><?= $this->Text->autoParagraph(h($settingEmailMalay->body)); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
