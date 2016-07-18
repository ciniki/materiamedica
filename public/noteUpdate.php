<?php
//
// Description
// ===========
// This method updates one or more elements of an existing note.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:     The ID of the business to the note is a part of.
// note_id:         The ID of the note to update.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_materiamedica_noteUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'note_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Note'), 
        'flags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Options'), 
        'note_date'=>array('required'=>'no', 'blank'=>'no', 'type'=>'date', 'name'=>'Date'), 
        'content'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Content'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.noteUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //  
    // Turn off autocommit
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionStart');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionRollback');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbTransactionCommit');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    $rc = ciniki_core_dbTransactionStart($ciniki, 'ciniki.materiamedica');
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Update the note
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    $rc = ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.materiamedica.note', 
        $args['note_id'], $args);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    //
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
