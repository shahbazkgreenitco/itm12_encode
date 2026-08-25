<?php

return [
   
    'title' => 'Benutzerdefinierte Steuer',

    'table_fields' => [
        'id' => 'ID',
        'tax_name' => 'Steuername',
        'created_at' => 'Erstellt am',
        'updated_at' => 'Aktualisiert am',
        'actions' => 'Aktionen',
    ],

    'data_table' => [
        'previous' => 'Zurück',
        'next' => 'Weiter',
        'showing_entries' => 'Zeige _START_ bis _END_ von _TOTAL_ Einträgen',
        'no_entries' => 'Zeige 0 bis 0 von 0 Einträgen',
        'filtered_from' => '(gefiltert aus insgesamt _MAX_ Einträgen)',
        'no_matching_records' => 'Keine passenden Einträge gefunden',
        'no_data' => 'Keine Daten in der Tabelle verfügbar',
        'length_menu' => '_MENU_ Einträge anzeigen',
        'show' => 'Anzeigen',
        'refresh' => 'Aktualisieren',
        'reload' => 'Neu laden',
        'search' => 'Suchen...',
    ],

    'form' => [
        'name' => 'Name',
        'tax_elements' => 'Steuerelemente',
    ],

    'buttons' => [
        'add_tax' => 'Steuer hinzufügen',
        'add_new_trigger' => 'Trigger hinzufügen',
        'edit_tax' => 'Steuer bearbeiten',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'add_options' => 'Option hinzufügen',
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'remove' => 'Entfernen',
        'ok' => 'OK',
        'confirm_delete' => 'Ja, löschen.',
        'cancel' => 'Abbrechen',
    ],

    'import_info_table' => [

    ],

    'placeholder_and_options' => [

    ],

    'alerts_and_messages' => [
        'insufficient_permission' => 'Sie verfügen nicht über die erforderlichen Berechtigungen, um diese Aktion auszuführen.',
        'permission_denied' => 'Zugriff verweigert. Sie verfügen nicht über die erforderliche Berechtigung.',
        'custom_tax_added' => 'Benutzerdefinierte Steuer erfolgreich hinzugefügt.',
        'something_went_wrong' => 'Etwas ist schiefgelaufen. Bitte überprüfen Sie, ob die eingegebenen Daten korrekt sind.',
        'record_already_deleted' => 'Dieser Datensatz wurde bereits gelöscht. Bitte aktualisieren Sie die Seite.',
        'remove_success' => 'Benutzerdefinierte Steuer erfolgreich entfernt.',
        'remove_failed' => 'Benutzerdefinierte Steuer konnte nicht entfernt werden.',
        'trigger_not_found' => 'Trigger wurde nicht gefunden.',
        'edit_success' => 'Details der benutzerdefinierten Steuer erfolgreich abgerufen.',
        'delete_custom_tax' => 'Möchten Sie diese benutzerdefinierte Steuer löschen?',
        'warning' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
        'update_failed' => 'Benutzerdefinierte Steuer konnte nicht aktualisiert werden.',
        'update_success' => 'Benutzerdefinierte Steuer erfolgreich aktualisiert.',
        'trigger_update_failed' => 'Trigger konnte nicht aktualisiert werden.',
        'no_problem_category_found' => 'Keine Problemkategorie gefunden.',
        'delete_tax' => 'Möchten Sie diese benutzerdefinierte Steuer wirklich löschen?',
        'departments_fetched_successfully' => 'Abteilungen erfolgreich geladen.',
        'delete_element' => 'Möchten Sie dieses Element der benutzerdefinierten Steuer wirklich löschen?',
        'name_required' => 'Name ist erforderlich.',
        'field_is_required' => 'Dieses Feld ist erforderlich.',
        'minimum_2_characters_required' => 'Mindestens 2 Zeichen erforderlich.',
        'maximum_100_characters_required' => 'Maximal 100 Zeichen erforderlich.',
        'fail' => 'Fehler',
        'deleted' => 'Gelöscht!',
        'oops' => 'Hoppla!',
        'danger' => 'Warnung',
        'success' => 'Erfolgreich',
        'clean_text_only' => 'Nur Buchstaben, Zahlen und bestimmte Zeichen sind zulässig. HTML-Tags sind nicht erlaubt.',
    ],
];