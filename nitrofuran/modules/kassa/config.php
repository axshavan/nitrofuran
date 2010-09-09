<?php

// названия таблиц
define('KASSA_CURRENCY_TABLE',             'kassa_currency');
define('KASSA_ACCOUNT_TABLE',              'kassa_account');
define('KASSA_OPERATION_TYPE_TABLE',       'kassa_operation_type');
define('KASSA_OPERATION_TYPE_GROUP_TABLE', 'kassa_operation_type_group');
define('KASSA_OPERATION_TABLE',            'kassa_operation');

// сервисные операции
// перенос со счёта
define('OPTYPE_ACCOUNT_TRANSACTON_FROM_ID', 18);
// перенос на счёт
define('OPTYPE_ACCOUNT_TRANSACTON_TO_ID', 19);

?>