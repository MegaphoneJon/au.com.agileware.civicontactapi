<?php

require_once 'civicontactsapp.civix.php';
use CRM_Civicontactsapp_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicontactsapp_civicrm_config(&$config) {
  _civicontactsapp_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civicontactsapp_civicrm_xmlMenu(&$files) {
  _civicontactsapp_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicontactsapp_civicrm_install() {
  _civicontactsapp_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function civicontactsapp_civicrm_postInstall() {
  _civicontactsapp_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civicontactsapp_civicrm_uninstall() {
  _civicontactsapp_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicontactsapp_civicrm_enable() {
  _civicontactsapp_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civicontactsapp_civicrm_disable() {
  _civicontactsapp_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civicontactsapp_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicontactsapp_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civicontactsapp_civicrm_caseTypes(&$caseTypes) {
  _civicontactsapp_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function civicontactsapp_civicrm_angularModules(&$angularModules) {
  _civicontactsapp_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civicontactsapp_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civicontactsapp_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_tabset().
 */
function civicontactsapp_civicrm_tabset($tabsetName, &$tabs, $context) {
  if($tabsetName == "civicrm/contact/view") {
    if (!empty($context)) {
      $session = CRM_Core_Session::singleton();
      $contactID = $context["contact_id"];
      $isMe = ($contactID == $session->get('userID'));

      if($isMe) {
        $tabscount = count($tabs);
        if(isset($tabs[$tabscount - 1]["weight"])) {
          $weight = ($tabs[$tabscount - 1]["weight"]) + 10;
        } else {
          $weight = ($tabscount+1) * 10;
        }
        $url = CRM_Utils_System::url( 'civicrm/contact/view/qrcode', "cid=$contactID");
        $tab = array(
          'title'   => ts('CCA QR Code'),
          'url'    => $url,
          'valid'   => 1,
          'active'  => 1,
          'weight'  => $weight,
          'current' => false,
        );
        $tabs[] = $tab;
      }
    }
  }
}


/**
 * Implements hook_civicrm_postProcess().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function civicontactsapp_civicrm_postProcess($formName, &$form) {
    if ($formName == "CRM_Group_Form_Edit" && isCiviTeamsExtensionInstalled() && ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)) {
        $teams = $form->getSubmitValue( 'teams' );
        $teams = explode("," ,$teams);
        $groupid = $form->getVar("_gid");
        $submitValues = $form->getSubmitValues();

        if($groupid == "") {
            $groupid = civicrm_api3('Group', 'get', array(
                'sequential' => 1,
                'return' => array("id"),
                'title' => $submitValues["title"],
            ));

            if($groupid["count"] > 0) {
                $groupid = $groupid["values"][0]["id"];
            }
        }

        if(!$groupid) {
            return;
        }

        if(count($teams) == 1 && $teams[0] == "") {
            $teams = array();
        }

        $teamsaddedids = array();

        if(count($teams) > 0) {
            $teamsadded = civicrm_api3("TeamEntity","get", array(
                "team_id"      => array(
                    "IN" => $teams,
                ),
                "return" => array(
                    "team_id",
                    "isactive",
                    "id"
                ),
                "sequential"    => TRUE,
                "entity_id"     => $groupid,
                "entity_table"  => "civicrm_group",
            ));

            foreach($teamsadded["values"] as $teamsaddedid) {
                $teamsaddedids[$teamsaddedid["team_id"]] = $teamsaddedid;
            }
        }

        foreach($teams as $teamid) {
            if(!array_key_exists($teamid, $teamsaddedids) || $teamsaddedids[$teamid]["isactive"] == 0) {
                $params = array(
                    "team_id"       => $teamid,
                    "entity_id"     => $groupid,
                    "entity_table"  => "civicrm_group",
                    "isactive"      => 1
                );
                if(array_key_exists($teamid, $teamsaddedids)) {
                    $params["id"] =  $teamsaddedids[$teamid]["id"];
                }
                civicrm_api3("TeamEntity","create", $params);
            }
        }

        $unAssignParams = array(
            "entity_id"    => $groupid,
            "entity_table"  => "civicrm_group",
            "api.TeamEntity.create" => array(
                "id"           => '$value.id',
                "entity_id"    => $groupid,
                "entity_table" => "civicrm_group",
                "isactive"    => 0,
            ),
        );

        if(count($teams) > 0) {
            $unAssignParams["team_id"] = array(
                "NOT IN" => $teams,
            );
        }

        civicrm_api3("TeamEntity","get", $unAssignParams);
    }
}

function isCiviTeamsExtensionInstalled() {
    $entities = civicrm_api3('Entity', 'get', array(
        'sequential' => 1,
    ));
    $entities = $entities["values"];
    return in_array("Team", $entities);
}

function getContactTeams() {
    $loggedinUserId = CRM_Core_Session::singleton()->getLoggedInContactID();
    $teams = civicrm_api3('TeamContact', 'get', array(
        'sequential' => 1,
        'contact_id' => $loggedinUserId,
        'return' => array("team_id"),
        'status' => 1,
    ));
    $teams = array_column($teams["values"], "team_id");
    return $teams;
}

function getTeamGroups($teams, $onlyActiveGroups, $updatedat = "") {
    if(count($teams) == 0) {
        $teams[] = "-1";
    }
    $params = array(
        'sequential' => 1,
        'entity_table' => "civicrm_group",
        'return' => array("entity_id", "isactive", "date_modified","team_id"),
        'team_id' => array('IN' => $teams),
        'options' => array('sort' => "date_modified DESC"),
    );
    if($updatedat != "") {
        $params["date_modified"] = $updatedat;
    }
    if($onlyActiveGroups) {
        $params["isactive"] = 1;
    }

    $teamGroups = civicrm_api3('TeamEntity', 'get', $params);
    return $teamGroups;
}

function getCCAActiveGroups($groupsToCheck = array(), $withName = false) {
    $cca_sync_custom_key = getCCACustomKey();
    $group_params = array(
        'sequential' => 1,
        'return' => array("id", "title"),
        $cca_sync_custom_key => 1,
    );
    if(count($groupsToCheck)) {
        $group_params["id"] = array("IN" => $groupsToCheck);
    }
    $group_ids = civicrm_api3('Group', 'get', $group_params);
    if(!$withName) {
        $group_ids = array_column($group_ids["values"], 'id');
        return $group_ids;
    }
    return $group_ids["values"];
}

function getGroupDetailsByIds($groupids, $returnDirectResponse = FALSE) {
    if(count($groupids) == 0) {
        $groupids = array("-1");
    }
    $group_params = array(
        'sequential' => 1,
        'return' => array("id", "title"),
        'id'     => array("IN" => $groupids),
    );
    $group_ids = civicrm_api3('Group', 'get', $group_params);
    if(!$returnDirectResponse) {
        return $group_ids["values"];
    }
    return $group_ids;
}

function getModifiedTeams($modifiedAt) {
    $loggedinUserId = CRM_Core_Session::singleton()->getLoggedInContactID();
    $teams = civicrm_api3('TeamContact', 'get', array(
        'sequential' => 1,
        'contact_id' => $loggedinUserId,
        'date_modified' => $modifiedAt,
        'options' => array('sort' => "date_modified DESC"),
    ));
    return $teams;
}

function getGroupContactsCount($groupname) {
    return civicrm_api3('GroupContact', 'getcount', array(
        'sequential' => 1,
        'contact_id.is_deleted' => 0,
        'contact_id.first_name' => array('<>' => ""),
        'group_id' => $groupname,
    ));
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @param string $formName
 * @param CRM_Core_Form $form
 */
function civicontactsapp_civicrm_buildForm($formName, &$form) {
    if ($formName == 'CRM_Group_Form_Edit' && ($form->getAction() == CRM_Core_Action::ADD || $form->getAction() == CRM_Core_Action::UPDATE)) {
        if(isCiviTeamsExtensionInstalled()) {
            $dbteams = civicrm_api3("Team", "get", array(
               "is_active" => 1
            ));
            $groupid = $form->getVar("_id");
            $existingteams = civicrm_api3("TeamEntity","get", array(
               "entity_table" => "civicrm_group",
               "entity_id"    => $groupid,
               "isactive"     => 1,
               "return"       => array("team_id"),
            ));

            $existingteamsids = array();
            if($groupid != "") {
                foreach($existingteams["values"] as $existingteam) {
                    $existingteamsids[] = $existingteam["team_id"];
                }

                $defaults['teams'] = implode(",", $existingteamsids);
                $form->setDefaults($defaults);
            }

            $teams = array();
            foreach($dbteams["values"] as $dbteam) {
                $teams[] = array(
                    "text" => $dbteam["team_name"],
                    "id" => $dbteam["id"],
                    "description" => "",
                    "selected"    => (in_array($dbteam["id"], $existingteamsids)) ? true: false,
                );
            }
            $form->add('select2', 'teams', ts('Teams'), $teams, FALSE, array(
                'placeholder' => "- Select Teams -",
                'multiple'    => true,
                'class'       => "big"
            ));
            CRM_Core_Region::instance('page-body')->add(array(
                'template' => "CRM/Civicontactsapp/Form/teamsfield.tpl"
            ));
        }
    }
}

/**
 * Implementation of hook_civicrm_entityTypes
 */
function civicontactsapp_civicrm_entityTypes(&$entityTypes) {
    if(!isset($entityTypes["CRM_Civicontactsapp_DAO_CCAGroupsLog"])) {
        $entityTypes[] = array(
            'name'  => 'CCAGroupsLog',
            'class' => 'CRM_Civicontactsapp_DAO_CCAGroupsLog',
            'table' => 'civicrm_cca_groups_log',
        );
    }

    if(!isset($entityTypes["CRM_Civicontactsapp_DAO_CCAGroupContactsLog"])) {
        $entityTypes[] = array(
            'name'  => 'CCAGroupContactsLog',
            'class' => 'CRM_Civicontactsapp_DAO_CCAGroupContactsLog',
            'table' => 'civicrm_cca_group_contacts_log',
        );
    }
}

/**
 * Implements _civicrm_managed().
 */
function civicontactsapp_civicrm_managed(&$entities) {
    $cca_sync_custom_key = getCCASyncCustomFieldKey();
    $entities[] = array(
        'module'  => 'au.com.agileware.civicontact',
        'name'    => 'ccaautogroup',
        'entity'  => 'Group',
        'cleanup' => 'never',
        'params' => array(
            'version' => 3,
            'name'    => 'CiviCRM App Contacts',
            'title'   => 'CiviCRM App Contacts',
            'description' => "Contacts from CiviCRM application will be added in this group.",
            'source' => "au.com.agileware.civicontact",
            'group_type' => "Access Control",
            'is_reserved' => 1,
            $cca_sync_custom_key => 1,
        ),
    );
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * Adds entries to the navigation menu.
 *
 * @param array $menu
 */
function civicontactsapp_civicrm_navigationMenu(&$menu) {
    $maxID = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
    $navId = $maxID + 287;
  
    // Get the id of System Settings Menu
    $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');
    $parentID = !empty($administerMenuId) ? $administerMenuId : NULL;
  
    $navigationMenu = array(
      'attributes' => array(
        'label' => 'Contact App',
        'name' => 'Contact App',
        'url' => NULL,
        'permission' => 'administer CiviCRM',
        'operator' => NULL,
        'separator' => NULL,
        'parentID' => $parentID,
        'active' => 1,
        'navID' => $navId,
      ),
      'child' => array(
        $navId + 1 => array(
          'attributes' => array(
            'label' => 'Settings',
            'name' => 'Settings',
            'url' => 'civicrm/cca/settings',
            'permission' => 'administer CiviCRM',
            'operator' => NULL,
            'separator' => 0,
            'active' => 1,
            'parentID' => $navId,
            'navID' => $navId + 1,
          ),
        ),
      ),
    );
    if ($parentID) {
      $menu[$parentID]['child'][$navId] = $navigationMenu;
    }
    else {
      $menu[$navId] = $navigationMenu;
    }
  }

function getCCASyncCustomFieldKey() {
    $customfield_result = civicrm_api3('CustomField', 'getsingle', array(
        'sequential' => 1,
        'return' => array("id"),
        'name' => "Sync_to_CCA",
    ));
    $cca_sync_custom_id = $customfield_result["id"];
    $cca_sync_custom_key = "custom_".$cca_sync_custom_id;

    return $cca_sync_custom_key;
}

/**
 * Implements hook_civicrm_post().
 */
function civicontactsapp_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if($objectName == "GroupContact") {
    $contactid = $objectRef[0];
    if($op == "delete" || $op == "create") {
      civicrm_api3('CCAGroupContactsLog', 'create', array(
        'groupid'   => $objectId,
        'contactid' => $contactid,
        'action'    => $op,
      ));
    }
  }

  if($objectName == "Group" && $op != "delete") {
    $cca_sync_custom_key = getCCASyncCustomFieldKey();

    $group_params = array(
      'sequential' => 1,
      'return'     => array("id", $cca_sync_custom_key),
      'id'         => $objectRef->id
    );
    $group_details = civicrm_api3('Group', 'getsingle', $group_params);

    $turnSyncOn = false;
    if(isset($group_details[$cca_sync_custom_key]) && sizeof($group_details[$cca_sync_custom_key]) > 0 && $group_details[$cca_sync_custom_key][0] == "1") {
      $turnSyncOn = true;
    }

    $group_last_log = civicrm_api3('CCAGroupsLog', 'get', array(
      'sequential' => 1,
      'groupid'    => $objectRef->id,
      'options' => array('limit' => 1, 'sort' => "id DESC"),
    ));

    $addNewLogEntry = true;
    if($group_last_log["count"] > 0) {
      $last_log = $group_last_log["values"][0];
      if($last_log["action"] == "on" && $turnSyncOn) {
        $addNewLogEntry = false;
      }

      if($last_log["action"] == "off" && !$turnSyncOn) {
        $addNewLogEntry = false;
      }
    }

    if($addNewLogEntry) {
      civicrm_api3('CCAGroupsLog', 'create', array(
        'groupid'  => $objectRef->id,
        'action'   => ($turnSyncOn) ? 'on' : 'off',
      ));
    }
  }

  if($objectName == "Group" && $op == "delete") {
      if(isCiviTeamsExtensionInstalled()) {
          civicrm_api3("TeamEntity","get", array(
              "entity_id"    => $objectId,
              "entity_table"  => "civicrm_group",
              "api.TeamEntity.create" => array(
                  "id"           => '$value.id',
                  "entity_id"    => $objectId,
                  "entity_table" => "civicrm_group",
                  "isactive"     => 0,
              ),
          ));
      }
  }
}

