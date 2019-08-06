<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PageSlotTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PageSlotTable Test Case
 */
class PageSlotTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PageSlotTable
     */
    public $PageSlot;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.page_slot',
        'app.page_templates'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PageSlot') ? [] : ['className' => 'App\Model\Table\PageSlotTable'];
        $this->PageSlot = TableRegistry::get('PageSlot', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PageSlot);

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
