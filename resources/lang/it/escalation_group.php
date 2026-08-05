<?php

return [

    'title' => 'Gruppo di escalation',
    'add_group' => 'Aggiungi dettagli gruppo',
    'edit_group' => 'Modifica dettagli gruppo',
    'delete_group' => 'Elimina gruppo',
    'manage_users' => 'Gestisci utenti del gruppo',
    'group_clone' => 'Clona gruppo di escalation',

    'download' => 'Scarica',
    'download_excel' => 'Scarica Excel',
    'bulk_import' => 'Importazione escalation',

    'search_placeholder' => 'Cerca gruppo di escalation',
    'refresh' => 'Aggiorna',
    'filter' => 'Filtro',
    'advance_filter' => 'Filtro avanzato',
    'active_filters' => 'Filtri attivi',

    'apply' => 'Applica',
    'clear' => 'Cancella',
    'save' => 'Salva',
    'save_changes' => 'Salva modifiche',
    'close' => 'Chiudi',

    'select' => 'Seleziona',
    'select_all' => 'Seleziona tutto',

    'yes' => 'Sì',
    'no' => 'No',

    'no_records_found' => 'Nessun record corrispondente trovato',

    'please_enter_valid_search' => 'Inserisci un valore valido per la ricerca',
    'search_data' => 'Inserisci un valore valido per la ricerca',

    'something_wrong' => 'Qualcosa è andato storto. Verifica che i dettagli inseriti siano corretti',
    'something_went_wrong' => 'Qualcosa è andato storto. Riprova.',

    'confirmation_message' => 'Sei sicuro di voler eliminare questo gruppo?',
    'confirmation_user' => 'Sei sicuro di voler eliminare questo utente?',

    'duplicate_escalation_group' => 'Gruppo di escalation duplicato.',

    'department' => 'Seleziona reparto',
    'select_company' => 'Seleziona azienda',
    'prob_category' => 'Seleziona categoria problema',
    'sub_category' => 'Seleziona sottocategoria',
    'location_base' => 'Seleziona in base alla posizione',

    'select_the_department' => 'Seleziona reparto',
    'Select_Sub_Category' => 'Seleziona sottocategoria',

    'filter_by_department' => 'Filtra per reparto',
    'filter_by_problem_category' => 'Filtra per categoria problema',
    'filter_by_sub_category' => 'Filtra per sottocategoria',
    'filter_by_location_based' => 'Filtra per posizione',

    'group_users' => 'Utenti del gruppo',
    'group_name' => 'Gruppi di escalation',
    'escalation_member' => 'Membri del gruppo di escalation',
    'back_to_escalation_group' => 'Torna al gruppo di escalation',

    'location' => 'Posizione',
    'access_location' => 'Accesso alla posizione',
    'all_location_access' => 'Seleziona tutte le posizioni',
    'location_placeholder' => 'Seleziona posizione',

    'add_group_user' => 'Aggiungi utente al gruppo',
    'add_group_member' => 'Aggiungi membro al gruppo',
    'edit_group_member' => 'Modifica membro del gruppo',

    'user_name' => 'Nome utente',
    'user_placeholder' => 'Seleziona utente',

    'unable_get_user' => 'Impossibile ottenere gli utenti del gruppo',
    'unable_add_user' => 'Impossibile aggiungere l\'utente al gruppo',
    'unable_update' => 'Impossibile aggiornare l\'utente nel gruppo',

    'group_add_user' => 'Utente aggiunto al gruppo di escalation con successo.',
    'already_add_user' => 'Utente già aggiunto a questo gruppo.',
    'already_exists_user' => 'L\'utente esiste già in questo gruppo.',

    'group_user' => 'Utenti del gruppo eliminati con successo.',
    'escalation_update' => 'Utente aggiornato nel gruppo di escalation con successo.',
    'escalation_group_update' => 'Dettagli utente del gruppo recuperati con successo.',

    'location_update' => 'Aggiornamento in corso...',
    'deleted' => 'Gruppo di escalation eliminato con successo',

    'allocated_to_pc' => 'Questo gruppo è assegnato alla categoria problema, non può essere eliminato',

    'empty_file' => 'Un file vuoto non può essere importato, compila i dati e riprova!',
    'imported_success' => 'Voce di escalation importata con successo',
    'duplicate_entry' => 'Le righe contengono una voce duplicata',
    'incorrect_values' => 'Le righe contengono dati errati',

    'import_escalation' => 'Importazione escalation',

    'escalate_to' => 'Escalation a',
    'escalate_value' => 'Valore escalation',
    'escalate_trigger_time' => 'Tempo di attivazione escalation',
    'sla_count' => 'Conteggio SLA',

    'escalate_to_desc' => 'Escalare a utenti o gruppi',
    'escalate_value_desc' => 'Deve essere un valore nome utente valido',
    'sla_count_desc' => 'Il conteggio SLA sarà impostato di default su Sì',

    'escalate_group_name' => 'Nome gruppo di escalation',
    'escalate_group_name_desc' => 'Nome gruppo di escalation in maiuscolo o minuscolo',

    'problem_category_created_na' => 'La categoria problema è disabilitata, impossibile creare il gruppo di escalation.',
    'sub_category_created_na' => 'La sottocategoria è disabilitata, impossibile creare il gruppo di escalation',

    'problem_category_update_na' => 'La categoria problema è disabilitata, impossibile aggiornare il gruppo di escalation.',
    'sub_category_update_na' => 'La sottocategoria è disabilitata, impossibile aggiornare il gruppo di escalation',

    'update_successfully' => 'Gruppo di escalation aggiornato con successo',
    'add_successfully' => 'Gruppo di escalation aggiunto',

    'company_name' => 'Azienda',
    'company_name_desc' => 'Può essere un valore da Config > Pagina Azienda.',

    'problem_category_desc' => 'Può essere un valore da Ticket di servizio > Pagina categoria problema.',

    'sub_category_label' => 'Sottocategoria',

    'sub_category_desc' => 'Può essere un valore da Ticket di servizio > Pagina categoria problema.',

    'location_based_desc' => 'Inserisci 1 per Sì, 0 per No.',

    'location_access_desc' => 'Deve essere un nome posizione da Config > Pagina Posizione.',

    'internal_location_access' => 'Accesso posizione interna',

    'internal_location_access_desc' => 'Deve essere un nome luogo da Config > Pagina Luoghi interni.',

    'table' => [
        'group_name' => 'Nome gruppo',
        'company' => 'Azienda',
        'department' => 'Reparto',
        'problem_category' => 'Categoria problema',
        'sub_category' => 'Sottocategoria',
        'member_count' => 'Numero membri',
        'created_at' => 'Creato il',
        'updated_at' => 'Aggiornato il',
        'action' => 'Azione',

        'show_10' => 'Mostra (10)',
        'show_25' => 'Mostra (25)',
        'show_50' => 'Mostra (50)',
        'show_100' => 'Mostra (100)',
    ],

    'form' => [
        'group_name' => 'Nome gruppo',
        'company' => 'Azienda',
        'department' => 'Reparto',
        'problem_category' => 'Categoria problema',
        'sub_category' => 'Sottocategoria',
        'location_based' => 'Basato sulla posizione',
        'problem_category' => 'Categoria',
    ],

    'escalation_group_user'=>[
        'back' => 'Indietro',
        'delete_user' => 'Elimina dettagli',
        'search_placeholder' => 'Cerca utenti del gruppo',
        'internal_place' => 'Luogo interno',
    ], 

    'press_enter_with_Search' => 'Premi Invio con il testo di ricerca',

];
