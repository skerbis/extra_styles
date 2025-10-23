<?php
/**
 * Extra Styles - Hauptseite
 */

use ExtraStyles\ExtraStyles;
use ExtraStyles\CssGenerator;

$addon = rex_addon::get('extra_styles');

$func = rex_request('func', 'string');
$id = rex_request('id', 'int');
$csrfToken = rex_csrf_token::factory('extra_styles');

$content = '';
$message = '';

// Löschen
if ('delete' == $func) {
    if (!$csrfToken->isValid()) {
        $message = rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    } else {
        if (ExtraStyles::delete($id)) {
            $message = rex_view::success($addon->i18n('extra_styles_deleted'));
        } else {
            $message = rex_view::error('Fehler beim Löschen');
        }
    }
    $func = '';
}

// CSS regenerieren
if ('regenerate' == $func) {
    if (!$csrfToken->isValid()) {
        $message = rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    } else {
        if (CssGenerator::generate()) {
            $message = rex_view::success($addon->i18n('extra_styles_css_generated'));
        } else {
            $message = rex_view::error($addon->i18n('extra_styles_css_error'));
        }
    }
    $func = '';
}

// Kopieren
if ('copy' == $func && $id > 0) {
    $sql = rex_sql::factory();
    try {
        $original = ExtraStyles::getById($id);
        if ($original) {
            $sql->setTable(rex::getTable('extra_styles'));
            $sql->setValue('name', $original['name'] . ' (Kopie)');
            $sql->setValue('slug', ExtraStyles::generateSlug($original['name'] . ' Kopie'));
            $sql->setValue('type', $original['type']);
            $sql->setValue('color', $original['color']);
            $sql->setValue('backdrop_blur', $original['backdrop_blur']);
            $sql->setValue('text_color', $original['text_color']);
            $sql->setValue('link_color', $original['link_color']);
            $sql->setValue('border_color', $original['border_color']);
            $sql->setValue('border_width', $original['border_width']);
            $sql->setValue('border_radius', $original['border_radius']);
            $sql->setValue('is_light', $original['is_light']);
            $sql->setValue('priority', $original['priority']);
            $sql->setValue('status', 0); // Kopie ist erstmal inaktiv
            $sql->setValue('createdate', date('Y-m-d H:i:s'));
            $sql->setValue('updatedate', date('Y-m-d H:i:s'));
            $sql->setValue('createuser', rex::getUser()->getValue('login'));
            $sql->setValue('updateuser', rex::getUser()->getValue('login'));
            $sql->insert();
            
            CssGenerator::generate();
            $message = rex_view::success($addon->i18n('extra_styles_copied'));
        }
    } catch (rex_sql_exception $e) {
        $message = rex_view::error($e->getMessage());
    }
    $func = '';
}

// Output messages
echo $message;

