<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SlottypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SlottypesTable Test Case
 */
class SlottypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SlottypesTable
     */
    public $Slottypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.slottypes',
        'app.packages',
        'app.packages_slottypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Slottypes') ? [] : ['className' => 'App\Model\Table\SlottypesTable'];
        $this->Slottypes = TableRegistry::get('Slottypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Slottypes);

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
