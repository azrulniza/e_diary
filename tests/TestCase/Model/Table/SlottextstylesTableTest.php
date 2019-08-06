<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SlottextstylesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SlottextstylesTable Test Case
 */
class SlottextstylesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SlottextstylesTable
     */
    public $Slottextstyles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.slottextstyles',
        'app.packages',
        'app.packages_slottextstyles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Slottextstyles') ? [] : ['className' => 'App\Model\Table\SlottextstylesTable'];
        $this->Slottextstyles = TableRegistry::get('Slottextstyles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Slottextstyles);

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
