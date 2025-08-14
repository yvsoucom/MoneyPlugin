<?php
use function App\Helpers\add_plugin_shortcode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

$pluginName = 'MoneyPlugin';

// Register shortcode [myplugin_hello] scoped to MyPlugin
add_plugin_shortcode($pluginName, 'paysem', function ($attrs, $content = '') {
    $sem = $attrs['sem'] ?? 0;
    $p = $attrs['p'] ?? '';
    $type = $attrs['type'] ?? '';
    $seller = $attrs['seller'] ?? '';
    
    // Create an instance of the PaySemController
    $controller = new \Plugins\MoneyPlugin\src\Http\Controllers\Shortcode\PaySemController();
    
    // Call the handle method with the attributes and content
    return $controller->handle($sem, $p, $type, $seller, $content);
});