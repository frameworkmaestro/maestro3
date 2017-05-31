<?php

/**
 */
class MTemplateLocator {
    public static function fetch(MTemplate $template, $folder, $file) {

        $templateEngine = Manager::getOptions('templateEngine') ?: 'smarty';
        if ($templateEngine == 'smarty') {
            $path = self::buildPath($folder, $file);
            if (self::appTemplateExists($path)) {
                $template->engine->setTemplateDir(self::getAppTemplatePath());
            }
            return $template->fetch($path);
        }
        if ($templateEngine == 'latte') {
            $folder = Manager::getThemePath() . '/templates/' . $folder;
            $template->setPath($folder);
            return $template->fetch($file);
        }

    }

    private static function buildPath($folder, $file) {
        $language = Manager::getOptions('language');
        $ds = DIRECTORY_SEPARATOR;
        return "$folder$ds$language$ds$file";
    }

    private static function appTemplateExists($path) {
        $file = self::getAppTemplatePath() . DIRECTORY_SEPARATOR . $path;
        return file_exists($file);
    }

    private static function getAppTemplatePath() {
        return  Manager::getThemePath() . DIRECTORY_SEPARATOR . 'templates';
    }

}