<?php
/**
* Rules 
*
* Rules
*
* @package project
* @author Serge J. <jey@tut.by>
* @copyright http://www.atmatic.eu/ (c)
* @version 0.1 (wizard, 14:07:59 [Jul 25, 2016])
*/
Define('DEF_CONDITION_TYPE_OPTIONS', '0=AND|1=OR'); // options for 'CONDITION_TYPE'
Define('DEF_ACTIVE_OPTIONS', '1=Yes|0=No'); // options for 'ACTIVE'
//
//
class rules extends module {
/**
* rules
*
* Module class constructor
*
* @access private
*/
function rules() {
  $this->name="rules";
  $this->title="Rules";
  $this->module_category="<#LANG_SECTION_OBJECTS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  if (IsSet($this->script_id)) {
   $out['IS_SET_SCRIPT_ID']=1;
  }
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='rules' || $this->data_source=='') {

  if ($this->mode=='add') {
   global $if;
   global $then;

   $if=trim($if);
   $then=trim($then);

   if ($if!='' && $then!='') {

    $if=str_replace('  ', ' ', $if);
    $if=preg_replace('/ или /uis', ' or ', $if);
    $if=preg_replace('/ и /uis', ' and ', $if);        

    $then=str_replace('  ', ' ', $then);
    $then=preg_replace('/ и /uis', ' and ', $then);            
    $then=preg_replace('/ или /uis', ' and ', $then);                        

    $or_type=0;
    $new_if=str_replace(' or ', ' and ', $if);
    if ($new_if!=$if) {
     $or_type=1;
    }
    $if=$new_if;

    $conditions=explode(' and ', $if);

    $found_conditions=array();
    $total=count($conditions);
    for($i=0;$i<$total;$i++) {
     $conditions[$i]=trim($conditions[$i]);
     $cond_rec=SQLSelectOne("SELECT * FROM rules_conditions WHERE TITLE LIKE '".DBSafe($conditions[$i])."'");
     if (!$cond_rec['ID']) {
      $cond_rec=array();
      $cond_rec['TITLE']=$conditions[$i];
      $cond_rec['ACTIVE']=0;
      $cond_rec['ID']=SQLInsert('rules_conditions', $cond_rec);
     }
     $found_conditions[]=$cond_rec['ID'];
    }

    $actions=explode(' and ', $then);
    $found_actions=array();
    $total=count($actions);
    for($i=0;$i<$total;$i++) {
     $actions[$i]=trim($actions[$i]);
     $action_rec=SQLSelectOne("SELECT * FROM rules_actions WHERE TITLE LIKE '".DBSafe($actions[$i])."'");
     if (!$action_rec['ID']) {
      $action_rec=array();
      $action_rec['TITLE']=$actions[$i];
      $action_rec['ACTIVE']=0;
      $action_rec['ID']=SQLInsert('rules_actions', $action_rec);
     }
     $found_actions[]=$action_rec['ID'];
    }

    $rule_rec=array();
    $rule_rec['TITLE']='IF '.$if.' THEN '.$then;
    $rule_rec['CONDITION_TYPE']=$or_type;
    $rule_rec['ID']=SQLInsert('rules', $rule_rec);

    $total=count($found_conditions);
    for($i=0;$i<$total;$i++) {
     $tmp=array();
     $tmp['RULE_ID']=$rule_rec['ID'];
     $tmp['CONDITION_ID']=$found_conditions[$i];
     SQLInsert('rules_linked_conditions', $tmp);
    }

    $total=count($found_actions);
    for($i=0;$i<$total;$i++) {
     $tmp=array();
     $tmp['RULE_ID']=$rule_rec['ID'];
     $tmp['ACTION_ID']=$found_actions[$i];
     SQLInsert('rules_linked_actions', $tmp);
    }

   }

   $this->redirect("?");
  }

  if ($this->view_mode=='' || $this->view_mode=='search_rules') {
   $this->search_rules($out);
  }
  if ($this->view_mode=='edit_rules') {
   $this->edit_rules($out, $this->id);
  }
  if ($this->view_mode=='delete_rules') {
   $this->delete_rules($this->id);
   $this->redirect("?data_source=rules");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='rules_conditions') {
  if ($this->view_mode=='' || $this->view_mode=='search_rules_conditions') {
   $this->search_rules_conditions($out);
  }
  if ($this->view_mode=='edit_rules_conditions') {
   $this->edit_rules_conditions($out, $this->id);
  }
  if ($this->view_mode=='delete_rules_conditions') {
   $this->delete_rules_conditions($this->id);
   $this->redirect("?data_source=rules_conditions");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='rules_actions') {
  if ($this->view_mode=='' || $this->view_mode=='search_rules_actions') {
   $this->search_rules_actions($out);
  }
  if ($this->view_mode=='edit_rules_actions') {
   $this->edit_rules_actions($out, $this->id);
  }
  if ($this->view_mode=='delete_rules_actions') {
   $this->delete_rules_actions($this->id);
   $this->redirect("?data_source=rules_actions");
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* rules search
*
* @access public
*/
 function search_rules(&$out) {
  require(DIR_MODULES.$this->name.'/rules_search.inc.php');
 }
/**
* rules edit/add
*
* @access public
*/
 function edit_rules(&$out, $id) {
  require(DIR_MODULES.$this->name.'/rules_edit.inc.php');
 }

 function propertySetHandle($object, $property, $value) {
   //TO-DO: implement
  $rules=SQLSelect("SELECT DISTINCT(rules_linked_conditions.RULE_ID) FROM rules_linked_conditions LEFT JOIN rules_conditions ON rules_linked_conditions.CONDITION_ID=rules_conditions.ID WHERE rules_conditions.LINKED_OBJECT LIKE '".DBSafe($object)."' AND rules_conditions.LINKED_PROPERTY LIKE '".DBSafe($property)."' AND rules_conditions.ACTIVE=1");
  $total=count($rules);
  for($i=0;$i<$total;$i++) {
   $this->processRule($rules[$i]['RULE_ID'], $value);
  }
 }

 function processRule($id, $value) {
  $rule=SQLSelectOne("SELECT * FROM rules WHERE ID='".(int)$id."'");
  if (!$rule['ACTIVE']) {
   return 0;
  }
  $or_type=$rule['CONDITION_TYPE']; // 0 = AND, 1 = OR
  $conditions=SQLSelect("SELECT rules_conditions.* FROM rules_linked_conditions LEFT JOIN rules_conditions ON rules_linked_conditions.CONDITION_ID=rules_conditions.ID WHERE RULE_ID=".$rule['ID']);

  $passed=0;

  $total=count($conditions);
  for($i=0;$i<$total;$i++) {
   $result=$this->checkCondition($conditions[$i], $value);
   if (!$result && ($or_type==0 || $total==1)) {
    $passed=0;
    break;
   } elseif ($result && ($or_type==1 || $total==1)) {
    // condition matched
    $passed=1;
    break;
   }
  }

  if ($passed) {
   $actions=SQLSelect("SELECT rules_actions.* FROM rules_linked_actions LEFT JOIN rules_actions ON rules_linked_actions.ACTION_ID=rules_actions.ID WHERE RULE_ID=".$rule['ID']);
   $total=count($actions);
   for($i=0;$i<$total;$i++) {
    $this->runAction($actions[$i], array('VALUE'=>$value));
   }
  }

 }

 function checkCondition($rec, $value) {

  $status=0;

  if ($rec['CONDITION_TYPE']==0) {

  /*
   if ($rec['LINKED_OBJECT']!='' && $rec['LINKED_PROPERTY']!='') {
    $value=gg(trim($rec['LINKED_OBJECT']).'.'.trim($rec['LINKED_PROPERTY']));
   } elseif ($rec['LINKED_PROPERTY']!='') {
    $value=gg($rec['LINKED_PROPERTY']);
   } else {
    $value=-1;
   }
   */

   if (($rec['CONDITION']==2 || $rec['CONDITION']==3) 
       && $rec['CONDITION_VALUE']!='' 
       && !is_numeric($rec['CONDITION_VALUE']) 
       && !preg_match('/^%/', $rec['CONDITION_VALUE'])) {
        $rec['CONDITION_VALUE']='%'.$rec['CONDITION_VALUE'].'%';
   }

   if (is_integer(strpos($rec['CONDITION_VALUE'], "%"))) {
    $rec['CONDITION_VALUE']=processTitle($rec['CONDITION_VALUE']);
   }

   if ($rec['CONDITION']==1 && $value==$rec['CONDITION_VALUE']) {
    $status=1;
   } elseif ($rec['CONDITION']==2 && (float)$value>(float)$rec['CONDITION_VALUE']) {
    $status=1;
   } elseif ($rec['CONDITION']==3 && (float)$value<(float)$rec['CONDITION_VALUE']) {
    $status=1;
   } elseif ($rec['CONDITION']==4 && $value!=$rec['CONDITION_VALUE']) {
    $status=1;
   } elseif ($rec['CONDITION']==5) {
    $status=1;
   } else {
    $status=0;
   }

  } elseif ($rec['CONDITION_TYPE']==1) {

   $display=0;

   if (is_integer(strpos($rec['CONDITION_ADVANCED'], "%"))) {
    $rec['CONDITION_ADVANCED']=processTitle($rec['CONDITION_ADVANCED']);
   }

                  try {
                   $code=$rec['CONDITION_ADVANCED'];
                   $success=eval($code);
                   if ($success===false) {
                    DebMes("Error in rule code: ".$code);
                    registerError('rules', "Error in rule code: ".$code);
                   }
                  } catch(Exception $e){
                   DebMes('Error: exception '.get_class($e).', '.$e->getMessage().'.');
                   registerError('rules', get_class($e).', '.$e->getMessage());
                  }

   $status=$success;

  }

  return $status;

 }

 function runAction($action, $params) {
  if ($action['SCRIPT_ID']) {
   runScript($action['SCRIPT_ID']);
  }
  if ($action['LINKED_OBJECT'] && $action['LINKED_METHOD']) {
   callMethod($action['LINKED_OBJECT'].'.'.$action['LINKED_METHOD']);
  }
 }


/**
* rules delete record
*
* @access public
*/
 function delete_rules($id) {
  $rec=SQLSelectOne("SELECT * FROM rules WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM rules_linked_conditions WHERE RULE_ID='".$rec['ID']."'");
  SQLExec("DELETE FROM rules_linked_actions WHERE RULE_ID='".$rec['ID']."'");
  SQLExec("DELETE FROM rules WHERE ID='".$rec['ID']."'");
 }
/**
* rules_conditions search
*
* @access public
*/
 function search_rules_conditions(&$out) {
  require(DIR_MODULES.$this->name.'/rules_conditions_search.inc.php');
 }
/**
* rules_conditions edit/add
*
* @access public
*/
 function edit_rules_conditions(&$out, $id) {
  require(DIR_MODULES.$this->name.'/rules_conditions_edit.inc.php');
 }
/**
* rules_conditions delete record
*
* @access public
*/
 function delete_rules_conditions($id) {
  $rec=SQLSelectOne("SELECT * FROM rules_conditions WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM rules_conditions WHERE ID='".$rec['ID']."'");
 }
/**
* rules_actions search
*
* @access public
*/
 function search_rules_actions(&$out) {
  require(DIR_MODULES.$this->name.'/rules_actions_search.inc.php');
 }
/**
* rules_actions edit/add
*
* @access public
*/
 function edit_rules_actions(&$out, $id) {
  require(DIR_MODULES.$this->name.'/rules_actions_edit.inc.php');
 }
/**
* rules_actions delete record
*
* @access public
*/
 function delete_rules_actions($id) {
  $rec=SQLSelectOne("SELECT * FROM rules_actions WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM rules_actions WHERE ID='".$rec['ID']."'");
 }

 function processSubscription($event, &$details) {
  if ($event=='COMMAND' && $details['message']!='') {
   //TO-DO: implement
   $actions=SQLSelect("SELECT * FROM rules_actions WHERE ACTIVE=1 AND TITLE LIKE '".DBSafe($details['message'])."'");
   $total=count($actions);
   for($i=0;$i<$total;$i++) {
    $this->runAction($actions[$i], array('message'=>$details['message']));
   }
   if ($total>0) {
    return 1;
   }
  }
 }

/**
* Title
*
* Description
*
* @access public
*/
 function updateRules($rule_id=0) {
  $qry="1";
  if ($rule_id) {
   $qry.=" AND ID='".(int)$rule_id."'";
  }
  $rules=SQLSelect("SELECT * FROM rules WHERE $qry");
  $total=count($rules);
  for($i=0;$i<$total;$i++) {
   $active=1;
   $conditions=SQLSelect("SELECT rules_conditions.* FROM rules_linked_conditions LEFT JOIN rules_conditions ON rules_linked_conditions.CONDITION_ID=rules_conditions.ID WHERE RULE_ID=".$rules[$i]['ID']." AND rules_conditions.ACTIVE!=1");
   if ($conditions[0]['ID']) {
    $active=0;
   }
   $actions=SQLSelect("SELECT rules_actions.* FROM rules_linked_actions LEFT JOIN rules_actions ON rules_linked_actions.ACTION_ID=rules_actions.ID WHERE RULE_ID=".$rules[$i]['ID']." AND rules_actions.ACTIVE!=1");
   if ($actions[0]['ID']) {
    $active=0;
   }
   if ($rules[$i]['ACTIVE']!=$active) {
    $rules[$i]['ACTIVE']=$active;
    SQLUpdate('rules', $rules[$i]);
   }
  }
 }

/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
  subscribeToEvent($this->name, 'COMMAND');
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS rules');
  SQLExec('DROP TABLE IF EXISTS rules_conditions');
  SQLExec('DROP TABLE IF EXISTS rules_actions');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall() {
/*
rules - Rules
rules_conditions - Conditions
rules_actions - Actions
*/
  $data = <<<EOD

 rules: ID int(10) unsigned NOT NULL auto_increment
 rules: TITLE varchar(255) NOT NULL DEFAULT ''
 rules: CONDITION_TYPE int(3) NOT NULL DEFAULT '0'
 rules: ACTIVE int(3) NOT NULL DEFAULT '0'

 rules_conditions: ID int(10) unsigned NOT NULL auto_increment
 rules_conditions: TITLE varchar(255) NOT NULL DEFAULT ''
 rules_conditions: SYSTEM varchar(255) NOT NULL DEFAULT ''
 rules_conditions: ACTIVE int(3) NOT NULL DEFAULT '0'
 rules_conditions: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 rules_conditions: LINKED_PROPERTY varchar(255) NOT NULL DEFAULT ''
 rules_conditions: CONDITION_TYPE int(3) NOT NULL DEFAULT '0'
 rules_conditions: CONDITION int(3) NOT NULL DEFAULT '0'
 rules_conditions: CONDITION_VALUE varchar(255) NOT NULL DEFAULT ''
 rules_conditions: CONDITION_ADVANCED text

 rules_actions: ID int(10) unsigned NOT NULL auto_increment
 rules_actions: TITLE varchar(255) NOT NULL DEFAULT ''
 rules_actions: SYSTEM varchar(255) NOT NULL DEFAULT ''
 rules_actions: ACTIVE int(3) NOT NULL DEFAULT '0'
 rules_actions: SCRIPT_ID int(10) NOT NULL DEFAULT '0'
 rules_actions: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 rules_actions: LINKED_METHOD varchar(255) NOT NULL DEFAULT ''

 rules_linked_conditions: ID int(10) unsigned NOT NULL auto_increment
 rules_linked_conditions: RULE_ID int(10) unsigned NOT NULL DEFAULT 0
 rules_linked_conditions: CONDITION_ID int(10) unsigned NOT NULL DEFAULT 0

 rules_linked_actions: ID int(10) unsigned NOT NULL auto_increment
 rules_linked_actions: RULE_ID int(10) unsigned NOT NULL DEFAULT 0
 rules_linked_actions: ACTION_ID int(10) unsigned NOT NULL DEFAULT 0


EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSnVsIDI1LCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
