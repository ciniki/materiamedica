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
function ciniki_materiamedica_maps($ciniki) {
	$maps = array();

	$maps['plant'] = array(
		'plant_type'=>array(	
			'10'=>'Tree',
			'20'=>'Plant',
			'30'=>'Shrub',
			'40'=>'Vine',
			),
		'growth_pattern'=>array(
			'10'=>'Deciduous',
			'20'=>'Evergreen',
			'30'=>'Annual',
			'40'=>'Biennial',
			'50'=>'Perennial',
			),
		);

	return array('stat'=>'ok', 'maps'=>$maps);
}
?>
