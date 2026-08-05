<?php

return [
    'view' => [
        'header' => "Depreciations",
        'page_heading' => "Depreciations",
        'search' => "Search",
        'refresh' => "Refresh",
        'add' => "Create Depreciation",
        "show_columns" => "Show Columns",
        'show' => "Show",
        'add_button' => "Add Depreciation",
    ],

    'config' => [
        'edit' => "Edit",
        'delete' => "Delete",
        'valid_value_search' => "Please enter a valid value for search",
        'deleted_successfully' => "Deleted successfully",
        'deleted_fail' => "Delete failed",
        'modal_yes_button' => "Yes, delete it!",
        'modal_cancel_button' => "Cancel",
        'depreciation_added_successfully' => "Depreciation added successfully",
        'depreciation_add_failed' => "Add Depreciation failed",
        'are_you_delete' => "Are you sure you want to delete this Depreciation?",
        'something_went_wrong' => "Something Went Wrong",
        'something_went_wrong_details' => "Something went wrong. Please check given details are correct",
    ],

    'table' => [
        'id' => "ID",
        'depreciation_name' => "Depreciation Name",
        'term' => "Term",
        'actions' => "Actions",
    ],

    'modal' => [
        'add_depreciation_header' => "Add Depreciation Details",
        'edit_depreciation_header' => "Edit Depreciation Details",
        'depreciation_name' => "Depreciation Name",
        'number_of_months' => "Number of Months",
        'depreciation_name_placeholder' => "Depreciation Name",
        'number_of_months_placeholder' => "Enter Months",
        'save' => "Save",
        'close' => "Close",
    ],
    'controller' => [
        'permission_denied' => 'Permission denied',
        'unable_to_add_depreciation' => 'Unable to add the depreciation',
        'unable_to_get_depreciation' => 'Unable to get the depreciation',
        'unable_to_edit_depreciation' => 'Unable to edit the depreciation',
        'please_provide_depreciation_name' => 'Please provide depreciation name.',
        'please_provide_depreciation_months' => 'Please provide depreciation months.',
        'depreciation_added_successfully' => 'Depreciation has been added successfully',
        'depreciation_updated_successfully' => 'Depreciation has been updated successfully',
        'some_problem_in_system' => 'Some problem in system!!',
        'licenses_attached_with_this_depreciation' => 'Some licenses are attached with this depreciation. Please remove them and try again !!',
        'assets_attached_with_this_depreciation' => 'Some assets are attached with this depreciation. Please remove them and try again !!',
        'models_attached_with_this_depreciation' => 'Some models are attached with this depreciation. Please remove them and try again !!',
        'interact_caches_attached_with_this_depreciation' => 'Some assets are attached with this depreciation. Please remove them and try again !!',
        'interact_records_attached_with_this_depreciation' => 'Some asset records are attached with this depreciation. Please remove them and try again !!',
        'depreciation_deleted_successfully' => 'Depreciation has been deleted successfully!',
    ]
];
