<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientHistoryTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientHistoryTypesTable Test Case
 */
class ClientHistoryTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientHistoryTypesTable
     */
    public $ClientHistoryTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.client_history_types',
        'app.client_histories',
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
        $config = TableRegistry::exists('ClientHistoryTypes') ? [] : ['className' => 'App\Model\Table\ClientHistoryTypesTable'];
        $this->ClientHistoryTypes = TableRegistry::get('ClientHistoryTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ClientHistoryTypes);

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
