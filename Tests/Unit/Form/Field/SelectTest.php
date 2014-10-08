<?php
namespace FluidTYPO3\Flux\Form\Field;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

/**
 * @package Flux
 */
class SelectTest extends AbstractFieldTest {

	/**
	 * @var array
	 */
	protected $chainProperties = array(
		'name' => 'test',
		'label' => 'Test field',
		'itemListStyle' => 'color: red',
		'selectedListStyle' => 'color: blue',
		'emptyOption' => TRUE,
		'items' => '1,2,3',
		'minItems' => 1,
		'maxItems' => 3,
		'requestUpdate' => TRUE,
	);

	/**
	 * @test
	 */
	public function canConsumeCommaSeparatedItems() {
		/** @var Select $instance */
		$instance = $this->createInstance();
		$instance->setItems('1,2');
		$this->assertSame(2, count($instance->getItems()));
		$this->performTestBuild($instance);
	}

	/**
	 * @test
	 */
	public function canConsumeSingleDimensionalArrayItems() {
		/** @var Select $instance */
		$instance = $this->createInstance();
		$instance->setItems(array(1, 2));
		$this->assertSame(2, count($instance->getItems()));
		$this->performTestBuild($instance);
	}

	/**
	 * @test
	 */
	public function canConsumeMultiDimensionalArrayItems() {
		/** @var Select $instance */
		$instance = $this->createInstance();
		$items = array(
			array('foo' => 'bar'),
			array('baz' => 'bay')
		);
		$instance->setItems($items);
		$this->assertSame(2, count($instance->getItems()));
		$this->performTestBuild($instance);
	}

	/**
	 * @test
	 */
	public function canConsumeQueryObjectItems() {
		$GLOBALS['TCA']['foobar']['ctrl']['label'] = 'username';
		/** @var Select $instance */
		$instance = $this->objectManager->get($this->createInstanceClassName());
		$query = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Query', array('execute', 'getType'), array(), '', FALSE);
		$query->expects($this->any())->method('getType')->will($this->returnValue('foobar'));
		$query->expects($this->any())->method('execute')->will($this->returnValue(array(
			new FrontendUser('user1'),
			new FrontendUser('user2')
		)));
		$instance->setItems($query);
		$result = $instance->getItems();
		$this->assertIsArray($result);
		$this->performTestBuild($instance);
		$this->assertEquals(array(
			array('user1', NULL), array('user2', NULL)
		), $result);
	}

	/**
	 * @test
	 */
	public function getLabelPropertyNameTranslatesTableNameFromObjectTypeRespectingTableMapping() {
		$instance = $this->objectManager->get($this->createInstanceClassName());
		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager', array('getConfiguration'));
		$instance->injectConfigurationManager($configurationManager);
		$table = 'foo';
		$type = 'bar';
		$fixture = array('config.' => array('tx_extbase.' => array('persistence.' => array('classes.' =>
			array($type . '.' => array('mapping.' => array('tableName' => $table . 'suffix')))))));
		$configurationManager->expects($this->once())->method('getConfiguration')->will($this->returnValue($fixture));
		$GLOBALS['TCA'][$table . 'suffix']['ctrl']['label'] = $table . 'label';
		$propertyName = $this->callInaccessibleMethod($instance, 'getLabelPropertyName', $table, $type);
		$this->assertEquals($table . 'label', $propertyName);
	}

}
