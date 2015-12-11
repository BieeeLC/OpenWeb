<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/MuDatabase.class.php");
$db = new MuDatabase();

require_once($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "System/Account.class.php");
$acc = new Account($db);

//NÍVEL DO PAINEL CUSTUN
define('VC_PAINEL_ADMIN', '1');

// DEFINE A TABELA E COLUNA DO VIP
define('VC_TABLE_VIP', 'MEMB_INFO');
define('VC_COLUMN_VIP', 'AccountLevel');
define('VC_NIVEL_VIP', '0');

// VERIFICA SE É GM PARA ACESSAR
define('VC_TABLE_GM', 'Character');
define('VC_COLUMN_GM', 'CtlCode');
define('VC_NIVEL_GM', '0'); // codigo do gm é 32
// VERIFICA NÍVEL AVANÇADO PARA USAR
//define('', '');
//define('', '');
//define('', '');
//define('', '');
// DEFINE A TABELA E COLUNAS DA MOEDAS
define('VC_TABLE_CURRENCY', 'Z_Credits');
define('VC_COLUMN_CURRENCY', 'value, type');
define('VC_MEMBYD_CURRENCY', 'memb___id');
define('VC_NIVEL_MEMBID_CURRENCY', $acc->memb___id);

//DEFINE NOMOE DAS MOEDAS
define('MOEDA_NAME_0', 'Créditos');
define('MOEDA_NAME_1', 'MOEDA 1');
define('MOEDA_NAME_2', 'MOEDA 2');
define('MOEDA_NAME_3', 'MOEDA 3');
define('MOEDA_NAME_4', 'MOEDA 4');
define('MOEDA_NAME_5', 'MOEDA 5');

define('VALOR_PACOTE_1_TYPE_1', 10);
define('VALOR_PACOTE_1_TYPE_2', 20);
define('VALOR_PACOTE_1_TYPE_3', 30);
define('VALOR_PACOTE_1_TYPE_4', 40);
define('VALOR_PACOTE_1_TYPE_5', 50);

define('DEBITO_PACOTE_1_TYPE_1_MOEDA_2', 20);
define('DEBITO_PACOTE_1_TYPE_2_MOEDA_2', 40);
define('DEBITO_PACOTE_1_TYPE_3_MOEDA_2', 60);
define('DEBITO_PACOTE_1_TYPE_4_MOEDA_2', 80);
define('DEBITO_PACOTE_1_TYPE_5_MOEDA_2', 100);

function getCurrency($Type) {
    switch ($Type):
        case 0: $NewType = MOEDA_NAME_0;
            break;
        case 1: $NewType = MOEDA_NAME_1;
            break;
        case 2: $NewType = MOEDA_NAME_2;
            break;
        case 3: $NewType = MOEDA_NAME_3;
            break;
        case 4: $NewType = MOEDA_NAME_4;
            break;
        case 5: $NewType = MOEDA_NAME_5;
            break;
    endswitch;
    return $NewType;
}
