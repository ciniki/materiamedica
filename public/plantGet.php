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
// business_id: 		The ID of the business to get the plant from.
// plant_id:			The ID of the plant to get.
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
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'plant_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Item'), 
		'images'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Images'),
		'tags'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Tags'),
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantGet'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'timezoneOffset');
	$utc_offset = ciniki_users_timezoneOffset($ciniki);

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryIDTree');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'datetimeFormat');
	ciniki_core_loadMethod($ciniki, 'ciniki', 'users', 'private', 'dateFormat');

	$datetime_format = ciniki_users_datetimeFormat($ciniki);
	$date_format = ciniki_users_dateFormat($ciniki);

	//
	// Load event maps
	//
	ciniki_core_loadMethod($ciniki, 'ciniki', 'materiamedica', 'private', 'maps');
	$rc = ciniki_materiamedica_maps($ciniki);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$maps = $rc['maps'];

	if( $args['plant_id'] == 0 ) {
		$plant = array(
			'id'=>'0',
			'family'=>'',
			'genus'=>'',
			'species'=>'',
			'plant_type'=>'0',
			'growth_pattern'=>'0',
			'permalink'=>'',
			'image_id'=>'0',
			'habitat'=>'',
			'cultivation'=>'',
			'history'=>'',
			'warnings'=>'',
			'contraindications'=>'',
			'quick_id'=>'',
			'notes'=>'',
			'reference_notes'=>'',
			);
	} else {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.permalink, "
			. "ciniki_materiamedica_plants.plant_type, "
			. "ciniki_materiamedica_plants.plant_type AS plant_type_text, "
			. "ciniki_materiamedica_plants.growth_pattern, "
			. "ciniki_materiamedica_plants.growth_pattern AS growth_pattern_text, "
			. "ciniki_materiamedica_plants.image_id, "
			. "ciniki_materiamedica_plants.habitat, "
			. "ciniki_materiamedica_plants.cultivation, "
			. "ciniki_materiamedica_plants.history, "
			. "ciniki_materiamedica_plants.warnings, "
			. "ciniki_materiamedica_plants.contraindications, "
			. "ciniki_materiamedica_plants.quick_id, "
			. "ciniki_materiamedica_plants.notes, "
			. "ciniki_materiamedica_plants.reference_notes "
			. "FROM ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plants.id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
			. "";

		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'plants', 'fname'=>'id', 'name'=>'plant',
				'fields'=>array('id', 'family', 'genus', 'species', 'permalink', 
					'plant_type', 'plant_type_text', 'growth_pattern', 'growth_pattern_text', 
					'image_id', 'habitat', 'cultivation', 
					'history', 'warnings', 'contraindications', 'quick_id', 'notes', 'reference_notes'),
				'maps'=>array('plant_type_text'=>$maps['plant']['plant_type'],
					'growth_pattern_text'=>$maps['plant']['growth_pattern'],
					),
				),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( !isset($rc['plants']) ) {
			return array('stat'=>'ok', 'err'=>array('pkg'=>'ciniki', 'code'=>'2568', 'msg'=>'Unable to find plant'));
		}
		$plant = $rc['plants'][0]['plant'];

		//
		// Get the categories and tags for the post
		//
		$strsql = "SELECT CONCAT('tag-', tag_type) AS tagtype, tag_type, tag_name AS lists "
			. "FROM ciniki_materiamedica_plant_tags "
			. "WHERE plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
			. "AND business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY tag_type, tag_name "
			. "";
		$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'tags', 'fname'=>'tagtype', 'name'=>'tags',
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
		// Get the additional images if requested
		//
		if( isset($args['images']) && $args['images'] == 'yes' ) {
			ciniki_core_loadMethod($ciniki, 'ciniki', 'images', 'private', 'loadCacheThumbnail');
			$strsql = "SELECT id, image_id, name, flags, parts, webflags, description "
				. "FROM ciniki_materiamedica_plant_images "
				. "WHERE plant_id = '" . ciniki_core_dbQuote($ciniki, $args['plant_id']) . "' "
				. "AND business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. "ORDER BY date_added, name "
				. "";
			$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
				array('container'=>'images', 'fname'=>'id', 'name'=>'image',
					'fields'=>array('id', 'image_id', 'name', 'flags', 'parts', 'webflags', 'description')),
				));
			if( $rc['stat'] != 'ok' ) {	
				return $rc;
			}
			if( isset($rc['images']) ) {
				$plant['images'] = $rc['images'];
				foreach($plant['images'] as $inum => $img) {
					if( isset($img['image']['image_id']) && $img['image']['image_id'] > 0 ) {
						$rc = ciniki_images_loadCacheThumbnail($ciniki, $args['business_id'], $img['image']['image_id'], 75);
						if( $rc['stat'] != 'ok' ) {
							return $rc;
						}
						$plant['images'][$inum]['image']['image_data'] = 'data:image/jpg;base64,' . base64_encode($rc['image']);
					}
				}
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
			. "WHERE business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY tag_type, tag_name "
			. "";
		$rc = ciniki_core_dbHashQueryIDTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'tags', 'fname'=>'tagtype', 'name'=>'tag',
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
