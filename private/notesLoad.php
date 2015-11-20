<?php
//
// Description
// -----------
// This function will check if the user has access to the materiamedica module.  
//
// Arguments
// ---------
// ciniki:
// business_id:			The business ID to check the session user against.
// method:				The requested method.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_materiamedica_notesLoad(&$ciniki, $business_id, $args) {
   
	//
	// Load the business intl settings
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'intlSettings');
	$rc = ciniki_businesses_intlSettings($ciniki, $business_id);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$intl_timezone = $rc['settings']['intl-default-timezone'];

	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki, 'mysql');

    //
    // Load the notes for one keys
    //
    if( isset($args['key']) && $args['key'] != '' ) {
        $strsql = "SELECT id, "
            . "note_key, "
            . "flags, "
            . "DATE_FORMAT(note_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS note_date, "
            . "content "
            . "FROM ciniki_materiamedica_notes "
            . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND note_key = '" . ciniki_core_dbQuote($ciniki, $args['key']) . "' "
            . "ORDER BY note_key, (flags&0x01) DESC, note_date, date_added "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.materiamedica', array(
            array('container'=>'notes', 'fname'=>'id', 'fields'=>array('id', 'flags', 'note_date', 'content')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['notes']) ) {
            return array('stat'=>'ok', 'notes'=>$rc['notes']);
        }
        return array('stat'=>'ok', 'notes'=>array());
    } 
    
    //
    // Load the notes for multiple keys
    //
    elseif( isset($args['like_key']) && $args['like_key'] != '' ) {
        $strsql = "SELECT id, "
            . "note_key, "
            . "flags, "
            . "DATE_FORMAT(note_date, '" . ciniki_core_dbQuote($ciniki, $date_format) . "') AS note_date, "
            . "content "
            . "FROM ciniki_materiamedica_notes "
            . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $business_id) . "' "
            . "AND (note_key LIKE '" . ciniki_core_dbQuote($ciniki, $args['like_key']) . "' "
                . "OR note_key LIKE '" . ciniki_core_dbQuote($ciniki, $args['like_key']) . "-%' "
                . ") "
            . "ORDER BY note_key, (flags&0x01) DESC, note_date, date_added "
            . "";
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
        $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.materiamedica', array(
            array('container'=>'note_keys', 'fname'=>'note_key', 'fields'=>array('note_key')),
            array('container'=>'notes', 'fname'=>'id', 'fields'=>array('id', 'flags', 'note_date', 'content')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        $note_keys = array();
        if( isset($rc['note_keys']) ) {
            foreach($rc['note_keys'] as $note) {
                $note_keys[$note['note_key']] = $note['notes'];
            }
        }
        return array('stat'=>'ok', 'note_keys'=>$note_keys);
    }

    return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2689', 'msg'=>'Unable to find notes'));
}
?>
