<?php
//
// Description
// ===========
// This will update the primary and secondary actions for a plant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:     The ID of the business to the plant is a part of.
// plant_id:        The ID of the plant to update.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_materiamedica_plantSystemActionsUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'plant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'plant'), 
        'system_num'=>array('required'=>'yes', 'blank'=>'no', 
            'validlist'=>array('10', '40', '60', '80', '100', '120', '140', '160', '180', '200', '220', '230', '240', '245', '250'), 'name'=>'System'), 
        'primary_actions'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Primary Actions'), 
        'secondary_actions'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Secondary Actions'), 
        'ailments'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Ailments'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantSystemActionsUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Get the current list of actions
    //
    $strsql = "SELECT ciniki_materiamedica_plant_actions.id, "
        . "ciniki_materiamedica_plant_actions.uuid, "
        . "ciniki_materiamedica_plant_actions.system, "
        . "ciniki_materiamedica_plant_actions.action_type, "
        . "ciniki_materiamedica_plant_actions.action "
        . "FROM ciniki_materiamedica_plant_actions "
        . "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND ciniki_materiamedica_plant_actions.plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
        . "AND ciniki_materiamedica_plant_actions.system = '" . ciniki_core_dbQuote($ciniki, $args['system_num']) . "' "
        . "ORDER BY system, action_type "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
        array('container'=>'types', 'fname'=>'action_type', 
            'fields'=>array('action_type')),
        array('container'=>'actions', 'fname'=>'action',
            'fields'=>array('id', 'uuid', 'system', 'action')),
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
    // Turn off autocommit
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectDelete');
    $rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.materiamedica');
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Add primary actions that do not exist in the database
    //
    if( isset($args['primary_actions']) && is_array($args['primary_actions']) ) {
        foreach($args['primary_actions'] as $action) {
            if( $action == '' ) { 
                continue;
            }
            if( !isset($system['primary_actions'][$action]) ) {
                $rc = ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_action', array(
                    'plant_id'=>$args['plant_id'],
                    'system'=>$args['system_num'],
                    'action_type'=>'10',
                    'action'=>$action,
                    ), 0x04);
                if( $rc['stat'] != 'ok' ) {
                    ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
                    return $rc;
                }
            }
        }
        //
        // Remove primary actions that do not exist in the arguments
        //
        if( isset($system['primary_actions']) ) {
            foreach($system['primary_actions'] as $action => $action_details) {
                if( !in_array($action, $args['primary_actions']) ) {
                    $rc = ciniki_core_objectDelete($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_action', 
                        $action_details['id'], $action_details['uuid'], 0x04);
                    if( $rc['stat'] != 'ok' ) {
                        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
                        return $rc;
                    }
                }
            }
        }
    }

    //
    // Add secondary actions that do not exist in the database
    //
    if( isset($args['secondary_actions']) && is_array($args['secondary_actions']) ) {
        foreach($args['secondary_actions'] as $action) {
            if( $action == '' ) { 
                continue;
            }
            if( !isset($system['secondary_actions'][$action]) ) {
                $rc = ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_action', array(
                    'plant_id'=>$args['plant_id'],
                    'system'=>$args['system_num'],
                    'action_type'=>'20',
                    'action'=>$action,
                    ), 0x04);
                if( $rc['stat'] != 'ok' ) {
                    ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
                    return $rc;
                }
            }
        }
        //
        // Remove secondary actions that do not exist in the arguments
        //
        if( isset($system['secondary_actions']) ) {
            foreach($system['secondary_actions'] as $action => $action_details) {
                if( !in_array($action, $args['secondary_actions']) ) {
                    $rc = ciniki_core_objectDelete($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_action', 
                        $action_details['id'], $action_details['uuid'], 0x04);
                    if( $rc['stat'] != 'ok' ) {
                        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
                        return $rc;
                    }
                }
            }
        }
    }

    //
    // Add secondary actions that do not exist in the database
    //
    if( isset($args['ailments']) && is_array($args['ailments']) ) {
        foreach($args['ailments'] as $action) {
            if( $action == '' ) { 
                continue;
            }
            if( !isset($system['ailments'][$action]) ) {
                $rc = ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_action', array(
                    'plant_id'=>$args['plant_id'],
                    'system'=>$args['system_num'],
                    'action_type'=>'100',
                    'action'=>$action,
                    ), 0x04);
                if( $rc['stat'] != 'ok' ) {
                    ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
                    return $rc;
                }
            }
        }
        //
        // Remove secondary actions that do not exist in the arguments
        //
        if( isset($system['ailments']) ) {
            foreach($system['ailments'] as $action => $action_details) {
                if( !in_array($action, $args['ailments']) ) {
                    $rc = ciniki_core_objectDelete($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_action', 
                        $action_details['id'], $action_details['uuid'], 0x04);
                    if( $rc['stat'] != 'ok' ) {
                        ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
                        return $rc;
                    }
                }
            }
        }
    }

    // Commit the database changes
    //
    $rc = ciniki_core_dbTransactionCommit($ciniki, 'ciniki.materiamedica');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
    // Update the last_change date in the business modules
    // Ignore the result, as we don't want to stop user updates if this fails.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'updateModuleChangeDate');
    ciniki_businesses_updateModuleChangeDate($ciniki, $args['business_id'], 'ciniki', 'materiamedica');

    return array('stat'=>'ok');
}
?>
