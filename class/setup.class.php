<?php

require_once __DIR__.'/train.class.php';
require_once __DIR__.'/controller.class.php';

class LegoTrainSetup {

    protected $name = null;
    protected $trains = array();
    protected $controllers = array();

    public function __construct(
        $config = null
    ) {

        if ( $config !== null ) {
            static::saveConfiguration(
                $config
            );
        } else {
            $config =
                static::getLastConfiguration();
        }

        $this->trains = array();
        $this->controllers = array();

        $this->name = key_exists('name', $config)
            ? $config['name']
            : static::getDefaultConfigurationName();

        //--------------------------------------------------------------------------------------------------------------
        // Carichiamo i controllori, se presenti
        //--------------------------------------------------------------------------------------------------------------

        if (key_exists('controllers', $config)) {

            $this->controllers = $config['controllers'];

        }

        //--------------------------------------------------------------------------------------------------------------
        // Carichiamo i treni, se presenti
        //--------------------------------------------------------------------------------------------------------------

        if (key_exists('trains', $config)) {

            $trains = $config['trains'];

            $supported_train_sets = LegoTrain::getSupportedTrainSets();

            foreach($trains as $train_num => $train_info){

                //---------------------------------------------------------------------------------
                // Recupero del SET del treno
                $train_set = key_exists('set', $train_info)
                    ? $train_info['set']
                    : null;
                $train_info['set'] = $train_set;

                $train_set_is_known =
                    key_exists($train_set, $supported_train_sets);

                //---------------------------------------------------------------------------------
                // Recupero del nome del treno
                $train_name = key_exists('name', $train_info)
                    ? $train_info['name']
                    : ( $train_set_is_known
                        ? $supported_train_sets[$train_set]['name']
                        : null
                    );
                $train_info['name'] = $train_name;

                //---------------------------------------------------------------------------------
                // Recupero del tema del tremo
                $train_theme = key_exists('theme', $train_info)
                    ? $train_info['theme']
                    : ( $train_set_is_known
                        ? $supported_train_sets[$train_set]['theme']
                        : null
                    );
                $train_info['theme'] = $train_theme;

                //---------------------------------------------------------------------------------
                // Recupero della descrizione del treno
                $train_description = key_exists('description', $train_info)
                    ? $train_info['description']
                    : null;
                $train_info['description'] = $train_description;

                //---------------------------------------------------------------------------------
                // Motori

                if (!key_exists('engines', $train_info)) {
                    $train_info['engines'] = array(
                        array(),
                    );
                }

                //---------------------------------------------------------------------------------
                // Luci

                if (!key_exists('lights', $train_info)) {
                    $train_info['lights'] = array(
                        array(),
                    );
                }

                $this->trains[$train_num] = $train_info;
            }
        }
    }

    public static function getControllers() {
        return static::getLastConfiguration()['controllers'];
    }

    public function getHtmlControlPanel(
        $hide = false
    ) {
        $html = '';

        $html .= '
        <div 
            data-type="panel" 
            data-id="control"' .
            ($hide ? ' style="display:none;"' : '') .
            '>
        ';

        $html .= static::getHtmlControlPanelContent();

        $html .= '
        </div>
        ';

        return $html;
    }

    public function getHtmlSounds() {
        return LegoTrainSound::getHtmlAudio();
    }

    public function getHtmlControlPanelContent() {
        $html = '';

        $controllers = static::getControllers();

        foreach ($this->trains as $train_num => $train_info) {
            $html .= LegoTrain::getHtmlTrainControlPanel(
                $controllers,
                $train_num,
                $train_info
            );
        }

        return $html;
    }

    protected function getHtmlSelectConfigurations(
        $selected_configuration = ''
    ) {
        $html = '';

        $html .= '
        <select class="form-control" aria-label="Select configuration">
        ';

        $dir = static::getConfigDir();
        $filenames = scandir($dir);
        foreach($filenames as $filename){
            if ($filename !== '.' && $filename !== '..'){
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if ($ext == 'json'){
                    $needle = 'setup';
                    if (strlen($filename) >= strlen($needle)){
                        if ( substr($filename, 0, strlen($needle)) == $needle ){
                            $configuration_name = substr(
                                $filename,
                                strlen($needle)+1,
                                -strlen($ext)-1
                            );
                            $selected = $selected_configuration === $filename
                                ? ' selected'
                                : '';
                            $html .= '
                            <option' . $selected . ' value="' . $filename .'">' . $configuration_name .'</option>
                            ';
                        }
                    }
                }
            }
        }

        $html .= '
        </select>
        ';

        return $html;
    }

