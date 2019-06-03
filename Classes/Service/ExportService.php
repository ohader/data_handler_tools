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

use Doctrine\DBAL\Schema\Column;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\DataHandling\DataSet;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

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
     * @var string
     */
	private $exportPath = '';

	/**
	 * @return ExportService
	 */
	public static function getInstance() {
		return GeneralUtility::makeInstance(
			ExportService::class
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

            $queryBuilder = $this->createQueryBuilder($tableName);
            $queryBuilder->where(
                $queryBuilder->expr()->eq('pid', (int)$pageId)
            );

            if (in_array('uid', $fields)) {
                $queryBuilder->orderBy('uid');
            }

            $elements = $queryBuilder
                ->select(...$fields)
                ->from($tableName)
                ->execute()
                ->fetchAll();

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
        $queryBuilder = $this->createQueryBuilder($tableName);

        if (!empty($idRange)) {
            $queryBuilder->where(
                $queryBuilder->expr()->gte('uid', $idRange[0]),
                $queryBuilder->expr()->lte('uid', $idRange[1])
            );
        }

        if (in_array('uid', $fields)) {
            $queryBuilder->orderBy('uid');
        }

        $elements = $queryBuilder
            ->select(...$fields)
            ->from($tableName)
            ->execute()
            ->fetchAll();

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
     * @param string $exportPath
     */
    public function setExportPath(string $exportPath)
    {
        if (!is_dir($exportPath)) {
            throw new \RuntimeException(
                sprintf('Directory %s does not exist', $exportPath),
                1549384451
            );
        }
        $this->exportPath = rtrim($exportPath, '/') . '/';
    }

	/**
	 * @param array $tableNames
	 * @param string $fileName
     * @param null|int $padding
	 */
	public function exportTables(array $tableNames, $fileName, int $padding = null) {
		$data = array();

		foreach ($tableNames as $tableName) {
			$fields = $this->getFields($tableName);
            $queryBuilder = $this->createQueryBuilder($tableName);

            if (in_array('uid', $fields)) {
                $queryBuilder->orderBy('uid');
            }

            $elements = $queryBuilder
                ->select(...$fields)
                ->from($tableName)
                ->execute()
                ->fetchAll();

			if (!empty($elements)) {
				$data[$tableName] = array(
					'fields' => $fields,
					'elements' => $elements,
				);
			}
		}

		if (!empty($data)) {
			$this->getDataSet($data)->persist($fileName, $padding);
		}
	}

	/**
	 * @param DataSet $dataSet
	 * @param FunctionalTestCase $test
	 * @param string $dataSetName
	 */
	public function reExportAll(DataSet $dataSet, FunctionalTestCase $test, $dataSetName) {
		$this->setFields($dataSet);
		$this->reExport($dataSet->getTableNames(), $test, $dataSetName);
	}

	/**
	 * @param array $tableNames
	 * @param FunctionalTestCase $test
	 * @param string $dataSetName
	 */
	public function reExport(array $tableNames, FunctionalTestCase $test, $dataSetName)
	{
		$paths = explode('\\', get_class($test));
		array_pop($paths);
		$path = $this->exportPath . 'dataSets/' . implode('/', $paths);
		GeneralUtility::mkdir_deep($path);
		$this->exportTables(
			$tableNames,
			$path . '/' . $dataSetName . '.csv',
			$this->getMaximumPadding()
		);
	}

	public function removeFieldName(string $tableName, string $fieldName)
	{
		$index = array_search($fieldName, $this->fields[$tableName] ?? [], true);
		if ($index !== false) {
			unset($this->fields[$tableName][$index]);
			$this->fields[$tableName] = array_values($this->fields[$tableName]);
		}
	}

	public function insertFieldName(string $tableName, string $fieldName, string $afterFieldName = null)
	{
		if (empty($this->fields[$tableName])) {
			$this->fields[$tableName] = [];
		}

		$index = array_search($afterFieldName, $this->fields[$tableName]);
		if ($index === false) {
			$this->fields[$tableName][] = $fieldName;
		} else {
			array_splice($this->fields[$tableName], $index, 0, [$fieldName]);
		}
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
     * @return string[]
     */
    protected function getTableNames()
    {
        return $this->getConnectionPool()
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->getSchemaManager()
            ->listTableNames();
    }

    /**
     * @param string $tableName
     * @return string[]
     */
    protected function getFieldNames($tableName)
    {
        return array_map(
            function (Column $column) {
                return $column->getName();
            },
            $this->getConnectionPool()
                ->getConnectionForTable($tableName)
                ->getSchemaManager()
                ->listTableColumns($tableName)
        );
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
	 * @return int
	 */
	protected function getMaximumPadding(): int
	{
		$maximums = array_map('count', $this->fields);
		// adding additional index since field values are indented by one
		return max($maximums) + 1;
	}

	/**
	 * @param array $data
	 * @return DataSet
	 */
	protected function getDataSet(array $data) {
		return GeneralUtility::makeInstance(
			DataSet::class,
			$data
		);
	}

    /**
     * @param string $tableName
     * @return QueryBuilder
     */
    protected function createQueryBuilder(string $tableName)
    {
        $queryBuilder = $this->getConnectionPool()
            ->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder;
    }

    /**
     * @return ConnectionPool
     */
    protected function getConnectionPool()
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}
