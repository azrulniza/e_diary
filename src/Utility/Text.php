<?php
namespace App\Utility;

class Text extends \Cake\Utility\Text{
    public static function nl2p($text){
        return '<p>'.str_replace('<br />', "</p>\n<p>", str_replace("\n", '', nl2br($text))).'</p>';
    }
}
