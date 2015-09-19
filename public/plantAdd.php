<?php
//
// Description
// ===========
// This method will add a new plant to the database.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to add the plant to.  The user must
//					an owner of the business.
//
// 
// Returns
// -------
// <rsp stat='ok' id='34' />
//
function ciniki_materiamedica_plantAdd(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'family'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Family'), 
        'genus'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Genus'), 
        'species'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Species'), 
        'common_name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Common Name'), 
        'plant_type'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plant Type'), 
        'growth_pattern'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Growth Pattern'), 
        'parts_used'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Parts Used'), 
		'image_id'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'0', 'name'=>'Image'),
        'habitat'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Habitat'), 
        'cultivation'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Cultivation'), 
        'history'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'History'), 
        'warnings'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Warnings'), 
        'contraindications'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Contraindications'), 
        'quick_id'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Quick ID'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Notes'), 
        'reference_notes'=>array('required'=>'no', 'blank'=>'yes', 'default'=>'', 'name'=>'Reference Notes'), 
		'tag-30'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Uses'),
		'tag-40'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Actions'),
        )); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   
    $args = $rc['args'];

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
	$args['permalink'] = ciniki_core_makePermalink($ciniki, $args['family'] . '-' . $args['genus'] . '-' . $args['species']);
    
    //  
    // Make sure this module is activated, and
    // check permission to run this function for this business
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'checkAccess');
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantAdd'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	//
	// Check the permalink doesn't already exist
	//
	$strsql = "SELECT id, permalink "
		. "FROM ciniki_materiamedica_plants "
		. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
		. "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
		. "";
	$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'plant');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( $rc['num_rows'] > 0 ) {
		return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2553', 'msg'=>'You already have a plant with this name, please choose another name.'));
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
	// Add the plant
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectAdd');
	$rc = ciniki_core_objectAdd($ciniki, $args['business_id'], 'ciniki.materiamedica.plant', $args);
	if( $rc['stat'] != 'ok' ) {	
		return $rc;
	}
	$plant_id = $rc['id'];

	//
	// Update the tags
	//
	$tag_types = array(
		'30'=>'uses',
		'40'=>'actions',
		);
	foreach($tag_types as $tag_type => $arg_name) {
		if( isset($args['tag-' . $tag_type]) ) {
			ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'tagsUpdate');
			$rc = ciniki_core_tagsUpdate($ciniki, 'ciniki.materiamedica', 'plant_tag', $args['business_id'],
				'ciniki_materiamedica_plant_tags', 'ciniki_materiamedica_history',
				'plant_id', $plant_id, $tag_type, $args['tag-' . $tag_type]);
			if( $rc['stat'] != 'ok' ) {
				ciniki_core_dbTransactionRollback($ciniki, 'ciniki.materiamedica');
				return $rc;
			}
		}
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

	return array('stat'=>'ok', 'id'=>$plant_id);
}
?>
