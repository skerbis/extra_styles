# Extra Styles AddOn für REDAXO

Ein benutzerfreundliches AddOn für REDAXO 5.x, mit dem Redakteure und Admins zusätzliche Farben und Stile für UIKit3-Module erstellen können.

## Features

✨ **Einfache Bedienung**: Intuitive Oberfläche mit Live-Preview  
🎨 **Moderner Color Picker**: Farben visuell auswählen  
🔄 **Dynamische CSS-Generierung**: Automatische Erstellung der custom.css  
📦 **UIKit3 kompatibel**: Erweitert die Standard-UIKit-Styles  
🛡️ **Fallback-sicher**: Bei Löschung wird auf "default" zurückgefallen  
🎯 **Typisiert**: Card, Section, Background, Border Styles  

## Installation

1. AddOn über den Installer oder manuell installieren
2. AddOn aktivieren
3. Unter "AddOns" → "Extra Styles" eigene Styles anlegen

## Verwendung

### Backend: Styles verwalten

1. Navigiere zu **AddOns** → **Extra Styles**
2. Klicke auf **"Stil hinzufügen"**
3. Fülle das Formular aus:
   - **Name**: Sprechender Name (z.B. "Dunkelblau", "Akzentfarbe")
   - **Slug**: Wird automatisch generiert (z.B. "dunkelblau")
   - **Typ**: Card, Section, Background oder Border
   - **Hintergrundfarbe**: Hauptfarbe (Hex-Code)
   - **Textfarbe**: Optional, für bessere Lesbarkeit
   - **Rahmenfarbe**: Optional, für Cards mit Rahmen
   - **Rahmenstärke**: In Pixel (Standard: 1)
   - **Helle Schrift**: Aktivieren für weiße Schrift
   - **Reihenfolge**: Sortierung in der Auswahlliste
   - **Status**: Aktiv/Inaktiv

4. Die **Live-Preview** zeigt sofort, wie der Stil aussieht
5. **Speichern** – die custom.css wird automatisch generiert

### Frontend: Styles in Modulen verwenden

#### Methode 1: Mit der API-Klasse (empfohlen)

```php
<?php
use ExtraStyles\ExtraStyles;
use FriendsOfRedaxo\MForm;

$MForm = MForm::factory()
    ->addSelectField("1.0.ukColor")
    ->setLabel('Farbe:')
    ->setAttribute('class', 'selectpicker')
    ->setOptions(ExtraStyles::getSelectOptions('card'));

echo $MForm->show();
```

#### Methode 2: Manuelle Integration

```php
<?php
use ExtraStyles\ExtraStyles;

// Alle Card-Styles abrufen
$cardStyles = ExtraStyles::getAll('card');

// In Select-Optionen umwandeln
$options = ['default' => 'Standard'];
foreach ($cardStyles as $style) {
    $options[$style['slug']] = $style['name'];
}

// In MForm verwenden
$MForm = MForm::factory()
    ->addSelectField("1.0.cardStyle")
    ->setLabel('Card-Stil:')
    ->setOptions($options);
```

### Beispiel: Oejv Cards Modul erweitern

**Vorher (input.php):**
```php
->addSelectField("$id.0.ukColor")
->setLabel('Farbe:')
->setOptions(array(
    'default' => 'Standard',
    'primary' => 'Hauptfarbe',
    'secondary' => 'Sekundär',
    'muted' => 'Muted',
))
```

**Nachher (input.php):**
```php
->addSelectField("$id.0.ukColor")
->setLabel('Farbe:')
->setOptions(ExtraStyles::getSelectOptions('card'))
```

Das war's! Die Custom Styles werden automatisch hinzugefügt.

### Output im Template

Die generierten CSS-Klassen folgen dem UIKit3-Standard:

- **Cards**: `.uk-card-{slug}`
- **Sections**: `.uk-section-{slug}`
- **Backgrounds**: `.uk-background-{slug}`
- **Borders**: `.uk-border-{slug}`

Beispiel:
```html
<!-- Style "dunkelblau" vom Typ "card" -->
<div class="uk-card uk-card-dunkelblau">
    <div class="uk-card-body">
        Inhalt
    </div>
</div>
```

## API-Referenz

### ExtraStyles Klasse

```php
use ExtraStyles\ExtraStyles;

// Alle Styles abrufen
$allStyles = ExtraStyles::getAll();

// Nur Card-Styles
$cardStyles = ExtraStyles::getAll('card');

// Select-Optionen für MForm (inkl. Standard-UIKit-Styles)
$options = ExtraStyles::getSelectOptions('card');

// Style nach ID
$style = ExtraStyles::getById(5);

// Style nach Slug
$style = ExtraStyles::getBySlug('dunkelblau');

// Style löschen
ExtraStyles::delete(5);
```

### CssGenerator Klasse

```php
use ExtraStyles\CssGenerator;

// CSS manuell neu generieren
CssGenerator::generate();

// Pfad zur custom.css
$path = CssGenerator::getCssPath();

// URL zur custom.css
$url = CssGenerator::getCssUrl();
```

