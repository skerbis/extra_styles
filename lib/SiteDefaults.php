<?php
/**
 * Extra Styles AddOn
 * Site Defaults API Class
 */

namespace ExtraStyles;

use rex_addon;

class SiteDefaults
{
    /**
     * Gibt das Info-Button-Menü als HTML aus
     * 
     * Verwendung im Template:
     * <?= ExtraStyles\SiteDefaults::getInfoButtonMenu() ?>
     * 
     * @return string HTML des Info-Button-Menüs
     */
    public static function getInfoButtonMenu(): string
    {
        $addon = rex_addon::get('extra_styles');
        
        $icon = $addon->getConfig('info_button_icon', 'info');
        $ratio = $addon->getConfig('info_button_ratio', '1.5');
        $hiddenText = $addon->getConfig('info_button_hidden_text', 'Mehr Informationen');
        $title = $addon->getConfig('info_button_title', 'Mehr Informationen');
        $cardClass = self::getInfoMenuCardClass();
        $menuItems = $addon->getConfig('info_menu_items', []);
        
        // Ratio mit Punkt statt Komma sicherstellen
        $ratio = str_replace(',', '.', $ratio);
        
        $html = '<div>';
        $html .= '<button class="uk-margin-right uk-light" type="button" uk-icon="icon: ' . htmlspecialchars($icon) . '; ratio: ' . htmlspecialchars($ratio) . '">';
        $html .= '<span class="uk-hidden">' . htmlspecialchars($hiddenText) . '</span>';
        $html .= '</button>';
        $html .= '<div uk-drop="mode: click">';
        $html .= '<div class="uk-card uk-light uk-card-body ' . htmlspecialchars($cardClass) . '">';
        
        if ($title) {
            $html .= '<strong>' . htmlspecialchars($title) . '</strong>';
            if (!empty($menuItems)) {
                $html .= '<hr>';
            }
        }
        
        $itemCount = 0;
        foreach ($menuItems as $item) {
            if (!empty($item['url']) && !empty($item['label'])) {
                if ($itemCount > 0 || $title) {
                    // HR nur wenn nicht der erste Eintrag ODER wenn Titel vorhanden
                }
                $html .= '<span uk-icon="icon: ' . htmlspecialchars($item['icon']) . '"></span> ';
                $html .= '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']) . '</a>';
                $itemCount++;
                // HR nach jedem Item außer dem letzten (wird später behandelt)
                $html .= '<hr>';
            }
        }
        
        // Letztes <hr> korrekt entfernen
        if ($itemCount > 0) {
            $html = substr($html, 0, -4); // Entfernt die letzten 4 Zeichen: <hr>
        }
        
        $html .= '</div>'; // Schließt uk-card
        $html .= '</div>'; // Schließt uk-drop
        $html .= '</div>'; // Schließt Wrapper
        
        return $html;
    }
    
    /**
     * Gibt die Logo-Beschriftung aus
     * 
     * Verwendung im Template:
     * <?= ExtraStyles\SiteDefaults::getLogoText() ?>
     * 
     * @return string Logo-Text
     */
    public static function getLogoText(): string
    {
        $addon = rex_addon::get('extra_styles');
        return $addon->getConfig('logo_text', \rex::getServerName());
    }
    
    /**
     * Gibt die Card-Klasse für das Info-Menü zurück
     * 
     * Verwendung im Template:
     * $cardClass = ExtraStyles\SiteDefaults::getInfoMenuCardClass();
     * $cardClass = ExtraStyles\SiteDefaults::getInfoMenuCardClass('uk-card-secondary');
     * 
     * @param string $fallback Optional: Eigene Fallback-Klasse (Standard: "uk-card-primary")
     * @return string Card CSS-Klasse
     */
    public static function getInfoMenuCardClass(string $fallback = 'uk-card-primary'): string
    {
        $addon = rex_addon::get('extra_styles');
        $cardClass = $addon->getConfig('info_menu_card_class', $fallback);
        
        // Fallback wenn leer
        if (empty(trim($cardClass))) {
            $cardClass = $fallback;
        }
        
        return $cardClass;
    }
    
    /**
     * Prüft ob ein Info-Menü konfiguriert ist
     * 
     * Verwendung im Template:
     * <?php if (ExtraStyles\SiteDefaults::hasInfoMenu()): ?>
     *     <?= ExtraStyles\SiteDefaults::getInfoButtonMenu() ?>
     * <?php endif; ?>
     * 
     * @return bool True wenn Menüpunkte vorhanden sind
     */
    public static function hasInfoMenu(): bool
    {
        $addon = rex_addon::get('extra_styles');
        $menuItems = $addon->getConfig('info_menu_items', []);
        
        if (!is_array($menuItems) || empty($menuItems)) {
            return false;
        }
        
        // Prüfen ob mindestens ein gültiger Menüpunkt existiert
        foreach ($menuItems as $item) {
            if (!empty($item['url']) && !empty($item['label'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Gibt Social Media Links als HTML aus (UIKit3 Icons)
     * 
     * Verwendung im Template:
     * <?= ExtraStyles\SiteDefaults::getSocialMediaLinks() ?>
     * 
     * @param bool $centered Zentriert (default: true)
     * @param float|string $ratio Icon-Größe (default: 1.5)
     * @return string HTML der Social Media Links
     */
    public static function getSocialMediaLinks(bool $centered = true, $ratio = 1.5): string
    {
        $addon = rex_addon::get('extra_styles');
        $socialLinks = $addon->getConfig('social_media_links', []);
        
        if (!is_array($socialLinks) || empty($socialLinks)) {
            return '';
        }
        
        // Ratio mit Punkt statt Komma sicherstellen
        $ratio = str_replace(',', '.', (string)$ratio);
        
        $html = '<div class="uk-text-center' . ($centered ? '' : ' uk-text-left') . '">';
        
        foreach ($socialLinks as $link) {
            if (!empty($link['url']) && !empty($link['platform'])) {
                $html .= '<a href="' . htmlspecialchars($link['url']) . '" ';
                $html .= 'class="uk-icon-link uk-margin-small-right" ';
                $html .= 'uk-icon="icon: ' . htmlspecialchars($link['platform']) . '; ratio: ' . htmlspecialchars($ratio) . '" ';
                
                if (!empty($link['label'])) {
                    $html .= 'aria-label="' . htmlspecialchars($link['label']) . '" ';
                    $html .= 'title="' . htmlspecialchars($link['label']) . '"';
                }
                
                $html .= '></a>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Prüft ob Social Media Links konfiguriert sind
     * 
     * @return bool True wenn Social Links vorhanden sind
     */
    public static function hasSocialMediaLinks(): bool
    {
        $addon = rex_addon::get('extra_styles');
        $socialLinks = $addon->getConfig('social_media_links', []);
        
        if (!is_array($socialLinks) || empty($socialLinks)) {
            return false;
        }
        
        foreach ($socialLinks as $link) {
            if (!empty($link['url']) && !empty($link['platform'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Gibt einen einzelnen Config-Wert zurück
     * 
     * @param string $key Config-Key
     * @param mixed $default Default-Wert
     * @return mixed
     */
    public static function getConfig(string $key, $default = null)
    {
        $addon = rex_addon::get('extra_styles');
        return $addon->getConfig($key, $default);
    }
}
