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
// tnid:         The ID of the tenant to get the plant from.
// plant_id:            The ID of the plant to get.
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
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'plant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plant'), 
        'system_num'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'System'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this tenant
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'checkAccess');
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['tnid'], 'ciniki.materiamedica.plantSystemActionsGet'); 
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
            . "WHERE ciniki_materiamedica_plant_actions.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
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
        if( isset($rc['types']['100']) ) {
            $system['ailments'] = $rc['types']['100']['actions'];
        }

        //
        // Get any notes
        //
        if( isset($args['notes']) && $args['notes'] == 'yes' ) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'notesLoad');
            $rc = ciniki_materiamedica_notesLoad($ciniki, $args['tnid'], array('key'=>'ciniki.materiamedica.plant-' . $args['plant_id'] . '-system-' . $args['system_num']));
            if( $rc['stat'] != 'ok' ) { 
                return $rc;
            }
            if( isset($rc['notes']) ) {
                $system['notes'] = $rc['notes'];
            } else {
                $system['notes'] = array();
            }
        }
    }

    $rsp = array('stat'=>'ok', 'system'=>$system, 'actions'=>array(), 'ailments'=>array());

    //
    // Lookup all the actions
    //
    $strsql = "SELECT DISTINCT action "
        . "FROM ciniki_materiamedica_plant_actions "
        . "WHERE ciniki_materiamedica_plant_actions.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND (action_type = 10 OR action_type = 20) "
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

    //
    // Lookup all ailments
    //
    $strsql = "SELECT DISTINCT action "
        . "FROM ciniki_materiamedica_plant_actions "
        . "WHERE ciniki_materiamedica_plant_actions.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND action_type = 100 "
        . "ORDER BY action "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQueryList');
    $rc = ciniki_core_dbQueryList($ciniki, $strsql, 'ciniki.materiamedica', 'actions', 'action');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( isset($rc['actions']) ) {
        $rsp['ailments'] = $rc['actions'];
    }

    return $rsp;
}
?>
