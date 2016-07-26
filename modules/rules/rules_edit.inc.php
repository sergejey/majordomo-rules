<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='rules';
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
  //updating 'CONDITION_TYPE' (select)
   global $condition_type;
   $rec['CONDITION_TYPE']=$condition_type;
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  //options for 'CONDITION_TYPE' (select)
  $tmp=explode('|', DEF_CONDITION_TYPE_OPTIONS);
  foreach($tmp as $v) {
   if (preg_match('/(.+)=(.+)/', $v, $matches)) {
    $value=$matches[1];
    $title=$matches[2];
   } else {
    $value=$v;
    $title=$v;
   }
   $out['CONDITION_TYPE_OPTIONS'][]=array('VALUE'=>$value, 'TITLE'=>$title);
   $condition_type_opt[$value]=$title;
  }
  for($i=0;$i<count($out['CONDITION_TYPE_OPTIONS']);$i++) {
   if ($out['CONDITION_TYPE_OPTIONS'][$i]['VALUE']==$rec['CONDITION_TYPE']) {
    $out['CONDITION_TYPE_OPTIONS'][$i]['SELECTED']=1;
    $out['CONDITION_TYPE']=$out['CONDITION_TYPE_OPTIONS'][$i]['TITLE'];
    $rec['CONDITION_TYPE']=$out['CONDITION_TYPE_OPTIONS'][$i]['TITLE'];
   }
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
