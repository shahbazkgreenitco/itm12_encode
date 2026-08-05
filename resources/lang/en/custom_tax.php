<?php

return [
    'title' => 'Custom Tax',
    
    'table_fields' => [
        'id' => 'ID',
        'tax_name' => 'Tax Name',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'actions' => 'Actions',
    ],

    'data_table' =>[
        'previous' => 'Previous',
        'next' => 'Next',
        'showing_entries' => 'Showing _START_ to _END_ of _TOTAL_ entries',
        'no_entries' => 'Showing 0 to 0 of 0 entries',
        'filtered_from' => '(filtered from _MAX_ total entries)',
        'no_matching_records' => 'No matching records found',
        'no_data' => 'No data available in table',
        'search' => 'Search:',
        'length_menu' => 'Show _MENU_ entries',
        'show' => 'Show',
        'refresh' => 'Refresh',
        'reload' => 'Reload',
        'search' => 'Search...',
    ],

    'form' => [
        'name' => 'Name',
        'tax_elements' => 'Tax Elements',
    ],

    'buttons' => [
        'add_tax' => 'Add Tax',
        'add_new_trigger' => 'Add Trigger',
        'edit_tax' => 'Edit Tax',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'add_options' => 'Add Option',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'remove' => 'Remove',
        'ok' => 'Ok',
        'confirm_delete' => 'Yes, delete it.',
        'cancel' => 'Cancel'
    ],

    'import_info_table' => [
        
    ],

    'placeholder_and_options' => [
       
    ],

    'alerts_and_messages' => [
        'insufficient_permission' => 'You do not have sufficient permission to perform this action.',
        'permission_denied' => 'Access denied. You do not have the required permission.',
        'custom_tax_added' => 'Custom tax added successfully.',
        'something_went_wrong' => 'Something went wrong. Please check that the provided details are correct.',
        'record_already_deleted' => 'This record has already been deleted. Please refresh the page.',
        'remove_success' => 'Custom tax has been removed successfully.',
        'remove_failed' => 'Failed to remove the custom tax.',
        'trigger_not_found' => 'Trigger not found.',
        'edit_success' => 'Custom tax details retrieved successfully.',
        'delete_custom_tax' => 'Do you want to delete this custom tax?',
        'warning' => 'This action cannot be undone.',
        'update_failed' => 'Failed to update the custom tax.',
        'update_success' => 'Custom tax updated successfully.',
        'trigger_update_failed' => 'Failed to update the Trigger.',
        'no_problem_category_found' => 'No problem category found.',
        'delete_tax' => 'Are you sure you want to delete this custom tax?',
        'departments_fetched_successfully' => 'Departments fetched successfully.',
        'delete_element' => 'Are you sure you want to delete this custom tax element?',
        'name_required' => 'Name is required.',
        'minimum_2_characters_required' => 'Minimum 2 characters required.',
        'fail'=>'Fail',
        'deleted' => 'Deleted!',
        'oops'=>'Oops!',
        'danger'=>'Danger',
        'success'=>'Success',
    ],
];
