<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  //searching 'TITLE' (varchar)
  global $title;
  if ($title!='') {
   $qry.=" AND TITLE LIKE '%".DBSafe($title)."%'";
   $out['TITLE']=$title;
  }
  if (IsSet($this->script_id)) {
   $script_id=$this->script_id;
   $qry.=" AND SCRIPT_ID='".$this->script_id."'";
  } else {
   global $script_id;
  }
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['rules_actions_qry'];
  } else {
   $session->data['rules_actions_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_rules_actions;
  if (!$sortby_rules_actions) {
   $sortby_rules_actions=$session->data['rules_actions_sort'];
  } else {
   if ($session->data['rules_actions_sort']==$sortby_rules_actions) {
    if (Is_Integer(strpos($sortby_rules_actions, ' DESC'))) {
     $sortby_rules_actions=str_replace(' DESC', '', $sortby_rules_actions);
    } else {
     $sortby_rules_actions=$sortby_rules_actions." DESC";
    }
   }
   $session->data['rules_actions_sort']=$sortby_rules_actions;
  }
  if (!$sortby_rules_actions) $sortby_rules_actions="ACTIVE, ID DESC";
  $out['SORTBY']=$sortby_rules_actions;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM rules_actions WHERE $qry ORDER BY ".$sortby_rules_actions);
  if ($res[0]['ID']) {
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $using=SQLSelectOne("SELECT ID FROM rules_linked_actions WHERE ACTION_ID='".$res[$i]['ID']."'");
    if (!$using['ID']) {
     $res[$i]['CAN_DELETE']=1;
    }
   }
   $out['RESULT']=$res;
  }
