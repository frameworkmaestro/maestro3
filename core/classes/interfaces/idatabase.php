<?php

interface IDataBase
{
    public function __construct($name);

    public function newConnection();

    public function getConnection();

    public function getConfig($key);

    public function getName();

    public function getPlatform();

    public function getORMLogger();

    public function getTransaction();

    public function lastInsertId();

    public function beginTransaction();

    public function getSQL($columns, $tables, $where, $orderBy, $groupBy, $having, $forUpdate);

    public function execute(database\MSQL $sql, $parameters);

    public function executeBatch($sqlArray);

    public function executeCommand($command, $parameters);

    public function count(database\MQuery $query);

    public function getNewId($sequence);

    public function prepare(database\MSQL $sql);

    public function query(database\MSQL $sql);

    public function executeQuery($command, $parameters, $page, $rows);

    public function getQueryCommand($command);

    public function getQuery(database\MSQL $sql);

    public function getTable($tableName);

    public function executeProcedure($sql, $aParams, $aResult);
}
