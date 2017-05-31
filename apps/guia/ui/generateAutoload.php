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

/**
 * Script para geração automática do arquivo autoload.php, através da leitura
 * da pasta class/ui/controls
 */

$template = file_get_contents('templateAutoload.php');
$autoload = generateAutoload('controls');
$autoload .= generateAutoload('painter');
$result = str_replace('[autoload]', $autoload, $template);
$file = 'autoload.php';
file_put_contents($file, $result);

function generateAutoload($dir) {
	$fileList = array();
	listFiles($dir,$fileList);
	asort($fileList);
	$dirName = '';
	$autoload = '';
	foreach ($fileList as $fileName) {
		if (strpos($fileName, 'php') !== false) {
			$info = pathinfo($fileName);
	        if ($dirName != $info['dirname']) {
		        $autoload .= "\n/* " . $info['dirname'] . " */ \n";
			    $dirName = $info['dirname'];
			}
			$autoload .= "    '{$info['filename']}' => 'ui/{$info['dirname']}/{$info['basename']}',\n";
		}
	}
	return $autoload;
}

function listFiles($dir, &$result = array()) {
    if (is_dir($dir)) {
        $thisdir = dir($dir);
        while ($entry = $thisdir->read()) {
            if (($entry != '.') && ($entry != '..') && (substr($entry, 0, 1) != '.')) {
                $isFile = is_file("$dir/$entry");
                $isDir = is_dir("$dir/$entry");
                if ($isDir) {
                    listFiles("$dir/$entry",$result);
                } elseif ($isFile) {
                    $result[] = "$dir/$entry";
                }
            }
        }
    }
}
?>

