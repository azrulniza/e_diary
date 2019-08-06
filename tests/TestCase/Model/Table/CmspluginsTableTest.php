<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmspluginsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmspluginsTable Test Case
 */
class CmspluginsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CmspluginsTable
     */
    public $Cmsplugins;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cmsplugins',
        'app.plugin_types',
        'app.packages',
        'app.packages_cmsplugins'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Cmsplugins') ? [] : ['className' => 'App\Model\Table\CmspluginsTable'];
        $this->Cmsplugins = TableRegistry::get('Cmsplugins', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Cmsplugins);

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
