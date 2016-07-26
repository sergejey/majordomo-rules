<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='rules_actions';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
   $ok=1;
  //updating 'TITLE' (varchar, required)
   global $title;
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'ACTIVE' (int)
   global $active;
   $rec['ACTIVE']=(int)$active;
  //updating 'SCRIPT_ID' (select)
   if (IsSet($this->script_id)) {
    $rec['SCRIPT_ID']=$this->script_id;
   } else {
   global $script_id;
   $rec['SCRIPT_ID']=$script_id;
   }
  //updating 'LINKED_OBJECT' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
  //updating 'LINKED_METHOD' (varchar)
   global $linked_method;
   $rec['LINKED_METHOD']=$linked_method;
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $this->updateRules();
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  //options for 'SCRIPT_ID' (select)
  $tmp=SQLSelect("SELECT ID, TITLE FROM scripts ORDER BY TITLE");
  $scripts_total=count($tmp);
  for($scripts_i=0;$scripts_i<$scripts_total;$scripts_i++) {
   $script_id_opt[$tmp[$scripts_i]['ID']]=$tmp[$scripts_i]['TITLE'];
  }
  for($i=0;$i<count($tmp);$i++) {
   if ($rec['SCRIPT_ID']==$tmp[$i]['ID']) $tmp[$i]['SELECTED']=1;
  }
  $out['SCRIPT_ID_OPTIONS']=$tmp;
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
