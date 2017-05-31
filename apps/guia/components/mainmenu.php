<?php

class MainMenu extends MAccordion {

    public function onCreate() {
        parent::onCreate();
        $this->setId('guiaMainMenu');

        $actions = Manager::getActions('guia');
        foreach ($actions as $i => $group) {
            $baseGroup = new MBaseGroup("menu{$i}", $group[0]);
            $baseGroup->setFieldSet(false);

            $tree = new MTree("tree{$i}");
            $groupActions = $group[ACTION_ACTIONS];
            $array = array();
            $j = 0;
            foreach($groupActions as $action){
                $array[$j] = array($j, $action[ACTION_CAPTION], $action[ACTION_PATH], 'root');
                if (is_array($action[ACTION_ACTIONS])) {
                    $k = $j;
                    foreach($action[ACTION_ACTIONS] as $internalAction){
                        $j++;
                        $array[$j] = array($j, $internalAction[ACTION_CAPTION], $internalAction[ACTION_PATH], $k);
                    }
                }
                $j++;
            }
            $tree->setItemsFromArray($array);
            $baseGroup->addControl($tree);
            $this->addControl($baseGroup);
        }
    }

}

?>