<?php
namespace XanUtility\Service\Excel;

use Concrete\Core\Application\Application;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use User;

class Export
{
    /** @var Spreadsheet */
    private $phpExcel = null;

    private $tabid = 0; //id of tabulation index on excel file
    private $startRow = 1;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->phpExcel = new Spreadsheet();
        $this->app = $app;
    }

    /**
     * Set Line number from where the insertion will start.
     *
     * @param int $startRow
     */
    public function setStartRow($startRow)
    {
        $this->startRow = (int) $startRow;
    }

    private function setFileProprietes()
    {
        $u = new User();
        // Set document properties
        $this->phpExcel->getProperties()
                            ->setCreator($u->getUserName())
                            ->setLastModifiedBy($u->getUserName());
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcel->setActiveSheetIndex(0);
    }

    /**
     * Download Excel File.
     *
     * @param $fileName
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download($fileName)
    {
        ob_end_clean();
        set_time_limit(0);
        $this->setFileProprietes();

        if (!ends_with($fileName, '.xlsx')) {
            $fileName .= '.xlsx';
        }

        header('Content-Encoding: UTF-8');
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Content-Title: ' . $this->phpExcel->getActiveSheet()->getTitle());
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Cache-control: private');
        header('Pragma: public'); // HTTP/1.0
        $objWriter = IOFactory::createWriter($this->phpExcel, 'Xlsx');
        $objWriter->save('php://output');
        $this->app->shutdown();
    }

    /**
     * Save The Excel file under the given directory.
     *
     * @param $fileName
     * @param $dirPath
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function saveAs($fileName, $dirPath)
    {
        $this->setFileProprietes();

        if (!ends_with($fileName, '.xlsx')) {
            $fileName .= '.xlsx';
        }

        if (!ends_with($dirPath, '/') && !ends_with($dirPath, '\\')) {
            $dirPath .= '/';
        }

        $objWriter = IOFactory::createWriter($this->phpExcel, 'Xlsx');
        $objWriter->save($dirPath . $fileName);
    }

    /**
     * Insert Content in Excel Worksheet.
     *
     * @param string $tabName tabulationName
     * @param array $headers columns names
     * @param array $data rows
     * @param bool $createNewTab boolean indicate that we like to create New tab
     *
     * @return array(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet, int)
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Style\Exception
     */
    public function addTabContent($tabName = '', array $headers = [], array $data = [], $createNewTab = false)
    {
        if (true == $createNewTab) {
            $this->phpExcel->createSheet();
            ++$this->tabid;
        }

        $this->phpExcel->setActiveSheetIndex($this->tabid);
        $activeSheet = $this->phpExcel->getActiveSheet();
        $tabName = str_replace(['*', ':', '?', '[', ']'], '', $tabName); // Remove invalid Chars
        $tabName = str_replace(['\\', '/'], '-', $tabName);
        $tabName = substr($tabName, 0, 30); // Maximum 31 characters allowed in sheet title
        $activeSheet->setTitle($tabName, false);

        $rowCount = $this->startRow;
        $contentStartRow = $this->startRow;

        if (!empty($headers)) {
            foreach ($headers as $i => $value) {
                $colIdx = $i + 1;
                $activeSheet->setCellValueByColumnAndRow($colIdx, $rowCount, $value);
                $activeSheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIdx))->setAutoSize(true);
            }

            $indexLastCell = Coordinate::stringFromColumnIndex(sizeof($headers));
            $headerStyle = $activeSheet->getStyle("A{$rowCount}:{$indexLastCell}{$rowCount}");
            $headerStyle->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
            $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('428BCA');
            $headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            ++$rowCount;
            ++$contentStartRow;
        }

        foreach ($data as $row) {
            foreach ($row as $i => $value) {
                $colIdx = $i + 1;
                // Check if value contains Html tags
                if ($value != strip_tags($value)) {
                    $hh = new Html();
                    $value = $hh->toRichTextObject($value);
                }
                $activeSheet->setCellValueByColumnAndRow($colIdx, $rowCount, $value);
            }
            ++$rowCount;
        }

        $lastRowIndex = $rowCount - 1;
        $indexLastCell = Coordinate::stringFromColumnIndex($colIdx);
        $contentStyle = $activeSheet->getStyle("A{$contentStartRow}:{$indexLastCell}{$lastRowIndex}");
        $contentStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $contentStyle->getAlignment()->setWrapText(true);

        return [$activeSheet, $rowCount];
    }

    /**
     * @return Spreadsheet
     */
    public function getPHPExcelObject()
    {
        return $this->phpExcel;
    }
}
