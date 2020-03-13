<?php

use CRM_Civicontact_ExtensionUtil as E;

/**
 * CCAGroupsLog.getmodifiedgroups API specification
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_c_c_a_groups_log_getmodifiedgroups_spec(&$spec) {
  $spec['createdat'] = [
    'api.required' => 0,
    'title' => 'Created At',
    'type' => CRM_Utils_Type::T_TIMESTAMP,
  ];
}

/**
 * CCAGroupsLog.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_c_c_a_groups_log_create_spec(&$spec) {
  // $spec['some_parameter']['api.required'] = 1;
}

/**
 * CCAGroupsLog.create API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_c_c_a_groups_log_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * CCAGroupsLog.delete API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_c_c_a_groups_log_delete($params) {
  return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * CCAGroupsLog.get API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_c_c_a_groups_log_get($params) {
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * CCAGroupsLog.getmodifiedgroups API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_c_c_a_groups_log_getmodifiedgroups($params) {
  $teamgroups = [];

  if (isCiviTeamsExtensionInstalled()) {
    $teams = getContactTeams();
    $teamgroups = getTeamGroups($teams, TRUE);
    $teamgroups = array_column($teamgroups["values"], "entity_id");
    if (!count($teamgroups)) {
      $teamgroups = ["-1"];
    }
  }

  if (isset($params["createdat"])) {
    $finalGroupsToProcess = [];
    if (isCiviTeamsExtensionInstalled()) {
      $modifiedteamgroups = getTeamGroups($teams, FALSE, $params["createdat"]);
      foreach ($modifiedteamgroups["values"] as $modifiedteamgroup) {
        $finalGroupsToProcess[$modifiedteamgroup["entity_id"]] = [
          "groupid" => $modifiedteamgroup["entity_id"],
          "action" => ($modifiedteamgroup["isactive"]) ? "on" : "off",
        ];
      }

      $modifiedteams = getModifiedTeams($params["createdat"]);
      $modifiedteams = $modifiedteams["values"];
      $modifiedteamsresult = [];

      foreach ($modifiedteams as $modifiedteam) {
        $modifiedteamsresult[$modifiedteam["team_id"]] = [
          "id" => $modifiedteam["team_id"],
          "status" => $modifiedteam["status"],
        ];
      }

      $modifiedteamids = array_column($modifiedteamsresult, "id");
      $modifiedteamgroups = getTeamGroups($modifiedteamids, FALSE);

      foreach ($modifiedteamgroups["values"] as $modifiedteamgroup) {
        if ($modifiedteamgroup["isactive"] && $modifiedteamsresult[$modifiedteamgroup["team_id"]]["status"]) {
          $finalGroupsToProcess[$modifiedteamgroup["entity_id"]] = [
            "groupid" => $modifiedteamgroup["entity_id"],
            "action" => "on",
          ];
        }
        else {
          $finalGroupsToProcess[$modifiedteamgroup["entity_id"]] = [
            "groupid" => $modifiedteamgroup["entity_id"],
            "action" => "off",
          ];
        }
      }
    }

    $params["options"] = ['sort' => "id DESC"];
    $groupslog = civicrm_api3_c_c_a_groups_log_get($params);
    foreach ($groupslog["values"] as $gp) {
      if (!array_key_exists($gp["groupid"], $finalGroupsToProcess)) {
        $finalGroupsToProcess[$gp["groupid"]] = [
          "groupid" => $gp["groupid"],
          "action" => $gp["action"],
        ];
      }
    }

    $finalgroupids = array_column($finalGroupsToProcess, "groupid");
    $groupsResult = getGroupDetailsByIds($finalgroupids, TRUE);

    foreach ($groupsResult["values"] as $index => $groupDetail) {
      $groupsResult["values"][$index]["action"] = $finalGroupsToProcess[$groupDetail["id"]]["action"];
    }

    return $groupsResult;
  }

  $teamgroups = getCCAActiveGroups($teamgroups);
  $groupsResult = getGroupDetailsByIds($teamgroups, TRUE);

  foreach ($groupsResult["values"] as $index => $groupDetail) {
    $groupsResult["values"][$index]["action"] = "on";
  }

  return $groupsResult;
}