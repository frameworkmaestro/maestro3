<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada 
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL 
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a 
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

class MExporter {

    private $fileType; // xls xlsx pdf csv
    private $exporter;

    public function __construct($fileType) {
        $this->fileType = $fileType;
        switch (strtoupper($fileType)) {
            case 'XLS':
            case 'XLSX':
                $this->exporter = new MExporterExcel();
                break;
            case 'CSV':
                $this->exporter = new MExporterCSV();
                break;
            case 'PDF':
                $this->exporter = new MExporterPDF();
                break;
        }
    }

    public function execute($data) {
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts
        $fileOutput = $id . "." . $this->fileType; // the report generated file
        $pathOut = Manager::getFilesPath($fileOutput, true);
        $output = $this->exporter->execute($data, $pathOut);
        return $output ? : \Manager::getDownloadURL('report', basename($fileOutput), true);
    }

    public function addColumns($columns) {
        foreach ($columns as $column) {
            $this->exporter->addColumn($column);
        }
    }

}

class MExporterPDF extends MPDFReport {

    public $data;

    public function __construct() {
        parent::__construct('Manager', null, null);
    }

    public function addColumn($column) {
        parent::addColumn(new MPDFReportColumn($column, $column, 'left', true, 'auto', true));
    }

    public function execute($array, $pathOut) {
        $this->data = $array;
        $this->rowCount = count($array);
        $this->pageLength = $this->rowCount;
        $this->generateReport();
        return $this->ezpdf->execute();
    }

}

class MExporterCSV extends MCSVDump {

    private $column;

    public function execute($array, $pathOut) {
        $arrayDados = array_merge(array($this->column), $array);
        $this->save($arrayDados, $pathOut);
    }

    public function addColumn($column) {
        $this->column[] = $column;
    }

}

class MExporterExcel extends PHPExcel {

    const INITIALCHAR = 65; // A

    public function execute($planilhas, $pathOut) {

        // Set document properties
        $this->getProperties()->setCreator(Manager::getConf("name"))
                ->setLastModifiedBy('Maestro Framework')
                ->setTitle("Maestro export file");

        // Add some data
        $sheetIndex = 0;
        try {
            foreach ($planilhas as $sheet => $data) {
                $this->createSheet($sheetIndex)->setTitle($sheet);
                $this->fillSheet($data, $sheetIndex);

                $sheetIndex++;
            }
        } catch (ERuntimeException $e) {
            throw new ERuntimeException($e->getMessage());
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($this, 'Excel5');
        $objWriter->save($pathOut);
    }

    public function fillSheet($data, $indexSheet = 0) {
        $line = 1;
        foreach ($data as $dataLine) {

            if (is_array($dataLine)) {
                $coluna = 0;

                foreach ($dataLine as $dataValue) {

                    $this->setValueByIndex($indexSheet, $coluna, $line, $dataValue);
                    $coluna++;
                }
            } else {
                $this->setValueByIndex($indexSheet, $coluna, $line, $dataLine);
            }
            $line++;
        }
    }

    public function setValueByIndex($indexSheet, $indexColumn, $indexLine, $value) {
        $charCode = MExporterExcel::INITIALCHAR;
        $sheetColumn = chr($charCode + $indexColumn);
        $this->setActiveSheetIndex($indexSheet)
                ->setCellValue($sheetColumn . $indexLine, $value)
                ->getColumnDimension($sheetColumn)
                ->setAutoSize(true);
    }

}

?>
