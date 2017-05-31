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

class MJasperReport extends MReport {

    public $fileType; // pdf doc xls rtf htm rpt
    public $fileOutput;
    public $dataSource;
    public $executionType; // local or remote (via Tomcat)
    public $name;

    function __construct($dataSource = 'admin', $executionType = 'local') {
        parent::__construct();
        $this->dataSource = $dataSource;
        $this->executionType = $executionType;
    }

    /**
     * Generate report file.
     * @param $save (boolean) true: show the file at new window; false: download the file
     */
    function execute($module, $name, $parameters = null, $rangeParam = null, $fileType = 'PDF', $save = false) {
        $this->name = $name;
        $this->fileType = isset($fileType) ? strtoupper(trim($fileType)) : 'PDF';
        $fileInput = addslashes($this->manager->getModulePath($module, 'views/' . $this->name . '.jasper'));  // Jasper report file
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts        
        $this->fileOutput = $id . "." . strtolower($this->fileType); // the report generated file
        $pathOut = Manager::getFrameworkPath('var/reports/' . $this->fileOutput);
        $javaPath = $this->manager->getOptions("javaPath");
        $MJasperPath = Manager::getFrameworkPath('classes/extensions/jasper');
        $scriptletsPath = $this->manager->getModulePath($module, 'views/scriptlets.jar'); // scriptlets allow to create custom functions for use at report
        $scriptletsPathCommon = $this->manager->getModulePath('common', 'views/scriptlets/scriptlets.jar'); // scriptlets allow to create custom functions for use at report
        $classPath = "$MJasperPath/lib/*:$MJasperPath/:$scriptletsPath:$scriptletsPathCommon:$javaPath/lib";
        $this->fill($fileInput, $pathOut, $fileType, $parameters, $classPath, $save);
    }

    function fill($fileInput, $fileOutput, $fileType, $parameters, $classPath, $save) {
        if ($this->executionType == 'local') { // execute java program at localhost
            $params = array();  // build a params array as base to json encoding
            if (is_array($parameters)) {
                foreach ($parameters as $pn => $pv) {
                    $params[$pn] = utf8_encode($pv);
                }
            }

            $db = Manager::$conf['db'][$this->dataSource];
            $params['dbUser'] = $db['user'];
            $params['jdbcDriver'] = $db['jdbc']['driver'];
            $params['jdbcDb'] = $db['jdbc']['db'];

            $prefix = substr(uniqid(md5(uniqid(""))), 0, 10);
            $params['pass'] = base64_encode($prefix . $db['password']);

            $params['relatorio'] = $fileInput;
            $params['fileOutput'] = $fileOutput;
            $params['fileType'] = $this->fileType;

            $javaPath = $this->manager->getOptions("javaPath");
//            $logPath = $this->manager->getConf('home.logs');
            $fileLog = $this->manager->getLog()->getLogFileName(str_replace("\\", "", "Jasper_" . str_replace('/', '', $this->name)) . "_" . substr(uniqid(md5(uniqid(""))), 0, 6) . '.log');

            $json = addslashes(json_encode($params));

            $MJasperPath = Manager::getFrameworkPath('classes/extensions/jasper');

            $debug = $this->manager->getOptions("debug");

            $cmd = $javaPath . "/bin/java -classpath $classPath MJasper \"{$json}\"" . ($debug ? " 2> $fileLog" : "");
            ////mdump($cmd);
            exec($cmd, $output);
            //var_dump($output);

            if (trim($output[0]) == "end") { //no errors!
                if ($this->fileType == "TXT") {  // adjust for CR+LF difference between Windows and Linux
                    Mutil::unix2dos($fileOutput);
                }

                if ($save) { // download
                    $this->manager->response->sendDownload($fileOutput);
                } else { // new window
                    $output = Manager::getAbsoluteURL('var/reports/' . $this->fileOutput);
                    $this->manager->getPage()->window($output);
                }
            } else {  // errors!
                $link = new MLink('', 'aqui', Manager::getActionURL('manager', "logs:$fileLog"), 'aqui', '_errors');
                $detalhes = "<br>Para mais detalhes clique " . $link->generate();
                throw new EControlException(implode("<br>", $output) . $detalhes);
            }
        } else if ($this->executionType == 'remote') {
            //Generate report throught another host, via TomCat
            $this->fileOutput = $this->manager->getConf("home.url_jasper") . "?bd={$this->db}&relatorio=$filein" . $parameters;
            $this->manager->getPage()->window($this->fileOutput);
        }
    }

    function executeJRXML($fileInput, $parameters = null, $fileType = 'PDF', $save = false) {
        $this->fileType = isset($fileType) ? strtoupper(trim($fileType)) : 'PDF';
        $id = uniqid(md5(uniqid("")));  // generate a unique id to avoid name conflicts        
        $this->fileOutput = $id . "." . strtolower($this->fileType); // the report generated file
        $pathOut = $this->manager->getConf("home.reports") . '/' . $this->fileOutput;
        $javaPath = $this->manager->getConf("home.java");
        $MJasperPath = $this->manager->getConf("home.extensions") . "/jasper";
        $scriptletsPath = $this->manager->getModulePath($module, 'reports/scriptlets.jar'); // scriptlets allow to create custom functions for use at report
        $classPath = "$MJasperPath/lib/*:$MJasperPath/:$scriptletsPath:$javaPath/lib";
        $this->fill($fileInput, $pathOut, $fileType, $parameters, $classPath, $save);
    }

}

?>