    public function getHtmlConfigurationPanel(
        $hide = false
    ) {
        $html = '';

        $html .= '
        <div 
            data-type="panel" 
            data-id="configuration" ' .
            ($hide ? ' style="display:none;"' : '') .
            '>
        ';

        //-----------------------------------------------------------------------------------------
        // SETUPS
        //-----------------------------------------------------------------------------------------

        $onkeyup = 'configuration_name_changed(this);';

        $html .= '
        <div data-type="configurations">
                                                                  
            <div class="config-panel">
                <div class="card" data-type="configuration">
                    <div class="card-header text-white bg-primary">
                        <h2><span class="fas fa-wrench"></span>&nbsp;Configuration</h2>
                    </div>
                     <div class="card-body">  
                      <p>
                       ' . static::getHtmlSelectConfigurations() . '
                         &nbsp;' . LegoTrainButton::getHtmlLoadConfigurationButton('display: none;') . '
                      </p>
                                                                                                                   
                      <table class="table" data-type="train-config">
                       <thead>
                        <tr class="table-primary">
                         <th>Key</th>
                         <th>Value</th>
                        </tr>
                       </thead>
                       <tbody>          
                        <tr>
                         <th>Name</th>
                         <td style="width: 100%"><input 
                            onkeyup="' . $onkeyup . '" 
                            data-type="configuration-name" 
                            class="form-control" 
                            data-old-value="' . $this->name . '" 
                            value="' . $this->name . '"
                            ></td>
                        </tr>
                        <tbody>
                       </tbody>
                       <tfoot></tfoot>
                      </table>                      
                      
                      ' . LegoTrainButton::getHtmlSaveConfigButton('display: none;') . '             
                                           
                     </div>                          
        ';

        //-----------------------------------------------------------------------------------------
        // CONTROLLERS
        //-----------------------------------------------------------------------------------------

        $html .= '
        <div style="margin: 20px;">
            <div class="card" data-type="config-controllers">            
                <div class="card-header text-white bg-danger">
                    <span style="float: left;">
                        <h2>
                            <span class="fas fa-microchip"></span>&nbsp;Controllers                    
                        </h2>
                    </span>
                    <span style="float: right;">
                        ' . LegoTrainButton::getHtmlCardOpenCloseButton('danger') . '
                    </span>
                </div>
                <div class="card-body">            
                    <div data-type="config-actions">
                         ' . LegoTrainButton::getHtmlAddControllerButton() . '
                         <br/><br/>
                    </div>
        ';

        $first_num_controller = 0;
        $last_num_controller = count($this->controllers)-1;
        foreach ($this->controllers as $controller_num => $controller_info) {
            $html .= LegoController::getHtmlControllerConfigurationPanel(
                $controller_num,
                $controller_info,
                $controller_num == $first_num_controller,
                $controller_num == $last_num_controller
            );
        }

        $html .= '
                </div>
            </div>                                    
        </div>                                  
        ';

        //-----------------------------------------------------------------------------------------
        // TRAINS
        //-----------------------------------------------------------------------------------------

        $html .= '
        <div style="margin: 20px;">
            <div class="card" data-type="config-trains">                                        
                <div class="card-header text-white bg-success">
                    <span style="float: left;">
                        <h2>
                            <span class="fas fa-train"></span>&nbsp;Trains
                        </h2>
                    </span>  
                    <span style="float: right;">
                        ' . LegoTrainButton::getHtmlCardOpenCloseButton('success') . '
                    </span>
                </div>
                <div class="card-body">
                    <div data-type="config-actions">
                        ' . LegoTrainButton::getHtmlAddTrainButton() . '
                        <br/><br/>
                    </div>
        ';

        $first_num_train = 0;
        $last_num_train = count($this->trains)-1;
        foreach ($this->trains as $train_num => $train_info) {
            $html .= LegoTrain::getHtmlTableTrainConfigurationPanel(
                $this->controllers,
                $train_num,
                $train_info,
                $train_num == $first_num_train,
                $train_num == $last_num_train
            );
        }

        $html .= '
                </div>  
            </div>
        </div>                
        ';

        $html .= '
            </div>
        </div>
        ';

        return $html;
    }

