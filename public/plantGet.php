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
function ciniki_materiamedica_plantGet($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'tnid'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Tenant'), 
        'plant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Item'), 
        'images'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Images'),
        'tags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Tags'),
        'systems'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Systems'),
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['tnid'], 'ciniki.materiamedica.plantGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'timezoneOffset');
    $utc_offset = ciniki_users_timezoneOffset($ciniki);

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryArrayTree');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'datetimeFormat');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');

    $datetime_format = ciniki_users_datetimeFormat($ciniki);
    $date_format = ciniki_users_dateFormat($ciniki);

    //
    // Load plant maps
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'maps');
    $rc = ciniki_materiamedica_maps($ciniki);
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $maps = $rc['maps'];

    if( $args['plant_id'] == 0 ) {
        //
        // Get the next plant number available
        //
        $strsql = "SELECT MAX(plant_number) AS plant_number "
            . "FROM ciniki_materiamedica_plants "
            . "WHERE ciniki_materiamedica_plants.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "";
        $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'max');
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['max']['plant_number']) ) {
            $plant_number = $rc['max']['plant_number'] + 1;
        } else {
            $plant_number = 1;
        }
        $plant = array(
            'id'=>'0',
            'plant_number'=>sprintf('%03d', $plant_number),
            'family'=>'',
            'genus'=>'',
            'species'=>'',
            'common_name'=>'',
            'plant_type'=>'0',
            'growth_pattern'=>'0',
            'parts_used'=>'0',
            'permalink'=>'',
            'image_id'=>'0',
            'image_caption'=>'',
            'habitat'=>'',
            'cultivation'=>'',
            'history'=>'',
            'energetics'=>'',
            'warnings'=>'',
            'contraindications'=>'',
            'quick_id'=>'',
            'notes'=>'',
            'reference_notes'=>'',
            );
    } else {
        $strsql = "SELECT ciniki_materiamedica_plants.id, "
            . "LPAD(ciniki_materiamedica_plants.plant_number, 3, '0') AS plant_number, "
            . "ciniki_materiamedica_plants.family, "
            . "ciniki_materiamedica_plants.genus, "
            . "ciniki_materiamedica_plants.species, "
            . "ciniki_materiamedica_plants.common_name, "
            . "ciniki_materiamedica_plants.permalink, "
            . "ciniki_materiamedica_plants.plant_type, "
            . "ciniki_materiamedica_plants.plant_type AS plant_type_text, "
            . "ciniki_materiamedica_plants.growth_pattern, "
            . "ciniki_materiamedica_plants.growth_pattern AS growth_pattern_text, "
            . "ciniki_materiamedica_plants.parts_used, "
            . "ciniki_materiamedica_plants.parts_used AS parts_used_text, "
            . "ciniki_materiamedica_plants.image_id, "
            . "ciniki_materiamedica_plants.image_caption, "
            . "ciniki_materiamedica_plants.habitat, "
            . "ciniki_materiamedica_plants.cultivation, "
            . "ciniki_materiamedica_plants.history, "
            . "ciniki_materiamedica_plants.energetics, "
            . "ciniki_materiamedica_plants.warnings, "
            . "ciniki_materiamedica_plants.contraindications, "
            . "ciniki_materiamedica_plants.quick_id, "
            . "ciniki_materiamedica_plants.notes, "
            . "ciniki_materiamedica_plants.reference_notes "
            . "FROM ciniki_materiamedica_plants "
            . "WHERE ciniki_materiamedica_plants.tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "AND ciniki_materiamedica_plants.id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
            . "";

        $rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
            array('container'=>'plants', 'fname'=>'id', 'name'=>'plant',
                'fields'=>array('id', 'plant_number', 'family', 'genus', 'species', 'common_name', 'permalink', 
                    'plant_type', 'plant_type_text', 'growth_pattern', 'growth_pattern_text', 'parts_used', 'parts_used_text', 
                    'image_id', 'image_caption', 'habitat', 'cultivation', 
                    'history', 'energetics', 'warnings', 'contraindications', 'quick_id', 'notes', 'reference_notes'),
                'maps'=>array('plant_type_text'=>$maps['plant']['plant_type'],
                    'growth_pattern_text'=>$maps['plant']['growth_pattern'],
                    ),
                'flags'=>array('parts_used_text'=>$maps['plant']['parts_used']),
                ),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( !isset($rc['plants']) ) {
            return array('stat'=>'ok', 'err'=>array('code'=>'ciniki.materiamedica.8', 'msg'=>'Unable to find plant'));
        }
        $plant = $rc['plants'][0]['plant'];

        //
        // Get the categories and tags for the post
        //
        $strsql = "SELECT CONCAT('tag-', tag_type) AS tagtype, tag_type, tag_name AS lists "
            . "FROM ciniki_materiamedica_plant_tags "
            . "WHERE plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
            . "AND tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "ORDER BY tag_type, tag_name "
            . "";
        $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
            array('container'=>'tags', 'fname'=>'tagtype',
                'fields'=>array('tag_type', 'lists'), 'dlists'=>array('lists'=>'::')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['tags']) ) {
            foreach($rc['tags'] as $arg => $tag) {
                $plant[$arg] = $tag['lists'];
            }
        }

        //
        // Get the systems if requested
        //
        if( isset($args['systems']) && $args['systems'] == 'yes' ) {
            $plant['actions'] = array();
            $strsql = "SELECT id, system, action_type, action AS actions "
                . "FROM ciniki_materiamedica_plant_actions "
                . "WHERE plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
                . "AND tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . "ORDER BY system, action_type, action "
                . "";
            $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
                array('container'=>'systems', 'fname'=>'system',
                    'fields'=>array('system')),
                array('container'=>'actions', 'fname'=>'action_type',
                    'fields'=>array('actions'), 'dlists'=>array('actions'=>'::')),
                ));
            if( $rc['stat'] != 'ok' ) { 
                return $rc;
            }
            $plant['systems'] = array();
            if( isset($rc['systems']) ) {
                $plant['systems'] = $rc['systems'];
            }
        }

        //
        // Get the additional images if requested
        //
        if( isset($args['images']) && $args['images'] == 'yes' ) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheThumbnail');
            $strsql = "SELECT id, image_id, name, flags, parts, webflags, description "
                . "FROM ciniki_materiamedica_plant_images "
                . "WHERE plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
                . "AND tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
                . "ORDER BY date_added, name "
                . "";
            $rc = ciniki_core_dbHashQueryArrayTree($ciniki, $strsql, 'ciniki.materiamedica', array(
                array('container'=>'images', 'fname'=>'id', 'name'=>'image',
                    'fields'=>array('id', 'image_id', 'name', 'flags', 'parts', 'webflags', 'description')),
                ));
            if( $rc['stat'] != 'ok' ) { 
                return $rc;
            }
            if( isset($rc['images']) ) {
                $plant['images'] = $rc['images'];
                foreach($plant['images'] as $inum => $img) {
                    if( isset($img['image_id']) && $img['image_id'] > 0 ) {
                        $rc = ciniki_images_loadCacheThumbnail($ciniki, $args['tnid'], $img['image_id'], 75);
                        if( $rc['stat'] != 'ok' ) {
                            return $rc;
                        }
                        $plant['images'][$inum]['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
                    }
                }
            }
        }

        //
        // Get any notes
        //
        if( isset($args['notes']) && $args['notes'] == 'yes' ) {
            ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'notesLoad');
            $rc = ciniki_materiamedica_notesLoad($ciniki, $args['tnid'], array('like_key'=>'ciniki.materiamedica.plant-' . $args['plant_id']));
            if( $rc['stat'] != 'ok' ) { 
                return $rc;
            }
            if( isset($rc['note_keys']) ) {
                $plant['_notes'] = $rc['note_keys'];
            } else {
                $plant['_notes'] = array();
            }
        }
    }

    //
    // Check if all tags should be returned
    //
    $tags = array();
    if( isset($args['tags']) && $args['tags'] == 'yes' ) {
        //
        // Get the available tags
        //
        $strsql = "SELECT DISTINCT CONCAT('tag-', tag_type) AS tagtype, tag_type, tag_name AS tag_names "
            . "FROM ciniki_materiamedica_plant_tags "
            . "WHERE tnid = '" . ciniki_core_dbQuote($ciniki, $args['tnid']) . "' "
            . "ORDER BY tag_type, tag_name "
            . "";
        $rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
            array('container'=>'tags', 'fname'=>'tagtype', 
                'fields'=>array('tag_type', 'tag_names'), 'dlists'=>array('tag_names'=>'::')),
            ));
        if( $rc['stat'] != 'ok' ) {
            return $rc;
        }
        if( isset($rc['tags']) ) {
            $tags = $rc['tags'];
        }
    }

    return array('stat'=>'ok', 'plant'=>$plant, 'tags'=>$tags);
}
?>
