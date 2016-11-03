<?php
//
// Description
// ===========
// This method will return all information for a note.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business to get the note from.
// note_id:         The ID of the note to get.
// 
// Returns
// -------
//
function ciniki_materiamedica_noteGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'note_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Note'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.noteGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    //
    // Load the business intl settings
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
    $rc = ciniki_businesses_intlSettings($ciniki, $args['business_id']);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $intl_timezone = $rc['settings']['intl-default-timezone'];

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');


    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');


    if( $args['note_id'] == 0 ) {
        $date_format = ciniki_users_dateFormat($ciniki, 'php');
        $dt = new DateTime('now', new DateTimeZone($intl_timezone));
        $note = array(
            'id'=>'0',
            'flags'=>'',
            'note_date'=>$dt->format($date_format),
            'content'=>'',
            );
        return array('stat'=>'ok', 'note'=>$note);
    } 

    $date_format = ciniki_users_dateFormat($ciniki, 'mysql');
    $strsql = "SELECT ciniki_materiamedica_notes.id, "
        . "ciniki_materiamedica_notes.flags, "
        . "DATE_FORMAT(ciniki_materiamedica_notes.note_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS note_date, "
        . "ciniki_materiamedica_notes.content "
        . "FROM ciniki_materiamedica_notes "
        . "WHERE ciniki_materiamedica_notes.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND ciniki_materiamedica_notes.id = '" . ciniki_core_dbQuote($ciniki, $args['note_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'note');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['note']) ) {
        return array('stat'=>'ok', 'err'=>array('code'=>'ciniki.materiamedica.6', 'msg'=>'Unable to find note'));
    }
    $note = $rc['note'];

    //
    // Get the list of references for the note
    //
    $note['citations'] = array();
    if( isset($ciniki['business']['modules']['ciniki.citations']) ) {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'citations', 'hooks', 'getObjectCitations');
        $rc = ciniki_citations_hooks_getObjectCitations($ciniki, $args['business_id'], array(
            'object'=>'ciniki.materiamedica.note',
            'object_id'=>$args['note_id'],
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['citations']) ) { 
            $note['citations'] = $rc['citations'];
        } else {
        }
    }

    return array('stat'=>'ok', 'note'=>$note);
}
?>
