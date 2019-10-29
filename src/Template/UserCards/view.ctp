<div class="row">
    <div class="col-xs-12">
        <div class="actions">
            <ul class="side-nav btn-group">
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('Edit User Card'), ['action' => 'edit', $userCard->id]) ?> </li>
                <li class="btn btn-danger btn-sm"><?= $this->Form->postLink(__('Delete User Card'), ['action' => 'delete', $userCard->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userCard->id)]) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List User Cards'), ['action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New User Card'), ['action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
                <li class="btn btn-info btn-sm"><?= $this->Html->link(__('List Cards'), ['controller' => 'Cards', 'action' => 'index']) ?> </li>
                <li class="btn btn-primary btn-sm"><?= $this->Html->link(__('New Card'), ['controller' => 'Cards', 'action' => 'add']) ?> </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="userCards view info-box">
            <div class="box box-info">
                <span class="info-box-icon bg-aqua">
                    <i class="fa fa-userCard"></i>
                </span>
                <div class="info-box-content">
                    <div class="box-header">
                        <h3 class="box-title"><?= h($userCard->id) ?></h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt><?= __('User') ?></dt>
                            <dd><?= $userCard->has('user') ? $this->Html->link($userCard->user->name, ['controller' => 'Users', 'action' => 'view', $userCard->user->id]) : '' ?></dd>
                            <dt><?= __('Card') ?></dt>
                            <dd><?= $userCard->has('card') ? $this->Html->link($userCard->card->name, ['controller' => 'Cards', 'action' => 'view', $userCard->card->id]) : '' ?></dd>
                            <dt><?= __('Remarks') ?></dt>
                            <dd><?= h($userCard->remarks) ?></dd>
                            <dt><?= __('Id') ?></dt>
                            <dd><?= $this->Number->format($userCard->id) ?></dd>
                            <dt><?= __('Status') ?></dt>
                            <dd><?= $this->Number->format($userCard->status) ?></dd>
                            <dt><?= __('pic') ?></dt>
                            <dd><?= $this->Number->format($userCard->pic) ?></dd>
                            <dt><?= __('Mdate') ?></dt>
                            <dd><?= h($userCard->mdate) ?></dd>
                            <dt><?= __('Cdate') ?></dt>
                            <dd><?= h($userCard->cdate) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
