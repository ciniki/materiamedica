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
function ciniki_materiamedica_plantActionGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'action_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Action'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantActionGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');

	if( $args['action_id'] == 0 ) {
		$action = array(
			'id'=>'0',
			'system'=>'',
			'action'=>'',
			'notes'=>'',
			);
	} else {
		$strsql = "SELECT ciniki_materiamedica_plant_actions.id, "
			. "ciniki_materiamedica_plant_actions.system, "
			. "ciniki_materiamedica_plant_actions.action, "
			. "ciniki_materiamedica_plant_actions.notes "
			. "FROM ciniki_materiamedica_plant_actions "
			. "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.id = '" . ciniki_core_dbQuote($ciniki, $args['action_id']) . "' "
			. "";

		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'actions', 'fname'=>'id', 'name'=>'action',
				'fields'=>array('id', 'system', 'action', 'notes')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( !isset($rc['actions']) ) {
			return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'2568', 'msg'=>'Unable to find plant action'));
		}
		$action = $rc['actions'][0]['action'];
	}

	return array('stat'=>'ok', 'action'=>$action);
}
?>
