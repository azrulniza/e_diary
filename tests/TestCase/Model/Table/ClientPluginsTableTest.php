<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientPluginsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientPluginsTable Test Case
 */
class ClientPluginsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientPluginsTable
     */
    public $ClientPlugins;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.client_plugins',
        'app.clients',
        'app.clients_plugins',
        'app.resellers',
        'app.plugins'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ClientPlugins') ? [] : ['className' => 'App\Model\Table\ClientPluginsTable'];
        $this->ClientPlugins = TableRegistry::get('ClientPlugins', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ClientPlugins);

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
