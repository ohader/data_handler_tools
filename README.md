# TYPO3 CMS DataHandler Tools

## Installation

Clone the repository to your extension directory

```
git clone https://github.com/ohader/data_handler_tools.git typo3conf/ext/data_handler_tools
```

## Re-exporting assertion data-sets

For re-exporting an existing assertion data-set the following steps are required

* enable extension to be loaded in functional tests in ```AbstractDataHandlerActionTestCase```
  in ```typo3_src/typo3/sysext/core/Tests/Functional/DataHandling/AbstractDataHandlerActionTestCase.php```,
  extend the existing ```$testExtensionsToLoad``` array

```
    /**
     * @var array
     */
    protected $testExtensionsToLoad = array(
        'typo3/sysext/core/Tests/Functional/Fixtures/Extensions/irre_tutorial',
        'typo3conf/ext/data_handler_tools',
    );
```

* trigger re-export if test assertion failed in ```AbstractDataHandlerActionTestCase::assertAssertionDataSet``` (same class)
  extend the assertion condition at the end of that method

```
    if (!empty($failMessages)) {
        $triggerReExport = false;
        $exportService = \OliverHader\DataHandlerTools\Service\ExportService::getInstance();
        // exporting to `dataSet` directory of original TYPO3 installation
        $exportService->setExportPath(ORIGINAL_ROOT);
        $exportService->setFields($dataSet);

        // adjusting fields to be be exported (remove if not required)
        foreach ($dataSet->getTableNames() as $tableName) {
            $fieldName = $GLOBALS['TCA'][$tableName]['ctrl']['origUid'] ?? null;
            if ($fieldName === null ||
                !in_array($fieldName, $dataSet->getFields($tableName, true))) {
                continue;
            }
            $exportService->removeFieldName($tableName, $fieldName);
            $triggerReExport = true;
        }

        if ($triggerReExport) {
            $exportService->reExport($dataSet->getTableNames(), $this, basename($fileName, '.csv'));
            return;
        }

        $this->fail(implode(LF, $failMessages));
    }
```

* find the re-exported CSV filed in the directory ```dataSets``` in your instance-root directory, like e.g.

```
  dataSets/
  \- TYPO3/
     \- CMS/
        \- Core/
           \- Tests/
              \- Functional/
                 \- DataHandling/
                    \- FAL/
                       \- Modify/
                          \- modifyContentWFileReference.csv
```
