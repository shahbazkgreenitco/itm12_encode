<?php

return [

    'title' => 'Tassa personalizzata',

    'table_fields' => [
        'id' => 'ID',
        'tax_name' => 'Nome della tassa',
        'created_at' => 'Data di creazione',
        'updated_at' => 'Data di aggiornamento',
        'actions' => 'Azioni',
    ],

    'data_table' => [
        'previous' => 'Precedente',
        'next' => 'Successivo',
        'showing_entries' => 'Visualizzazione da _START_ a _END_ di _TOTAL_ elementi',
        'no_entries' => 'Visualizzazione da 0 a 0 di 0 elementi',
        'filtered_from' => '(filtrati da _MAX_ elementi totali)',
        'no_matching_records' => 'Nessun record corrispondente trovato',
        'no_data' => 'Nessun dato disponibile nella tabella',
        'length_menu' => 'Mostra _MENU_ elementi',
        'show' => 'Mostra',
        'refresh' => 'Aggiorna',
        'reload' => 'Ricarica',
        'search' => 'Cerca...',
    ],

    'form' => [
        'name' => 'Nome',
        'tax_elements' => 'Elementi della tassa',
    ],

    'buttons' => [
        'add_tax' => 'Aggiungi tassa',
        'add_new_trigger' => 'Aggiungi trigger',
        'edit_tax' => 'Modifica tassa',
        'edit' => 'Modifica',
        'delete' => 'Elimina',
        'add_options' => 'Aggiungi opzione',
        'save' => 'Salva',
        'cancel' => 'Annulla',
        'remove' => 'Rimuovi',
        'ok' => 'OK',
        'confirm_delete' => 'Sì, eliminalo.',
        'cancel' => 'Annulla',
    ],

    'import_info_table' => [

    ],

    'placeholder_and_options' => [

    ],

    'alerts_and_messages' => [
        'insufficient_permission' => 'Non disponi delle autorizzazioni necessarie per eseguire questa operazione.',
        'permission_denied' => 'Accesso negato. Non disponi dell\'autorizzazione richiesta.',
        'custom_tax_added' => 'Tassa personalizzata aggiunta con successo.',
        'something_went_wrong' => 'Si è verificato un errore. Verifica che i dati inseriti siano corretti.',
        'record_already_deleted' => 'Questo record è già stato eliminato. Aggiorna la pagina.',
        'remove_success' => 'Tassa personalizzata rimossa con successo.',
        'remove_failed' => 'Impossibile rimuovere la tassa personalizzata.',
        'trigger_not_found' => 'Trigger non trovato.',
        'edit_success' => 'Dettagli della tassa personalizzata recuperati con successo.',
        'delete_custom_tax' => 'Vuoi eliminare questa tassa personalizzata?',
        'warning' => 'Questa azione non può essere annullata.',
        'update_failed' => 'Impossibile aggiornare la tassa personalizzata.',
        'update_success' => 'Tassa personalizzata aggiornata con successo.',
        'trigger_update_failed' => 'Impossibile aggiornare il trigger.',
        'no_problem_category_found' => 'Nessuna categoria di problema trovata.',
        'delete_tax' => 'Sei sicuro di voler eliminare questa tassa personalizzata?',
        'departments_fetched_successfully' => 'Reparti recuperati con successo.',
        'delete_element' => 'Sei sicuro di voler eliminare questo elemento della tassa personalizzata?',
        'name_required' => 'Il nome è obbligatorio.',
        'minimum_2_characters_required' => 'Sono richiesti almeno 2 caratteri.',
        'maximum_100_characters_required' => 'Sono richiesti al massimo 100 caratteri.',
        'fail' => 'Errore',
        'deleted' => 'Eliminato!',
        'oops' => 'Ops!',
        'danger' => 'Pericolo',
        'success' => 'Successo',
        'clean_text_only' => 'Sono consentiti solo lettere, numeri e caratteri limitati. I tag HTML non sono consentiti.',
    ],
];