<?php
use function App\Helpers\add_plugin_shortcode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

$pluginName = 'MoneyPlugin';

 // usage in post [MoneyPlugin:paysem sem="50" p="product123" type="buy" seller="42"]  

add_plugin_shortcode($pluginName, 'paysem', function ($attrs, $content = '') {
    $sem = $attrs['sem'] ?? 0;
    $p = $attrs['p'] ?? '';
    $type = $attrs['type'] ?? '';
    $seller = $attrs['seller'] ?? '';

    // Build the URL with query parameters
    $url = route('plugins.MoneyPlugin.shortcode.paysem', [
        'sem'    => $sem,
        'p'      => $p,
        'type'   => $type,
        'seller' => $seller
    ]);

    // Render clickable button
    $buttonHtml = '<a href="' . e($url) . '" class="btn btn-primary">
        Pay ' . e($sem) . ' SEM
    </a>';

    

    return $buttonHtml;
});
