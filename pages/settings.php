<?php
/**
 * Extra Styles - Settings Page
 * 
 * Hier kann der Admin festlegen, welche Style-Typen verfügbar sein sollen
 */

$addon = rex_addon::get('extra_styles');

// CSRF Token Check
$func = rex_request('func', 'string');
$csrfToken = rex_csrf_token::factory('extra_styles_settings');

// Save Settings
if ($func == 'save' && $csrfToken->isValid()) {
    $enabledTypes = rex_post('enabled_types', 'array', []);
    $customCss = rex_post('custom_css', 'string', '');
    
    // Save Site Defaults availability
    $enableSocialMediaLinks = rex_post('enable_social_media_links', 'boolean', false);
    $addon->setConfig('enable_social_media_links', $enableSocialMediaLinks);
    
    // Save to addon config
    $addon->setConfig('enabled_types', [
        'card' => in_array('card', $enabledTypes),
        'section' => in_array('section', $enabledTypes),
        'background' => in_array('background', $enabledTypes),
        'border' => in_array('border', $enabledTypes),
    ]);
    
    // Always save custom CSS
    $oldCustomCss = $addon->getConfig('custom_css', '');
    $addon->setConfig('custom_css', $customCss);
    
    // Regenerate CSS file if custom CSS changed
    if ($customCss !== $oldCustomCss) {
        \ExtraStyles\CssGenerator::generate();
    }
    
    echo rex_view::success($addon->i18n('extra_styles_settings_saved'));
}

// Get current settings (after save to get updated values)
$enabledTypes = $addon->getConfig('enabled_types', [
    'card' => true,
    'section' => true,
    'background' => true,
    'border' => true,
]);

$customCss = $addon->getConfig('custom_css', '');
$enableSocialMediaLinks = $addon->getConfig('enable_social_media_links', true);

?>

<div class="rex-page-section">
    <h2><?= $addon->i18n('extra_styles_settings_title') ?></h2>
    <p><?= $addon->i18n('extra_styles_settings_description') ?></p>
</div>

<form action="<?= rex_url::currentBackendPage() ?>" method="post">
    <?= $csrfToken->getHiddenField() ?>
    <input type="hidden" name="func" value="save" />
    <!-- Hidden fields to preserve custom CSS when saving type settings -->
    <input type="hidden" name="custom_css" value="<?= htmlspecialchars($customCss) ?>" />
    
    <section class="rex-page-section">
        <div class="panel panel-default">
            <header class="panel-heading">
                <div class="panel-title"><?= $addon->i18n('extra_styles_settings_enabled_types') ?></div>
            </header>
            <div class="panel-body">
                
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="enabled_types[]" value="card" <?= $enabledTypes['card'] ? 'checked' : '' ?> />
                            <?= $addon->i18n('extra_styles_type_card_enabled') ?>
                        </label>
                        <p class="help-block">uk-card-* Klassen für Kacheln und Content-Boxen</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="enabled_types[]" value="section" <?= $enabledTypes['section'] ? 'checked' : '' ?> />
                            <?= $addon->i18n('extra_styles_type_section_enabled') ?>
                        </label>
                        <p class="help-block">uk-section-* Klassen für große Abschnitte mit Padding</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="enabled_types[]" value="background" <?= $enabledTypes['background'] ? 'checked' : '' ?> />
                            <?= $addon->i18n('extra_styles_type_background_enabled') ?>
                        </label>
                        <p class="help-block">uk-background-* Klassen für Hintergründe ohne Padding</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="enabled_types[]" value="border" <?= $enabledTypes['border'] ? 'checked' : '' ?> />
                            <?= $addon->i18n('extra_styles_type_border_enabled') ?>
                        </label>
                        <p class="help-block">uk-border-* Klassen nur für Rahmen</p>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    
    <section class="rex-page-section">
        <div class="panel panel-default">
            <header class="panel-heading">
                <div class="panel-title"><?= $addon->i18n('extra_styles_site_defaults_availability') ?></div>
            </header>
            <div class="panel-body">
                
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="enable_social_media_links" value="1" <?= $enableSocialMediaLinks ? 'checked' : '' ?> />
                            <?= $addon->i18n('extra_styles_enable_social_media_links') ?>
                        </label>
                        <p class="help-block">Social Media Links im Backend unter Site Defaults verfügbar machen</p>
                    </div>
                </div>
                
            </div>
            <footer class="panel-footer">
                <div class="rex-form-panel-footer">
                    <div class="btn-toolbar">
                        <button class="btn btn-save rex-form-aligned" type="submit" name="save" value="1">
                            <?= rex_i18n::msg('form_save') ?>
                        </button>
                    </div>
                </div>
            </footer>
        </div>
    </section>
</form>

<form action="<?= rex_url::currentBackendPage() ?>" method="post">
    <?= $csrfToken->getHiddenField() ?>
    <input type="hidden" name="func" value="save" />
    <!-- Hidden fields to preserve enabled types when saving custom CSS -->
    <?php foreach ($enabledTypes as $type => $enabled): ?>
        <?php if ($enabled): ?>
            <input type="hidden" name="enabled_types[]" value="<?= $type ?>" />
        <?php endif; ?>
    <?php endforeach; ?>
    
    <section class="rex-page-section">
        <div class="panel panel-default">
            <header class="panel-heading">
                <div class="panel-title"><?= $addon->i18n('extra_styles_custom_css_title') ?></div>
            </header>
            <div class="panel-body">
                
                <div class="form-group">
                    <label for="custom_css"><?= $addon->i18n('extra_styles_custom_css_label') ?></label>
                    <p class="help-block"><?= $addon->i18n('extra_styles_custom_css_description') ?></p>
                    <textarea 
                        id="custom_css" 
                        name="custom_css" 
                        class="form-control codemirror" 
                        data-codemirror-theme="dracula" 
                        data-codemirror-mode="css"
                        rows="20"
                    ><?= htmlspecialchars($customCss) ?></textarea>
                </div>
                
            </div>
            <footer class="panel-footer">
                <div class="rex-form-panel-footer">
                    <div class="btn-toolbar">
                        <button class="btn btn-save rex-form-aligned" type="submit" name="save" value="1">
                            <?= rex_i18n::msg('form_save') ?>
                        </button>
                    </div>
                </div>
            </footer>
        </div>
    </section>
</form>

<style>
.help-block {
    font-size: 0.9em;
    color: #666;
    margin: 5px 0 0 25px;
}
</style>
