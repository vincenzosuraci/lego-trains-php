<?php

class LegoTrainButton {

    public static function getHtmlCardOpenCloseButton(
        $class
    ){
        $html = '';

        $html .= '
        <button              
            class="btn btn-' . $class . '" 
            onclick="toggle_card(this);"
            ><span class="fas fa-caret-up"></span></button>
        ';

        return $html;
    }

    public static function getHtmlButtonByTags(
        $fa,
        $text,
        $tags
    ) {
        $html = '';

        $html_tags = '';
        foreach($tags as $tag_name => $tag_value){
            $html_tags .= ' ' . $tag_name . '="' . $tag_value . '"';
        }

        $html .= '        
        <button' . $html_tags . '><span class="fas fa-' . $fa . '"></span>' . $text . '</button>        
        ';

        return $html;
    }

    public static function getHtmlTrainSoundButton(
        $audio,
        $fa
    ) {
        $html = '';

        $tags = array(
            'type' => 'button',
            'data-type' => 'train-sound',
            'data-id' => $audio,
            'onclick' => "play_train_sound('" . $audio . "');",
            'class' => 'btn btn-outline-warning'
        );

        $html .= static::getHtmlButtonByTags(
            $fa,
            '',
            $tags
        );

        return $html;
    }

    public static function getHtmlActionButton(
        $action,
        $btn_class,
        $fa,
        $text = '',
        $style = ''
    ) {
        $html = '';

        $tags = array(
            'type' => 'button',
            'data-type' => 'action',
            'data-action' => $action,
            'class' => 'btn btn-' . $btn_class
        );

        if (strlen($style) > 0) {
            $tags['style'] = $style;
        }

        $html .= static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );

        return $html;
    }

    public static function getHtmlMoveTrainUpButton() {
        return static::getHtmlMoveUpButton('train');
    }

    public static function getHtmlMoveControllerUpButton() {
        return static::getHtmlMoveUpButton('controller');
    }

    protected static function getHtmlMoveUpButton($type) {
        $fa = 'caret-square-up';
        $text = '&nbsp;Move up';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-outline-primary',
            'data-action' => 'move-up',
            'onclick' => 'move_' . $type . '_up(this);'
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlLoadConfigurationButton($style) {
        $fa = 'file-export';
        $text = '&nbsp;Load';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-primary',
            'onclick' => 'load_configuration(this);',
            'style' => $style
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlMoveTrainDownButton() {
        return static::getHtmlMoveDownButton('train');
    }

    public static function getHtmlMoveControllerDownButton() {
        return static::getHtmlMoveDownButton('controller');
    }

    protected static function getHtmlMoveDownButton($type) {
        $fa = 'caret-square-down';
        $text = '&nbsp;Move down';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-outline-primary',
            'data-action' => 'move-down',
            'onclick' => 'move_' . $type . '_down(this);'
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlSaveConfigButton(
        $style = ''
    ) {
        $fa = 'save';
        $text = '&nbsp;Save';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-success',
            'onclick' => 'save_configuration(this);',
            'data-action' => 'save-config',
        );
        if (strlen($style) > 0) {
            $tags['style'] = $style;
        }
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlAddControllerButton() {
        $fa = 'plus';
        $text = '&nbsp;Add controller';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-outline-danger',
            'onclick' => 'add_controller(this);',
            'data-action' => 'add-controller',
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlAddTrainButton() {
        $fa = 'plus';
        $text = '&nbsp;Add train';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-outline-success',
            'onclick' => 'add_train(this);',
            'data-action' => 'add-train',
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlDeleteTrainButton() {
        $fa = 'trash';
        $text = '&nbsp;Delete';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-outline-danger',
            'onclick' => 'delete_train(this);',
            'data-action' => 'delete-train',
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlDeleteControllerButton() {
        $fa = 'trash';
        $text = '&nbsp;Delete';
        $tags = array(
            'type' => 'button',
            'class' => 'btn btn-outline-danger',
            'onclick' => 'delete_controller(this);',
            'data-action' => 'delete-controller',
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

    public static function getHtmlPlusButton() {
        return static::getHtmlActionButton(
            '+',
            'outline-primary',
            'caret-up'
        );
    }

    public static function getHtmlMinusButton() {
        return static::getHtmlActionButton(
            '-',
            'outline-primary',
            'caret-down'
        );
    }

    public static function getHtmlStopButton() {
        return static::getHtmlActionButton(
            'stop',
            'outline-danger',
            'stop'
        );
    }

    public static function getHtmlLightButton(
        $num,
        $turned_on = false
    ) {
        $fa = 'lightbulb';
        $text = '';
        $tags = array(
            'type' => 'button',
            'data-type' => 'action',
            'data-light-num' => $num,
            'class' => 'btn btn-' . ($turned_on ? 'success' : 'outline-danger'),
            'data-action' => ($turned_on ? 'light-off' : 'light-on'),
        );
        return static::getHtmlButtonByTags(
            $fa,
            $text,
            $tags
        );
    }

}