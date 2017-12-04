<?php
//
// Description
// ===========
// This method updates one or more elements of an existing plant.
//
// Arguments
// ---------
// api_key:
// auth_token:
// tnid:     The ID of the tenant to the plant is a part of.
// plant_id:        The ID of the plant to update.
//
// Returns
// -------
// <rsp stat='ok' />
//
function ciniki_materiamedica_plantUpdate(&$ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'plant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Plant'), 
        'plant_number'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Number'), 
        'family'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Family'), 
        'genus'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Genus'), 
        'species'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Species'), 
        'common_name'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Common Name'), 
        'plant_type'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Plant Type'), 
        'growth_pattern'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Growth Pattern'), 
        'parts_used'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Parts Used'), 
        'image_id'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Image'),
        'image_caption'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Image Caption'), 
        'habitat'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Habitat'), 
        'cultivation'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Cultivation'), 
        'history'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'History'), 
        'energetics'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Energetics'), 
        'warnings'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Warnings'), 
        'contraindications'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Contraindications'), 
        'quick_id'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Quick ID'), 
        'notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Notes'), 
        'reference_notes'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Reference Notes'), 
        'tag-30'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Uses'),
        'tag-40'=>array('required'=>'no', 'blank'=>'yes', 'type'=>'list', 'delimiter'=>'::', 'name'=>'Actions'),
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['tnid'], 'ciniki.materiamedica.plantUpdate'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }

    // 
    // Force lowercase on species
    //
    if( isset($args['genus']) ) {
        $args['genus'] = ucfirst(strtolower($args['genus']));
    }
    if( isset($args['species']) ) {
        $args['species'] = strtolower($args['species']);
    }
    
    //
    // Get the existing plant
    //
    $strsql = "SELECT family, genus, species "
        . "FROM ciniki_materiamedica_plants "
        . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
        . "AND id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
        . "";
    $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'plant');
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    if( !isset($rc['plant']) ) {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.materiamedica.17', 'msg'=>'That plant does not exist.'));
    }
    $plant = $rc['plant'];

    if( isset($args['family']) || isset($args['genus']) || isset($args['species']) ) {
        ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'makePermalink');
        $args['permalink'] = ciniki_core_makePermalink($ciniki, 
            (isset($args['genus'])?$args['genus']:$plant['genus']) . '-' . (isset($args['species'])?$args['species']:$plant['species'])
            );
        //
        // Make sure the permalink is unique
        //
        $strsql = "SELECT id, permalink "
            . "FROM ciniki_materiamedica_plants "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND permalink = '" . ciniki_core_dbQuote($ciniki, $args['permalink']) . "' "
            . "AND id <> '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'plant');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( $rc['num_rows'] > 0 ) {
            return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.materiamedica.18', 'msg'=>'You already have a plant with this name, please choose another name'));
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
    // Update the plant
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'objectUpdate');
    $rc = ciniki_core_objectUpdate($ciniki, $args['tnid'], 'ciniki.materiamedica.plant', 
        $args['plant_id'], $args);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }

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
            $rc = ciniki_core_tagsUpdate($ciniki, 'ciniki.materiamedica', 'plant_tag', $args['tnid'],
                'ciniki_materiamedica_plant_tags', 'ciniki_materiamedica_history',
                'plant_id', $args['plant_id'], $tag_type, $args['tag-' . $tag_type]);
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
    // Update the last_change date in the tenant modules
    // Ignore the result, as we don't want to stop user updates if this fails.
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'tenants', 'private', 'updateModuleChangeDate');
    ciniki_tenants_updateModuleChangeDate($ciniki, $args['tnid'], 'ciniki', 'materiamedica');

    return array('stat'=>'ok');
}
?>
