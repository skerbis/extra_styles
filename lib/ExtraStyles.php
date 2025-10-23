<?php
/**
 * Extra Styles AddOn
 * API Class für Module
 */

namespace ExtraStyles;

use rex;
use rex_sql;

class ExtraStyles
{
    /**
     * Alle aktiven Styles eines bestimmten Typs abrufen
     * 
     * @param string|null $type Typ filtern: card, section, background, border (null = alle)
     * @return array Array von Styles
     */
    public static function getAll(?string $type = null): array
    {
        // Prüfen ob der Typ in den Settings aktiviert ist
        if ($type !== null && !self::isTypeEnabled($type)) {
            return [];
        }
        
        $sql = rex_sql::factory();
        $query = 'SELECT * FROM ' . rex::getTable('extra_styles') . ' WHERE status = 1';
        
        if ($type !== null) {
            $query .= ' AND type = :type';
        }
        
        $query .= ' ORDER BY priority ASC, name ASC';
        
        $sql->setQuery($query, $type !== null ? ['type' => $type] : []);
        
        $styles = [];
        while ($sql->hasNext()) {
            $styles[] = [
                'id' => $sql->getValue('id'),
                'name' => $sql->getValue('name'),
                'slug' => $sql->getValue('slug'),
                'type' => $sql->getValue('type'),
                'color' => $sql->getValue('color'),
                'text_color' => $sql->getValue('text_color'),
                'border_color' => $sql->getValue('border_color'),
                'border_width' => $sql->getValue('border_width'),
                'border_radius' => $sql->getValue('border_radius'),
                'is_light' => $sql->getValue('is_light'),
                'priority' => $sql->getValue('priority'),
                'status' => $sql->getValue('status'),
            ];
            $sql->next();
        }
        
        return $styles;
    }
    
