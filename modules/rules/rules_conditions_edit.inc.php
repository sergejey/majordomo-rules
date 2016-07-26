<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='rules_conditions';
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
  //updating 'ACTIVE' (select)
   global $active;
   $rec['ACTIVE']=$active;
  //updating 'LINKED_OBJECT' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
  //updating 'LINKED_PROPERTY' (varchar)
   global $linked_property;
   $rec['LINKED_PROPERTY']=$linked_property;

   global $condition_type;
   $rec['CONDITION_TYPE']=(int)$condition_type;


  //updating 'CONDITION' (int)
   global $condition;
   $rec['CONDITION']=(int)$condition;
  //updating 'CONDITION_VALUE' (varchar)
   global $condition_value;
   $rec['CONDITION_VALUE']=$condition_value;
  //updating 'CONDITION_ADVANCED' (text)
   global $condition_advanced;
   $rec['CONDITION_ADVANCED']=$condition_advanced;
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

    if ($rec['LINKED_OBJECT'] && $rec['LINKED_PROPERTY']) {
     addLinkedProperty($rec['LINKED_OBJECT'], $rec['LINKED_PROPERTY'], $this->name);
    }

   } else {
    $out['ERR']=1;
   }
  }
  //options for 'ACTIVE' (select)
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
