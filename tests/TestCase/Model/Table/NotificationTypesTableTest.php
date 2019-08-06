<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NotificationTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NotificationTypesTable Test Case
 */
class NotificationTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\NotificationTypesTable
     */
    public $NotificationTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.notification_types'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('NotificationTypes') ? [] : ['className' => 'App\Model\Table\NotificationTypesTable'];
        $this->NotificationTypes = TableRegistry::get('NotificationTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NotificationTypes);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
