<?php
//
// Description
// -----------
//
// Arguments
// ---------
//
// Returns
// -------
//
function ciniki_materiamedica_objects($ciniki) {
	$objects = array();
	$objects['plant'] = array(
		'name'=>'Plant',
		'sync'=>'yes',
		'table'=>'ciniki_materiamedica_plants',
		'fields'=>array(
			'family'=>array(),
			'genus'=>array(),
			'species'=>array(),
			'permalink'=>array(),
			'plant_type'=>array(),
			'growth_pattern'=>array(),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'habitat'=>array(),
			'cultivation'=>array(),
			'history'=>array(),
			'contraindications'=>array(),
			'notes'=>array(),
			'reference_notes'=>array(),
			),
		'history_table'=>'ciniki_materiamedica_history',
		);
	$objects['plant_tag'] = array(
		'name'=>'Plant Tag',
		'sync'=>'yes',
		'table'=>'ciniki_materiamedica_plant_tags',
			'fields'=>array(
			'plant_id'=>array('ref'=>'ciniki.materiamedica.plant'),
			'tag_type'=>array(),
			'tag_name'=>array(),
			'permalink'=>array(),
			),
		'history_table'=>'ciniki_materiamedica_history',
		);
	$objects['plant_image'] = array(
		'name'=>'Plant Image',
		'sync'=>'yes',
		'table'=>'ciniki_materiamedica_images',
		'fields'=>array(
			'plant_id'=>array('ref'=>'ciniki.materiamedica.plant'),
			'name'=>array(),
			'permalink'=>array(),
			'flags'=>array('default'=>'0'),
			'parts'=>array('default'=>'0'),
			'webflags'=>array('default'=>'0'),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			),
		'history_table'=>'ciniki_materiamedica_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
