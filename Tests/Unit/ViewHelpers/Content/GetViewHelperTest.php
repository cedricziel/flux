<?php
namespace FluidTYPO3\Flux\ViewHelpers\Content;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>
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

use FluidTYPO3\Flux\Tests\Fixtures\Data\Records;
use FluidTYPO3\Flux\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @author Claus Due <claus@wildside.dk>
 * @package Flux
 */
class GetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * Setup
	 */
	protected function setUp() {
		parent::setUp();
		$GLOBALS['TSFE'] = new TypoScriptFrontendController($GLOBALS['TYPO3_CONF_VARS'], 1, 0);
	}

	/**
	 * @test
	 */
	public function canRenderViewHelper() {
		$arguments = array(
			'area' => 'void',
			'as' => 'records',
			'order' => 'sorting'
		);
		$variables = array(
			'record' => Records::$contentRecordWithoutParentAndWithoutChildren
		);
		$node = new TextNode('Hello loopy world!');
		$output = $this->executeViewHelper($arguments, $variables, $node);
		$this->assertSame($node->getText(), $output);
	}

	/**
	 * @test
	 */
	public function canRenderViewHelperWithLoadRegister() {
		$arguments = array(
			'area' => 'void',
			'as' => 'records',
			'order' => 'sorting',
			'loadRegister' => array(
				'maxImageWidth' => 300
			)
		);
		$variables = array(
			'record' => Records::$contentRecordWithoutParentAndWithoutChildren
		);
		$node = new TextNode('Hello loopy world!');
		$output = $this->executeViewHelper($arguments, $variables, $node);
		$this->assertSame($node->getText(), $output);
	}

	/**
	 * @test
	 */
	public function canRenderViewHelperWithExistingAsArgumentAndTakeBackup() {
		$arguments = array(
			'area' => 'void',
			'as' => 'nameTaken',
			'order' => 'sorting'
		);
		$variables = array(
			'nameTaken' => 'taken',
			'record' => Records::$contentRecordWithoutParentAndWithoutChildren
		);
		$node = new TextNode('Hello loopy world!');
		$content = $this->executeViewHelper($arguments, $variables, $node);
		$this->assertIsString($content);
	}

	/**
	 * @test
	 */
	public function canRenderViewHelperWithNonExistingAsArgument() {
		$arguments = array(
			'area' => 'void',
			'as' => 'freevariablename',
			'order' => 'sorting'
		);
		$variables = array(
			'record' => Records::$contentRecordWithoutParentAndWithoutChildren
		);
		$node = new TextNode('Hello loopy world!');
		$output = $this->executeViewHelper($arguments, $variables, $node);
		$this->assertSame($node->getText(), $output);
	}

	/**
	 * @test
	 */
	public function canReturnArrayOfUnrenderedContentElements() {
		$arguments = array(
			'area' => 'void',
			'render' => FALSE,
			'order' => 'sorting'
		);
		$variables = array(
			'record' => Records::$contentRecordWithoutParentAndWithoutChildren
		);
		$output = $this->executeViewHelper($arguments, $variables);
		$this->assertIsArray($output);
	}

	/**
	 * @test
	 */
	public function canReturnArrayOfRenderedContentElements() {
		$arguments = array(
			'area' => 'void',
			'render' => TRUE,
			'order' => 'sorting'
		);
		$variables = array(
			'record' => Records::$contentRecordWithoutParentAndWithoutChildren
		);
		$output = $this->executeViewHelper($arguments, $variables);
		$this->assertIsArray($output);
	}

	/**
	 * @test
	 */
	public function canProcessRecords() {
		$GLOBALS['TSFE']->sys_page = $this->getAccessibleMock('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
		$instance = $this->createInstance();
		$records = array(
			array('uid' => 0),
			array('uid' => 99999999999),
		);
		$output = $this->callInaccessibleMethod($instance, 'getRenderedRecords', $records);
		$this->assertIsArray($output);
	}

}
