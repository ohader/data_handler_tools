# TYPO3 CMS DataHandler Tools

## Installation

Clone the repository to your extension directory

```git clone https://github.com/ohader/data_handler_tools.git typo3conf/ext/data_handler_tools```

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
        \OliverHader\DataHandlerTools\Service\ExportService::getInstance()
            ->reExportAll($dataSet, $this, $dataSetName);
        $this->fail(implode(LF, $failMessages));
    }
  ```
* find the re-exported CSV filed in the directory ```dataSets``` in your instance-root directory
