<?php
/** @var rex_addon $this */

// Lösche Datenbanktabelle
$sql = rex_sql::factory();
$sql->setQuery('DROP TABLE IF EXISTS `' . rex::getTable('extra_styles') . '`');

// Lösche custom.css (optional - kann auch behalten werden)
$customCssPath = rex_path::assets('addons/extra_styles/custom.css');
if (file_exists($customCssPath)) {
    rex_file::delete($customCssPath);
}
