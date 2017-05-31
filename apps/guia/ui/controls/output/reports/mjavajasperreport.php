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

/*
 * JasperReports via JavaBridge running on Tomcat
 */
require_once(Manager::getOptions("javaBridge"));

class MJavaJasperReport extends MReport
{
    private $fileType; // pdf doc xls rtf htm rpt
    private $fileOutput;
    private $name;

    function getInputPath($fileInput)
    {
        $realPath = realpath(pathinfo($this->view->viewFile, PATHINFO_DIRNAME) . '/' . $fileInput);
        return $realPath ?: $fileInput;
    }

    function executeJRXML($fileInput, $parameters = null, $fileType = 'PDF', $save = false)
    {
        $this->fileType = isset($fileType) ? strtoupper(trim($fileType)) : 'PDF';
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts
        $this->fileOutput = $id . "." . strtolower($this->fileType); // the report generated file
        $pathOut = Manager::getFilesPath($this->fileOutput, true);
        $pathIn = $this->getInputPath($fileInput);
        $javaPath = Manager::getConf("home.java");
        $MJasperPath = Manager::home . '/' . Manager::getConf("home.extensions") . "/jasper";
        $scriptletsPath = Manager::getModulePath($module, 'reports/scriptlets.jar'); // scriptlets allow to create custom functions for use at report
        $classPath = "\"$MJasperPath/lib/*;$MJasperPath/build/classes;$scriptletsPath;$javaPath/lib\"";
        return $this->fill($pathIn, $pathOut, $fileType, $parameters, $classPath, $save);
    }

    function executeArray($array, $fileInput, $parameters = null, $fileType = 'PDF', $save = false)
    {
        $this->fileType = isset($fileType) ? strtoupper(trim($fileType)) : 'PDF';
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts
        $this->fileOutput = $id . "." . strtolower($this->fileType); // the report generated file
        $pathOut = Manager::getFilesPath($this->fileOutput, true);
        $pathIn = $this->getInputPath($fileInput);
        return $this->fillArray($array, $pathIn, $pathOut, $fileType, $parameters, $classPath, $save);
    }

    function executeCSV($array, $fileInput, $parameters = null, $fileType = 'PDF', $save = false)
    {
        $this->fileType = isset($fileType) ? strtoupper(trim($fileType)) : 'PDF';
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts
        $this->fileOutput = $id . "." . strtolower($this->fileType); // the report generated file
        $pathOut = Manager::getFilesPath($this->fileOutput, true);
        $pathIn = $this->getInputPath($fileInput);
        return $this->fillCSV($array, $pathIn, $pathOut, $fileType, $parameters, $classPath, $save);
    }

    function executeDB($db, $fileInput, $parameters = null, $fileType = 'PDF', $save = false)
    {
        $this->fileType = isset($fileType) ? strtoupper(trim($fileType)) : 'PDF';
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts
        $this->fileOutput = $id . "." . strtolower($this->fileType); // the report generated file
        $pathOut = Manager::getFilesPath($this->fileOutput, true);
        $pathIn = $this->getInputPath($fileInput);
        return $this->fillDB($db, $pathIn, $pathOut, $fileType, $parameters, $classPath, $save);
    }

    function fill($fileInput, $fileOutput, $fileType, $parameters, $classPath, $save)
    {

    }

