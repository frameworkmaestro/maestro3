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

return [
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Framework Maestro3',
    // preloading 'log' component
    'preload' => [
        'log'
    ],
    'theme' => [
        'name' => 'default',
        'template' => 'index'
    ],
    'options' => [
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
    ],
    'mad' => [
        'module' => "common",
        'access' => "acesso",
        'group' => "grupo",
        'log' => "log",
        'session' => "sessao",
        'transaction' => "transacao",
        'user' => "usuario"
    ],
    'login' => [
        'module' => "",
        'class' => "MAuthDbMd5",
        'check' => false,
        'shared' => true,
        'auto' => false
    ],
    'session' => [
        'handler' => "file",
        'timeout' => "30",
        'exception' => false,
        'check' => true
    ],
    'logs' => [
        'level' => 2,
        'handler' => "socket",
        'peer' => '127.0.0.1',
        'strict' => '127.0.0.1',
        'port' => 0,
    ],
    'cache' => [
        'type' => "php", // php, java, apc, memcache
        'memcache' => [
            'host' => "127.0.0.1",
            'port' => "11211",
            'default.ttl' => 0
        ],
        'apc' => [
            'default.ttl' => 0
        ]
    ],
    'mailer' => [
        'smtpServer' => 'localhost',
        'smtpFrom' => 'maestro@maestro.org',
        'smtpFromName' => 'Framework Maestro',
        'smtpAuthUser' => '',
        'smtpAuthPass' => '',
    ],
    'extensions' => [
    ],
    'db' => [
        'manager' => [
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'dbname' => 'exemplos',
            'user' => 'postgres',
            'password' => 'pgadmin',
            'formatDate' => 'DD/MM/YYYY',
            'formatDateWhere' => 'YYYY/MM/DD',
            'formatTime' => 'HH24:MI:SS',
            'configurationClass' => 'Doctrine\DBAL\Configuration',
        ],
    ],
];
