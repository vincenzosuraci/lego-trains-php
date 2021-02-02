<?php

require_once __DIR__.'/button.class.php';
require_once __DIR__.'/controller.class.php';
require_once __DIR__.'/sound.class.php';

class LegoTrain {

    const PF = 2007;
    const PU = 2016;

    const PF_NAME = 'Power Functions';
    const PU_NAME = 'Powered Up';

    const PF_CHANNEL_1 = 0;
    const PF_CHANNEL_2 = 1;
    const PF_CHANNEL_3 = 2;
    const PF_CHANNEL_4 = 3;

    const PF_MOTOR_MAX = 7;
    const PU_MOTOR_MAX = 10;

    const PF_OUTPUT_A = 0;
    const PF_OUTPUT_RED = 0;
    const PF_OUTPUT_B = 1;
    const PF_OUTPUT_BLU = 1;

    const PU_PORT_A = 0;
    const PU_PORT_B = 1;

    protected static $PF_OUTPUTS = array(
        self::PF_OUTPUT_RED => 'Red',
        self::PF_OUTPUT_BLU => 'Blu',
    );

    protected static $PU_PORTS = array(
        self::PU_PORT_A => 'A',
        self::PU_PORT_B => 'B',
    );

    protected static $PF_CHANNELS = array(
        self::PF_CHANNEL_1 => 1,
        self::PF_CHANNEL_2 => 2,
        self::PF_CHANNEL_3 => 3,
        self::PF_CHANNEL_4 => 4,
    );

    protected static $SYSTEMS = array(
        self::PF => array(
            'name' => self::PF_NAME,
            'img' => 'lego-system-PF.jpg',
            'technology' => 'Infrared',
            'keywords' => array('channel', 'output', 'value'),
        ),
        self::PU => array(
            'name' => self::PU_NAME,
            'img' => 'lego-system-PU.jpg',
            'technology' => 'Bluetooth',
            'keywords' => array('addr', 'port', 'value'),
        ),
    );

    protected static $SETS = array(
        '60197' => array(
            'theme' => 'City',
            'name' => 'Treno passeggeri',
            'system' => self::PU
        ),
        '60051' => array(
            'theme' => 'City',
            'name' => 'Treno passeggeri alta velocità',
            'system' => self::PF
        ),
        '60052' => array(
            'theme' => 'City',
            'name' => 'Treno merci',
            'system' => self::PF
        ),
        '60098' => array(
            'theme' => 'City',
            'name' => 'Treno trasporto pesante',
            'system' => self::PF
        ),
        '60198' => array(
            'theme' => 'City',
            'name' => 'Treno merci',
            'system' => self::PU
        ),
    );

