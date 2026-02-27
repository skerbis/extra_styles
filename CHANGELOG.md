# Changelog

Alle wichtigen Änderungen am Extra Styles AddOn werden hier dokumentiert.

## [1.0.1] - 2026-02-27

### Geändert
- Manuelle CSS-Regeln in den Einstellungen sind jetzt nur noch für Administratoren sichtbar und bearbeitbar.
- Serverseitige Absicherung ergänzt: `custom_css` wird nur gespeichert, wenn der aktuelle Benutzer Administrator ist.
- Reihenfolge der CSS-Ausgabe verbessert: individuelle CSS-Regeln werden am Ende der generierten Datei angehängt, damit bestehende Regeln gezielt überschrieben werden können.

### Technisch
- AddOn-Version in `package.yml` auf `1.0.1` erhöht.

## [1.0.0] - 2025-10-22

### Hinzugefügt
- Initiales Release des Extra Styles AddOns
- Verwaltungsoberfläche für Custom Styles mit UIKit3-Interface
- Moderner Color Picker (Pickr) für Farbauswahl
- Live-Preview beim Erstellen/Bearbeiten von Styles
- Unterstützung für 4 Style-Typen: Card, Section, Background, Border
- Automatische CSS-Generierung in `assets/addons/extra_styles/custom.css`
- API-Klasse `ExtraStyles` für Module-Integration
- CSS-Generator-Klasse für dynamische Style-Ausgabe
- Deutsche und englische Sprachdateien
- Umfassende Dokumentation (README.md, INTEGRATION.md)
- Beispiel-Integration für Oejv Cards und Oejv Slides Module

### Features
- **Flexible Styles**: Name, Slug, Typ, Farben (Hintergrund, Text, Rahmen)
- **UIKit3-Kompatibel**: Erweitert Standard-UIKit-Classes
- **Fallback-sicher**: Bei Löschung wird auf "default" zurückgefallen
- **Prioritäten**: Sortierung der Styles in Auswahllisten
- **Status**: Aktiv/Inaktiv schalten ohne Löschen
- **Auto-Slug**: Automatische Generierung aus Namen
- **Timestamps**: createdate/updatedate/createuser/updateuser

### Technisch
- REDAXO >= 5.13
- PHP >= 8.0
- Extension Points für REX_FORM_CONTROL_FIELDS und REX_FORM_SAVED
- Datenbanktabelle `rex_extra_styles`
- Pickr Color Picker via CDN
- Automatisches CSS-Einbinden im Frontend

### Integration
- MForm-Integration via `ExtraStyles::getSelectOptions('type')`
- Kompatibel mit MBlock
- Legacy-Optionen bleiben erhalten (array_merge)
- Keine Änderungen an Output-Dateien nötig

### Dokumentation
- README.md: Vollständige Anleitung für Redakteure und Entwickler
- INTEGRATION.md: Schritt-für-Schritt Modul-Integration
- Code-Beispiele für alle Use Cases
- API-Referenz
- Troubleshooting-Guide

---

Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/)
