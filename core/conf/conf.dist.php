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

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Maestro Application',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'models.*'
    ),
    'theme' => array(
        'name' => 'default',
        'template' => 'index'
    ),
    'options' => array(
        'startup' => 'guia',
        'dbsession' => false,
        'authmd5' => true,
        'debug' => true,
        'charset' => 'UTF-8',
        'timezone' => "America/Sao_Paulo",
        'formatDate' => 'd/m/Y',
        'formatTimestamp' => 'd/m/Y H:i:s',
        'mode' => 'DEV',
        'painter' => 'html',
        'dispatch' => 'index.php',
        'language' => 'pt_br',
        'locale' => array("pt_BR", "ptb")
    ),
    'mad' => array(
        'module' => "common",
        'access' => "acesso",
        'group' => "grupo",
        'log' => "log",
        'session' => "sessao",
        'transaction' => "transacao",
        'user' => "usuario"
    ),
    'login' => array(
        'module' => "",
        'class' => "MAuthDbMd5",
        'check' => false,
        'shared' => true,
        'auto' => false
    ),
    'session' => array(
        'handler' => "file",
        'timeout' => "30",
        'exception' => false,
        'check' => true
    ),
    'logs' => array(
        'level' => 2,
        'handler' => "socket",
        'peer' => '127.0.0.1',
        'strict' => '127.0.0.1',
        'port' => 0,
    ),
    'cache' => array(
        'type' => "php", // php, java, apc, memcache
        'memcache' => array(
            'host' => "127.0.0.1",
            'port' => "11211",
            'default.ttl' => 0
        ),
        'apc' => array(
            'default.ttl' => 0
        )
    ),
    'extensions' => array(
    ),
    'db' => array(
        'manager' => array(
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'dbname' => 'exemplos',
            'user' => 'postgres',
            'password' => 'pgadmin',
            'formatDate' => 'DD/MM/YYYY',
            'formatDateWhere' => 'YYYY/MM/DD',
            'formatTime' => 'HH24:MI:SS',
            'configurationClass' => 'Doctrine\DBAL\Configuration',
        ),
    ),
);