    function fillArray($array, $fileInput, $fileOutput, $fileType, $parameters, $classPath, $save)
    {
        $params = $this->prepareParameters($parameters);
        $params->put('REPORT_LOCALE', new Java("java.util.Locale", 'pt', 'BR'));
        $extension = substr($fileInput, strrpos($fileInput, '.'));
        try {
            $sJfm = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
            if ($extension == ".jrxml") {
                $s1 = new JavaClass("net.sf.jasperreports.engine.xml.JRXmlLoader");
                $jasperDesign = $s1->load($fileInput);
                $sJcm = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
                $report = $sJcm->compileReport($jasperDesign);
            } else {
                $report = $fileInput;
            }
            $n = count($array);
            $i = 0;
            $ar = java("java.lang.reflect.Array")->newInstance(java("java.util.HashMap"), $n);
            foreach ($array as $row) {
                $p = new Java("java.util.HashMap");
                foreach ($row as $field => $value) {
                    $name = "COLUMN_" . $field;
                    $p->put($name, $value);
                }
                $ar[$i++] = $p;
            }
            $sJds = new Java("net.sf.jasperreports.engine.data.JRMapArrayDataSource", $ar);
            $print = $sJfm->fillReport($report, $params, $sJds);
            $sJem = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
            $output = \Manager::getDownloadURL('report', basename($this->fileOutput), true);
            $sJem->exportReportToPdfFile($print, $fileOutput);
        } catch (Exception $e) {
            dump_java_exception($e);
        }
        return $output;
    }

    function fillCSV($array, $fileInput, $fileOutput, $fileType, $parameters, $classPath, $save)
    {
        $params = $this->prepareParameters($parameters);
        $params->put('REPORT_LOCALE', new Java("java.util.Locale", 'pt', 'BR'));
        $extension = substr($fileInput, strrpos($fileInput, '.'));
        try {
            $sJfm = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
            if ($extension == ".jrxml") {
                $s1 = new JavaClass("net.sf.jasperreports.engine.xml.JRXmlLoader");
                $jasperDesign = $s1->load($fileInput);
                $sJcm = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
                $report = $sJcm->compileReport($jasperDesign);
            } else {
                $report = $fileInput;
            }
            //Add the array keys in the first row
            if ($parameters['ARRAY_KEYS_AS_HEADER']) {
                array_unshift($array, array_keys($array[0]));
            }
            $csvDump = new MCSVDump(Manager::getOptions('csv'), MCSVDump::WINDOWS_EOL);
            $fileCSV = str_replace('.pdf', '.csv', $fileOutput);
            $csvDump->save($array, basename($fileCSV));
            $sJds = new Java("net.sf.jasperreports.engine.data.JRCsvDataSource", $fileCSV, 'UTF8');
            $sJds->setFieldDelimiter(Manager::getOptions('csv'));
            if ($parameters['ARRAY_KEYS_AS_HEADER']) {
                $sJds->setUseFirstRowAsHeader(true);
            }

            $print = $sJfm->fillReport($report, $params, $sJds);
            $sJem = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
            $output = \Manager::getDownloadURL('report', basename($this->fileOutput), true);
            $sJem->exportReportToPdfFile($print, $fileOutput);
        } catch (Exception $e) {
            dump_java_exception($e);
        }
        return $output;
    }

    function fillDB($db, $fileInput, $fileOutput, $fileType, $parameters, $classPath, $save)
    {
        $params = $this->prepareParameters($parameters);
        $params->put('REPORT_LOCALE', new Java("java.util.Locale", 'pt', 'BR'));
        $extension = substr($fileInput, strrpos($fileInput, '.'));
        try {
            $sJfm = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");
            if ($extension == ".jrxml") {
                $s1 = new JavaClass("net.sf.jasperreports.engine.xml.JRXmlLoader");
                $jasperDesign = $s1->load($fileInput);
                $sJcm = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");
                $report = $sJcm->compileReport($jasperDesign);
            } else {
                $report = $fileInput;
            }
            // Create the JDBC Connection
            $conn = new Java("org.altic.jasperReports.JdbcConnection");
            // Call the driver to be used
            $conn->setDriver(\Manager::getConf("db.{$db}.jdbc.driver"));
            // Connection URL
            $conn->setConnectString(\Manager::getConf("db.{$db}.jdbc.db"));
            // Server Connection Username
            $conn->setUser(\Manager::getConf("db.{$db}.user"));
            // Server Connection Password
            $conn->setPassword(\Manager::getConf("db.{$db}.password"));
            $print = $sJfm->fillReport($report, $params, $conn->getConnection());
            $sJem = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");
            $output = \Manager::getDownloadURL('report', basename($this->fileOutput), true);
            $sJem->exportReportToPdfFile($print, $fileOutput);
        } catch (Exception $e) {
            dump_java_exception($e);
        }
        return $output;
    }

