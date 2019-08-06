<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PageSlotsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PageSlotsTable Test Case
 */
class PageSlotsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PageSlotsTable
     */
    public $PageSlots;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.page_slots',
        'app.page_templates',
        'app.page_slot'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PageSlots') ? [] : ['className' => 'App\Model\Table\PageSlotsTable'];
        $this->PageSlots = TableRegistry::get('PageSlots', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PageSlots);

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
