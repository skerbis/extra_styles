<?php
/** @var rex_addon $this */

use ExtraStyles\CssGenerator;

$addon = rex_addon::get('extra_styles');

// Permissions registrieren
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('extra_styles[]');
    rex_perm::register('extra_styles[site_defaults]', 'Extra Styles: Site Defaults');
}

// Backend Assets laden
if (rex::isBackend() && rex_be_controller::getCurrentPagePart(1) === 'extra_styles') {
    // Pickr Color Picker
    rex_view::addCssFile($addon->getAssetsUrl('vendor/pickr/nano.min.css'));
    rex_view::addJsFile($addon->getAssetsUrl('vendor/pickr/pickr.min.js'));
    
    // Backend CSS/JS
    rex_view::addCssFile($addon->getAssetsUrl('extra_styles_backend.css'));
    rex_view::addJsFile($addon->getAssetsUrl('extra_styles_backend.js'));
}

// Custom CSS überall einbinden (generierte Datei liegt unter /assets/addons/extra_styles/custom.css)
$customCssUrl = rex_url::assets('addons/extra_styles/custom.css');

if (rex::isBackend()) {
    rex_view::addCssFile($customCssUrl);
}

// Frontend: Custom CSS einbinden
if (!rex::isBackend()) {
    rex_extension::register('OUTPUT_FILTER', function($ep) use ($customCssUrl) {
        $content = $ep->getSubject();
        $cssLink = '<link rel="stylesheet" href="' . $customCssUrl . '?' . time() . '">';
        $content = str_replace('</head>', $cssLink . "\n</head>", $content);
        return $content;
    });
}

// CSS regenerieren wenn nötig
rex_extension::register('PACKAGES_INCLUDED', function() use ($addon) {
    if ($addon->getConfig('regenerate_css', false)) {
        CssGenerator::generate();
        $addon->setConfig('regenerate_css', false);
    }
});

// Bei Änderungen CSS neu generieren
if (rex::isBackend() && rex_be_controller::getCurrentPagePart(1) === 'extra_styles') {
    rex_extension::register(['REX_FORM_SAVED', 'REX_FORM_DELETED'], function($ep) {
        CssGenerator::generate();
    });
}
