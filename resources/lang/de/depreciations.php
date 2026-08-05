<?php

return [
    'view' => [
        'header' => "Abschreibungen",
        'page_heading' => "Abschreibungen",
        'search' => "Suchen",
        'refresh' => "Aktualisieren",
        'add' => "Abschreibung erstellen",
        "show_columns" => "Spalten anzeigen",
        'show' => "Anzeigen",
        'add_button' => "Abschreibung hinzufügen",
    ],

    'config' => [
        'edit' => "Bearbeiten",
        'delete' => "Löschen",
        'valid_value_search' => "Bitte geben Sie einen gültigen Suchwert ein",
        'deleted_successfully' => "Erfolgreich gelöscht",
        'deleted_fail' => "Löschen fehlgeschlagen",
        'modal_yes_button' => "Ja, löschen!",
        'modal_cancel_button' => "Abbrechen",
        'depreciation_added_successfully' => "Abschreibung erfolgreich hinzugefügt",
        'depreciation_add_failed' => "Hinzufügen der Abschreibung fehlgeschlagen",
        'are_you_delete' => "Sind Sie sicher, dass Sie diese Abschreibung löschen möchten?",
        'something_went_wrong' => "Etwas ist schiefgelaufen",
        'something_went_wrong_details' => "Etwas ist schiefgelaufen. Bitte überprüfen Sie die eingegebenen Details",
    ],

    'table' => [
        'id' => "ID",
        'depreciation_name' => "Name der Abschreibung",
        'term' => "Laufzeit",
        'actions' => "Aktionen",
    ],

    'modal' => [
        'add_depreciation_header' => "Abschreibungsdetails hinzufügen",
        'edit_depreciation_header' => "Abschreibungsdetails bearbeiten",
        'depreciation_name' => "Name der Abschreibung",
        'number_of_months' => "Anzahl der Monate",
        'depreciation_name_placeholder' => "Name der Abschreibung",
        'number_of_months_placeholder' => "Monate eingeben",
        'save' => "Speichern",
        'close' => "Schließen",
    ],
    'controller' => [
        'permission_denied' => 'Zugriff verweigert',
        'unable_to_add_depreciation' => 'Abschreibung konnte nicht hinzugefügt werden',
        'unable_to_get_depreciation' => 'Abschreibung konnte nicht abgerufen werden',
        'unable_to_edit_depreciation' => 'Abschreibung konnte nicht bearbeitet werden',
        'please_provide_depreciation_name' => 'Bitte geben Sie einen Namen für die Abschreibung an.',
        'please_provide_depreciation_months' => 'Bitte geben Sie die Abschreibungsdauer in Monaten an.',
        'depreciation_added_successfully' => 'Abschreibung wurde erfolgreich hinzugefügt',
        'depreciation_updated_successfully' => 'Abschreibung wurde erfolgreich aktualisiert',
        'some_problem_in_system' => 'Ein Problem im System ist aufgetreten!!',
        'licenses_attached_with_this_depreciation' => 'Einige Lizenzen sind mit dieser Abschreibung verknüpft. Bitte entfernen Sie diese und versuchen Sie es erneut !!',
        'assets_attached_with_this_depreciation' => 'Einige Assets sind mit dieser Abschreibung verknüpft. Bitte entfernen Sie diese und versuchen Sie es erneut !!',
        'models_attached_with_this_depreciation' => 'Einige Modelle sind mit dieser Abschreibung verknüpft. Bitte entfernen Sie diese und versuchen Sie es erneut !!',
        'interact_caches_attached_with_this_depreciation' => 'Einige Assets sind mit dieser Abschreibung verknüpft. Bitte entfernen Sie diese und versuchen Sie es erneut !!',
        'interact_records_attached_with_this_depreciation' => 'Einige Anlagen-Datensätze sind mit dieser Abschreibung verknüpft. Bitte entfernen Sie diese und versuchen Sie es erneut !!',
        'depreciation_deleted_successfully' => 'Abschreibung wurde erfolgreich gelöscht!',
    ]
];