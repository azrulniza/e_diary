<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientHistoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientHistoriesTable Test Case
 */
class ClientHistoriesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientHistoriesTable
     */
    public $ClientHistories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.client_histories',
        'app.clients',
        'app.client_history_types',
        'app.client_statuses'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ClientHistories') ? [] : ['className' => 'App\Model\Table\ClientHistoriesTable'];
        $this->ClientHistories = TableRegistry::get('ClientHistories', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ClientHistories);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
