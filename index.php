<?php

require_once __DIR__.'/class/html.class.php';
require_once __DIR__.'/class/setup.class.php';

// Recuperiamo l'instestazione della pagina
$head = LegoTrainHtml::getHtmlHead();

// Stabiliamo con qual pannello partire
$active_panel = 'control';

// Recuperiamo la navigation bar
$nav_bar = LegoTrainHtml::getHtmlNavigationBar(
    $active_panel
);

// Generiamo la configurazione dei treni
$train_setup = new LegoTrainSetup();

// Recuperiamo il corpo della pagina
$body = LegoTrainHtml::getHtmlBody(
    array(
        'html' =>
            $nav_bar .
            $train_setup->getHtmlSounds() .
            $train_setup->getHtmlControlPanel($active_panel != 'control') .
            $train_setup->getHtmlConfigurationPanel($active_panel != 'configuration')
    )
);

// Recuperiamo il pié di pagina
$foot = LegoTrainHtml::getHtmlFoot();

// Recuperiamo l'html di tutta la pagina web
$html = LegoTrainHtml::getHtmlPage(
    array(
        'html' => $head . $body . $foot,
    )
);

// Stampiamo la pagina web
echo $html;
