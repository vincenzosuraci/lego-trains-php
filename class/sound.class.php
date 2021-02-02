<?php

class LegoTrainSound {

    public static function getAudioFiles(
        $audio_file_exts = array(
            'mp3'
        )
    ) {
        $audio_files = array();

        $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sounds' . DIRECTORY_SEPARATOR;
        $filenames = scandir($dir);
        foreach($filenames as $filename) {
            if ($filename !== '.' && $filename !== '..'){
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (in_array($ext, $audio_file_exts)){
                    $filename_wo_ext = substr($filename,0,-(1+strlen($ext)));
                    $src = 'sounds/' . $filename;
                    $audio_files[$filename_wo_ext] = array(
                        'dir' => $dir,
                        'ext' => $ext,
                        'src' => $src
                    );
                }
            }
        }

        return $audio_files;
    }

    public static function getHtmlAudio() {

        $html = '';

        $audio_files = static::getAudioFiles();

        foreach($audio_files as $name => $info) {
            $html .= '
            <audio 
                data-type="audio"
                data-ext="' . $info['ext'] . '"
                data-id="' . $name . '" 
                src="' . $info['src'] . '"
                ></audio>
            ';
        }

        return $html;

    }

    public static function getHtmlSelectSound(
        $audio_type,
        $selected_audio_name,
        $onchange
    ) {
        $html = '';

        $html .= '
        <select 
            class="form-control" 
            data-type="config"
            data-id="sounds-' . $audio_type . '"
            data-old-value="' . $selected_audio_name . '"
            aria-label="Select train audio" 
            onchange="play_sound(this); ' . $onchange . '"
            >            
            <option value=""></option>
        ';

        $audio_files = static::getAudioFiles();
        foreach($audio_files as $audio_name => $audio_info) {
            $selected = ($selected_audio_name == $audio_name)
                ? ' selected'
                : '';
            $html .= '
        <option' . $selected . ' value="' . $audio_name .'">' . $audio_name .'</option>
        ';
        }

        $html .= '
        </select>
        ';

        return $html;

    }

}