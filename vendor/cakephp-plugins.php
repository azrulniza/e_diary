<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'AdminLTEBakeOverride' => $baseDir . '/plugins/AdminLTEBakeOverride/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'CakePdf' => $baseDir . '/vendor/friendsofcake/cakepdf/',
        'Captcha' => $baseDir . '/plugins/Captcha/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'Shim' => $baseDir . '/vendor/dereuromark/cakephp-shim/',
        'TinyAuth' => $baseDir . '/vendor/dereuromark/cakephp-tinyauth/',
        'Tools' => $baseDir . '/vendor/dereuromark/cakephp-tools/',
        'WyriHaximus/TwigView' => $baseDir . '/vendor/wyrihaximus/twig-view/',
        'Xety/Cake3Upload' => $baseDir . '/vendor/xety/cake3-upload/'
    ]
];