    function prepareParameters($parameters)
    {
        $params = array();  // build a params array as base to json encoding
        if (is_array($parameters)) {
            foreach ($parameters as $pn => $pv) {
                $params[$pn] = is_object($pv) ? $pv->__toString() : $pv;
            }
        }
        $p = new Java("java.util.HashMap");
        foreach ($params as $pn => $pv) {
            $prefix = substr($pn, 0, 4);
            $name = substr($pn, 4);
            if ($prefix == 'int_') {
                $p->put($name, $this->convertValue($pv, "java.lang.Integer"));
            } else if ($prefix == 'dbl_') {
                $p->put($name, $this->convertValue($pv, "java.lang.Double"));
            } else if ($prefix == 'boo_') {
                $p->put($name, $this->convertValue($pv, "java.lang.Boolean"));
            } else if ($prefix == 'str_') {
                $p->put($name, $this->convertValue($pv, "java.lang.String"));
            } else if ($prefix == 'stm_') {
                $p->put($name, $this->convertValue($pv, "java.io.InputStream"));
            } else if ($prefix == 'img_') {
                $p->put($name, $this->convertValue($pv, "java.awt.Image"));
            } else {
                $p->put($pn, $this->convertValue($pv, "java.lang.String"));
            }
        }
        return $p;
    }

    function convertValue($value, $className)
    {
        // if we are a string, just use the normal conversion
        // methods from the java extension...
        try {
            if ($className == 'java.lang.String') {
                $temp = new Java('java.lang.String', MUtil::NVL($value, ''));
                return $temp;
            } else if ($className == 'java.lang.Boolean' ||
                    $className == 'java.lang.Integer' ||
                    $className == 'java.lang.Long' ||
                    $className == 'java.lang.Short' ||
                    $className == 'java.lang.Double' ||
                    $className == 'java.math.BigDecimal') {
                $temp = new Java($className, $value);
                return $temp;
            } else if ($className == 'java.sql.Timestamp' ||
                    $className == 'java.sql.Time') {
                $temp = new Java($className);
                $javaObject = $temp->valueOf($value);
                return $javaObject;
            } else if ($className == 'java.io.InputStream') {
                $mfile = MFile::file($value);
                $file = new java("java.io.File", $mfile->getPath());
                $stream = new Java('java.io.FileInputStream', $file);
                $temp = new Java('javax.imageio.ImageIO');
                $image = $temp->read($stream);
                return $image;
            } else if ($className == 'java.awt.Image') {
                $mfile = MFile::file($value);
                $file = new java("java.io.File", $mfile->getPath());
                $temp = new Java('javax.imageio.ImageIO');
                $image = $temp->read($file);
                return $image;
            }
        } catch (Exception $err) {
            dump_java_exception($err);
            mdump( 'unable to convert value, ' . $value . ' could not be converted to ' . $className);
            return false;
        }
        mdump( 'unable to convert value, class name ' . $className . ' not recognised');
        return false;
    }

}

// Dumps a very detailed error message for a given java_exception
function dump_java_exception($ex)
{
    $trace = new java("java.io.ByteArrayOutputStream");
    mdump("Java Exception in File '" . $ex->getFile() . "' Line:" . $ex->getLine() . " - Message: " . $ex->getCause()->toString());
    $cause = $ex->getCause();
    $cause->printStackTrace(new java("java.io.PrintStream", $trace));
    mdump("<PRE>Java Stack Trace:\n" . $trace->toString() . "\n</PRE>");
}

?>