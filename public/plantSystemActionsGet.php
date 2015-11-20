<?php
//
// Description
// ===========
// This method will return all information for a plant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id: 		The ID of the business to get the plant from.
// plant_id:			The ID of the plant to get.
// 
// Returns
// -------
//
function ciniki_materiamedica_plantSystemActionsGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'plant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plant'), 
        'system_num'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'System'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'checkAccess');
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantSystemActionsGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');

	if( $args['system_num'] == 0 ) {
		$system = array();
	} else {
		$strsql = "SELECT ciniki_materiamedica_plant_actions.id, "
			. "ciniki_materiamedica_plant_actions.system, "
			. "ciniki_materiamedica_plant_actions.action_type, "
			. "ciniki_materiamedica_plant_actions.action AS actions "
			. "FROM ciniki_materiamedica_plant_actions "
			. "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.system = '" . ciniki_core_dbQuote($ciniki, $args['system_num']) . "' "
            . "ORDER BY system, action_type, action "
			. "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
		$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'types', 'fname'=>'action_type', 
				'fields'=>array('action_type', 'actions'), 'dlists'=>array('actions'=>'::')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
        $system = array();
        if( isset($rc['types']['10']) ) {
            $system['primary_actions'] = $rc['types']['10']['actions'];
        }
        if( isset($rc['types']['20']) ) {
            $system['secondary_actions'] = $rc['types']['20']['actions'];
        }
	}

	$rsp = array('stat'=>'ok', 'system'=>$system, 'actions'=>array());

    //
    // Lookup all the actions
    //
    $strsql = "SELECT DISTINCT action "
        . "FROM ciniki_materiamedica_plant_actions "
        . "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "ORDER BY action "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQueryList');
    $rc = ciniki_core_dbQueryList($ciniki, $strsql, 'ciniki.materiamedica', 'actions', 'action');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['actions']) ) {
        $rsp['actions'] = $rc['actions'];
    }

    return $rsp;
}
?>
