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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Oliver Hader <oliver.hader@typo3.org>
 */
class ReferenceService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $recordIds = array();

	/**
	 * @return ReferenceService
	 */
	public static function getInstance() {
		return GeneralUtility::makeInstance(
			ReferenceService::class
		);
	}

	/**
	 * @param string $tableName
	 * @param int $uid
	 */
	public function add($tableName, $uid) {
		$uid = (int)$uid;
		if (empty($this->recordIds[$tableName])) {
			$this->recordIds[$tableName] = array();
		}

		if (!empty($uid) && !in_array($uid, $this->recordIds[$tableName])) {
			$this->recordIds[$tableName][] = $uid;
		}
	}

	public function persist() {
		$referenceIndex = $this->getReferenceIndex();
		foreach ($this->recordIds as $tableName => $uids) {
			foreach ($uids as $uid) {
				$referenceIndex->updateRefIndexTable($tableName, $uid);
			}
		}
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\ReferenceIndex
	 */
	protected function getReferenceIndex() {
		return GeneralUtility::makeInstance(
			\TYPO3\CMS\Core\Database\ReferenceIndex::class
		);
	}

}
