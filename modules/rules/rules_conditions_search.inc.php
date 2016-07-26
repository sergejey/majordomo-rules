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
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['rules_conditions_qry'];
  } else {
   $session->data['rules_conditions_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_rules_conditions;
  if (!$sortby_rules_conditions) {
   $sortby_rules_conditions=$session->data['rules_conditions_sort'];
  } else {
   if ($session->data['rules_conditions_sort']==$sortby_rules_conditions) {
    if (Is_Integer(strpos($sortby_rules_conditions, ' DESC'))) {
     $sortby_rules_conditions=str_replace(' DESC', '', $sortby_rules_conditions);
    } else {
     $sortby_rules_conditions=$sortby_rules_conditions." DESC";
    }
   }
   $session->data['rules_conditions_sort']=$sortby_rules_conditions;
  }
  if (!$sortby_rules_conditions) $sortby_rules_conditions="ACTIVE, ID DESC";
  $out['SORTBY']=$sortby_rules_conditions;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM rules_conditions WHERE $qry ORDER BY ".$sortby_rules_conditions);
  if ($res[0]['ID']) {
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $using=SQLSelectOne("SELECT ID FROM rules_linked_conditions WHERE CONDITION_ID='".$res[$i]['ID']."'");
    if (!$using['ID']) {
     $res[$i]['CAN_DELETE']=1;
    }
   }
   $out['RESULT']=$res;
  }