// Übersicht
if ('' == $func) {
    $query = 'SELECT id, name, slug, type, color, backdrop_blur, text_color, border_color, border_width, is_light, status, priority FROM ' . rex::getTable('extra_styles') . ' ORDER BY priority ASC, name ASC';
    
    $list = rex_list::factory($query, 100);
    $list->addTableAttribute('class', 'table-striped table-hover');
    $list->setNoRowsMessage($addon->i18n('extra_styles_no_rows'));
    
    // Icon-Spalte
    $thIcon = '<a class="rex-link-expanded" href="' . $list->getUrl(['func' => 'add']) . '" title="' . $addon->i18n('extra_styles_add') . '"><i class="rex-icon rex-icon-add"></i></a>';
    $list->addColumn($thIcon, '<i class="rex-icon fa-palette"></i>', 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
    
    // ID
    $list->setColumnLabel('id', rex_i18n::msg('id'));
    $list->setColumnLayout('id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);
    
    // Name mit Beschreibung
    $list->setColumnLabel('name', $addon->i18n('extra_styles_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'id' => '###id###']);
    $list->setColumnFormat('name', 'custom', function() use ($list, $addon) {
        $name = '<strong>' . rex_escape($list->getValue('name')) . '</strong>';
        $name .= '<br><small class="rex-note">' . $addon->i18n('extra_styles_type_' . $list->getValue('type')) . ' &middot; <code>' . rex_escape($list->getValue('slug')) . '</code></small>';
        return $name;
    });
    
    // Versteckte Spalten
    $list->removeColumn('slug');
    $list->removeColumn('type');
    $list->removeColumn('text_color');
    $list->removeColumn('border_color');
    $list->removeColumn('border_width');
    $list->removeColumn('is_light');
    $list->removeColumn('priority');
    
    // Vorschau
    $list->setColumnLabel('color', $addon->i18n('extra_styles_preview'));
    $list->setColumnFormat('color', 'custom', function() use ($list) {
        $bgColor = $list->getValue('color'); // Kann HEX oder RGBA sein
        $backdropBlur = $list->getValue('backdrop_blur') ?? 0;
        $textColor = $list->getValue('text_color');
        $borderColor = $list->getValue('border_color');
        $borderWidth = $list->getValue('border_width');
        $isLight = $list->getValue('is_light');
        $name = rex_escape($list->getValue('name'));
        
        // Wrapper mit Muster-Hintergrund
        $wrapperStyle = 'background: repeating-linear-gradient(45deg, #f0f0f0, #f0f0f0 10px, #ffffff 10px, #ffffff 20px); padding: 2px; border-radius: 4px; display: inline-block;';
        
        $style = 'background-color: ' . $bgColor . '; padding: 10px 15px; border-radius: 4px; display: inline-block; min-width: 150px; position: relative;';
        
        if ($backdropBlur > 0) {
            $style .= ' backdrop-filter: blur(' . $backdropBlur . 'px); -webkit-backdrop-filter: blur(' . $backdropBlur . 'px);';
        }
        
        if ($textColor) {
            $style .= ' color: ' . $textColor . ';';
        } elseif ($isLight) {
            $style .= ' color: #fff;';
        } else {
            $style .= ' color: #333;';
        }
        
        if ($borderColor) {
            $style .= ' border: ' . $borderWidth . 'px solid ' . $borderColor . ';';
        }
        
        return '<div style="' . $wrapperStyle . '"><div style="' . $style . '">' . $name . '</div></div>';
    });
    
    // Status
    $list->setColumnLabel('status', $addon->i18n('extra_styles_status'));
    $list->setColumnFormat('status', 'custom', function() use ($list, $addon) {
        if ($list->getValue('status')) {
            return '<span class="rex-online"><i class="rex-icon rex-icon-online"></i> ' . $addon->i18n('extra_styles_active') . '</span>';
        }
        return '<span class="rex-offline"><i class="rex-icon rex-icon-offline"></i> ' . $addon->i18n('extra_styles_inactive') . '</span>';
    });
    
    // Funktionen
    $list->addColumn($addon->i18n('extra_styles_functions'), '', -1, ['<th class="rex-table-action" colspan="3">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams($addon->i18n('extra_styles_functions'), ['func' => 'edit', 'id' => '###id###']);
    $list->setColumnFormat($addon->i18n('extra_styles_functions'), 'custom', function() use ($list, $addon) {
        return $list->getColumnLink($addon->i18n('extra_styles_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . $addon->i18n('extra_styles_edit'));
    });
    
    // Kopieren
    $list->addColumn('copy', '<i class="rex-icon rex-icon-duplicate"></i> ' . $addon->i18n('extra_styles_copy'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams('copy', ['func' => 'copy', 'id' => '###id###'] + $csrfToken->getUrlParams());
    
    // Löschen
    $list->addColumn('delete', '<i class="rex-icon rex-icon-delete"></i> ' . $addon->i18n('extra_styles_delete'), -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams('delete', ['func' => 'delete', 'id' => '###id###'] + $csrfToken->getUrlParams());
    $list->addLinkAttribute('delete', 'data-confirm', $addon->i18n('extra_styles_confirm_delete'));
    
    $content .= $list->get();
    
    // CSS Regenerieren Button
    $formElements = [];
    $n = [];
    $n['field'] = '<a class="btn btn-default" href="' . rex_url::currentBackendPage(['func' => 'regenerate'] + $csrfToken->getUrlParams()) . '"><i class="rex-icon fa-sync"></i> ' . $addon->i18n('extra_styles_css_regenerate') . '</a>';
    $formElements[] = $n;
    
    $fragment = new rex_fragment();
    $fragment->setVar('elements', $formElements, false);
    $buttons = $fragment->parse('core/form/submit.php');
    
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $addon->i18n('extra_styles_caption'), false);
    $fragment->setVar('body', $content, false);
    $fragment->setVar('buttons', $buttons, false);
    $content = $fragment->parse('core/page/section.php');
    
    echo $content;
}

// Formular (Add/Edit)
if ('add' == $func || 'edit' == $func) {
    $title = 'edit' == $func ? $addon->i18n('extra_styles_edit_title') : $addon->i18n('extra_styles_add_title');
    
    // Layout: Formular links, Preview rechts
    echo '<div class="row">';
    echo '<div class="col-lg-8">';
    
    $form = rex_form::factory(rex::getTable('extra_styles'), '', 'id=' . $id);
    $form->addParam('id', $id);
    $form->setApplyUrl(rex_url::currentBackendPage());
    $form->setEditMode('edit' == $func);
    
    // Extension Point für After-Save (CSS regenerieren)
    rex_extension::register('REX_FORM_SAVED', function(rex_extension_point $ep) use ($form) {
        if ($form !== $ep->getParam('form')) {
            return;
        }
        CssGenerator::generate();
    });
    
    // Extension Point für After-Save (CSS regenerieren)
    rex_extension::register('REX_FORM_SAVED', function(rex_extension_point $ep) use ($form) {
        if ($form !== $ep->getParam('form')) {
            return;
        }
        CssGenerator::generate();
    });
    
    // Name
    $field = $form->addTextField('name');
    $field->setLabel($addon->i18n('extra_styles_name'));
    $field->getValidator()->add('notEmpty', $addon->i18n('extra_styles_name') . ' ist erforderlich');
    
    // Slug
    $field = $form->addTextField('slug');
    $field->setLabel($addon->i18n('extra_styles_slug'));
    $field->setNotice('<button type="button" class="btn btn-default" onclick="generateSlugFromName()"><i class="rex-icon fa-magic"></i> Aus Name generieren</button><br>Nur Kleinbuchstaben und Bindestriche.');
    $field->getValidator()->add('notEmpty', $addon->i18n('extra_styles_slug') . ' ist erforderlich');
    
    // Type - nur aktivierte Typen anzeigen (ohne custom)
    $field = $form->addSelectField('type');
    $field->setLabel($addon->i18n('extra_styles_type'));
    $select = $field->getSelect();
    
    $availableTypes = [];
    if (ExtraStyles::isTypeEnabled('card')) {
        $availableTypes['card'] = $addon->i18n('extra_styles_type_card');
    }
    if (ExtraStyles::isTypeEnabled('section')) {
        $availableTypes['section'] = $addon->i18n('extra_styles_type_section');
    }
    if (ExtraStyles::isTypeEnabled('background')) {
        $availableTypes['background'] = $addon->i18n('extra_styles_type_background');
    }
    if (ExtraStyles::isTypeEnabled('border')) {
        $availableTypes['border'] = $addon->i18n('extra_styles_type_border');
    }
    
    $select->addOptions($availableTypes);
    $field->getValidator()->add('notEmpty', $addon->i18n('extra_styles_type') . ' ist erforderlich');
    
    // Color
    $field = $form->addTextField('color');
    $field->setLabel($addon->i18n('extra_styles_color'));
    $field->setNotice('Hex-Code (#ff0000) oder RGBA (rgba(255, 0, 0, 0.5)) für Transparenz');
    $field->setAttribute('data-colorpicker', 'true');
    $field->getValidator()->add('notEmpty', $addon->i18n('extra_styles_color') . ' ist erforderlich');
    
    // Backdrop Blur
    $field = $form->addTextField('backdrop_blur');
    $field->setLabel($addon->i18n('extra_styles_backdrop_blur'));
    $field->setNotice('Backdrop-Filter in Pixel (0 = deaktiviert, empfohlen: 5-20)');
    $field->setAttribute('type', 'number');
    $field->setAttribute('min', '0');
    $field->setAttribute('max', '100');
    
    // Text Color
    $field = $form->addTextField('text_color');
    $field->setLabel($addon->i18n('extra_styles_text_color'));
    $field->setNotice('Optional: Hex-Code für Textfarbe');
    $field->setAttribute('data-colorpicker', 'true');
    
    // Link Color
    $field = $form->addTextField('link_color');
    $field->setLabel($addon->i18n('extra_styles_link_color'));
    $field->setNotice('Optional: Hex-Code für Linkfarbe (hebt Links vom Text ab)');
    $field->setAttribute('data-colorpicker', 'true');
    
    // Border Color
    $field = $form->addTextField('border_color');
    $field->setLabel($addon->i18n('extra_styles_border_color'));
    $field->setNotice('Optional: Hex-Code für Rahmenfarbe');
    $field->setAttribute('data-colorpicker', 'true');
    
    // Border Width
    $field = $form->addTextField('border_width');
    $field->setLabel($addon->i18n('extra_styles_border_width'));
    $field->setNotice('In Pixel (Standard: 1)');
    
    // Border Radius
    $field = $form->addTextField('border_radius');
    $field->setLabel($addon->i18n('extra_styles_border_radius'));
    $field->setNotice('z.B. 8px, 50%, 0.5rem');
    
    // Is Light
    $field = $form->addSelectField('is_light');
    $field->setLabel($addon->i18n('extra_styles_is_light'));
    $select = $field->getSelect();
    $select->addOptions([
        0 => 'Dunkle Schrift (Standard)',
        1 => 'Weiße Schrift'
    ]);
    
    // Priority
    $field = $form->addTextField('priority');
    $field->setLabel($addon->i18n('extra_styles_priority'));
    $field->setNotice('Sortierung (niedriger = weiter oben)');
    
    // Status
    $field = $form->addSelectField('status');
    $field->setLabel($addon->i18n('extra_styles_status'));
    $select = $field->getSelect();
    $select->addOptions([
        0 => $addon->i18n('extra_styles_inactive'),
        1 => $addon->i18n('extra_styles_active')
    ]);
    
    $content .= $form->get();
    
    // JavaScript für Slug-Generator
    $content .= '
    <script>
    function generateSlugFromName() {
        var nameInput = document.querySelector("input[id*=\'-name\']");
        var slugInput = document.querySelector("input[id*=\'-slug\']");
        
        if (!nameInput || !slugInput) {
            console.error("Name or Slug field not found");
            return;
        }
        
        var name = nameInput.value;
        if (!name) {
            alert("Bitte zuerst einen Namen eingeben");
            return;
        }
        
        var slug = name
            .toLowerCase()
            .replace(/ä/g, "ae")
            .replace(/ö/g, "oe")
            .replace(/ü/g, "ue")
            .replace(/ß/g, "ss")
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
        
        slugInput.value = slug;
    }
    </script>
    ';
    
    // JavaScript für Slug-Generator
    $content .= '
    <script>
    function generateSlugFromName() {
        var nameInput = document.querySelector("input[id*=\\"name\\"]");
        var slugInput = document.querySelector("input[id*=\\"slug\\"]");
        
        if (!nameInput || !slugInput) {
            console.error("Name or Slug field not found");
            return;
        }
        
        var name = nameInput.value;
        if (!name) {
            alert("Bitte zuerst einen Namen eingeben");
            return;
        }
        
        var slug = name
            .toLowerCase()
            .replace(/ä/g, "ae")
            .replace(/ö/g, "oe")
            .replace(/ü/g, "ue")
            .replace(/ß/g, "ss")
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
        
        slugInput.value = slug;
    }
    </script>
    ';
    
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $title, false);
    $fragment->setVar('body', $content, false);
    $content = $fragment->parse('core/page/section.php');
    
    echo $content;
    
    echo '</div>'; // Ende col-lg-8
    
    // Preview Panel rechts
    echo '<div class="col-lg-4">';
    
    $previewContent = '
    <div id="extra-styles-preview-wrapper">
        <div id="preview-box" style="padding: 30px; min-height: 200px; border-radius: 0; transition: all 0.3s ease; background: #f5f5f5; color: #333;">
            <h3 style="margin: 0 0 15px 0; font-size: 1.3em; font-weight: bold;">Beispiel Überschrift</h3>
            <p style="margin: 0 0 10px 0; line-height: 1.5;">Dies ist ein Beispieltext um die Farben und Stile zu testen. Der Text sollte gut lesbar sein und <a href="#" onclick="return false;">hier ist ein Beispiel-Link</a> im Fließtext.</p>
            <p style="margin: 0;"><small style="font-size: 0.85em;">Kleingedruckter Text zur Überprüfung der Lesbarkeit.</small></p>
        </div>
    </div>
    
    <style>
    .col-lg-4 {
        position: sticky;
        top: 0;
        align-self: flex-start;
    }
    #preview-box {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    </style>
    ';
    
    $previewFragment = new rex_fragment();
    $previewFragment->setVar('class', 'info', false);
    $previewFragment->setVar('title', $addon->i18n('extra_styles_preview'), false);
    $previewFragment->setVar('body', $previewContent, false);
    echo $previewFragment->parse('core/page/section.php');
    
    echo '</div>'; // Ende col-lg-4
    echo '</div>'; // Ende row
}
