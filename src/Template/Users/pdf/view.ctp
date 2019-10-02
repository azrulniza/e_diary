<div class="users view">
<h2>User</h2>
    <dl>
        <dt><?= __('Id') ?></dt>
        <dd>
            <?= $this->Number->format($user->id) ?>
            &nbsp;
        </dd>
        <dt><?= __('name') ?></dt>
        <dd>
            <?= h($user->name) ?>
            &nbsp;
        </dd>
        <dt><?= __('Username') ?></dt>
        <dd>
            <?= h($user->email) ?>
            &nbsp;
        </dd>
        <dt><?= __('Role') ?></dt>
        <dd>
            <?= h($user->ic_number) ?>
            &nbsp;
        </dd>
        <dt><?= __('Created') ?></dt>
        <dd>
            <?= h($user->cdate) ?>
            &nbsp;
        </dd>
        <dt><?= __('Modified') ?></dt>
        <dd>
            <?= h($user->mdate) ?>
            &nbsp;
        </dd>
    </dl>
</div>