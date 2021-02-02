<?php

if (key_exists('command', $_REQUEST)){
    $command = $_REQUEST['command'];
    switch ($command) {
        case 'load_control_panel':
            require_once __DIR__.'/../class/setup.class.php';
            $train_setup = new LegoTrainSetup();
            echo $train_setup->getHtmlControlPanelContent();
            break;
        case 'save_configuration':
            require_once __DIR__.'/../class/setup.class.php';
            $config = $_REQUEST['config'];
            LegoTrainSetup::saveConfiguration(
                $config
            );
            break;
        case 'add_new_controller':
            require_once __DIR__.'/../class/controller.class.php';
            $is_first=$_REQUEST['is_first'];
            $is_last=$_REQUEST['is_first'];
            $num=$_REQUEST['num'];
            echo LegoController::getHtmlControllerConfigurationPanel(
                $num,
                array(),
                $is_first,
                $is_last
            );
            break;
        case 'add_new_train':
            require_once __DIR__.'/../class/train.class.php';
            require_once __DIR__.'/../class/setup.class.php';
            $is_first=$_REQUEST['is_first'];
            $is_last=$_REQUEST['is_first'];
            $num=$_REQUEST['num'];
            echo LegoTrain::getHtmlTableTrainConfigurationPanel(
                LegoTrainSetup::getControllers(),
                $num,
                array(),
                $is_first,
                $is_last
            );
            break;
        case 'connect_power_up':
            echo 'Connect power up >>> I\'ll do it!';
            break;
        case 'execute_actions':
            if (key_exists('urls', $_REQUEST)) {
                foreach($_REQUEST['urls'] as $url) {
                    file_get_contents($url);
                    echo date('Y-m-d H:i:s') . ' >>> ' . $url . '<br/>';
                }
            }
            break;
        default:
            echo 'Command ' . $command . ' unsupported';
            break;
    }
} else {
    echo 'No commad found';
}