<?php
/**
 * Extra Styles - CSS Preview Page
 */

$addon = rex_addon::get('extra_styles');
$cssPath = rex_path::assets('addons/extra_styles/custom.css');

if (!file_exists($cssPath)) {
    echo rex_view::error('CSS-Datei existiert noch nicht. Bitte zuerst einen Stil speichern.');
    return;
}

$cssContent = rex_file::get($cssPath);
$cssSize = filesize($cssPath);
$cssModified = date('d.m.Y H:i:s', filemtime($cssPath));

?>

<div class="rex-page-section">
    <div class="panel panel-default">
        <header class="panel-heading">
            <div class="panel-title">Generierte CSS-Datei</div>
        </header>
        <div class="panel-body">
            <dl class="dl-horizontal">
                <dt>Dateipfad:</dt>
                <dd><code><?= rex_path::assets('addons/extra_styles/custom.css') ?></code></dd>
                
                <dt>Dateigröße:</dt>
                <dd><?= number_format($cssSize / 1024, 2) ?> KB</dd>
                
                <dt>Letzte Änderung:</dt>
                <dd><?= $cssModified ?></dd>
                
                <dt>Anzahl Zeilen:</dt>
                <dd><?= substr_count($cssContent, "\n") + 1 ?></dd>
            </dl>
            
            <div class="btn-toolbar" style="margin-top: 20px;">
                <a href="<?= \ExtraStyles\ExtraStyles::getCssUrl() ?>" class="btn btn-primary" download>
                    <i class="rex-icon fa-download"></i> CSS herunterladen
                </a>
                <a href="<?= \ExtraStyles\ExtraStyles::getCssUrl() ?>" class="btn btn-default" target="_blank">
                    <i class="rex-icon fa-external-link"></i> Im Browser öffnen
                </a>
            </div>
        </div>
    </div>
</div>

<div class="rex-page-section">
    <div class="panel panel-default">
        <header class="panel-heading">
            <div class="panel-title">CSS-Inhalt</div>
        </header>
        <div class="panel-body">
            <pre class="css-preview-code"><?= htmlspecialchars($cssContent) ?></pre>
        </div>
    </div>
</div>

<style>
.css-preview-code {
    background: #f5f5f5;
    color: #333;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow-x: auto;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', monospace;
    font-size: 13px;
    line-height: 1.6;
    max-height: 600px;
    margin: 0;
    white-space: pre;
}

.dl-horizontal dt {
    width: 140px;
    text-align: left;
}

.dl-horizontal dd {
    margin-left: 160px;
}
</style>
