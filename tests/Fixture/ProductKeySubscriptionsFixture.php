<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProductKeySubscriptionsFixture
 *
 */
class ProductKeySubscriptionsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'product_key_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'subscription_type_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'start' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'end' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'added_by' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'last_notification_type_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'renew_status' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'date_created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'date_modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'product_key_fk' => ['type' => 'index', 'columns' => ['product_key_id'], 'length' => []],
            'subscription_type_fk' => ['type' => 'index', 'columns' => ['subscription_type_id'], 'length' => []],
            'users_fk' => ['type' => 'index', 'columns' => ['added_by'], 'length' => []],
            'start' => ['type' => 'index', 'columns' => ['start'], 'length' => []],
            'end' => ['type' => 'index', 'columns' => ['end'], 'length' => []],
            'last_notification_type_id' => ['type' => 'index', 'columns' => ['last_notification_type_id', 'renew_status'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'product_key_id' => 1,
            'subscription_type_id' => 1,
            'start' => '2017-11-07',
            'end' => '2017-11-07',
            'added_by' => 1,
            'last_notification_type_id' => 1,
            'renew_status' => 1,
            'date_created' => '2017-11-07 10:26:09',
            'date_modified' => '2017-11-07 10:26:09'
        ],
    ];
}