    protected static function getLastConfiguration() {

        $config = null;

        $config_dir = static::getConfigDir();
        $filenames = scandir($config_dir);
        $last_filemtime = null;
        $last_config_file = null;
        foreach ( $filenames as $filename ) {
            if ($filename !== '.' && $filename !== '..') {
                $file = $config_dir . $filename;
                if ( is_file($file) ) {
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if ( $ext === 'json' ) {
                        $needle = 'setup-';
                        $needle_len = strlen($needle);
                        if (
                            strlen($filename) >= $needle_len &&
                            substr($filename, 0, $needle_len) === $needle
                        ) {
                            $filemtime = filemtime($file);
                            if ($last_filemtime === null || $last_filemtime < $filemtime) {
                                $last_filemtime = $filemtime;
                                $last_config_file = $file;
                            }
                        }
                    }
                }
            }
        }

        if ($last_config_file !== null){
            if ( false !== ($json = file_get_contents($last_config_file) ) ) {
                if ( false === ( $config = json_decode($json, true) ) ) {
                    $config = null;
                }
            }
        }

        if ( $config === null ) {
            $config = static::getDefaultConfiguration();
            static::saveConfiguration($config);
        }

        return $config;
    }

    protected static function getDefaultConfiguration() {
        return array(
            'name' => static::getDefaultConfigurationName(),
            'controllers' => array(
                array(
                    'name' => 'arduino-yun',
                    'ipv4_address' => '192.168.1.41',
                    'protocol' => 'http',
                    'port' => 80,
                    'commands' => array(
                        LegoTrain::PF => '/arduino/PF/{{channel}}/{{output}}/{{value}}',
                    )
                )
            ),
            'trains' => array(
                array(
                    'set' => 60051,
                    'name' => 'Treno passeggeri alta velocità',
                    'description' => 'Locomotiva BIANCA',
                    'engines' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PF,
                            LegoTrain::PF => array(
                                'channel' => LegoTrain::PF_CHANNEL_1,
                                'output' => LegoTrain::PF_OUTPUT_RED
                            ),
                        ),
                    ),
                    'lights' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PF,
                            LegoTrain::PF => array(
                                'channel' => LegoTrain::PF_CHANNEL_1,
                                'output' => LegoTrain::PF_OUTPUT_BLU
                            ),
                        ),
                    ),
                ),
                array(
                    'set' => 60052,
                    'name' => 'Treno merci',
                    'description' => 'Locomotiva BLU',
                    'engines' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PF,
                            LegoTrain::PF => array(
                                'channel' => LegoTrain::PF_CHANNEL_3,
                                'output' => LegoTrain::PF_OUTPUT_BLU,
                            ),
                        ),
                    ),
                ),
                array(
                    'set' => 60098,
                    'name' => 'Treno trasporto pesante',
                    'description' => 'Locomotiva ROSSA',
                    'engines' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PF,
                            LegoTrain::PF => array(
                                'channel' => LegoTrain::PF_CHANNEL_2,
                                'output' => LegoTrain::PF_OUTPUT_RED,
                            ),
                        ),
                    ),
                    'lights' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PF,
                            LegoTrain::PF => array(
                                'channel' => LegoTrain::PF_CHANNEL_2,
                                'output' => LegoTrain::PF_OUTPUT_BLU,
                            ),
                        ),
                    ),
                ),
                array(
                    'set' => 60197,
                    'name' => 'Treno passeggeri',
                    'description' => 'Locomotiva GIALLA',
                    'engines' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PU,
                            LegoTrain::PU => array(
                                'addr' => '90:84:2b:be:5c:3a',
                                'port' => LegoTrain::PU_PORT_A
                            ),
                        ),
                    ),
                    'lights' => array(
                        array(
                            'controller' => 0,
                            'system' => LegoTrain::PU,
                            LegoTrain::PU => array(
                                'port' => LegoTrain::PU_PORT_B,
                            ),
                        )
                    ),
                ),
            )
        );
    }

    protected static function getConfigDir(){
        $dir = __DIR__ . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'config' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)){
            mkdir($dir);
        }
        return $dir;
    }

    protected static function getConfigFile(
        $setup_name
    ){
        $dir = static::getConfigDir();
        $filename = 'setup-' . $setup_name . '.json';
        return $dir . $filename;
    }

    protected static function getDefaultConfigurationName() {
        return date('Ymd');
    }

    public static function saveConfiguration(
        $config
    ){
        $setup_name = key_exists('name', $config)
            ? $config['name']
            : static::getDefaultConfigurationName();

        $file = static::getConfigFile(
            $setup_name
        );

        return file_put_contents(
            $file,
            json_encode($config)
        );
    }



}