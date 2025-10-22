# Extra Styles - Modul-Integration Beispiele

Dieses Dokument zeigt, wie die Oejv-Module für die Verwendung von Extra Styles aktualisiert wurden.

## Beispiel 1: Oejv Cards Modul

### Vorher (input.php)

```php
->addSelectField("$id.0.ukColor")
->setLabel('Farbe:')
->setAttribute('class', 'selectpicker')
->setOptions(array(
    'default' => 'Standard',
    'primary' => 'Hauptfarbe',
    'secondary' => 'Sekundär',
    'muted' => 'Muted',
    'transparent' => 'Transparent',
    'transparent uk-light' => 'Transparent helle Schrift'
))
```

### Nachher (input.php)

```php
use ExtraStyles\ExtraStyles;

->addSelectField("$id.0.ukColor")
->setLabel('Farbe:')
->setAttribute('class', 'selectpicker')
->setOptions(array_merge(
    ExtraStyles::getSelectOptions('card'),
    ['transparent uk-light' => 'Transparent helle Schrift']
))
```

**Änderungen:**
1. `use ExtraStyles\ExtraStyles;` am Dateianfang hinzugefügt
2. `.setOptions()` verwendet jetzt `array_merge()` mit `ExtraStyles::getSelectOptions('card')`
3. Spezielle Legacy-Option `transparent uk-light` wird nach dem Merge hinzugefügt

### Sektions-Farbe

```php
->addSelectField("3.0.ukcolor")
->setLabel('Farbe:')
->setAttribute('class', 'selectpicker')
->setOptions(ExtraStyles::getSelectOptions('section'))
```

## Beispiel 2: Oejv Slides Modul

### Vorher (input.php)

```php
->addSelectField("2")
->setLabel('Farbeinstellungen:')
->setAttribute('class', 'selectpicker')
->setOptions(array(
    'default' => 'Standard',
    'primary' => 'Haupt-/Logo-Farbe',
    'secondary' => 'Sekundärfarbe',
    'muted' => 'Hell / Stumm',
    'none uk-light' => 'Helle Schrift erzwingen (bei Hintergrundbildern)',
))
```

### Nachher (input.php)

```php
use ExtraStyles\ExtraStyles;

->addSelectField("2")
->setLabel('Farbeinstellungen:')
->setAttribute('class', 'selectpicker')
->setOptions(array_merge(
    ExtraStyles::getSelectOptions('background'),
    ['none uk-light' => 'Helle Schrift erzwingen (bei Hintergrundbildern)']
))
```

## Typ-Übersicht

| Typ | Verwendung | CSS-Klasse | Beispiel |
|-----|------------|------------|----------|
| `card` | Für Cards/Kacheln | `uk-card-{slug}` | Card-Hintergründe |
| `section` | Für Sektionen | `uk-section-{slug}` | Sektion-Hintergründe |
| `background` | Für allgemeine Hintergründe | `uk-background-{slug}` | Hintergrundfarben |
| `border` | Nur Rahmen | `uk-border-{slug}` | Rahmen ohne Hintergrund |

## Best Practices

### 1. Legacy-Optionen beibehalten

Wenn ein Modul spezielle CSS-Klassen verwendet (z.B. `transparent uk-light`), füge diese mit `array_merge()` hinzu:

```php
->setOptions(array_merge(
    ExtraStyles::getSelectOptions('card'),
    ['transparent uk-light' => 'Transparent helle Schrift']
))
```

### 2. Richtigen Typ wählen

- **Cards**: `getSelectOptions('card')`
- **Sections**: `getSelectOptions('section')`
- **Backgrounds**: `getSelectOptions('background')`
- **Borders**: `getSelectOptions('border')`

### 3. Import nicht vergessen

```php
use ExtraStyles\ExtraStyles;
```

## Output.php anpassen (optional)

Die Output-Dateien müssen normalerweise **nicht** angepasst werden, da die Slugs direkt als CSS-Klassen verwendet werden:

```php
// Funktioniert automatisch:
$ukColor = 'uk-card-' . $rexVar['ukColor'];
```

Falls ein Style gelöscht wurde und der Slug noch in der DB steht, wird die CSS-Klasse trotzdem ausgegeben, aber das CSS fehlt (Fallback auf Browser-Standard).

### Expliziter Fallback (optional)

Wenn du sicherstellen willst, dass immer eine gültige Klasse existiert:

```php
$styleSlug = $rexVar['ukColor'];
$style = \ExtraStyles\ExtraStyles::getBySlug($styleSlug);

if (!$style) {
    $styleSlug = 'default'; // Fallback
}

$ukColor = 'uk-card-' . $styleSlug;
```

## Vollständiges Beispiel: Neues Modul erstellen

```php
<?php
use FriendsOfRedaxo\MForm;
use ExtraStyles\ExtraStyles;

$id = 1;
$MForm = MForm::factory()
    ->addTextField("$id.0.title", ['label' => 'Titel'])
    ->addTextAreaField("$id.0.text", ['label' => 'Text'])
    ->addSelectField("$id.0.cardColor")
        ->setLabel('Card-Farbe:')
        ->setAttribute('class', 'selectpicker')
        ->setOptions(ExtraStyles::getSelectOptions('card'));

echo MBlock::show($id, $MForm->show());
?>
```

## Testen

1. Installiere das `extra_styles` AddOn
2. Erstelle einen Custom Style (z.B. "Dunkelblau", Typ: Card)
3. Öffne ein Modul im Backend
4. Die neue Farbe sollte in der Auswahlliste erscheinen
5. Prüfe im Frontend, ob die CSS-Klasse `uk-card-dunkelblau` angewendet wird
6. Prüfe, ob `assets/addons/extra_styles/custom.css` existiert und die Regeln enthält

## Fehlersuche

### Style erscheint nicht in der Auswahl
- Prüfe, ob der Style **aktiv** ist (Status = 1)
- Prüfe den **Typ** (muss zum `getSelectOptions('typ')` passen)
- Cache leeren

### CSS wird nicht angewendet
- Prüfe, ob `custom.css` generiert wurde
- Klicke auf "CSS regenerieren" im AddOn
- Browser-Cache leeren
- DevTools: Prüfe, ob CSS-Datei geladen wird

### PHP-Fehler "Class ExtraStyles not found"
- Stelle sicher, dass `use ExtraStyles\ExtraStyles;` am Anfang steht
- Prüfe, ob das AddOn installiert und aktiv ist
