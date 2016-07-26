<?php


$dictionary=array(

'RULES'=>'Правила',
'CONDITIONS'=>'Условия',
'ACTIONS'=>'Действия', 
'IF'=>'ЕСЛИ',
'THEN'=>'ТОГДА',
'AND'=>'И',
'OR'=>'ИЛИ',
'ACTIVE'=>'Активно',
'CONDITION'=>'Условие',
'VALUE_UPDATED'=>'[значение обновилось]'

/* end module names */


);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
