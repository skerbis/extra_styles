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
        $addon->setConfig('info_menu_card_class', rex_post('info_menu_card_class', 'string'));
        
        // Info Menu Items (Repeater) - Array direkt auslesen
        $menuItemsRaw = rex_post('menu_items', 'array', []);
        $menuItems = [];
        
        // Array neu aufbauen und leere Einträge filtern
        foreach ($menuItemsRaw as $item) {
            if (is_array($item) && !empty($item['url']) && !empty($item['label'])) {
                $menuItems[] = [
                    'icon' => isset($item['icon']) ? $item['icon'] : '',
                    'url' => $item['url'],
                    'label' => $item['label']
                ];
            }
        }
        
        $addon->setConfig('info_menu_items', $menuItems);
        
        // Logo Beschriftung
        $addon->setConfig('logo_text', rex_post('logo_text', 'string'));
        
        $message = rex_view::success($addon->i18n('extra_styles_site_defaults_saved'));
    }
}

// Aktuelle Werte
$infoButtonIcon = $addon->getConfig('info_button_icon', '');
$infoButtonRatio = $addon->getConfig('info_button_ratio', '1.5');
$infoButtonHiddenText = $addon->getConfig('info_button_hidden_text', 'Mehr Informationen');
$infoButtonTitle = $addon->getConfig('info_button_title', 'Mehr Informationen');
$infoMenuCardClass = $addon->getConfig('info_menu_card_class', 'uk-card-primary');
$infoMenuItems = $addon->getConfig('info_menu_items', []);
$logoText = $addon->getConfig('logo_text', rex::getServerName());

echo $message;

$content = '
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $csrfToken->getHiddenField() . '
    
    <fieldset class="form-horizontal">
        <legend>' . $addon->i18n('extra_styles_info_button_title') . '</legend>
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="info_button_icon">' . $addon->i18n('extra_styles_info_button_icon') . '</label>
            <div class="col-sm-9">
                <select class="form-control" id="info_button_icon" name="info_button_icon">
                    <option value="">-- Icon wählen --</option>
                    <option value="info"' . ($infoButtonIcon == 'info' ? ' selected' : '') . '>Info</option>
                    <option value="menu"' . ($infoButtonIcon == 'menu' ? ' selected' : '') . '>Menu</option>
                    <option value="list"' . ($infoButtonIcon == 'list' ? ' selected' : '') . '>List</option>
                    <option value="more-vertical"' . ($infoButtonIcon == 'more-vertical' ? ' selected' : '') . '>More Vertical</option>
                    <option value="more"' . ($infoButtonIcon == 'more' ? ' selected' : '') . '>More</option>
                    <option value="settings"' . ($infoButtonIcon == 'settings' ? ' selected' : '') . '>Settings</option>
                    <option value="cog"' . ($infoButtonIcon == 'cog' ? ' selected' : '') . '>Cog</option>
                    <option value="question"' . ($infoButtonIcon == 'question' ? ' selected' : '') . '>Question</option>
                    <option value="plus"' . ($infoButtonIcon == 'plus' ? ' selected' : '') . '>Plus</option>
                    <option value="plus-circle"' . ($infoButtonIcon == 'plus-circle' ? ' selected' : '') . '>Plus Circle</option>
                    <option value="bolt"' . ($infoButtonIcon == 'bolt' ? ' selected' : '') . '>Bolt</option>
                    <option value="star"' . ($infoButtonIcon == 'star' ? ' selected' : '') . '>Star</option>
                    <option value="heart"' . ($infoButtonIcon == 'heart' ? ' selected' : '') . '>Heart</option>
                    <option value="home"' . ($infoButtonIcon == 'home' ? ' selected' : '') . '>Home</option>
                    <option value="world"' . ($infoButtonIcon == 'world' ? ' selected' : '') . '>World</option>
                    <option value="bookmark"' . ($infoButtonIcon == 'bookmark' ? ' selected' : '') . '>Bookmark</option>
                </select>
                <p class="help-block">UIKit Icon für den Button</p>
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
        
        <div class="form-group">
            <label class="col-sm-3 control-label" for="info_menu_card_class">' . $addon->i18n('extra_styles_info_menu_card_class') . '</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" id="info_menu_card_class" name="info_menu_card_class" value="' . htmlspecialchars($infoMenuCardClass) . '" />
                <p class="help-block">CSS-Klasse für die Card (z.B. uk-card-primary, uk-card-secondary oder eigene Extra-Styles-Klasse)</p>
            </div>
        </div>
    </fieldset>
    
    <fieldset class="form-horizontal">
        <legend>' . $addon->i18n('extra_styles_info_menu_items') . '</legend>
        
        <div id="menu-items-container">';

