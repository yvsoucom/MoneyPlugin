<?php
// plugins/MyPlugin/Filters/PluginExampleFilter.php
 
use function App\Helpers\add_plugin_filter;
$pluginName = 'MoneyPlugin';

// Add a filter scoped to this plugin only
add_plugin_filter($pluginName, 'modify_greeting', function ($greeting) {
    return $greeting . ' Greetings from MyPlugin!';
});
