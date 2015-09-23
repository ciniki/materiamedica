<?php
//
// Description
// ===========
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:		The ID of the business to get the list from.
// 
// Returns
// -------
//
function ciniki_materiamedica_plantList($ciniki) {
    //  
    // Find all the required and optional arguments
    //  
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
		'tag_type'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Type'),
		'tag_name'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Tag'),
        'limit'=>array('required'=>'no', 'blank'=>'no', 'name'=>'Limit'), 
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
    $rc = ciniki_materiamedica_checkAccess($ciniki, $args['business_id'], 'ciniki.materiamedica.plantList'); 
    if( $rc['stat'] != 'ok' ) { 
        return $rc;
    }   

	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');

	//
	// Get the tag stats
	//
	$tags = array();
	if( isset($args['tag_type']) && $args['tag_type'] == 'system' ) {
		$strsql = "SELECT DISTINCT 'system' AS tag_type, system AS tag_name, COUNT(DISTINCT plant_id) AS num_plants "
			. "FROM ciniki_materiamedica_plant_actions "
			. "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.plant_id > 0 "
			. "GROUP BY tag_type, tag_name "
			. "";
		ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'tags', 'fname'=>'tag_name', 'name'=>'tag',
				'fields'=>array('tag_name', 'num_plants')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['tags']) ) {
			$tags = $rc['tags'];
		}

		//
		// Get untaged materiamedica
		//
		$strsql = "SELECT ciniki_materiamedica_plant_actions.system, "
			. "COUNT(ciniki_materiamedica_plants.id) AS num_plants "
			. "FROM ciniki_materiamedica_plants "
			. "LEFT JOIN ciniki_materiamedica_plant_actions ON ("
				. "ciniki_materiamedica_plants.id = ciniki_materiamedica_plant_actions.plant_id "
				. "AND ciniki_materiamedica_plant_actions.system <> '' "
				. "AND ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. ") "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ISNULL(system) "
			. "";
		$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'untagged');
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['untagged']) && $rc['untagged']['num_plants'] > 0 ) {
			$tags[] = array('tag'=>array('permalink'=>'--', 
				'tag_name'=>'Unknown', 
				'num_plants'=>$rc['untagged']['num_plants'],
				));
		}
	}
	elseif( isset($args['tag_type']) && $args['tag_type'] == 'action' ) {
		$strsql = "SELECT DISTINCT 'action' AS tag_type, action AS tag_name, COUNT(DISTINCT plant_id) AS num_plants "
			. "FROM ciniki_materiamedica_plant_actions "
			. "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.plant_id > 0 "
			. "GROUP BY tag_type, tag_name "
			. "";
		ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'tags', 'fname'=>'tag_name', 'name'=>'tag',
				'fields'=>array('tag_name', 'num_plants')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['tags']) ) {
			$tags = $rc['tags'];
		}

		//
		// Get untaged materiamedica
		//
		$strsql = "SELECT ciniki_materiamedica_plant_actions.action, "
			. "COUNT(ciniki_materiamedica_plants.id) AS num_plants "
			. "FROM ciniki_materiamedica_plants "
			. "LEFT JOIN ciniki_materiamedica_plant_actions ON ("
				. "ciniki_materiamedica_plants.id = ciniki_materiamedica_plant_actions.plant_id "
				. "AND ciniki_materiamedica_plant_actions.action <> '' "
				. "AND ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. ") "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ISNULL(action) "
			. "";
		$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'untagged');
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['untagged']) && $rc['untagged']['num_plants'] > 0 ) {
			$tags[] = array('tag'=>array('permalink'=>'--', 
				'tag_name'=>'Unknown', 
				'num_plants'=>$rc['untagged']['num_plants'],
				));
		}
	}
	elseif( isset($args['tag_type']) && $args['tag_type'] != '' ) {
		$strsql = "SELECT tag_type, tag_name, COUNT(plant_id) AS num_plants "
			. "FROM ciniki_materiamedica_plant_tags "
			. "WHERE ciniki_materiamedica_plant_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
			. "AND ciniki_materiamedica_plant_tags.plant_id > 0 "
			. "GROUP BY tag_type, tag_name "
			. "";
		ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
		$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
			array('container'=>'tags', 'fname'=>'tag_name', 'name'=>'tag',
				'fields'=>array('tag_name', 'num_plants')),
			));
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['tags']) ) {
			$tags = $rc['tags'];
		}

		//
		// Get untaged materiamedica
		//
		$strsql = "SELECT ciniki_materiamedica_plant_tags.tag_name, "
			. "COUNT(ciniki_materiamedica_plants.id) AS num_plants "
			. "FROM ciniki_materiamedica_plants "
			. "LEFT JOIN ciniki_materiamedica_plant_tags ON ("
				. "ciniki_materiamedica_plants.id = ciniki_materiamedica_plant_tags.plant_id "
				. "AND ciniki_materiamedica_plant_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
				. "AND ciniki_materiamedica_plant_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. ") "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ISNULL(tag_name) "
			. "";
		$rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.materiamedica', 'untagged');
		if( $rc['stat'] != 'ok' ) {
			return $rc;
		}
		if( isset($rc['untagged']) && $rc['untagged']['num_plants'] > 0 ) {
			$tags[] = array('tag'=>array('permalink'=>'--', 
				'tag_name'=>'Unknown', 
				'num_plants'=>$rc['untagged']['num_plants'],
				));
		}
	}

	//
	// Get the list of plants requested
	//
	$plants = array();
	if( isset($args['tag_type']) && $args['tag_type'] == 'all' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY genus, species "
			. "";
	} elseif( isset($args['tag_type']) && $args['tag_type'] == 'system' && isset($args['tag_name']) && $args['tag_name'] == 'Unknown' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plants "
			. "LEFT JOIN ciniki_materiamedica_plant_actions ON ("
				. "ciniki_materiamedica_plants.id = ciniki_materiamedica_plant_actions.plant_id "
				. "AND ciniki_materiamedica_plant_actions.system <> '' "
				. "AND ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. ") "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ISNULL(ciniki_materiamedica_plant_actions.id) "
			. "ORDER BY genus, species "
			. "";
	} elseif( isset($args['tag_type']) && $args['tag_type'] == 'action' && isset($args['tag_name']) && $args['tag_name'] == 'Unknown' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plants "
			. "LEFT JOIN ciniki_materiamedica_plant_actions ON ("
				. "ciniki_materiamedica_plants.id = ciniki_materiamedica_plant_actions.plant_id "
				. "AND ciniki_materiamedica_plant_actions.action <> '' "
				. "AND ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. ") "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ISNULL(ciniki_materiamedica_plant_actions.id) "
			. "ORDER BY genus, species "
			. "";
	}
	elseif( isset($args['tag_type']) && $args['tag_type'] != '' && isset($args['tag_name']) && $args['tag_name'] == 'Unknown' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plants "
			. "LEFT JOIN ciniki_materiamedica_plant_tags ON ("
				. "ciniki_materiamedica_plants.id = ciniki_materiamedica_plant_tags.plant_id "
				. "AND ciniki_materiamedica_plant_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
				. "AND ciniki_materiamedica_plant_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
				. ") "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ISNULL(ciniki_materiamedica_plant_tags.tag_name) "
			. "ORDER BY genus, species "
			. "";
	} elseif( isset($args['tag_type']) && $args['tag_type'] == 'system' && isset($args['tag_name']) && $args['tag_name'] != '' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "	
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plant_actions, ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.system = '" . ciniki_core_dbQuote($ciniki, $args['tag_name']) . "' "
			. "AND ciniki_materiamedica_plant_actions.plant_id = ciniki_materiamedica_plants.id "
			. "AND ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY genus, species "
			. "";
	} elseif( isset($args['tag_type']) && $args['tag_type'] == 'action' && isset($args['tag_name']) && $args['tag_name'] != '' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "	
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plant_actions, ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plant_actions.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_actions.action = '" . ciniki_core_dbQuote($ciniki, $args['tag_name']) . "' "
			. "AND ciniki_materiamedica_plant_actions.plant_id = ciniki_materiamedica_plants.id "
			. "AND ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY genus, species "
			. "";
	} elseif( isset($args['tag_type']) && $args['tag_type'] != '' && isset($args['tag_name']) && $args['tag_name'] != '' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "	
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plant_tags, ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plant_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
			. "AND ciniki_materiamedica_plant_tags.tag_name = '" . ciniki_core_dbQuote($ciniki, $args['tag_name']) . "' "
			. "AND ciniki_materiamedica_plant_tags.plant_id = ciniki_materiamedica_plants.id "
			. "AND ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY genus, species "
			. "";
	} elseif( isset($args['tag_type']) && $args['tag_type'] != '' ) {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "	
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plant_tags, ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plant_tags.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "AND ciniki_materiamedica_plant_tags.tag_type = '" . ciniki_core_dbQuote($ciniki, $args['tag_type']) . "' "
			. "AND ciniki_materiamedica_plant_tags.plant_id = ciniki_materiamedica_plants.id "
			. "AND ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY genus, species "
			. "";
	} else {
		$strsql = "SELECT ciniki_materiamedica_plants.id, "
			. "ciniki_materiamedica_plants.family, "
			. "ciniki_materiamedica_plants.genus, "
			. "ciniki_materiamedica_plants.species, "
			. "ciniki_materiamedica_plants.common_name "
			. "FROM ciniki_materiamedica_plants "
			. "WHERE ciniki_materiamedica_plants.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' "
			. "ORDER BY genus, species "
			. "";
		if( isset($args['limit']) && $args['limit'] != '' && $args['limit'] > 0 ) {
			$strsql .= "LIMIT " . ciniki_core_dbQuote($ciniki, $args['limit']) . " ";
		} else {
			$strsql .= "LIMIT 25 ";
		}
	}
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQueryTree');
	$rc = ciniki_core_dbHashQueryTree($ciniki, $strsql, 'ciniki.materiamedica', array(
		array('container'=>'plants', 'fname'=>'id', 'name'=>'plant',
			'fields'=>array('id', 'family', 'genus', 'species', 'common_name')),
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	if( isset($rc['plants']) ) {
		$plants = $rc['plants'];
	}

	return array('stat'=>'ok', 'tags'=>$tags, 'plants'=>$plants);
}
?>
