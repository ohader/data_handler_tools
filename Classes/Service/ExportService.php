<?php
namespace OliverHader\DataHandlerTools\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Oliver Hader <oliver.hader@typo3.org>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Tests\Functional\DataHandling\Framework\DataSet;
use TYPO3\CMS\Core\Tests\FunctionalTestCase;

/**
 * @author Oliver Hader <oliver.hader@typo3.org>
 */
class ExportService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $fields = array(
		'__defaultElement' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l10n_parent', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id'),
		'pages' => array('uid', 'pid', 'sorting', 'deleted', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'title'),
		'pages_language_overlay' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'title'),
//		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'image', 'categories', 'tx_irretutorial_1ncsv_hotels'),
//		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'image', 'categories'),
//		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'tx_testdatahandler_group'),
//		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'tx_irretutorial_1ncsv_hotels'),
#		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'tx_irretutorial_1nff_hotels'),
//		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'tx_irretutorial_mnmmasym_hotels'),
#		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header'),
		'tt_content' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l18n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'header', 'image'),
		'sys_language' => array('uid', 'pid', 'hidden', 'title', 'flag'),
		'sys_category' => array('__defaultElement', 'title', 'parent', 'items'),
		'sys_category_record_mm' => array('uid_local', 'uid_foreign', 'tablenames', 'sorting', 'sorting_foreign', 'fieldname',),
		'sys_workspace' => array('uid', 'pid', 'deleted', 'title', 'adminusers' ,'members' ,'reviewers', 'db_mountpoints', 'file_mountpoints', 'publish_time,unpublish_time', 'freeze', 'live_edit', 'vtypes', 'disable_autocreate', 'swap_modes', 'publish_access', 'custom_stages', 'stagechg_notification', 'edit_notification_mode', 'edit_notification_defaults', 'edit_allow_notificaton_settings', 'publish_notification_mode', 'publish_notification_defaults', 'publish_allow_notificaton_settings'),
		'sys_workspace_stage' => '*',
		'be_users' => array('uid', 'pid', 'username', 'admin', 'usergroup', 'disable', 'realName', 'workspace_id', 'workspace_preview'),
		'tx_testdatahandler_element' => array('__defaultElement', 'title'),
		'tx_irretutorial_1nff_hotel' => array('__defaultElement', 'title', 'parentid', 'parenttable', 'parentidentifier', 'offers'),
		'tx_irretutorial_1nff_offer' => array('__defaultElement', 'title', 'parentid', 'parenttable', 'parentidentifier', 'prices'),
		'tx_irretutorial_1nff_price' => array('__defaultElement', 'title', 'parentid', 'parenttable', 'parentidentifier'),
		'tx_irretutorial_1ncsv_hotel' => array('__defaultElement', 'title', 'offers'),
		'tx_irretutorial_1ncsv_offer' => array('__defaultElement', 'title', 'prices'),
		'tx_irretutorial_1ncsv_price' => array('__defaultElement', 'title'),
		'tx_irretutorial_mnmmasym_hotel' => array('__defaultElement', 'title', 'offers'),
		'tx_irretutorial_mnmmasym_offer' => array('__defaultElement', 'title', 'prices'),
		'tx_irretutorial_mnmmasym_price' => array('__defaultElement', 'title'),
		'tt_content_tx_irretutorial_mnmmasym_hotel_rel' => array('uid', 'uid_local', 'uid_foreign', 'tablenames', 'sorting', 'sorting_foreign'),
		'tx_irretutorial_mnmmasym_hotel_offer_rel' => array('uid', 'uid_local', 'uid_foreign', 'tablenames', 'sorting', 'sorting_foreign'),
		'tx_irretutorial_mnmmasym_offer_price_rel' => array('uid', 'uid_local', 'uid_foreign', 'tablenames', 'sorting', 'sorting_foreign'),
		'sys_file_reference' => array('uid', 'pid', 'sorting', 'deleted', 'sys_language_uid', 'l10n_parent', 't3ver_wsid', 't3ver_state', 't3ver_stage', 't3ver_oid', 't3ver_move_id', 'uid_local', 'uid_foreign', 'tablenames', 'fieldname', 'sorting_foreign', 'table_local', 'title', 'description', 'alternative', 'link', 'downloadname'),
		'sys_file' => array('__defaultElement', 'type', 'storage', 'identifier', 'extension', 'mime_type', 'name', 'sha1', 'size', 'creation_date', 'modification_date', 'missing', 'metadata', 'identifier_hash', 'folder_hash', 'last_indexed'),
		'sys_file_storage' => '*',
		'sys_file_metadata' => array('__defaultElement', 'file', 'title', 'width', 'height', 'description', 'alternative', 'categories'),
		'sys_refindex' => array('hash', 'tablename', 'recuid', 'field', 'flexpointer', 'softref_key', 'softref_id', 'sorting', 'deleted', 'ref_table', 'ref_uid', 'ref_string', 'workspace'),
	);

	/**
	 * @return ExportService
	 */
	public static function getInstance() {
		return GeneralUtility::makeInstance(
			'OliverHader\\DataHandlerTools\\Service\\ExportService'
		);
	}

	public static function execute() {
		if (empty($GLOBALS['argv'][2]) || empty($GLOBALS['argv'][3]) || empty($GLOBALS['argv'][4]) || !GeneralUtility::inList('page,table', $GLOBALS['argv'][2])) {
			echo 'scope, pageId and fileName needs to be submitted...' . PHP_EOL;
			echo 'scope is one out of "page" or "table"' . PHP_EOL;
			echo ' * page:  ' . $GLOBALS['argv'][0] . ' ' . $GLOBALS['argv'][1] . ' page <pageId> <fileName>' . PHP_EOL;
			echo ' * table: ' . $GLOBALS['argv'][0] . ' ' . $GLOBALS['argv'][1] . ' table <tableName> <fileName> [idRange]' . PHP_EOL;
			return;
		}

		$scope = $GLOBALS['argv'][2];
		$exportService = self::getInstance();

		if ($scope === 'page') {
			$pageId = $GLOBALS['argv'][3];
			$fileName = GeneralUtility::getFileAbsFileName($GLOBALS['argv'][4]);
			$exportService->exportPage($pageId, $fileName);
		} elseif ($scope === 'table') {
			$tableName = $GLOBALS['argv'][3];
			$fileName = GeneralUtility::getFileAbsFileName($GLOBALS['argv'][4]);
			$idRange = (empty($GLOBALS['argv'][5]) ? array() : GeneralUtility::trimExplode('-', $GLOBALS['argv'][5]));
			$idRange = array_map('intval', $idRange);
			$exportService->exportTable($tableName, $fileName, $idRange);
		}
	}

	/**
	 * @param integer $pageId
	 * @param string $fileName
	 */
	public function exportPage($pageId, $fileName) {
		$data = array();

		foreach ($this->getTableNames() as $tableName) {
			$fields = $this->getFields($tableName);

			if (!in_array('uid', $fields) || !in_array('pid', $fields)) {
				continue;
			}

			$elements = $this->getDatabaseConnection()->exec_SELECTgetRows(
				implode(', ', $fields),
				$tableName,
				'pid=' . (int)$pageId,
				'',
				'uid'
			);

			if (!empty($elements)) {
				$data[$tableName] = array(
					'fields' => $fields,
					'elements' => $elements,
				);
			}
		}

		if (!empty($data)) {
			$this->getDataSet($data)->persist($fileName);
		}
	}

	/**
	 * @param string $tableName
	 * @param string $fileName
	 * @param array $idRange
	 */
	public function exportTable($tableName, $fileName, array $idRange = array()) {
		$data = array();

		$fields = $this->getFields($tableName);

		$elements = $this->getDatabaseConnection()->exec_SELECTgetRows(
			implode(', ', $fields),
			$tableName,
			(empty($idRange) ? '1=1' : 'uid>=' . $idRange[0] . ' AND uid<=' . $idRange[1]),
			'',
			(in_array('uid', $fields) ? 'uid' : '')
		);

		if (!empty($elements)) {
			$data[$tableName] = array(
				'fields' => $fields,
				'elements' => $elements,
			);
		}

		if (!empty($data)) {
			$this->getDataSet($data)->persist($fileName);
		}
	}

	/**
	 * @param array $tableNames
	 * @param string $fileName
	 */
	public function exportTables(array $tableNames, $fileName) {
		$data = array();

		foreach ($tableNames as $tableName) {
			$fields = $this->getFields($tableName);

			$elements = $this->getDatabaseConnection()->exec_SELECTgetRows(
				implode(', ', $fields),
				$tableName,
				'1=1',
				'',
				(in_array('uid', $fields) ? 'uid' : '')
			);

			if (!empty($elements)) {
				$data[$tableName] = array(
					'fields' => $fields,
					'elements' => $elements,
				);
			}
		}

		if (!empty($data)) {
			$this->getDataSet($data)->persist($fileName);
		}
	}

	/**
	 * @param DataSet $dataSet
	 * @param FunctionalTestCase $test
	 * @param $dataSetName
	 */
	public function reExportAll(DataSet $dataSet, FunctionalTestCase $test, $dataSetName) {
		$this->setFields($dataSet);
		$paths = explode('\\', get_class($test));
		array_pop($paths);
		$path = 'dataSets/' . implode('/', $paths);
		GeneralUtility::mkdir_deep($path);
		$this->exportTables($dataSet->getTableNames(), $path . '/' . $dataSetName . '.csv');
	}

	/**
	 * @param DataSet $dataSet
	 */
	public function setFields(DataSet $dataSet) {
		foreach ($dataSet->getTableNames() as $tableName) {
			$this->fields[$tableName] = $dataSet->getFields($tableName);
		}
	}

	/**
	 * @return array
	 */
	protected function getTableNames() {
		return array_keys($this->getDatabaseConnection()->admin_get_tables());
	}

	/**
	 * @param string $tableName
	 * @return array
	 */
	protected function getFieldNames($tableName) {
		return array_keys($this->getDatabaseConnection()->admin_get_fields($tableName));
	}

	/**
	 * @param string $tableName
	 * @return array
	 */
	protected function getFields($tableName) {
		$fields = array();

		if (empty($this->fields[$tableName])) {
			$fields = $this->fields['__defaultElement'];
		} elseif ($this->fields[$tableName] === '*') {
			$fields = $this->getFieldNames($tableName);
		} else {
			foreach ($this->fields[$tableName] as $field) {
				if (strpos($field, '__') === 0 && !empty($this->fields[$field])) {
					$fields = array_merge($fields, $this->fields[$field]);
				} else {
					$fields[] = $field;
				}
			}
		}

		$fields = array_intersect($fields, $this->getFieldNames($tableName));

		return $fields;
	}

	/**
	 * @param array $data
	 * @return \TYPO3\CMS\Core\Tests\Functional\DataHandling\Framework\DataSet
	 */
	protected function getDataSet(array $data) {
		return GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Core\\Tests\\Functional\\DataHandling\\Framework\\DataSet',
			$data
		);
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

}
