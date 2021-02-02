<?php

class LegoTrainHtml {

    const PANEL_CONFIGURATION = 'configuration';
    const PANEL_CONTROL = 'control';

    public static function getHtmlNavigationBar(
        $active_panel = 'configuration'
    ) {
        $html = '';

        $html .= '
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">
                <img
                        src="img/logo/lego.png"                        
                        class="d-inline-block align-top"                        
                        alt="LEGO"
                        width="30" 
                        height="30" 
                        style="margin-right:10px;"    
                    />Lego Trains App v0.1
            </a>                             
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item' . ($active_panel == static::PANEL_CONFIGURATION ? ' active' : '') . '">
                        <a 
                            class="nav-link" 
                            href="#" 
                            onclick="showPanel(this);" 
                            data-type="nav-item"
                            data-id="configuration"
                            ><i class="fas fa-wrench"></i>&nbsp;Configuration</a>
                    </li>
                    <li class="nav-item"' . ($active_panel == static::PANEL_CONTROL ? ' active' : '') . '>
                        <a 
                            class="nav-link" 
                            href="#" 
                            onclick="showPanel(this);" 
                            data-type="nav-item"
                            data-id="control"
                            ><i class="fas fa-cog" ></i>&nbsp;Control panel</a>
                    </li> 
                </ul>               
            </div>
        </nav>
        ';

        return $html;
    }

    public static function getHtmlHead(
        $options = array(
            'title' => 'Lego Trains App v0.1',
            'html' => '',
        )
    ) {

        $html = '';

        $title = key_exists('title', $options)
            ? $options['title']
            : 'Lego Trains App v0.1';

        $html_head = key_exists('html', $options)
            ? $options['html']
            : '';

        $html .= '
        <head>
        
            <meta charset="UTF-8">

            <title>' . $title . '</title>
        
            <link rel="icon" type="image/png" href="img/icon/favicon.ico"/>
        
            <link href="css/bootstrap.min.css" rel="stylesheet">
            <link href="css/main.css?_' . time() . '" rel="stylesheet">                        
            <link href="css/all.min.css" rel="stylesheet">            
            <!--link href="css/bootstrap-grid.min.css" rel="stylesheet"-->
            <!--link href="css/bootstrap-reboot.min.css" rel="stylesheet"-->                        
            
            <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>                                    
            <script type="text/javascript" src="js/bootstrap.min.js"></script>
            <!--script type="text/javascript" src="js/bootstrap.bundle.min.js"></script-->                                    
            <script type="text/javascript" src="js/gauge.min.js"></script>
            <script type="text/javascript" src="js/main.js?_' . time() . '"></script>            
            
            ' . $html_head . '                   
            
        </head>
        ';
        
        return $html;
    }

    public static function getHtmlBody(
        $options = array (
            'html' => '',
        )
    ) {
        $html = '';

        $html_body = key_exists('html', $options)
            ? $options['html']
            : '';

        $html .= '
        <body>         
                         
        ' . $html_body . '                     
                                                      
        </body>
        ';

        return $html;
    }

    public static function getHtmlFoot(
        $options = array (
            'html' => '',
        )
    ) {
        $html = '';

        $html_foot = key_exists('html', $options)
            ? $options['html']
            : '';

        $html .= '
        <foot>
         ' . $html_foot . '
         <div class="footer" style="text-align: center;">
          <p>&copy; Vincenzo Suraci 2020</p>
         </div>                  
         
        </foot>
        ';

        return $html;
    }

    public static function getHtmlPage(
        $options = array (
            'html' => '',
        )
    ) {
        $html = '';

        $html_page = key_exists('html', $options)
            ? $options['html']
            : '';

        $html .= '
        <!DOCTYPE html>
        <html lang="en" >
         ' . $html_page . '
        </html>
        ';

        return $html;
    }
    
}
