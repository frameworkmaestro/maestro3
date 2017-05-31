<?php

/**
 * Created by PhpStorm.
 * User: diego
 * Date: 01/02/16
 * Time: 15:31
 */
class MNotification
{
    private $infos = [];
    private $warnings = [];
    private $errors = [];

    public function addError($message)
    {
        $this->errors[] = $message;
    }

    public function addInfo($message)
    {
        $this->infos[] = $message;
    }

    public function addWarning($message)
    {
        $this->warnings[] = $message;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function hasInfos()
    {
        return count($this->infos) > 0;
    }

    public function hasWarnings()
    {
        return count($this->warnings) > 0;
    }

    public function getErrors($glue = ', ', $prefix = '')
    {
        return $this->implodeNotifications($this->errors, $glue, $prefix);
    }

    public function getInfos($glue = ', ', $prefix = '')
    {
        return $this->implodeNotifications($this->infos, $glue, $prefix);
    }

    public function getWarnings($glue = ', ', $prefix = '')
    {
        return $this->implodeNotifications($this->warnings, $glue, $prefix);
    }

    /**
     * @param $glue
     * @return string
     */
    private function implodeNotifications($notifications, $glue = ', ', $prefix = '')
    {
        if (!empty($prefix)) {
            array_walk($notifications, array($this, 'applyPrefix'), $prefix);
        }

        return implode($glue, $notifications);
    }

    private function applyPrefix(&$item, $key, $prefix)
    {
        $item = "{$prefix} {$item}";
    }
}