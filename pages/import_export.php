<?php
/**
 * Extra Styles - Import/Export Page
 */

use ExtraStyles\ExtraStyles;
use ExtraStyles\CssGenerator;

// Export MUSS ganz am Anfang sein, BEVOR rex_addon geladen wird
$func = rex_request('func', 'string');

if ($func == 'export') {
    $csrfToken = rex_csrf_token::factory('extra_styles_import_export');
    
    if ($csrfToken->isValid()) {
        // Output Buffer leeren BEVOR irgendwas ausgegeben wurde
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM ' . rex::getTable('extra_styles') . ' ORDER BY priority ASC, name ASC');
        
        $styles = [];
        for ($i = 0; $i < $sql->getRows(); $i++) {
            $styles[] = [
                'name' => $sql->getValue('name'),
                'slug' => $sql->getValue('slug'),
                'type' => $sql->getValue('type'),
                'color' => $sql->getValue('color'),
                'text_color' => $sql->getValue('text_color'),
                'link_color' => $sql->getValue('link_color'),
                'border_color' => $sql->getValue('border_color'),
                'border_width' => $sql->getValue('border_width'),
                'border_radius' => $sql->getValue('border_radius'),
                'is_light' => $sql->getValue('is_light'),
                'priority' => $sql->getValue('priority'),
                'status' => $sql->getValue('status'),
            ];
            $sql->next();
        }
        
        // Custom CSS hinzufügen
        $addon = rex_addon::get('extra_styles');
        $exportData = [
            'styles' => $styles,
            'custom_css' => $addon->getConfig('custom_css', ''),
        ];
        
        $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'extra_styles_' . date('Y-m-d_H-i-s') . '.json';
        
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        echo $json;
        exit;
    }
}

$addon = rex_addon::get('extra_styles');
$csrfToken = rex_csrf_token::factory('extra_styles_import_export');

$message = '';

