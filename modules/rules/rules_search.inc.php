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
   $qry=$session->data['rules_qry'];
  } else {
   $session->data['rules_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_rules;
  if (!$sortby_rules) {
   $sortby_rules=$session->data['rules_sort'];
  } else {
   if ($session->data['rules_sort']==$sortby_rules) {
    if (Is_Integer(strpos($sortby_rules, ' DESC'))) {
     $sortby_rules=str_replace(' DESC', '', $sortby_rules);
    } else {
     $sortby_rules=$sortby_rules." DESC";
    }
   }
   $session->data['rules_sort']=$sortby_rules;
  }
  if (!$sortby_rules) $sortby_rules="ID DESC";
  $out['SORTBY']=$sortby_rules;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM rules WHERE $qry ORDER BY ".$sortby_rules);
  if ($res[0]['ID']) {
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $conditions=SQLSelect("SELECT rules_conditions.* FROM rules_linked_conditions LEFT JOIN rules_conditions ON rules_linked_conditions.CONDITION_ID=rules_conditions.ID WHERE RULE_ID=".$res[$i]['ID']);
    $res[$i]['CONDITIONS']=$conditions;
    $res[$i]['CONDITIONS'][count($conditions)-1]['LAST']=1;

    $actions=SQLSelect("SELECT rules_actions.* FROM rules_linked_actions LEFT JOIN rules_actions ON rules_linked_actions.ACTION_ID=rules_actions.ID WHERE RULE_ID=".$res[$i]['ID']);
    $res[$i]['ACTIONS']=$actions;
    $res[$i]['ACTIONS'][count($actions)-1]['LAST']=1;

   }
   $out['RESULT']=$res;
  }

  $out['ALL_ACTIONS']=SQLSelect("SELECT * FROM rules_actions ORDER BY TITLE");
  $out['ALL_CONDITIONS']=SQLSelect("SELECT * FROM rules_conditions ORDER BY TITLE");
