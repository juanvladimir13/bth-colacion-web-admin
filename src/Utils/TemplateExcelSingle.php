<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Utils;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TemplateExcelSingle
{
    private Spreadsheet $document;
    private Worksheet $sheet;
    private string $pathFile;
    private string $fileName;
    private string $pathTemplateFile;

    private string $titleSheet;
    private int $indexSheet;
    private bool $securitySheet;
    private int $startRow;
    private string $startColumn;
    private string $errorMessage;
    private string $password;

    public function __construct(
        string $fileName,
        string $titleSheet,
        bool   $securitySheet = false,
        string $pathTemplateFile = './reports/filiacion-template.xlsx',
        int    $indexSheet = 0,
        int    $startRow = 6,
        string $startColumn = 'A',
        string $pathFile = '',
        string $password = 'ju4nvl4d1m1r!#'
    )
    {
        $this->fileName = \str_ends_with($fileName, '.xlsx') ? $fileName : "$fileName.xlsx";
        $this->pathFile = $pathFile;
        $this->pathTemplateFile = $pathTemplateFile;
        $this->titleSheet = $titleSheet;
        $this->indexSheet = $indexSheet;

        $this->errorMessage = '';
        $this->password = $password;

        $this->startRow = $startRow;
        $this->startColumn = $startColumn;
        $this->securitySheet = $securitySheet;
    }

    private function checkProccess(): bool
    {
        if (!file_exists($this->pathTemplateFile)) {
            $this->errorMessage = "El archivo de plantilla no existe: $this->pathTemplateFile";
            return false;
        }

        if ($this->pathFile == '') {
            return true;
        }

        if (!is_writable($this->pathFile)) {
            $this->errorMessage = "No se puede escribir en el directorio destino: $this->pathFile";
            return false;
        }
        return true;
    }

    public function createDocument(): bool
    {
        if (!$this->checkProccess()) {
            return false;
        }

        $this->document = IOFactory::load($this->pathTemplateFile);
        $this->document->getProperties()
            ->setCreator('Sistemas Informaticos')
            ->setCompany('Modulo Tecnologico Productivo')
            ->setDescription('Documentos administrativos');

        $this->sheet = $this->document->getSheet($this->indexSheet);
        $this->sheet->setTitle($this->titleSheet);
        if ($this->securitySheet) {
            $this->appendSecuritySheet();
        }
        return true;
    }

    private function appendSecuritySheet(): void
    {
        $this->document->getActiveSheet()->getProtection()->setSheet(true);
        $this->document->getActiveSheet()->getProtection()->setSort(true);
        $this->document->getActiveSheet()->getProtection()->setDeleteColumns(true);
        $this->document->getActiveSheet()->getProtection()->setDeleteRows(true);
        $this->document->getActiveSheet()->getProtection()->setInsertColumns(true);
        $this->document->getActiveSheet()->getProtection()->setInsertRows(true);
        $this->document->getActiveSheet()->getProtection()->setPassword($this->password);
    }

    public function setTitle(string $title, int $colCount, int $startRow): void
    {
        $endColumn = chr(ord($this->startColumn) + $colCount) . $startRow;
        $startColumn = $this->startColumn . $startRow;
        $this->sheet->mergeCells($startColumn . ':' . $endColumn, Worksheet::MERGE_CELL_CONTENT_MERGE);
        $this->sheet->setCellValue($startColumn, $title);
        $this->sheet->getStyle($startColumn)->applyFromArray($this->getStyleTitle());
    }

    public function setHeader(array $data): void
    {
        foreach ($data as $column => $value) {
            $this->sheet->setCellValue($column, $value);
        }
    }

    private function setRowsFromArray(array $rows): void
    {
        $this->sheet->fromArray($rows, null, chr(ord($this->startColumn) + 1) . $this->startRow);

        $rowCount = count($rows);
        for ($index = 0; $index < $rowCount; $index++) {
            $rowIndex = $index + $this->startRow;
            $this->sheet->setCellValue($this->startColumn . $rowIndex, $index + 1);
        }
    }

    private function setRowsStyle(int $rowCount, int $colCount): void
    {
        $endColumn = chr(ord($this->startColumn) + $colCount);
        for ($index = 0; $index < $rowCount; $index++) {
            $rowIndex = $index + $this->startRow;
            $this->sheet->setCellValue($this->startColumn . $rowIndex, $index + 1);
            $this->sheet->getStyle(
                $this->startColumn . $rowIndex . ':' . $endColumn . $rowIndex
            )
                ->applyFromArray($this->getStyleTableContent());
        }
    }

    private function setColumnDimension(array $columnDimension): void
    {
        foreach ($columnDimension as $column => $width) {
            $this->sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    public function downloadDocument(): void
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($this->document, 'Xlsx');
        $writer->save('php://output');
    }

    private function saveDocument(): void
    {
        $writer = IOFactory::createWriter($this->document, 'Xlsx');
        $writer->save($this->pathFile . '/' . $this->fileName);
    }

    private function getStyleTableContent(): array
    {
        return [
            'font' => [
                'size' => 11,
                'name' => 'Arial'
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function getStyleTitle(): array
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_BOTTOM,
            ]
        ];
    }

    public function buildFiliacion(array $rows, bool $includeDataSencible): void
    {
        $rowCount = count($rows);
        for ($row = 0; $row < $rowCount; $row++) {
            $rowIndex = $row + $this->startRow;
            $this->sheet->setCellValue(
                chr(ord($this->startColumn)) . $rowIndex,
                $row + 1
            );

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 1) . $rowIndex,
                $rows[$row]['registro']
            );

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 2) . $rowIndex,
                $rows[$row]['estudiante']
            );

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 3) . $rowIndex,
                $rows[$row]['genero']
            );

            if ($includeDataSencible) {
                $this->sheet->setCellValue(
                    chr(ord($this->startColumn) + 4) . $rowIndex,
                    $rows[$row]['carnet']
                );
            }

            if ($includeDataSencible) {
                $this->sheet->setCellValueExplicit(
                    chr(ord($this->startColumn) + 5) . $rowIndex,
                    (string)$rows[$row]['rude'],
                    DataType::TYPE_STRING
                );
            }

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 6) . $rowIndex,
                $rows[$row]['celular']
            );

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 7) . $rowIndex,
                $rows[$row]['ue']
            );

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 8) . $rowIndex,
                $rows[$row]['curso_paralelo_origen']
            );

            $tutor = ($rows[$row]['nombres_principal'] == '' || $rows[$row]['apellidos_principal'] == '') ?
                $rows[$row]['nombres_secundario'] . ' ' . $rows[$row]['apellidos_secundario'] : $rows[$row]['nombres_principal'] . ' ' . $rows[$row]['apellidos_principal'];

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 9) . $rowIndex,
                $tutor
            );

            $tutorParentesco = ($rows[$row]['nombres_principal'] == '' || $rows[$row]['apellidos_principal'] == '') ?
                $rows[$row]['parentesco_secundario'] : $rows[$row]['parentesco_principal'];

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 10) . $rowIndex,
                is_null($tutorParentesco) ? '' : mb_strtoupper($tutorParentesco, 'UTF-8')
            );

            $tutorCelular = ($rows[$row]['nombres_principal'] == '' || $rows[$row]['apellidos_principal'] == '') ?
                $rows[$row]['celular_secundario'] : $rows[$row]['celular_principal'];
            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 11) . $rowIndex,
                $tutorCelular
            );

            $this->sheet->setCellValue(
                chr(ord($this->startColumn) + 12) . $rowIndex,
                $rows[$row]['direccion']
            );

            if ($includeDataSencible) {
                $this->sheet->setCellValueExplicit(
                    chr(ord($this->startColumn) + 13) . $rowIndex,
                    $rows[$row]['fecha'],
                    DataType::TYPE_STRING
                );
            }
        }
        $this->pathFile !== '' ? $this->saveDocument() : $this->downloadDocument();
    }

    public function buildCentralizador(array $rows, array $columnDimension): void
    {
        $rowCount = count($rows);
        if ($rowCount > 0) {
            $this->setRowsFromArray($rows);
            $this->setRowsStyle(count($rows), count($rows[0]));
            $this->setColumnDimension($columnDimension);
        }

        $this->pathFile !== '' ? $this->saveDocument() : $this->downloadDocument();
    }

    // especialidad, curso, paralelo, materia
    public function buildCentralizadorMateriaCurso(array $rows, int $columnCount): void
    {
        $rowCount = count($rows);
        if ($rowCount > 0) {
            $this->setRowsFromArray($rows);
            $this->setRowsStyle(count($rows), count($rows[0]));
        }
        $this->pathFile !== '' ? $this->saveDocument() : $this->downloadDocument();
    }

    // especialidad, curso, paralelo
    public function buildCentralizadorCurso(array $rows, int $columnCount): void
    {
        $rowCount = count($rows);
        if ($rowCount > 0) {
            $this->setRowsFromArray($rows);
            $this->setRowsStyle(count($rows), count($rows[0]));
        }
        $this->pathFile !== '' ? $this->saveDocument() : $this->downloadDocument();
    }

    // especialidad, curso, paralelo, materia
    public function buildCentralizadorCursoUE(array $rows, int $columnCount): void
    {
        $rowCount = count($rows);
        if ($rowCount > 0) {
            $this->setRowsFromArray($rows);
            $this->setRowsStyle(count($rows), count($rows[0]));
        }
        $this->pathFile !== '' ? $this->saveDocument() : $this->downloadDocument();
    }

    public function buildUnidadEducativaCurso(array $rows, int $columnCount): void
    {
        $rowCount = count($rows);
        if ($rowCount > 0) {
            $this->setRowsFromArray($rows);
            $this->setRowsStyle(count($rows), count($rows[0]));
        }
        $this->pathFile !== '' ? $this->saveDocument() : $this->downloadDocument();
    }
}