// Import
if ($func == 'import' && $csrfToken->isValid()) {
    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
        // Backup vor Import erstellen
        $backupDir = rex_path::addonData('extra_styles', 'backups');
        rex_dir::create($backupDir);
        
        $backupSql = rex_sql::factory();
        $backupSql->setQuery('SELECT * FROM ' . rex::getTable('extra_styles') . ' ORDER BY priority ASC, name ASC');
        
        $backupStyles = [];
        for ($i = 0; $i < $backupSql->getRows(); $i++) {
            $backupStyles[] = [
                'name' => $backupSql->getValue('name'),
                'slug' => $backupSql->getValue('slug'),
                'type' => $backupSql->getValue('type'),
                'color' => $backupSql->getValue('color'),
                'text_color' => $backupSql->getValue('text_color'),
                'link_color' => $backupSql->getValue('link_color'),
                'border_color' => $backupSql->getValue('border_color'),
                'border_width' => $backupSql->getValue('border_width'),
                'border_radius' => $backupSql->getValue('border_radius'),
                'is_light' => $backupSql->getValue('is_light'),
                'priority' => $backupSql->getValue('priority'),
                'status' => $backupSql->getValue('status'),
            ];
            $backupSql->next();
        }
        
        // Custom CSS auch im Backup speichern
        $backupData = [
            'styles' => $backupStyles,
            'custom_css' => $addon->getConfig('custom_css', ''),
        ];
        
        $backupJson = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.json';
        rex_file::put($backupFile, $backupJson);
        
        $json = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = json_decode($json, true);
        
        if (is_array($data)) {
            // Neues Format mit custom_css oder altes Format (nur Array)
            $styles = isset($data['styles']) ? $data['styles'] : $data;
            $customCss = isset($data['custom_css']) ? $data['custom_css'] : null;
            
            // Custom CSS importieren wenn vorhanden
            if ($customCss !== null) {
                $addon->setConfig('custom_css', $customCss);
            }
            
            $imported = 0;
            
            foreach ($styles as $style) {
                try {
                    // Prüfen ob Slug bereits existiert
                    $checkSql = rex_sql::factory();
                    $checkSql->setQuery('SELECT id FROM ' . rex::getTable('extra_styles') . ' WHERE slug = ?', [$style['slug']]);
                    
                    $sql = rex_sql::factory();
                    $sql->setTable(rex::getTable('extra_styles'));
                    
                    if ($checkSql->getRows() > 0) {
                        // Update existierenden Eintrag
                        $sql->setWhere(['id' => $checkSql->getValue('id')]);
                        $sql->setValue('name', $style['name']);
                        $sql->setValue('slug', $style['slug']);
                        $sql->setValue('type', $style['type']);
                        $sql->setValue('color', $style['color']);
                        $sql->setValue('text_color', $style['text_color'] ?? null);
                        $sql->setValue('link_color', $style['link_color'] ?? null);
                        $sql->setValue('border_color', $style['border_color'] ?? null);
                        $sql->setValue('border_width', $style['border_width'] ?? 1);
                        $sql->setValue('border_radius', $style['border_radius'] ?? null);
                        $sql->setValue('is_light', $style['is_light'] ?? 0);
                        $sql->setValue('priority', $style['priority'] ?? 100);
                        $sql->setValue('status', $style['status'] ?? 1);
                        $sql->setValue('updatedate', date('Y-m-d H:i:s'));
                        $sql->setValue('updateuser', rex::getUser()->getValue('login'));
                        $sql->update();
                    } else {
                        // Insert neuen Eintrag
                        $sql->setValue('name', $style['name']);
                        $sql->setValue('slug', $style['slug']);
                        $sql->setValue('type', $style['type']);
                        $sql->setValue('color', $style['color']);
                        $sql->setValue('text_color', $style['text_color'] ?? null);
                        $sql->setValue('link_color', $style['link_color'] ?? null);
                        $sql->setValue('border_color', $style['border_color'] ?? null);
                        $sql->setValue('border_width', $style['border_width'] ?? 1);
                        $sql->setValue('border_radius', $style['border_radius'] ?? null);
                        $sql->setValue('is_light', $style['is_light'] ?? 0);
                        $sql->setValue('priority', $style['priority'] ?? 100);
                        $sql->setValue('status', $style['status'] ?? 1);
                        $sql->setValue('createdate', date('Y-m-d H:i:s'));
                        $sql->setValue('updatedate', date('Y-m-d H:i:s'));
                        $sql->setValue('createuser', rex::getUser()->getValue('login'));
                        $sql->setValue('updateuser', rex::getUser()->getValue('login'));
                        $sql->insert();
                    }
                    
                    $imported++;
                } catch (rex_sql_exception $e) {
                    // Fehler ignorieren und weitermachen
                }
            }
            
            CssGenerator::generate();
            $message = rex_view::success($addon->i18n('extra_styles_import_success', $imported));
        } else {
            $message = rex_view::error($addon->i18n('extra_styles_import_invalid'));
        }
    } else {
        $message = rex_view::error($addon->i18n('extra_styles_import_error'));
    }
}

echo $message;

?>

<div class="row">
    <div class="col-lg-6">
        <section class="rex-page-section">
            <div class="panel panel-default">
                <header class="panel-heading">
                    <div class="panel-title"><?= $addon->i18n('extra_styles_export_title') ?></div>
                </header>
                <div class="panel-body">
                    <p><?= $addon->i18n('extra_styles_export_description') ?></p>
                    
                    <form action="<?= rex_url::currentBackendPage() ?>" method="post">
                        <?= $csrfToken->getHiddenField() ?>
                        <input type="hidden" name="func" value="export" />
                        
                        <button class="btn btn-primary" type="submit">
                            <i class="rex-icon fa-download"></i> <?= $addon->i18n('extra_styles_export_button') ?>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
    
    <div class="col-lg-6">
        <section class="rex-page-section">
            <div class="panel panel-default">
                <header class="panel-heading">
                    <div class="panel-title"><?= $addon->i18n('extra_styles_import_title') ?></div>
                </header>
                <div class="panel-body">
                    <p><?= $addon->i18n('extra_styles_import_description') ?></p>
                    
                    <form action="<?= rex_url::currentBackendPage() ?>" method="post" enctype="multipart/form-data">
                        <?= $csrfToken->getHiddenField() ?>
                        <input type="hidden" name="func" value="import" />
                        
                        <div class="form-group">
                            <label for="import_file"><?= $addon->i18n('extra_styles_import_file') ?></label>
                            <input type="file" class="form-control" id="import_file" name="import_file" accept=".json" required />
                        </div>
                        
                        <button class="btn btn-primary" type="submit">
                            <i class="rex-icon fa-upload"></i> <?= $addon->i18n('extra_styles_import_button') ?>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
.panel-body p {
    margin-bottom: 15px;
    color: #666;
}
</style>
