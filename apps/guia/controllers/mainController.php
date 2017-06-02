<?php

class MainController extends MController {

    public function main() {
        $this->render();
    }

    public function menu() {
        $actions = Manager::getActions('guia');
        $array = [];
        $primary = [];
        foreach ($actions as $i => $group) {
            $primary[$i] = [$i, $group[ACTION_CAPTION], $group[ACTION_PATH]];
            $groupActions = $group[ACTION_ACTIONS];
            $j = 0;
            foreach($groupActions as $action){
                if (is_array($action[ACTION_ACTIONS])) {
                    foreach($action[ACTION_ACTIONS] as $internalAction){
                        $array[$i][$j] = [$j, $internalAction[ACTION_CAPTION], $internalAction[ACTION_PATH]];
                        $j++;
                    }
                }
            }
        }
        $this->data->primary = $primary;
        $this->data->menu = $array;
        mdump($primary);
        mdump($array);
        $this->render();
    }

}