<?php
/**
 * Extra Styles - Site Defaults
 * Einstellungen für Info-Button-Menü und Logo-Beschriftung
 */

$addon = rex_addon::get('extra_styles');

$content = '';
$message = '';

$csrfToken = rex_csrf_token::factory('extra_styles_site_defaults');

// Formular verarbeiten
if (rex_post('btn_save', 'string')) {
    if (!$csrfToken->isValid()) {
        $message = rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    } else {
        // Info Button Einstellungen
        $addon->setConfig('info_button_icon', rex_post('info_button_icon', 'string'));
        $addon->setConfig('info_button_ratio', rex_post('info_button_ratio', 'string'));
        $addon->setConfig('info_button_hidden_text', rex_post('info_button_hidden_text', 'string'));
        $addon->setConfig('info_button_title', rex_post('info_button_title', 'string'));
        
        // Info Menu Items (Repeater)
        $menuItems = rex_post('menu_items', [
            ['icon', 'string'],
            ['url', 'string'],
            ['label', 'string']
        ]);
        $addon->setConfig('info_menu_items', $menuItems);
        
        // Logo Beschriftung
        $addon->setConfig('logo_text', rex_post('logo_text', 'string'));
        
        $message = rex_view::success($addon->i18n('extra_styles_site_defaults_saved'));
    }
}

// Aktuelle Werte
$infoButtonIcon = $addon->getConfig('info_button_icon', 'info');
$infoButtonRatio = $addon->getConfig('info_button_ratio', '1.5');
$infoButtonHiddenText = $addon->getConfig('info_button_hidden_text', 'Mehr Informationen');
$infoButtonTitle = $addon->getConfig('info_button_title', 'Mehr Informationen');
$infoMenuItems = $addon->getConfig('info_menu_items', [
    ['icon' => 'instagram', 'url' => '', 'label' => 'Instagram'],
    ['icon' => 'facebook', 'url' => '', 'label' => 'Facebook'],
    ['icon' => 'youtube', 'url' => '', 'label' => 'YouTube'],
]);
$logoText = $addon->getConfig('logo_text', '');

echo $message;

$content = '
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $csrfToken->getHiddenField() . '
    
    <fieldset class="form-horizontal">
        <legend>' . $addon->i18n('extra_styles_info_button_title') . '</legend>
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="info_button_icon">' . $addon->i18n('extra_styles_info_button_icon') . '</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="info_button_icon" name="info_button_icon" value="' . htmlspecialchars($infoButtonIcon) . '" />
                <p class="help-block">UIKit Icon-Name (z.B. info, menu, list)</p>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="info_button_ratio">' . $addon->i18n('extra_styles_info_button_ratio') . '</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="info_button_ratio" name="info_button_ratio" value="' . htmlspecialchars($infoButtonRatio) . '" />
                <p class="help-block">Größe des Icons (z.B. 1.5, 2, 2.5) - Browser wandelt Komma automatisch in Punkt um</p>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="info_button_hidden_text">' . $addon->i18n('extra_styles_info_button_hidden_text') . '</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="info_button_hidden_text" name="info_button_hidden_text" value="' . htmlspecialchars($infoButtonHiddenText) . '" />
                <p class="help-block">Versteckter Text für Barrierefreiheit (mit uk-hidden)</p>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="info_button_title">' . $addon->i18n('extra_styles_info_button_title') . '</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="info_button_title" name="info_button_title" value="' . htmlspecialchars($infoButtonTitle) . '" />
                <p class="help-block">Titel im Drop-Down Menü</p>
            </div>
        </div>
    </fieldset>
    
    <fieldset class="form-horizontal">
        <legend>' . $addon->i18n('extra_styles_info_menu_items') . '</legend>
        
        <div id="menu-items-container">';

foreach ($infoMenuItems as $index => $item) {
    $content .= '
        <div class="panel panel-default menu-item-panel" data-index="' . $index . '">
            <div class="panel-heading">
                <strong>Menüpunkt ' . ($index + 1) . '</strong>
                <button type="button" class="btn btn-xs btn-danger pull-right remove-menu-item" style="margin-top: -3px;">
                    <i class="rex-icon fa-trash"></i> Entfernen
                </button>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">UIKit Icon</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="menu_items[' . $index . '][icon]" value="' . htmlspecialchars($item['icon']) . '" />
                        <p class="help-block">z.B. instagram, facebook, youtube, search</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">URL</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="menu_items[' . $index . '][url]" value="' . htmlspecialchars($item['url']) . '" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">Bezeichnung</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="menu_items[' . $index . '][label]" value="' . htmlspecialchars($item['label']) . '" />
                    </div>
                </div>
            </div>
        </div>';
}

$content .= '
        </div>
        
        <button type="button" class="btn btn-default" id="add-menu-item">
            <i class="rex-icon fa-plus"></i> Menüpunkt hinzufügen
        </button>
    </fieldset>
    
    <fieldset class="form-horizontal">
        <legend>' . $addon->i18n('extra_styles_logo_text_title') . '</legend>
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="logo_text">' . $addon->i18n('extra_styles_logo_text') . '</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="logo_text" name="logo_text" value="' . htmlspecialchars($logoText) . '" />
                <p class="help-block">Text für die Logo-Beschriftung</p>
            </div>
        </div>
    </fieldset>
    
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1">
                <i class="rex-icon rex-icon-save"></i> ' . $addon->i18n('form_save') . '
            </button>
        </div>
    </div>
</form>

<script>
jQuery(function($) {
    var menuItemIndex = ' . count($infoMenuItems) . ';
    
    // Menüpunkt hinzufügen
    $("#add-menu-item").on("click", function() {
        var html = `
        <div class="panel panel-default menu-item-panel" data-index="${menuItemIndex}">
            <div class="panel-heading">
                <strong>Menüpunkt ${menuItemIndex + 1}</strong>
                <button type="button" class="btn btn-xs btn-danger pull-right remove-menu-item" style="margin-top: -3px;">
                    <i class="rex-icon fa-trash"></i> Entfernen
                </button>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">UIKit Icon</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="menu_items[${menuItemIndex}][icon]" value="" />
                        <p class="help-block">z.B. instagram, facebook, youtube, search</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">URL</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="menu_items[${menuItemIndex}][url]" value="" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">Bezeichnung</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="menu_items[${menuItemIndex}][label]" value="" />
                    </div>
                </div>
            </div>
        </div>`;
        
        $("#menu-items-container").append(html);
        menuItemIndex++;
    });
    
    // Menüpunkt entfernen
    $(document).on("click", ".remove-menu-item", function() {
        $(this).closest(".menu-item-panel").remove();
    });
});
</script>

<style>
.menu-item-panel {
    margin-bottom: 15px;
}
.menu-item-panel .form-group:last-child {
    margin-bottom: 0;
}
</style>
';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $addon->i18n('extra_styles_site_defaults_title'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
