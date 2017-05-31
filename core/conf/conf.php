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
    'name' => 'Framework Maestro3',
    // preloading 'log' component
    'preload' => array('log'),
    'theme' => array(
        'name' => 'default',
        'template' => 'index'        
    ),
    'options' => array(
        'startup' => 'guia',
        'dbsession' => false,
        'debug' => true,
        'charset' => 'UTF-8',
        'timezone' => "America/Sao_Paulo",
        'separatorDate' => '/',
        'formatDate' => 'd/m/Y',
        'formatTimestamp' => 'd/m/Y H:i:s',
        'csv' => ';',
        'mode' => 'DEV',
        'painter' => 'dojo',
        'dispatch' => 'index.php',
        'varPath' => Manager::getHome() . '/core/var/tmp',
        'javaPath' => '/opt/java',
        'javaBridge' => 'http://localhost:8080/JavaBridge/java/Java.inc',
        'language' => 'pt_br',
        'locale' => array("pt_BR.utf8", "ptb") // no linux verificar os locales instalados com "locale -a"
    ),
    'mad' => array(
        'module' => "auth",
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
        'check' => true,
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
        'tag' => 'auriga',
        'path' => Manager::getHome() . '/core/var/log',
        'level' => 2,
        'handler' => "socket",
        'peer' => '200.131.19.163',
        //'strict' => '127.0.0.1',
        'port' => 9998,
        'errorCodes' => array(
            E_ERROR,
            E_WARNING,
            E_PARSE,
            E_RECOVERABLE_ERROR,
            E_USER_ERROR,
            E_COMPILE_ERROR,
            E_CORE_ERROR
        )
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
    'mailer' => array(
        'smtpServer' => 'localhost',
        'smtpFrom' => 'maestro@maestro.org',
        'smtpFromName' => 'Framework Maestro',
        'smtpAuthUser' => '',
        'smtpAuthPass' => '',
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