## Style-Typen

### Card (`uk-card-{slug}`)
Für UIKit3 Cards (Kacheln). Ersetzt/ergänzt:
- `uk-card-default`
- `uk-card-primary`
- `uk-card-secondary`
- `uk-card-muted`

### Section (`uk-section-{slug}`)
Für Sektionen/Abschnitte. Ersetzt/ergänzt:
- `uk-section-default`
- `uk-section-primary`
- `uk-section-secondary`
- `uk-section-muted`

### Background (`uk-background-{slug}`)
Für Hintergründe. Ersetzt/ergänzt:
- `uk-background-default`
- `uk-background-primary`
- `uk-background-secondary`
- `uk-background-muted`

### Border (`uk-border-{slug}`)
Nur für Rahmen, ohne Hintergrund.

## Tipps für Redakteure

1. **Sprechende Namen verwenden**: "Dunkelblau" statt "Farbe 1"
2. **Live-Preview nutzen**: Sehen Sie sofort, wie es aussieht
3. **Textfarbe beachten**: Für dunkle Hintergründe "Helle Schrift" aktivieren
4. **Rahmen optional**: Border-Farbe nur setzen, wenn gewünscht
5. **Status**: Inaktive Styles bleiben erhalten, werden aber nicht angezeigt

## Technische Details

### Datenbank

Tabelle: `rex_extra_styles`

Felder:
- `id`: Primärschlüssel
- `name`: Anzeigename
- `slug`: CSS-Klassen-Name (eindeutig)
- `type`: card, section, background, border
- `color`: Hintergrundfarbe (Hex)
- `text_color`: Textfarbe (Hex, optional)
- `border_color`: Rahmenfarbe (Hex, optional)
- `border_width`: Rahmenstärke (Integer)
- `is_light`: Helle Schrift (Boolean)
- `priority`: Sortierung (Integer)
- `status`: Aktiv/Inaktiv (Boolean)
- `createdate`, `updatedate`, `createuser`, `updateuser`

### CSS-Generierung

Die `custom.css` wird automatisch generiert:
- Bei Speichern/Löschen eines Styles
- Bei AddOn-Update
- Manuell über "CSS regenerieren"

Speicherort: `assets/addons/extra_styles/custom.css`

Die Datei wird im Frontend automatisch eingebunden.

### Dependencies

- **REDAXO**: >= 5.13
- **PHP**: >= 8.0
- **UIKit3**: Im REDAXO integriert
- **Pickr**: Color Picker (via CDN)

## Entwickler-Hinweise

### Extension Points

Das AddOn triggert folgende Extension Points:

```php
// Nach dem Speichern
rex_extension::register('REX_FORM_SAVED', function($ep) {
    // CSS wird automatisch regeneriert
});

// Nach dem Löschen
rex_extension::register('REX_FORM_DELETED', function($ep) {
    // CSS wird automatisch regeneriert
});
```

### Eigene Styles programmatisch anlegen

```php
$sql = rex_sql::factory();
$sql->setTable(rex::getTable('extra_styles'));
$sql->setValue('name', 'Mein Style');
$sql->setValue('slug', 'mein-style');
$sql->setValue('type', 'card');
$sql->setValue('color', '#ff0000');
$sql->setValue('status', 1);
$sql->setValue('createdate', date('Y-m-d H:i:s'));
$sql->setValue('updatedate', date('Y-m-d H:i:s'));
$sql->setValue('createuser', rex::getUser()->getValue('login'));
$sql->setValue('updateuser', rex::getUser()->getValue('login'));
$sql->insert();

// CSS regenerieren
ExtraStyles\CssGenerator::generate();
```

## Troubleshooting

### CSS wird nicht geladen
1. Prüfen Sie, ob die `custom.css` existiert: `assets/addons/extra_styles/custom.css`
2. Klicken Sie auf "CSS regenerieren"
3. Prüfen Sie die Dateiberechtigungen

### Styles werden nicht angezeigt
1. Prüfen Sie den **Status**: Muss "Aktiv" sein
2. Leeren Sie den Browser-Cache
3. Prüfen Sie, ob die CSS-Klasse korrekt im HTML ist

### Color Picker funktioniert nicht
1. Prüfen Sie die Browser-Konsole auf JavaScript-Fehler
2. Stellen Sie sicher, dass Pickr geladen wird (CDN)
3. Deaktivieren Sie Browser-Extensions

## Support

Bei Fragen oder Problemen:
- GitHub Issues
- REDAXO Slack: #addons
- Forum: https://www.redaxo.org/forum/

## Lizenz

MIT License

## Credits

- **UIKit3**: https://getuikit.com/
- **Pickr**: https://github.com/Simonwep/pickr
- **REDAXO**: https://www.redaxo.org/

---

**Version**: 1.0.0  
**Autor**: REDAXO AddOn  
**Erfordert**: REDAXO >= 5.13, PHP >= 8.0
