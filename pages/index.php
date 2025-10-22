<?php

/**
 * Extra Styles AddOn
 */

$addon = rex_addon::get('extra_styles');

echo rex_view::title($addon->i18n('extra_styles_title'));

rex_be_controller::includeCurrentPageSubPath();
