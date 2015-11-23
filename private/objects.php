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
			'plant_number'=>array('default'=>'1'),
			'family'=>array(),
			'genus'=>array(),
			'species'=>array(),
			'common_name'=>array('default'=>''),
			'permalink'=>array(),
			'plant_type'=>array(),
			'growth_pattern'=>array(),
			'parts_used'=>array('default'=>'0'),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'image_caption'=>array('default'=>''),
			'habitat'=>array(),
			'cultivation'=>array(),
			'history'=>array(),
			'energetics'=>array(),
			'contraindications'=>array(),
			'warnings'=>array(),
			'quick_id'=>array(),
			'notes'=>array(),
			'reference_notes'=>array(),
			),
		'history_table'=>'ciniki_materiamedica_history',
		);
	$objects['plant_action'] = array(
		'name'=>'Plant Action',
		'sync'=>'yes',
		'table'=>'ciniki_materiamedica_plant_actions',
		'fields'=>array(
			'plant_id'=>array('ref'=>'ciniki.materiamedica.plant'),
			'system'=>array(),
			'action_type'=>array(),
			'action'=>array(),
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
		'table'=>'ciniki_materiamedica_plant_images',
		'fields'=>array(
			'plant_id'=>array('ref'=>'ciniki.materiamedica.plant'),
			'name'=>array(),
			'permalink'=>array(),
			'flags'=>array('default'=>'0'),
			'parts'=>array('default'=>'0'),
			'location'=>array('default'=>''),
			'date_taken'=>array('default'=>''),
			'webflags'=>array('default'=>'0'),
			'image_id'=>array('ref'=>'ciniki.images.image'),
			'description'=>array(),
			),
		'history_table'=>'ciniki_materiamedica_history',
		);
	$objects['note'] = array(
		'name'=>'Note',
		'sync'=>'yes',
		'table'=>'ciniki_materiamedica_notes',
		'fields'=>array(
			'note_key'=>array(),
			'flags'=>array('default'=>'0'),
			'note_date'=>array(),
			'content'=>array(),
			),
		'history_table'=>'ciniki_materiamedica_history',
		);
	
	return array('stat'=>'ok', 'objects'=>$objects);
}
?>
