<?php
namespace XanUtility\Service\Excel;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\File;

class Import
{
    private $objPHPExcel;
    private $error;
    private $headers = [];
    private $data = [];

    /**
     * Import constructor.
     * @param File $file
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function __construct($file)
    {
        $this->error = new ErrorList();
        $fv = $file->getApprovedVersion();
        if ('xls' == $fv->getExtension() || 'xlsx' == $fv->getExtension()) {
            $this->objPHPExcel = IOFactory::load(absolute_path($fv->getRelativePath()));
            $this->extractData();
        } else {
            $this->error->add(t('Invalid Excel File'));
        }

        return $this->error;
    }

    private function extractData()
    {
        $this->extractHeaders();
        foreach ($this->objPHPExcel->getAllSheets() as $sheetIdx => $sheet) {
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

    private function extractCellValue(Cell $cell)
    {
        if (Date::isDateTime($cell)) {
            return Date::excelToDateTimeObject($cell->getValue());
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