    public static function getHtmlSelectTrainSet(
        $selected_train_set = '',
        $onchange = ''
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control" 
            data-type="config"
            data-id="set"
            data-old-value="' . $selected_train_set . '"
            aria-label="Select train set" 
            onchange="' . $onchange . '"
            >            
            <option value="">Custom</option>
        ';

        $sets = static::getSupportedTrainSets();
        foreach($sets as $train_set => $train_set_info) {
            $selected = ($selected_train_set == $train_set)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $train_set .'">(' . $train_set . ') ' . $train_set_info['name'] .'</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getHtmlSelectPowerFunctionsChannel(
        $selected_channel,
        $element,
        $onchange
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control"
            data-type="config"
            data-id="' . $element . '-' . static::PF. '-channel" 
            data-old-value="' . $selected_channel . '"
            aria-label="Select channel" 
            onchange="' . $onchange . '"
            >
        ';

        $channels = static::$PF_CHANNELS;
        foreach($channels as $channel => $channel_name) {
            $selected = ($selected_channel == $channel)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $channel .'">' . $channel_name .'</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getHtmlSelectPowerFunctionsOutput(
        $selected_output ,
        $element,
        $onchange
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control" 
            data-type="config"
            data-id="' . $element . '-' . static::PF . '-output"
            data-old-value="' . $selected_output . '"
            aria-label="Select output" 
            onchange="' . $onchange . '"
            >
        ';

        $outputs = static::$PF_OUTPUTS;
        foreach($outputs as $output => $output_name) {
            $selected = ($selected_output == $output)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $output .'">' . $output_name .'</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getHtmlSelectInverted(
        $selected_value,
        $element,
        $onchange
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control" 
            data-type="config"
            data-id="' . $element . '-inverted"
            data-old-value="' . $selected_value . '"             
            onchange="' . $onchange . '"
            >
        ';

        $value_names = array(
            0 => 'Straight',
            1 => 'Inverted',
        );
        foreach($value_names as $value => $value_name) {
            $selected = ($selected_value == $value)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $value .'">' . $value_name .'</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getHtmlInputPoweredUpAddress(
        $addr,
        $element,
        $onkeyup
    ) {
        $html = '';

        $html .= '
        <input 
            type="text"
            onkeyup="' . $onkeyup . '"                                  
            maxLength="17"
            pattern="[0-9A-Za-z]{1,17}"
            class="form-control" 
            data-type="config"
            data-id="' . $element . '-' . static::PU . '-addr"
            data-old-value="' . $addr . '"
            placeholder="xx:xx:xx:xx:xx:xx"             
            value="' . $addr . '"
            />
        ';

        return $html;
    }

    public static function getHtmlSelectPoweredUpPort(
        $selected_port,
        $element,
        $onchange
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control" 
            data-type="config"
            data-id="' . $element . '-' . static::PU . '-port"
            data-old-value="' . $selected_port . '"
            aria-label="Select port" 
            onchange="' . $onchange . '"
            >
        ';

        $ports = static::$PU_PORTS;
        foreach($ports as $port => $port_name) {
            $selected = ($selected_port == $port)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $port .'">' . $port_name .'</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getHtmlSelectTrainSystem(
        $selected_system,
        $element,
        $onchange
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control" 
            data-type="config"
            data-id="' . $element . '-system"
            data-old-value="' . $selected_system . '"
            aria-label="Select train system" 
            onchange="' . $onchange . '"
            >
            <option value="">---</option>
        ';

        $systems = static::getSupportedTrainSystems();
        foreach($systems as $system => $system_info) {
            $selected = ($selected_system == $system)
                ? ' selected'
                : '';
            $html .= '
            <option' . $selected . ' value="' . $system .'">' . $system_info['name'] .' (' . $system_info['technology'] . ')</option>
            ';
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public static function getSupportedTrainSets(){
        return static::$SETS;
    }

    public static function getSupportedTrainSystems(){
        return static::$SYSTEMS;
    }

    protected static function getHtmlSoundButtonGroup(
        $train_info
    ) {
        $html = '';

        if ( key_exists('sounds', $train_info)) {

            $html .= '
            <div class="btn-group mr-2" role="group">
            ';

            foreach ( $train_info['sounds'] as $sound => $audio ) {
                if (strlen($audio) > 0) {
                    $fa = $sound;
                    switch ($sound) {
                        case 'whistle':
                            $fa = 'bullhorn';
                            break;
                        case 'station':
                            $fa = 'clock';
                            break;
                        case 'passing':
                            $fa = 'train';
                            break;
                        case 'doors':
                            $fa = 'door-open';
                            break;
                    }
                    $html .= LegoTrainButton::getHtmlTrainSoundButton(
                        $audio,
                        $fa
                    );
                }
            }

            $html .= '            
            </div>
            ';
        }

        return $html;
    }

    public static function getHtmlTrainControlPanel(
        $controllers,
        $train_num,
        $train_info
    ){
        $train_set = key_exists('set', $train_info)
            ? $train_info['set']
            : null;

        $train_theme = key_exists('theme', $train_info)
            ? $train_info['theme']
            : null;

        $train_name = key_exists('name', $train_info)
            ? $train_info['name']
            : null;

        $train_description = key_exists('description', $train_info)
            ? $train_info['description']
            : null;

        $html = '';

        $html .= '
        <!--
        
        //--------------------------------------------------------------------------------------------------------------
        // Train #' . ($train_num+1) . '
        // Set: ' . $train_set . '        
        // Name: ' . $train_name . '
        // Theme: ' . $train_theme . '
        // Description: ' . $train_description . '
        //--------------------------------------------------------------------------------------------------------------
    
        -->
        ';

        $div_tags = '         
        data-type="control-train"
        data-num="' . $train_num . '"
        data-set="' . $train_set . '" 
        ';

        $buttons = '';
        $gauges = '';

        $buttons = '<div class="btn-toolbar" role="toolbar">';

        //-------------------------------------------------------------------------------------
        // TRAIN's ENGINE
        //-------------------------------------------------------------------------------------

        if (key_exists('engines', $train_info)) {

            $system = null;
            $urls = array();

            foreach ($train_info['engines'] as $engine_num => $engine_info) {

                if (key_exists('controller', $engine_info)) {

                    $controller = $engine_info['controller'];
                    $inverted = key_exists('inverted', $engine_info)
                        ? $engine_info['inverted']
                        : 0;

                    if (key_exists($controller, $controllers)) {

                        $controller_info = $controllers[$controller];

                        $url = $controller_info['protocol'] . '://' .
                            $controller_info['ipv4_address'] . ':' .
                            $controller_info['port'];

                        $system = $engine_info['system'];

                        if (
                            key_exists($system, $controller_info['commands'])
                        ) {
                            $path = $controller_info['commands'][$system];

                            foreach ($engine_info[$system] as $param => $value) {
                                $path = str_replace('{{' . $param . '}}', $value, $path);
                            }

                            $url .= $path;
                            $urls[$inverted][] = $url;
                        }
                    }
                }
            }

            $div_tags .= '
                data-engine-urls="' . htmlentities(json_encode($urls)) . '"
                data-engine-value="0" 
                ';

            $speeds = array();

            $buttons .= static::getHtmlSoundButtonGroup(
                $train_info
            );

            $buttons .=
                '<div class="btn-group mr-2" role="group">' .
                    LegoTrainButton::getHtmlPlusButton() .
                    LegoTrainButton::getHtmlStopButton() .
                    LegoTrainButton::getHtmlMinusButton() .
                '</div>';

            switch ($system) {
                case static::PF:
                    $speeds = array(
                        'max' => 7,
                        'high' => 5,
                        'medium' => 3,
                    );
                    $div_tags .= '
                    data-engine-max-value="7"
                    ';
                    break;
                case static::PU:
                    $speeds = array(
                        'max' => 10,
                        'high' => 8,
                        'medium' => 5,
                    );
                    $div_tags .= '
                    data-engine-max-value="10"
                    ';
                    break;
            }

            $gauges .= '
                <canvas 
                    data-type="gauge-speed" 
                    data-max-speed="' . $speeds['max'] . '" 
                    data-high-speed="' . $speeds['high'] . '" 
                    data-medium-speed="' . $speeds['medium'] . '"                     
                    class="gauge"
                    ></canvas>
                ';
        }

        if (key_exists('lights', $train_info)) {

            $buttons .= '<div class="btn-group" role="group">';

            //-------------------------------------------------------------------------------------
            // TRAIN's LIGHT
            //-------------------------------------------------------------------------------------

            $system = null;
            $urls = array(
                array()
            );
            foreach ($train_info['lights'] as $light_num => $light_info) {

                if (key_exists('controller', $light_info)) {

                    $controller = $light_info['controller'];

                    if (key_exists($controller, $controllers)) {

                        $controller_info = $controllers[$controller];

                        $url = $controller_info['protocol'] . '://' .
                            $controller_info['ipv4_address'] . ':' .
                            $controller_info['port'];

                        $system = $light_info['system'];

                        if (
                            key_exists($system, $controller_info['commands'])
                        ) {
                            $path = $controller_info['commands'][$system];

                            foreach ($light_info[$system] as $param => $value) {
                                $path = str_replace('{{' . $param . '}}', $value, $path);
                            }

                            $url .= $path;
                            $urls[0][] = $url;

                        }
                    }
                }

                $div_tags .= '
                    data-light-' . $light_num . '-urls="' . htmlentities(json_encode($urls)) . '"
                    data-light-' . $light_num . '-value="0" 
                    ';

                switch ($system) {
                    case static::PF:
                        $div_tags .= '
                            data-light-' . $light_num . '-max-value="7"
                            ';
                        break;
                    case static::PU:
                        $div_tags .= '
                            data-light-' . $light_num . '-max-value="10"
                            ';
                        break;
                }

                if (count($urls[0]) > 0) {
                    $buttons .= LegoTrainButton::getHtmlLightButton(
                        $light_num
                    );
                }

            }

            $buttons .= '</div>';
        }

        $buttons .= '</div>';

        $html .= '
        <div    
            class="train-control-block"                                  
            ' . $div_tags . '
            >
            <div class="train-upper-block">
                <div class="train-block-img">
                    <img src="img/train/' . $train_set . '.jpg" class="train-img" alt="' . $train_set . '">
                </div>
                <div class="train-block-gauge">
                    ' . $gauges . '                
                </div>
                <div class="train-block-log">
                    <textarea data-type="log" class="log"></textarea>
                </div>
            </div>
            <div class="train-lower-block">
                <div style="train-block-buttons">' . $buttons . '</div>
            </div>                
        </div>
        ';

        return $html;
    }

    public static function getHtmlTableTrsTrainConfigurationPanelByType(
        $type,
        $fa,
        $num,
        $info,
        $controllers,
        $onchange,
        $add_inverted = true
    ){
        $html = '';

        $controller = isset($info['controller'])
            ? $info['controller']
            : null;
        $show_system = strlen($controller) > 0;
        $system = isset($info['system'])
            ? $info['system']
            : null;
        $show_pf_channel =
            $show_system &&
            $system == self::PF;
        $pf_channel = isset($info[LegoTrain::PF]['channel'])
            ? $info[LegoTrain::PF]['channel']
            : null;
        $show_pf_output =
            $show_system &&
            $system == self::PF;
        $pf_output = isset($info[LegoTrain::PF]['output'])
            ? $info[LegoTrain::PF]['output']
            : null;
        $show_pu_addr =
            $show_system &&
            $system == self::PU;
        $pu_addr = isset($info[LegoTrain::PU]['addr'])
            ? $info[LegoTrain::PU]['addr']
            : null;
        $show_pu_port =
            $show_system &&
            $system == self::PU;
        $pu_port = isset($info[LegoTrain::PU]['output'])
            ? $info[LegoTrain::PU]['output']
            : null;
        $show_inverted = strlen($system) > 0;
        $inverted = isset($info['inverted'])
            ? $info['inverted']
            : 0;

        $display_none_style = ' style="display: none;"';

        $html .= '
        <tr class="table-active">
            <th colspan="2"><span class="fas fa-' . $fa . '"></span>&nbsp;' . ucfirst($type) . ' #' . ($num+1) . '</th>           
        </tr>
        <tr 
        data-elem="' . $type . '"
        data-num="' . $num . '"
        data-type="controller"
        >
            <th><span class="fas fa-microchip"></span>&nbsp;Controller</th>
            <td>' .
            LegoController::getHtmlSelectController(
                $controllers,
                $controller,
                $type . 's-' . $num,
                $onchange
            ) . '
            </td>
        </tr>
        <tr 
        data-elem="' . $type . '"
        data-num="' . $num . '"
        data-type="system"        
        ' . ($show_system ? '' : $display_none_style) . '
        >
            <th><span class="fas fa-wifi"></span>&nbsp;Lego System</th>
            <td>' .
            static::getHtmlSelectTrainSystem(
                $system,
                $type . 's-' . $num,
                $onchange
            ) . '
            </td>
        </tr>
        <tr 
        data-elem="' . $type . '"
        data-num="' . $num . '"
        data-system="' . static::PF . '"
        data-type="channel"        
        ' . ($show_pf_channel ? '' : $display_none_style) . '
        >
            <th><span class="fas fa-arrows-alt-h"></span>&nbsp;Power Functions channel</th>
            <td>' .
            static::getHtmlSelectPowerFunctionsChannel(
                $pf_channel,
                $type . 's-' . $num,
                $onchange
            ) . '
            </td>
        </tr>
        <tr         
        data-elem="' . $type . '"
        data-num="' . $num . '"
        data-system="' . static::PF . '"
        data-type="output"
        ' . ($show_pf_output ? '' : $display_none_style) . '
        >
            <th><span class="fas fa-toggle-on"></span>&nbsp;Power Functions output</th>
            <td>' .
            static::getHtmlSelectPowerFunctionsOutput(
                $pf_output,
                $type . 's-' . $num,
                $onchange
            ) . '
            </td>
        </tr>
        <tr 
        data-elem="' . $type . '"
        data-num="' . $num . '"
        data-system="' . static::PU . '"
        data-type="addr"        
        ' . ($show_pu_addr ? '' : $display_none_style) . '
        >
            <th><span class="fas fa-network-wired"></span>&nbsp;Powered Up address</th>
            <td>' .
            static::getHtmlInputPoweredUpAddress(
                $pu_addr,
                $type . 's-' . $num,
                $onchange
            ) . '
            </td>
        </tr>
        <tr         
        data-elem="' . $type . '"
        data-num="' . $num . '"
        data-system="' . static::PU . '"
        data-type="port"
        ' . ($show_pu_port ? '' : $display_none_style) . '
        >
            <th><span class="fas fa-toggle-on"></span>&nbsp;Powered Up port</th>
            <td>' .
            static::getHtmlSelectPoweredUpPort(
                $pu_port,
                $type . 's-' . $num,
                $onchange
            ) . '
            </td>
        </tr>
        ';

        if ($add_inverted){
            $html .= '
            <tr 
            data-elem="' . $type . '"
            data-num="' . $num . '"        
            data-type="inverted"
            ' . ($show_inverted ? '' : $display_none_style) . '
            >
                <th><span class="fas fa-random"></span>&nbsp;Invert speed commands</th>
                <td>' .
                static::getHtmlSelectInverted(
                    $inverted,
                    $type . 's-' . $num,
                    $onchange
                ) . '
            </td>
            </tr>
            ';
        }

        return $html;
    }

    public static function getHtmlTableTrsTrainEngineConfigurationPanel(
        $engine_num,
        $engine_info,
        $controllers,
        $onchange
    ){
        $html = '';

        $html .= static::getHtmlTableTrsTrainConfigurationPanelByType(
            'engine',
            'cogs',
            $engine_num,
            $engine_info,
            $controllers,
            $onchange
        );

        return $html;
    }

    public static function getHtmlTableTrsTrainLightConfigurationPanel(
        $light_num,
        $light_info,
        $controllers,
        $onchange
    ){
        $html = '';

        $html .= static::getHtmlTableTrsTrainConfigurationPanelByType(
            'light',
            'lightbulb',
            $light_num,
            $light_info,
            $controllers,
            $onchange,
            false
        );

        return $html;
    }

    public static function getHtmlTableTrainConfigurationPanel(
        $controllers,
        $train_num,
        $train_info,
        $is_first = false,
        $is_last = false
    ) {
        $train_set = key_exists('set', $train_info)
            ? $train_info['set']
            : null;
        $train_name = key_exists('name', $train_info)
            ? $train_info['name']
            : null;
        $train_description = key_exists('description', $train_info)
            ? $train_info['description']
            : null;

        $onkeyup = 'train_config_changed(this);';
        $onchange = 'train_config_changed(this);';

        $buttons = array(
            LegoTrainButton::getHtmlSaveConfigButton('display:none;'),
            LegoTrainButton::getHtmlDeleteTrainButton()
        );

        if (!$is_first){
            $buttons[] = LegoTrainButton::getHtmlMoveTrainUpButton();
        }

        if (!$is_last){
            $buttons[] = LegoTrainButton::getHtmlMoveTrainDownButton();
        }

        $html = '';

        $html .= '               
          <div 
            class="card card-margin-bottom" 
            data-type="config-train"
            data-num="' . $train_num . '"
            >
            <a id="anchor-train-num-' . $train_num . '"></a>
            <div class="card-header text-white bg-success">
                <span style="float: left;">
                    <span class="fas fa-train"></span>&nbsp;<b>Train #' . ($train_num+1) . '</b>
                </span>    
                <span style="float: right;">
                    ' . LegoTrainButton::getHtmlCardOpenCloseButton('success btn-sm') . '
                </span>
            </div>
            <div class="card-body">
        ';

        $html .= '
        <table class="table" data-type="train-config">
            <thead>
                <tr class="table-success">
                <th>Key</th>
                <th>Value</th>
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
                        data-old-value="' . $train_name . '" 
                        value="' . $train_name . '"
                        ></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><textarea 
                        onkeyup="' . $onkeyup . '"
                        data-type="config" 
                        data-id="description" 
                        data-old-value="' . htmlentities($train_description) . '" 
                        class="form-control"
                        >' . $train_description . '</textarea>
                    </td>
                </tr>
                <tr>
                    <th>Lego Set</th>
                    <td>' .
                        static::getHtmlSelectTrainSet(
                        $train_set,
                        $onchange
                        ) . '
                    </td>
                </tr>
          ';

        $train_sound_whistle = isset($train_info['sounds']['whistle'])
            ? $train_info['sounds']['whistle']
            : '';
        $train_sound_passing = isset($train_info['sounds']['passing'])
            ? $train_info['sounds']['passing']
            : '';
        $train_sound_station = isset($train_info['sounds']['station'])
            ? $train_info['sounds']['station']
            : '';
        $train_sound_doors = isset($train_info['sounds']['doors'])
            ? $train_info['sounds']['doors']
            : '';

        $html .= '
        <tr class="table-active">
            <th colspan="2"><span class="fas fa-volume-up"></span>&nbsp;Sounds</th>           
        </tr> 
        <tr>
            <th><span class="fas fa-bullhorn"></span>&nbsp;Whistle</th>
            <td>' .
            LegoTrainSound::getHtmlSelectSound(
                'whistle',
                $train_sound_whistle,
                $onchange
            ) . '
            </td>
        </tr>
        <tr>
            <th><span class="fas fa-train"></span>&nbsp;Passing the train</th>
            <td>' .
            LegoTrainSound::getHtmlSelectSound(
                'passing',
                $train_sound_passing,
                $onchange
            ) . '
            </td>
        </tr>
        <tr>
            <th><span class="fas fa-clock"></span>&nbsp;Train at station</th>
            <td>' .
            LegoTrainSound::getHtmlSelectSound(
                'station',
                $train_sound_station,
                $onchange
            ) . '
            </td>
        </tr>
        <tr>
            <th><span class="fas fa-door-open"></span>&nbsp;Train doors</th>
            <td>' .
            LegoTrainSound::getHtmlSelectSound(
                'doors',
                $train_sound_doors,
                $onchange
            ) . '
            </td>
        </tr>       
        ';

        if (!key_exists('engines', $train_info)) {
            $train_info['engines'] = array(
                array(),
            );
        }
        foreach($train_info['engines'] as $engine_num => $engine_info) {
            $html .= static::getHtmlTableTrsTrainEngineConfigurationPanel(
                $engine_num,
                $engine_info,
                $controllers,
                $onchange
            );
        }


        if (!key_exists('lights', $train_info)) {
            $train_info['lights'] = array(
                array(),
            );
        }
        foreach($train_info['lights'] as $light_num => $light_info) {
            $html .= static::getHtmlTableTrsTrainLightConfigurationPanel(
                $light_num,
                $light_info,
                $controllers,
                $onchange
            );
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