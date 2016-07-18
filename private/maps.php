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
            '0'=>'',
            '10'=>'Tree',
            '20'=>'Plant',
            '30'=>'Shrub',
            '40'=>'Vine',
            ),
        'growth_pattern'=>array(
            '0'=>'',
            '10'=>'Deciduous',
            '20'=>'Evergreen',
            '30'=>'Annual',
            '40'=>'Biennial',
            '50'=>'Perennial',
            ),
        'parts_used'=>array(
            0x01=>'Bark',
            0x02=>'Flowers',
            0x04=>'Fruits',
            0x08=>'Roots',
            0x10=>'Seeds',
            0x20=>'Stems',
            0x40=>'Leaves',
            0x80=>'Wood',
            ),
        );

    return array('stat'=>'ok', 'maps'=>$maps);
}
?>
