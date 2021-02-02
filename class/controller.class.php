<?php

require_once __DIR__.'/button.class.php';
require_once __DIR__.'/train.class.php';

class LegoController {

    public static function from_csv_to_array($csv) {
        $array = array();
        $elems = explode(',', $csv);
        foreach($elems as $elem) {
            $elem = trim($elem);
            if (strlen($elem) > 0){
                $array[] = $elem;
            }
        }
        return $array;
    }

    public static function getHtmlSelectController(
        $controllers,
        $csv_selected_controllers,
        $element,
        $onchange
    ){
        $html = '';

        $selected_controllers = static::from_csv_to_array(
            $csv_selected_controllers
        );

        $html .= '
        <select 
            class="form-control"
            data-type="config"
            data-id="' . $element . '-controller" 
            data-old-value="' . $csv_selected_controllers . '"
            aria-label="Select controller" 
            onchange="' . $onchange . '"
            >            
            <option value="">---</option>
        ';

        foreach($controllers as $controller => $controller_info) {
            $selected = in_array($controller, $selected_controllers)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $controller .'">' . $controller_info['name'] .'</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getHtmlControllerConfigurationPanel(
        $controller_num,
        $controller_info,
        $is_first = false,
        $is_last = false
    ) {
        $controller_name = key_exists('name', $controller_info)
            ? $controller_info['name']
            : null;
        $controller_description = key_exists('description', $controller_info)
            ? $controller_info['description']
            : null;
        $controller_ipv4_address = key_exists('ipv4_address', $controller_info)
            ? $controller_info['ipv4_address']
            : null;
        $controller_port = key_exists('port', $controller_info)
            ? $controller_info['port']
            : 80;
        $controller_protocol = key_exists('protocol', $controller_info)
            ? $controller_info['protocol']
            : 'http';
        $controller_commands = key_exists('commands', $controller_info)
            ? $controller_info['commands']
            : array();

        $onkeyup = 'controller_config_changed(this);';

        $buttons = array(
            LegoTrainButton::getHtmlSaveConfigButton('display:none;'),
            LegoTrainButton::getHtmlDeleteControllerButton()
        );

        if (!$is_first){
            $buttons[] = LegoTrainButton::getHtmlMoveControllerUpButton();
        }

        if (!$is_last){
            $buttons[] = LegoTrainButton::getHtmlMoveControllerDownButton();
        }

        $html = '';

        $html .= '        
        <div 
            class="card card-margin-bottom"
            data-type="config-controller"
            data-controller-num="' . $controller_num . '"
            >     
            <a id="controller-num-' . $controller_num . '" data-controller-num="' . $controller_num . '"></a>     
            <div class="card-header text-white bg-danger">
                <span style="float: left;">
                    <span class="fas fa-microchip"></span>&nbsp;<b>Controller #' . $controller_num . '</b>
                </span>    
                <span style="float: right;">
                    ' . LegoTrainButton::getHtmlCardOpenCloseButton('danger btn-sm') . '
                </span>
            </div>
            <div class="card-body">
        ';

        $html .= '
                <table class="table" data-type="controller-config">
                 <thead>
                  <tr class="table-danger">
                   <th>Key</th>
                   <th style="width: 100%;">Value</th>
                  </tr>
                 </thead>
                 <tbody>          
                  <tr>
                   <th>Name</th>
                   <td><input 
                    onkeyup="' . $onkeyup . '"
                    data-type="config" 
                    data-id="name" 
                    class="form-control" 
                    data-old-value="' . $controller_name . '" 
                    value="' . $controller_name . '"
                    ></td>
                  </tr>          
                  <tr>
                   <th>Description</th>
                   <td><textarea 
                    onkeyup="' . $onkeyup . '" 
                    data-type="config"
                    data-id="description" 
                    data-old-value="' . htmlentities($controller_description) . '" 
                    class="form-control"
                    >' . $controller_description . '</textarea></td>
                  </tr>
                  <tr>
                   <th>Ipv4 address</th>
                   <td><input 
                    onkeyup="' . $onkeyup . '" 
                    data-type="config"
                    data-id="ipv4_address" 
                    class="form-control" 
                    data-old-value="' . $controller_ipv4_address . '" 
                    value="' . $controller_ipv4_address . '" 
                    minlength="7" 
                    maxlength="15" 
                    size="15" 
                    placeholder="xxx.xxx.xxx.xxx"
                    pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$"
                    ></td>
                  </tr> 
                  <tr>
                   <th>Port</th>
                   <td><input 
                    type="number"
                    min="1"
                    max="65535"
                    onkeyup="' . $onkeyup . '" 
                    data-type="config"
                    data-id="port" 
                    class="form-control" 
                    data-old-value="' . $controller_port . '" 
                    value="' . $controller_port . '"             
                    ></td>
                  </tr>
                  <tr>
                   <th>Protocol</th>
                   <td><input 
                    onkeyup="' . $onkeyup . '"                     
                    data-type="config"
                    data-id="protocol" 
                    class="form-control" 
                    data-old-value="' . $controller_protocol . '" 
                    value="' . $controller_protocol . '" 
                    placeholder="e.g. http, https"            
                    ></td>
                  </tr>
          ';

        foreach(LegoTrain::getSupportedTrainSystems() as $system => $system_info) {

            $controller_command = key_exists($system, $controller_commands)
                ? $controller_commands[$system]
                : null;

            $html .= '
                    <tr>
                     <th><img alt="' . $system_info['name'] . '" width="100" src="img/system/' . $system_info['img'] . '"></th>
                     <td><input 
                        onkeyup="' . $onkeyup . '"
                        data-type="config" 
                        data-id="commands-' . $system . '" 
                        class="form-control" 
                        data-old-value="' . $controller_command . '" 
                        value="' . $controller_command . '" 
                        placeholder="keywords: {{' . implode('}}, {{', $system_info['keywords']) . '}}"            
                        ></td>
                    </tr>
            ';
        }

        $html .= '
                 </tbody>
                 <tfoot>         
                 </tfoot>
                </table>         
        ' . implode('&nbsp;', $buttons) . '
        
        ';

        $html .= '                                
            </div>            
        </div>                                                
        ';

        return $html;
    }


}