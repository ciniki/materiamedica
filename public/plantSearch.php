<?php
//
// Description
// -----------
//
// Returns
// -------
//
function ciniki_materiamedica_plantSearch($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'start_needle'=>array('required'=>'yes', 'blank'=>'yes', 'name'=>'Search String'), 
        'limit'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Limit'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantSearch', 0, 0); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Get the number of faqs in each status for the business, 
	// if no rows found, then return empty array
	//
	$strsql = "SELECT id, family, genus, species, common_name "
		. "FROM ciniki_materiamedica_plants "
		. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND (family LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. "OR genus LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. "OR species LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. "OR common_name LIKE '" . ciniki_core_dbQuote($ciniki, $args['start_needle']) . "%' "
			. ") "
		. "";
	$strsql .= "ORDER BY family, genus, species COLLATE latin1_general_cs "
		. "";
	if( isset($args['limit']) && $args['limit'] != '' && $args['limit'] > 0 ) {
		$strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";
	} else {
		$strsql .= "LIMIT 25 ";
	}
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
		array('container'=>'plants', 'fname'=>'id', 'name'=>'plant', 
			'fields'=>array('id', 'family', 'genus', 'species', 'common_name')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( !isset($rc['plants']) || !is_array($rc['plants']) ) {
		return array('stat'=>'ok', 'plants'=>array());
	}
	return array('stat'=>'ok', 'plants'=>$rc['plants']);
}
?>
