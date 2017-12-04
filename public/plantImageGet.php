<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:         The ID of the tenant to add the plant image to.
// plant_image_id:      The ID of the plant image to get.
//
// Returns
// -------
//
function ciniki_materiamedica_plantImageGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'plant_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plant Image'),
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['tnid'], 'ciniki.materiamedica.plantImageGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');
    $date_format = ciniki_users_dateFormat($ciniki, 'mysql');

    //
    // Get the main information
    //
    $strsql = "SELECT ciniki_materiamedica_plant_images.id, "
        . "ciniki_materiamedica_plant_images.plant_id, "
        . "ciniki_materiamedica_plant_images.name, "
        . "ciniki_materiamedica_plant_images.permalink, "
        . "ciniki_materiamedica_plant_images.flags, "
        . "ciniki_materiamedica_plant_images.parts, "
        . "ciniki_materiamedica_plant_images.location, "
        . "IFNULL(DATE_FORMAT(ciniki_materiamedica_plant_images.date_taken, '" . ciniki_core_dbQuote($ciniki, $date_format) . "'), '') AS date_taken, "
        . "ciniki_materiamedica_plant_images.webflags, "
        . "ciniki_materiamedica_plant_images.image_id, "
        . "ciniki_materiamedica_plant_images.description "
        . "FROM ciniki_materiamedica_plant_images "
        . "WHERE ciniki_materiamedica_plant_images.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND ciniki_materiamedica_plant_images.id = '" . ciniki_core_dbQuote($ciniki, $args['plant_image_id']) . "' "
        . "";
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
        array('container'=>'images', 'fname'=>'id', 'name'=>'image',
            'fields'=>array('id', 'plant_id', 'name', 'permalink', 'flags', 'parts', 'location', 'date_taken', 'webflags', 'image_id', 'description')),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['images']) ) {
        return array('stat'=>'ok', 'err'=>array('code'=>'ciniki.materiamedica.13', 'msg'=>'Unable to find image'));
    }
    $image = $rc['images'][0]['image'];

    //
    // Check if this is the primary image
    //
    $strsql = "SELECT image_id "
        . "FROM ciniki_materiamedica_plants "
        . "WHERE ciniki_materiamedica_plants.id = '" . ciniki_core_dbQuote($ciniki, $image['plant_id']) . "' "
        . "AND ciniki_materiamedica_plants.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'plant');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['plant']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.materiamedica.14', 'msg'=>'Unable to find plant information'));
    }
    if( $rc['plant']['image_id'] == $image['image_id'] ) {
        $image['primary_image'] = 'yes';
    } else {
        $image['primary_image'] = 'no';
    }
    
    return array('stat'=>'ok', 'image'=>$image);
}
?>