    /**
     * Prüfen ob ein Style-Typ in den Settings aktiviert ist
     * 
     * @param string $type
     * @return bool
     */
    public static function isTypeEnabled(string $type): bool
    {
        $addon = \rex_addon::get('extra_styles');
        $enabledTypes = $addon->getConfig('enabled_types', [
            'card' => true,
            'section' => true,
            'background' => true,
            'border' => true,
        ]);
        
        return isset($enabledTypes[$type]) && $enabledTypes[$type];
    }    /**
     * Gibt ein Style-Array für Select-Felder zurück
     * Kombiniert Standard-UIKit-Styles mit Custom Styles
     * 
     * @param string $type Typ: card, section, background
     * @return array Key-Value Array für Select-Options
     */
    public static function getSelectOptions(string $type): array
    {
        $options = [];
        
        // Standard UIKit Styles
        switch ($type) {
            case 'card':
                $options = [
                    'default' => 'Standard',
                    'primary' => 'Hauptfarbe',
                    'secondary' => 'Sekundär',
                    'muted' => 'Muted',
                    'transparent' => 'Transparent',
                ];
                break;
                
            case 'section':
                $options = [
                    'default' => 'Standard',
                    'primary' => 'Primär',
                    'secondary' => 'Sekundär',
                    'muted' => 'Muted',
                ];
                break;
                
            case 'background':
                $options = [
                    'default' => 'Standard',
                    'primary' => 'Primär',
                    'secondary' => 'Sekundär',
                    'muted' => 'Muted',
                ];
                break;
                
            default:
                $options = [
                    'default' => 'Standard',
                ];
                break;
        }
        
        // Custom Styles hinzufügen
        try {
            $customStyles = self::getAll($type);
            if (is_array($customStyles)) {
                foreach ($customStyles as $style) {
                    if (isset($style['slug']) && isset($style['name']) && $style['slug'] && $style['name']) {
                        // Bei is_light wird "uk-light" an den Slug angehängt
                        $value = $style['slug'];
                        if (isset($style['is_light']) && $style['is_light']) {
                            $value .= ' uk-light';
                        }
                        $options[$value] = $style['name'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Bei Fehler einfach nur die Standard-Optionen zurückgeben
            // z.B. wenn Tabelle noch nicht existiert
        }
        
        return $options;
    }
    
    /**
     * Gibt einen einzelnen Style zurück
     * 
     * @param int $id
     * @return array|null
     */
    public static function getById(int $id): ?array
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM ' . rex::getTable('extra_styles') . ' WHERE id = ?', [$id]);
        
        if ($sql->getRows() === 1) {
            return [
                'id' => $sql->getValue('id'),
                'name' => $sql->getValue('name'),
                'slug' => $sql->getValue('slug'),
                'type' => $sql->getValue('type'),
                'color' => $sql->getValue('color'),
                'text_color' => $sql->getValue('text_color'),
                'border_color' => $sql->getValue('border_color'),
                'border_width' => $sql->getValue('border_width'),
                'border_radius' => $sql->getValue('border_radius'),
                'is_light' => $sql->getValue('is_light'),
                'priority' => $sql->getValue('priority'),
                'status' => $sql->getValue('status'),
            ];
        }
        
        return null;
    }
    
    /**
     * Gibt einen einzelnen Style nach Slug zurück
     * 
     * @param string $slug
     * @return array|null
     */
    public static function getBySlug(string $slug): ?array
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM ' . rex::getTable('extra_styles') . ' WHERE slug = ? AND status = 1', [$slug]);
        
        if ($sql->getRows() === 1) {
            return [
                'id' => $sql->getValue('id'),
                'name' => $sql->getValue('name'),
                'slug' => $sql->getValue('slug'),
                'type' => $sql->getValue('type'),
                'color' => $sql->getValue('color'),
                'text_color' => $sql->getValue('text_color'),
                'border_color' => $sql->getValue('border_color'),
                'border_width' => $sql->getValue('border_width'),
                'border_radius' => $sql->getValue('border_radius'),
                'is_light' => $sql->getValue('is_light'),
                'priority' => $sql->getValue('priority'),
                'status' => $sql->getValue('status'),
            ];
        }
        
        return null;
    }
    
    /**
     * Generiert einen Slug aus einem Namen
     * 
     * @param string $name
     * @return string
     */
    public static function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Prüfen ob Slug bereits existiert
        $originalSlug = $slug;
        $counter = 1;
        
        while (self::slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Prüft ob ein Slug bereits existiert
     * 
     * @param string $slug
     * @param int|null $excludeId ID die ausgeschlossen werden soll (bei Updates)
     * @return bool
     */
    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = rex_sql::factory();
        
        if ($excludeId) {
            $sql->setQuery('SELECT id FROM ' . rex::getTable('extra_styles') . ' WHERE slug = ? AND id != ?', [$slug, $excludeId]);
        } else {
            $sql->setQuery('SELECT id FROM ' . rex::getTable('extra_styles') . ' WHERE slug = ?', [$slug]);
        }
        
        return $sql->getRows() > 0;
    }
    
    /**
     * Löscht einen Style
     * 
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        try {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable('extra_styles'));
            $sql->setWhere(['id' => $id]);
            $sql->delete();
            
            // CSS neu generieren
            CssGenerator::generate();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Gibt die CSS-Datei URL mit Cachebuster zurück
     * Der Cachebuster basiert auf dem Änderungszeitpunkt der CSS-Datei
     * 
     * Verwendung im Template:
     * <link rel="stylesheet" href="<?= ExtraStyles\ExtraStyles::getCssUrl() ?>">
     * 
     * @return string URL zur CSS-Datei mit Cachebuster
     */
    public static function getCssUrl(): string
    {
        $cssPath = \rex_path::assets('addons/extra_styles/custom.css');
        $cssUrl = \rex_url::assets('addons/extra_styles/custom.css');
        
        // Prüfen ob Datei existiert und Änderungszeitpunkt als Cachebuster verwenden
        if (file_exists($cssPath)) {
            $mtime = filemtime($cssPath);
            $cssUrl .= '?v=' . $mtime;
        }
        
        return $cssUrl;
    }
    
    /**
     * Gibt einen kompletten Link-Tag für die CSS-Datei mit Cachebuster zurück
     * 
     * Verwendung im Template:
     * <?= ExtraStyles\ExtraStyles::getCssTag() ?>
     * 
     * @return string Kompletter <link> Tag
     */
    public static function getCssTag(): string
    {
        return '<link rel="stylesheet" href="' . self::getCssUrl() . '">';
    }
}

