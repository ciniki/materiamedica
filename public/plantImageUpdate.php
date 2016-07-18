<?php
//
// Description
// -----------
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business to add the plant image to.
// name:                The name of the plant image.  
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_materiamedica_plantImageUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'plant_image_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plant Image'), 
        'image_id'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Image'),
        'primary_image'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Make Primary Image'),
        'name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Title'), 
        'permalink'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Permalink'), 
        'flags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Flags'), 
        'parts'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Plant Parts'), 
        'location'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Location'), 
        'date_taken'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'date', 'name'=>'Date'), 
        'webflags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Website Flags'), 
        'sequence'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Sequence'), 
        'description'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Description'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantImageUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    //
    // Get the existing image details
    //
    $strsql = "SELECT uuid, image_id, plant_id "
        . "FROM ciniki_materiamedica_plant_images "
        . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['plant_image_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'item');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['item']) ) {
        return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2564', 'msg'=>'Plant image not found'));
    }
    $item = $rc['item'];

    if( isset($args['name']) ) {
        if( $args['name'] != '' ) {
            $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($args['name'])));
        } else {
            $args['permalink'] = preg_replace('/ /', '-', preg_replace('/[^a-z0-9 ]/', '', strtolower($item['uuid'])));
        }
        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id, name, permalink FROM ciniki_materiamedica_plant_images "
            . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
            . "AND plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['plant_image_id']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'image');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('pkg'=>'ciniki', 'code'=>'2565', 'msg'=>'You already have an image with this name, please choose another name'));
        }
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
    // Update the plant image in the database
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    $rc = ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.materiamedica.plant_image', $args['plant_image_id'], $args, 0x04);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

    $image_id = (isset($args['image_id'])?$args['image_id']:$item['image_id']);

    //
    // Check if this image should be the primary
    //
    if( isset($args['primary_image']) ) {
        if( $args['primary_image'] == 'yes' ) {
            $rc = ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.materiamedica.plant', $item['plant_id'], array('image_id'=>$image_id), 0x04);
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
        } elseif( $args['primary_image'] == 'no' ) {
            $strsql = "SELECT image_id "
                . "FROM ciniki_materiamedica_plants "
                . "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
                . "AND id = '" . ciniki_core_dbQuote($ciniki, $item['plant_id']) . "' "
                . "";
            $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'plant');
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            // Check if primary image should be removed
            if( isset($rc['plant']['image_id']) && $rc['plant']['image_id'] == $image_id ) {
                $rc = ciniki_core_objectUpdate($ciniki, $args['business_id'], 'ciniki.materiamedica.plant', $item['plant_id'], array('image_id'=>0), 0x04);
                if( $rc['stat'] != 'ok' ) {
                    return $rc;
                }
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

    return array('stat'=>'ok');
}
?>
