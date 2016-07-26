<?php


$dictionary=array(

'RULES'=>'Rules',
'CONDITIONS'=>'Conditions',
'ACTIONS'=>'Actions', 
'IF'=>'IF',
'THEN'=>'THEN',
'AND'=>'AND',
'OR'=>'OR',
'ACTIVE'=>'Active',
'CONDITION'=>'Condition',
'VALUE_UPDATED'=>'[value updated]'

/* end module names */


);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