if (!empty($infoMenuItems) && is_array($infoMenuItems)) {
    foreach ($infoMenuItems as $index => $item) {
        // Sicherstellen dass $item ein Array ist
        if (!is_array($item)) {
            continue;
        }
        
        $itemIcon = isset($item['icon']) ? $item['icon'] : '';
        $itemUrl = isset($item['url']) ? $item['url'] : '';
        $itemLabel = isset($item['label']) ? $item['label'] : '';
        
        $content .= '
        <div class="panel panel-default menu-item-panel" data-index="' . (int)$index . '">
            <div class="panel-heading">
                <strong>Menüpunkt ' . ((int)$index + 1) . '</strong>
                <button type="button" class="btn btn-xs btn-danger pull-right remove-menu-item" style="margin-top: -3px;">
                    <i class="rex-icon fa-trash"></i> Entfernen
                </button>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">UIKit Icon</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="menu_items[' . (int)$index . '][icon]">
                            <option value="">-- Icon wählen --</option>
                            <option value="instagram"' . ($itemIcon == 'instagram' ? ' selected' : '') . '>Instagram</option>
                            <option value="facebook"' . ($itemIcon == 'facebook' ? ' selected' : '') . '>Facebook</option>
                            <option value="youtube"' . ($itemIcon == 'youtube' ? ' selected' : '') . '>YouTube</option>
                            <option value="twitter"' . ($itemIcon == 'twitter' ? ' selected' : '') . '>Twitter</option>
                            <option value="linkedin"' . ($itemIcon == 'linkedin' ? ' selected' : '') . '>LinkedIn</option>
                            <option value="github"' . ($itemIcon == 'github' ? ' selected' : '') . '>GitHub</option>
                            <option value="whatsapp"' . ($itemIcon == 'whatsapp' ? ' selected' : '') . '>WhatsApp</option>
                            <option value="search"' . ($itemIcon == 'search' ? ' selected' : '') . '>Search</option>
                            <option value="mail"' . ($itemIcon == 'mail' ? ' selected' : '') . '>Mail</option>
                            <option value="phone"' . ($itemIcon == 'phone' ? ' selected' : '') . '>Phone</option>
                            <option value="location"' . ($itemIcon == 'location' ? ' selected' : '') . '>Location</option>
                            <option value="link"' . ($itemIcon == 'link' ? ' selected' : '') . '>Link</option>
                            <option value="download"' . ($itemIcon == 'download' ? ' selected' : '') . '>Download</option>
                            <option value="calendar"' . ($itemIcon == 'calendar' ? ' selected' : '') . '>Calendar</option>
                            <option value="clock"' . ($itemIcon == 'clock' ? ' selected' : '') . '>Clock</option>
                            <option value="info"' . ($itemIcon == 'info' ? ' selected' : '') . '>Info</option>
                            <option value="question"' . ($itemIcon == 'question' ? ' selected' : '') . '>Question</option>
                            <option value="home"' . ($itemIcon == 'home' ? ' selected' : '') . '>Home</option>
                            <option value="world"' . ($itemIcon == 'world' ? ' selected' : '') . '>World</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">URL</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="menu_items[' . (int)$index . '][url]" value="' . htmlspecialchars($itemUrl) . '" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label">Bezeichnung</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="menu_items[' . (int)$index . '][label]" value="' . htmlspecialchars($itemLabel) . '" />
                    </div>
                </div>
            </div>
        </div>';
    }
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
    var menuItemIndex = ' . (is_array($infoMenuItems) ? count($infoMenuItems) : 0) . ';
    
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
                        <select class="form-control" name="menu_items[${menuItemIndex}][icon]">
                            <option value="">-- Icon wählen --</option>
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="youtube">YouTube</option>
                            <option value="twitter">Twitter</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="github">GitHub</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="search">Search</option>
                            <option value="mail">Mail</option>
                            <option value="phone">Phone</option>
                            <option value="location">Location</option>
                            <option value="link">Link</option>
                            <option value="download">Download</option>
                            <option value="calendar">Calendar</option>
                            <option value="clock">Clock</option>
                            <option value="info">Info</option>
                            <option value="question">Question</option>
                            <option value="home">Home</option>
                            <option value="world">World</option>
                        </select>
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
