<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PageSlotTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PageSlotTypesTable Test Case
 */
class PageSlotTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PageSlotTypesTable
     */
    public $PageSlotTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.page_slot_types'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PageSlotTypes') ? [] : ['className' => 'App\Model\Table\PageSlotTypesTable'];
        $this->PageSlotTypes = TableRegistry::get('PageSlotTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PageSlotTypes);

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
