<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MenuGroupsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MenuGroupsTable Test Case
 */
class MenuGroupsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\MenuGroupsTable
     */
    public $MenuGroups;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.menu_groups',
        'app.menus'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('MenuGroups') ? [] : ['className' => 'App\Model\Table\MenuGroupsTable'];
        $this->MenuGroups = TableRegistry::get('MenuGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MenuGroups);

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
