<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PluginsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PluginsTable Test Case
 */
class PluginsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PluginsTable
     */
    public $Plugins;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.plugins',
        'app.clients',
        'app.resellers',
        'app.clients_plugins'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Plugins') ? [] : ['className' => 'App\Model\Table\PluginsTable'];
        $this->Plugins = TableRegistry::get('Plugins', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Plugins);

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
