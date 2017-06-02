<?php

class MainController extends MController {

    public function main() {
        $this->render();
    }

    public function menu() {
        $actions = Manager::getActions('guia');
        $primary = [];
        $secondary = [];
        $tertiary = [];
        foreach ($actions as $i => $group) {
            $primary[$i] = [$i, $group[ACTION_CAPTION], $group[ACTION_PATH]];
            foreach($group[ACTION_ACTIONS] as $j => $action){
                $secondary[$i][$j] = [$j, $action[ACTION_CAPTION], $action[ACTION_PATH]];
                if (is_array($action[ACTION_ACTIONS])) {
                    foreach($action[ACTION_ACTIONS] as $k => $internalAction){
                        $tertiary[$i][$j][$k] = [$k, $internalAction[ACTION_CAPTION], $internalAction[ACTION_PATH]];
                    }
                }
            }
        }
        $this->data->primary = $primary;
        $this->data->secondary = $secondary;
        $this->data->tertiary = $tertiary;
        $this->render();
    }

}