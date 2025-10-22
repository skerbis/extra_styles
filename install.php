<?php
/** @var rex_addon $this */

// Installiere Datenbanktabelle
$sql = rex_sql::factory();
$sql->setQuery(str_replace('%TABLE_PREFIX%', rex::getTablePrefix(), rex_file::get(__DIR__ . '/install.sql')));

// Stelle sicher dass border_radius und custom_css Spalten existieren (für Updates von alten Versionen)
$table = rex::getTable('extra_styles');
try {
    $checkSql = rex_sql::factory();
    
    // link_color Spalte
    $checkSql->setQuery("SHOW COLUMNS FROM `$table` LIKE 'link_color'");
    if ($checkSql->getRows() == 0) {
        $sql->setQuery("ALTER TABLE `$table` ADD `link_color` VARCHAR(7) DEFAULT NULL AFTER `text_color`");
    }
    
    // border_radius Spalte
    $checkSql->setQuery("SHOW COLUMNS FROM `$table` LIKE 'border_radius'");
    if ($checkSql->getRows() == 0) {
        $sql->setQuery("ALTER TABLE `$table` ADD `border_radius` VARCHAR(20) DEFAULT NULL AFTER `border_width`");
    }
} catch (rex_sql_exception $e) {
    // Ignorieren - Spalte existiert bereits oder Tabelle noch nicht vorhanden
}

// Erstelle Assets-Ordner
$assetsPath = rex_path::assets('addons/extra_styles');
if (!file_exists($assetsPath)) {
    rex_dir::create($assetsPath);
}

// Erstelle leere custom.css
$customCssPath = $assetsPath . 'custom.css';
if (!file_exists($customCssPath)) {
    rex_file::put($customCssPath, "/* Extra Styles - Custom CSS */\n/* Diese Datei wird automatisch generiert */\n");
}

// Setze Config-Werte
$this->setConfig('css_generated', false);
