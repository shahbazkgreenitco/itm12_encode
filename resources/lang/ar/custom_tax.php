<?php

return [
    'title' => 'الضريبة المخصصة',

    'table_fields' => [
        'id' => 'المعرف',
        'tax_name' => 'اسم الضريبة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'actions' => 'الإجراءات',
    ],

    'data_table' => [
        'previous' => 'السابق',
        'next' => 'التالي',
        'showing_entries' => 'عرض _START_ إلى _END_ من أصل _TOTAL_ سجل',
        'no_entries' => 'عرض 0 إلى 0 من أصل 0 سجل',
        'filtered_from' => '(تمت التصفية من أصل _MAX_ سجل)',
        'no_matching_records' => 'لم يتم العثور على سجلات مطابقة',
        'no_data' => 'لا توجد بيانات متاحة في الجدول',
        'search' => 'بحث...',
        'length_menu' => 'عرض _MENU_ سجل',
        'show' => 'عرض',
        'refresh' => 'تحديث',
        'reload' => 'إعادة تحميل',
    ],

    'form' => [
        'name' => 'الاسم',
        'tax_elements' => 'عناصر الضريبة',
    ],

    'buttons' => [
        'add_tax' => 'إضافة ضريبة',
        'add_new_trigger' => 'إضافة مشغل',
        'edit_tax' => 'تعديل الضريبة',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'add_options' => 'إضافة خيار',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'remove' => 'إزالة',
        'ok' => 'موافق',
        'confirm_delete' => 'نعم، قم بالحذف.',
        'cancel' => 'إلغاء',
    ],

    'import_info_table' => [

    ],

    'placeholder_and_options' => [

    ],

    'alerts_and_messages' => [
        'insufficient_permission' => 'ليس لديك الصلاحية الكافية لتنفيذ هذا الإجراء.',
        'permission_denied' => 'تم رفض الوصول. ليس لديك الصلاحية المطلوبة.',
        'custom_tax_added' => 'تمت إضافة الضريبة المخصصة بنجاح.',
        'something_went_wrong' => 'حدث خطأ ما. يرجى التأكد من صحة البيانات المدخلة.',
        'record_already_deleted' => 'تم حذف هذا السجل بالفعل. يرجى تحديث الصفحة.',
        'remove_success' => 'تم حذف الضريبة المخصصة بنجاح.',
        'remove_failed' => 'فشل في حذف الضريبة المخصصة.',
        'trigger_not_found' => 'لم يتم العثور على المشغل.',
        'edit_success' => 'تم استرجاع تفاصيل الضريبة المخصصة بنجاح.',
        'delete_custom_tax' => 'هل تريد حذف هذه الضريبة المخصصة؟',
        'warning' => 'لا يمكن التراجع عن هذا الإجراء.',
        'update_failed' => 'فشل في تحديث الضريبة المخصصة.',
        'update_success' => 'تم تحديث الضريبة المخصصة بنجاح.',
        'trigger_update_failed' => 'فشل في تحديث المشغل.',
        'no_problem_category_found' => 'لم يتم العثور على فئة المشكلة.',
        'delete_tax' => 'هل أنت متأكد من حذف هذه الضريبة المخصصة؟',
        'departments_fetched_successfully' => 'تم جلب الأقسام بنجاح.',
        'delete_element' => 'هل أنت متأكد من حذف عنصر الضريبة المخصصة؟',
        'name_required' => 'الاسم مطلوب.',
        'minimum_2_characters_required' => 'يجب أن يحتوي الاسم على حرفين على الأقل.',
        'fail' => 'فشل',
        'deleted' => 'تم الحذف!',
        'oops' => 'عذرًا!',
        'danger' => 'خطر',
        'success' => 'نجاح',
    ],
];