<?php
/** @var rex_addon $this */

// Spalten border_radius und custom_css hinzufügen falls noch nicht vorhanden
$sql = rex_sql::factory();
$table = rex::getTable('extra_styles');

try {
    // border_radius
    $sql->setQuery("SHOW COLUMNS FROM `$table` LIKE 'border_radius'");
    if ($sql->getRows() == 0) {
        $sql->setQuery("ALTER TABLE `$table` ADD `border_radius` VARCHAR(20) DEFAULT NULL AFTER `border_width`");
    }
    
    // custom_css
    $sql->setQuery("SHOW COLUMNS FROM `$table` LIKE 'custom_css'");
    if ($sql->getRows() == 0) {
        $sql->setQuery("ALTER TABLE `$table` ADD `custom_css` TEXT DEFAULT NULL AFTER `border_radius`");
    }
} catch (rex_sql_exception $e) {
    // Fehler ignorieren
}

// Bei Updates CSS neu generieren
$this->setConfig('css_generated', false);
$this->setConfig('regenerate_css', true);
