<?php
namespace XanUtility\Service\Excel;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\File;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet;

class Import
{
    private $objPHPExcel;
    private $error;
    private $headers = [];
    private $data = [];

    /**
     * @param File $file
     */
    public function __construct($file)
    {
        $this->error = new ErrorList();
        $fv = $file->getApprovedVersion();
        if ('xls' == $fv->getExtension() || 'xlsx' == $fv->getExtension()) {
            $this->objPHPExcel = PHPExcel_IOFactory::load(DIR_BASE . $fv->getRelativePath());
            $this->extractData();
        } else {
            $this->error->add(t('Invalid Excel File'));
        }

        return $this->error;
    }

    private function extractData()
    {
        $this->extractHeaders();
        foreach ($this->objPHPExcel->getAllSheets() as $sheetIdx => $sheet /* @var $sheet PHPExcel_Worksheet */) {
            $header = $this->headers[$sheetIdx];
            $sheetData = [];
            if ($sheet->cellExists('A2')) {
                foreach ($sheet->getRowIterator(2) as $row) {
                    $rowData = [];
                    foreach ($row->getCellIterator() as $index => $cell) {
                        $rowData[$header[$index]] = $this->extractCellValue($cell);
                    }
                    $sheetData[] = $rowData;
                }
                $this->data[] = ["name" => $sheet->getTitle(), 'data' => $sheetData];
            }
        }
    }

    private function extractHeaders()
    {
        foreach ($this->objPHPExcel->getAllSheets() as $sheet) {
            $columns = [];
            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCellIterator() as $index => $cell) {
                    $colName = trim($cell->getValue());
                    $columns[$index] = $colName;
                }
                break;
            }
            $this->headers[] = $columns;
        }
    }

    private function extractCellValue(PHPExcel_Cell $cell)
    {
        if (PHPExcel_Shared_Date::isDateTime($cell)) {
            return PHPExcel_Shared_Date::ExcelToPHPObject($cell->getValue());
        } else {
            return trim($cell->getValue());
        }
    }

    /**
     * @param int $tabIndex
     *
     * @return array
     */
    public function getTabHeader($tabIndex)
    {
        return $this->headers[$tabIndex];
    }

    /**
     * array structure:
     *  [ &nbsp;  'name' => '', // tab name <br/>
     *    &nbsp;  'data' => [      // tab data        <br/>
     *    &nbsp;               'rowIndex' => [ 'columnHeaderName' => 'cellValue', ...],  <br/>
     *    &nbsp;               ...    <br/>
     *    &nbsp;           ]  <br/>
     *  ] <br/>.
     *
     * @param int $tabIndex
     *
     * @return array
     */
    public function getTabData($tabIndex)
    {
        return $this->data[$tabIndex];
    }

    public function getAllData()
    {
        return $this->data;
    }
}
