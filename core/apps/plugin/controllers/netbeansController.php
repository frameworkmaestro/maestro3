<?php

class NetBeansController extends MController {
    
    public function pluginButton(){       
        Manager::getPage()->setTemplateName('plugin');        
        $fileInfo = pathinfo($this->data->filePath);                
        $filePath = $this->data->filePath;        
        do {
            $x = strpos($filePath, 'apps');
            if ($x !== false) {
                $filePath = substr($filePath, $x+5);
            }
        } while ($x !== false);
        $filePath = str_replace('/modules','',$filePath);                                
        $context = new MContext($filePath);        
        $controller = Manager::getController($context->getApp(), $context->getModule(), $context->getController(),$context);        
        $controller->render($fileInfo['filename']);                
    }
}

?>
