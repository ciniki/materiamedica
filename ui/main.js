//
// The materiamedica app to manage an artists collection
//
function ciniki_materiamedica_main() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		};
	this.tagTypes = {
//		'system':{'name':'Systems', 'arg':'system', 'visible':'yes'},
//		'action':{'name':'Actions', 'arg':'action', 'visible':'yes'},
		'30':{'name':'Uses', 'arg':'uses', 'visible':'no'},
//		'40':{'name':'Actions', 'arg':'actions', 'visible':'no'},
		};
	this.plantTypes = {
		'10':'Tree',
		'20':'Plant',
		'30':'Shrub',
		'40':'Vine',
		};
	this.growthPatterns = {
		'10':'Deciduous',
		'20':'Evergreen',
		'30':'Annual',
		'40':'Biennial',
		'50':'Perennial',
		};
	this.partsUsed = {
		'1':{'name':'Bark'},
		'2':{'name':'Flowers'},
		'3':{'name':'Fruits'},
		'4':{'name':'Leaves'},
		'5':{'name':'Roots'},
		'6':{'name':'Seeds'},
		'7':{'name':'Stems'},
		};
    this.systems = {
        '10':{'name':'General'},
        '40':{'name':'Digestive'},
        '60':{'name':'Cardiovascular'},
        '80':{'name':'Respiratory'},
        '100':{'name':'Nervous'},
        '120':{'name':'Urinary'},
        '140':{'name':'Reproductive'},
        '160':{'name':'Muscoloskeletal'},
        '180':{'name':'Skin'},
        '200':{'name':'Immune'},
        '220':{'name':'Endocrine'},
        };
	this.init = function() {
		//
		// Setup the main panel to list the collection
		//
		this.menu = new M.panel('Materia Media',
			'ciniki_materiamedica_main', 'menu',
			'mc', 'medium', 'sectioned', 'ciniki.materiamedica.main.menu');
		this.menu.data = {};
		this.menu.formtab = 'all';
		this.menu.tag_name = '';
//		this.menu.formtabs = {'label':'', 'tabs':{
//			'all':{'label':'All', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,\'all\',\'\');'},
//			'system':{'label':'Systems', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,\'system\',\'\');'},
//			'action':{'label':'Actions', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,\'action\',\'\');'},
//			'30':{'label':'Uses', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,30,\'\');'},
//			'40':{'label':'Actions', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,40,\'\');'},
//			}};
		this.menu.forms = {};
		this.menu.forms.all = {
//			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':2, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':2,
				'headerValues':['Genus Species', 'Common'],
				'cellClasses':['multiline', ''],
				'sortable':'yes', 'sortTypes':['text', 'text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.forms.system = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':2, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':2,
				'headerValues':['Genus Species', 'Common'],
				'cellClasses':['multiline', ''],
				'sortable':'yes', 'sortTypes':['text', 'text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.forms.action = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':2, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':2,
				'headerValues':['Genus Species', 'Common'],
				'cellClasses':['multiline',''],
				'sortable':'yes', 'sortTypes':['text', 'text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['30'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':2, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':2,
				'headerValues':['Genus Species', 'Common'],
				'cellClasses':['multiline', ''],
				'sortable':'yes', 'sortTypes':['text', 'text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['40'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':2, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':2,
				'headerValues':['Genus Species', 'Common'],
				'cellClasses':['multiline', ''],
				'sortable':'yes', 'sortTypes':['text', 'text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.sections = this.menu.forms.all;	
		this.menu.cellValue = function(s, i, j, d) {
			if( s == 'tags' ) {
				return d.tag.tag_name + ' <span class="count">' + d.tag.num_plants + '</span>';
			} 
			else if( s == 'plants' || s == 'search' ) {
				switch(j) {
					case 0: return '<span class="maintext"><i>' + d.plant.genus + ' ' + d.plant.species + '</i></span>'
						+ '<span class="subtext">' + d.plant.family + '</span>';
					case 1: return d.plant.common_name;
				}
			}
		};
		this.menu.rowFn = function(s, i, d) {
			switch(s) {
				case 'tags': return 'M.ciniki_materiamedica_main.showMenu(null,null,\'' + escape(d.tag.tag_name) + '\');';
				case 'plants': return 'M.ciniki_materiamedica_main.plantShow(\'M.ciniki_materiamedica_main.showMenu();\', \'' + d.plant.id + '\',M.ciniki_materiamedica_main.menu.data[unescape(\'' + escape(s) + '\')]);'; 
			}
		};
		this.menu.sectionData = function(s) { 
			return this.data[s];
		};
		this.menu.liveSearchCb = function(s, i, v) {
			if( v != '' ) {
				M.api.getJSONBgCb('ciniki.materiamedica.plantSearch', {'business_id':M.curBusinessID, 'start_needle':v, 'limit':'15'},
					function(rsp) {
						M.ciniki_materiamedica_main.menu.liveSearchShow(s, null, M.gE(M.ciniki_materiamedica_main.menu.panelUID + '_' + s), rsp.plants);
					});
			}
			return true;
		};
		this.menu.liveSearchResultClass = function(s, f, i, j, d) {
			return 'multiline';
		};
		this.menu.liveSearchResultValue = function(s, f, i, j, d) {
			return this.cellValue(s, i, j, d);
		};
		this.menu.liveSearchResultRowFn = function(s, f, i, j, d) {
			return 'M.ciniki_materiamedica_main.plantShow(\'M.ciniki_materiamedica_main.showMenu(null);\', \'' + d.plant.id + '\');'; 
		};
		this.menu.liveSearchResultRowStyle = function(s, f, i, d) { return ''; };
 //		Currently not allowing full search
//		this.menu.liveSearchSubmitFn = function(s, search_str) {
//			M.ciniki_materiamedica_main.searchArtCatalog('M.ciniki_materiamedica_main.showMenu();', search_str);
//		};
		this.menu.addButton('add', 'Add', 'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);');
		this.menu.addClose('Back');

		//
		// Display information about a plant
		//
		this.plant = new M.panel('Plant',
			'ciniki_materiamedica_main', 'plant',
			'mc', 'medium mediumaside', 'sectioned', 'ciniki.materiamedica.main.plant');
		this.plant.next_plant_id = 0;
		this.plant.prev_plant_id = 0;
		this.plant.data = null;
		this.plant.plant_id = 0;
        this.plant.curtab = 'systems';
		this.plant.sections = {
			'_image':{'label':'Image', 'switchrefresh':'no', 'aside':'yes', 'type':'imageform', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'history':'no'},
			}},
			'info':{'label':'Details', 'switchrefresh':'no', 'aside':'yes', 'list':{
				'plant_number':{'label':'Number', 'type':'text'},
				'name':{'label':'Name', 'type':'text'},
				'common_name':{'label':'Common', 'type':'text'},
				'family':{'label':'Family', 'type':'text'},
				'type_growth':{'label':'Type', 'type':'text'},
				'parts_used':{'label':'Parts Used', 'type':'text'},
//				'tag-30':{'label':'Uses', 'visible':'yes'},
//				'tag-40':{'label':'Actions', 'visible':'yes'},
			}},
			'warnings':{'label':'Warnings', 'switchrefresh':'no', 'aside':'yes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.warnings!=null&&M.ciniki_materiamedica_main.plant.data.warnings!='')?'yes':'no');
				}},
			'contraindications':{'label':'Contraindications', 'switchrefresh':'no', 'aside':'yes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.contraindications!=null&&M.ciniki_materiamedica_main.plant.data.contraindications!='')?'yes':'no');
				}},
			'quick_id':{'label':'Quick ID', 'switchrefresh':'no', 'aside':'yes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.quick_id!=null&&M.ciniki_materiamedica_main.plant.data.quick_id!='')?'yes':'no');
				}},
            '_tabs':{'label':'', 'type':'paneltabs', 'selected':'systems', 'tabs':{
                'systems':{'label':'<span class="faicon">&#xf21e;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'systems\');'},
                'harvesting':{'label':'<span class="faicon">&#xf073;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'harvesting\');'},
                'constituents':{'label':'<span class="faicon">&#xf0c3;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'constituents\');'},
                'habitat':{'label':'<span class="faicon">&#xf06c;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'habitat\');'},
                'images':{'label':'<span class="faicon">&#xf030;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'images\');'},
                'energetics':{'label':'<span class="faicon">&#xf0e7;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'energetics\');'},
                'preparations':{'label':'<span class="faicon">&#xf0f4;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'preparations\');'},
                'notes':{'label':'<span class="faicon">&#xf044;</span>', 'fn':'M.ciniki_materiamedica_main.plant.switchTab(\'notes\');'},
                }},
//			'actions':{'label':'System Actions', 'type':'simplegrid', 'num_cols':2,
//				'headerValues':['System', 'Action'],
//				'addTxt':'Add',
//				'addFn':'M.ciniki_materiamedica_main.actionEdit(\'M.ciniki_materiamedica_main.plantShow()\',0,M.ciniki_materiamedica_main.plant.plant_id);',
//				},
			'habitat':{'label':'Habitat', 'type':'htmlcontent', 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='habitat'?'yes':'hidden'); 
                }},
			'cultivation':{'label':'Cultivation', 'type':'htmlcontent', 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='habitat'?'yes':'hidden'); 
                }},
			'history':{'label':'History', 'type':'htmlcontent', 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='energetics'?'yes':'hidden'); 
//				return ((M.ciniki_materiamedica_main.plant.data.history!=null&&M.ciniki_materiamedica_main.plant.data.history!='')?'yes':'no');
				}},
			'notes':{'label':'Notes', 'type':'htmlcontent', 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='notes'?'yes':'hidden'); 
//                return ((M.ciniki_materiamedica_main.plant.data.notes!=null&&M.ciniki_materiamedica_main.plant.data.notes!='')?'yes':'no');
				}},
//			'reference_notes':{'label':'References', 'type':'htmlcontent', 'visible':function() {
//                return 'no';
//                return ((M.ciniki_materiamedica_main.plant.data.reference_notes!=null&&M.ciniki_materiamedica_main.plant.data.reference_notes!='')?'yes':'no');
//                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='notes'?'yes':'no'); 
//				}},
			'images':{'label':'Additional Images', 'type':'simplethumbs', 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='images'?'yes':'hidden'); 
                }},
			'_images':{'label':'', 'type':'simplegrid', 'num_cols':1, 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='images'?'yes':'hidden'); 
                },
				'addTxt':'Add Image',
				'addFn':'M.startApp(\'ciniki.materiamedica.plantimages\',null,\'M.ciniki_materiamedica_main.plantShow();\',\'mc\',{\'plant_id\':M.ciniki_materiamedica_main.plant.plant_id,\'title\':M.ciniki_materiamedica_main.plant.data.genus + \' \' + M.ciniki_materiamedica_main.plant.data.species,\'add\':\'yes\'})',
				},
            };
        // Add systems
        for(var i in this.systems) {
            this.plant.sections['system'+i] = {'label':this.systems[i].name, 'type':'simplelist', 'systemnum':i, 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='systems'&&M.ciniki_materiamedica_main.plant.data.systems[this.systemnum]!=null?'yes':'hidden');
                }};
            this.plant.sections['-system-'+i] = {'label':'', 'type':'simplegrid', 'systemnum':i, 'num_cols':1, 
                'addTxt':'Add Note',
                'addFn':'M.ciniki_materiamedica_main.noteEdit(\'M.ciniki_materiamedica_main.plantShow()\',\'ciniki.materiamedica.plant-\'+M.ciniki_materiamedica_main.plant.plant_id+\'-system-' + i + '\',0);', 'visible':function() {
                    return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='systems'&&M.ciniki_materiamedica_main.plant.data.systems[this.systemnum]!=null?'yes':'hidden');
                }};
        };
        this.plant.sections['systems'] = {'label':'', 'showadd':'yes', 'visible':function() {
                return (M.ciniki_materiamedica_main.plant.sections._tabs.selected=='systems'&&this.showadd=='yes'?'yes':'hidden');
                }, 'buttons': {
                    'add':{'label':'Add System', 'fn':'M.ciniki_materiamedica_main.systemEdit(\'M.ciniki_materiamedica_main.plantShow();\',M.ciniki_materiamedica_main.plant.plant_id,0);'},
                }};
		this.plant.sectionData = function(s) {
			if( this.sections[s].type != null && this.sections[s].type == 'htmlcontent' ) {
				return this.data[s].replace(/\n/g, '<br/>'); 
			}
            if( s.match(/-system-/) ) {
                if( this.data._notes != null && this.data._notes['ciniki.materiamedica.plant-'+this.plant_id+s] != null ) {
                    return this.data._notes['ciniki.materiamedica.plant-'+this.plant_id+s];
                }
                return null;
            }
            if( s.match(/system/) ) {
                if( this.data.systems[s.replace(/system/,'')] == null ) { return null; } 
                return this.data.systems[s.replace(/system/,'')].actions;
            }
			if( s == 'info' ) { return this.sections[s].list; }
			return this.data[s];
			};
		this.plant.listLabel = function(s, i, d) {
            if( s.match(/system/) ) {
                switch (i) {
                    case '10': return 'Primary';
                    case '20': return 'Secondary';
                }
            }
            return d.label;
		};
		this.plant.listValue = function(s, i, d) {
			if( s == 'info' && i == 'name' ) { return '<i>' + this.data.genus + ' ' + this.data.species + '</i>'; }
			if( s == 'info' && i == 'type_growth' ) { return this.data.plant_type_text + ' - ' + this.data.growth_pattern_text; }
			if( s == 'info' && i == 'parts_used' ) { return this.data.parts_used_text; }
            if( s.match(/system/) ) {
                return this.data.systems[s.replace(/system/,'')].actions[i].actions.replace(/::/g, ', '); 
                return this.data.systems[s.replace(/system/,'')].actions[i].actions.replace(/::/g, ', '); 
            }
			if( i.match(/tag-/) ) { 
				if( this.data[i] != null ) {
					return this.data[i].replace(/::/g, ', '); 
				}
				return '';
			}
			return this.data[i];
		};
        this.plant.listFn = function(s, i, d) {
            if( s.match(/system/) ) {
                return 'M.ciniki_materiamedica_main.systemEdit(\'M.ciniki_materiamedica_main.plantShow();\',M.ciniki_materiamedica_main.plant.plant_id,\'' + s.replace(/system/,'') + '\');';
            }
            return null;
        };
		this.plant.fieldValue = function(s, i, d) {
			return this.data[i];
		};
		this.plant.noData = function(s) {
			return '';
		};
		this.plant.cellValue = function(s, i, j, d) {
            if( s.match(/system/) ) {
                return d.content;
            }
		};
		this.plant.rowFn = function(s, i, d) {
            if( s.match(/system/) ) {
				return 'M.ciniki_materiamedica_main.noteEdit(\'M.ciniki_materiamedica_main.plantShow();\',\'' + d.note_key + '\',\'' + d.id + '\');';
            }
		};
		this.plant.prevButtonFn = function() {
			if( this.prev_plant_id > 0 ) {
				return 'M.ciniki_materiamedica_main.plantShow(null,\'' + this.prev_plant_id + '\');';
			}
			return null;
		};
		this.plant.nextButtonFn = function() {
			if( this.next_plant_id > 0 ) {
				return 'M.ciniki_materiamedica_main.plantShow(null,\'' + this.next_plant_id + '\');';
			}
			return null;
		};
		this.plant.thumbFn = function(s, i, d) {
			return 'M.startApp(\'ciniki.materiamedica.plantimages\',null,\'M.ciniki_materiamedica_main.plantShow();\',\'mc\',{\'plant_image_id\':\'' + d.id + '\',\'title\':M.ciniki_materiamedica_main.plant.data.genus + \' \' + M.ciniki_materiamedica_main.plant.data.species});';
		};
		this.plant.addDropImage = function(iid) {
			var rsp = M.api.getJSON('ciniki.materiamedica.plantImageAdd',
				{'business_id':M.curBusinessID, 'image_id':iid,
					'plant_id':M.ciniki_materiamedica_main.plant.plant_id});
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			return true;
		};
		this.plant.addDropImageRefresh = function() {
			if( M.ciniki_materiamedica_main.plant.plant_id > 0 ) {
				var rsp = M.api.getJSONCb('ciniki.materiamedica.plantGet', {'business_id':M.curBusinessID,
					'plant_id':M.ciniki_materiamedica_main.plant.plant_id, 'images':'yes'}, function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						}
						M.ciniki_materiamedica_main.plant.data.images = rsp.plant.images;
						M.ciniki_materiamedica_main.plant.refreshSection('images');
					});
			}
		};
        this.plant.switchTab = function(tab) {
            if( tab != null ) { this.sections._tabs.selected = tab; }
            for(var i in this.sections) {
                if( this.sections[i].switchrefresh == null ) {
                    this.refreshSection(i);
                }
            }
            this.show();
        };

		this.plant.addButton('edit', 'Edit', 'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.plantShow();\',M.ciniki_materiamedica_main.plant.plant_id);');
		this.plant.addButton('next', 'Next');
		this.plant.addClose('Back');
		this.plant.addLeftButton('prev', 'Prev');

		//
		// The panel to display the edit form
		//
		this.edit = new M.panel('Plant',
			'ciniki_materiamedica_main', 'edit',
			'mc', 'medium mediumaside', 'sectioned', 'ciniki.materiamedica.main.edit');
		this.edit.plant_id = 0;
		this.edit.data = null;
		this.edit.sections = {
//			'_image':{'label':'Image', 'aside':'yes', 'type':'imageform', 'fields':{
//				'image_id':{'label':'', 'type':'image_id', 'controls':'all', 'hidelabel':'yes', 'history':'no'},
//			}},
//			'_image_caption':{'label':'', 'aside':'yes', 'fields':{
//				'image_caption':{'label':'Caption', 'type':'text'},
//			}},
			'info':{'label':'Details', 'aside':'yes', 'type':'simpleform', 'fields':{
				'plant_number':{'label':'Number', 'type':'text', 'size':'small'},
				'family':{'label':'Family', 'type':'text', 'livesearch':'yes'},
				'genus':{'label':'Genus', 'type':'text', 'livesearch':'yes'},
				'species':{'label':'Species', 'type':'text', 'livesearch':'yes'},
				'common_name':{'label':'Common', 'type':'text', 'livesearch':'yes'},
				'plant_type':{'label':'Type', 'type':'toggle', 'toggles':this.plantTypes},
				'growth_pattern':{'label':'Growth', 'type':'toggle', 'toggles':this.growthPatterns},
				'parts_used':{'label':'Parts Used', 'type':'flags', 'join':'yes', 'flags':this.partsUsed},
			}},
			'_warnings':{'label':'Warnings', 'aside':'yes', 'type':'simpleform', 'fields':{
				'warnings':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
//			'_30':{'label':'Uses', 'aside':'yes', 'fields':{
//				'tag-30':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new use:'},
//				}},
//			'_40':{'label':'Actions', 'aside':'yes', 'fields':{
//				'tag-40':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new action:'},
//				}},
			'_contraindications':{'label':'Contraindications', 'aside':'yes', 'type':'simpleform', 'fields':{
				'contraindications':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_quick_id':{'label':'Quick ID', 'aside':'yes', 'type':'simpleform', 'fields':{
				'quick_id':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_habitat':{'label':'Habitat', 'type':'simpleform', 'fields':{
				'habitat':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
			'_cultivation':{'label':'Cultivation', 'type':'simpleform', 'fields':{
				'cultivation':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
			'_history':{'label':'History', 'type':'simpleform', 'fields':{
				'history':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
			'_notes':{'label':'Notes', 'type':'simpleform', 'fields':{
				'notes':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
//			'_reference_notes':{'label':'References', 'type':'simpleform', 'fields':{
//				'reference_notes':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
//			}},
			'_buttons':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_materiamedica_main.plantSave();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_materiamedica_main.plantDelete();'},
			}},
		};
		this.edit.fieldValue = function(s, i, d) { 
			return this.data[i]; 
		}
		this.edit.sectionData = function(s) {
			return this.data[s];
		};
		this.edit.liveSearchCb = function(s, i, value) {
			if( i == 'family' || i == 'genus' || i == 'species' || i == 'common_name' ) {
				var rsp = M.api.getJSONBgCb('ciniki.materiamedica.plantSearchField', {'business_id':M.curBusinessID, 'field':i, 'start_needle':value, 'limit':15},
					function(rsp) {
						M.ciniki_materiamedica_main.edit.liveSearchShow(s, i, M.gE(M.ciniki_materiamedica_main.edit.panelUID + '_' + i), rsp.results);
					});
			}
		};
		this.edit.liveSearchResultValue = function(s, f, i, j, d) {
			if( (f == 'family' || f == 'genus' || f == 'species' || f == 'common_name' ) && d.result != null ) { return d.result.name; }
			return '';
		};
		this.edit.liveSearchResultRowFn = function(s, f, i, j, d) { 
			if( (f == 'family' || f == 'genus' || f == 'species' || f == 'common_name' )
				&& d.result != null ) {
				return 'M.ciniki_materiamedica_main.edit.updateField(\'' + s + '\',\'' + f + '\',\'' + escape(d.result.name) + '\');';
			}
		};
		this.edit.updateField = function(s, fid, result) {
			M.gE(this.panelUID + '_' + fid).value = unescape(result);
			this.removeLiveSearch(s, fid);
		};
		this.edit.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.materiamedica.plantHistory', 'args':{'business_id':M.curBusinessID, 
				'plant_id':this.plant_id, 'field':i}};
		}
		this.edit.addDropImage = function(iid) {
			M.ciniki_materiamedica_main.edit.setFieldValue('image_id', iid, null, null);
			return true;
		};
		this.edit.deleteImage = function() {
			this.setFieldValue('image_id', 0, null, null);
			return true;
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_materiamedica_main.plantSave();');
		this.edit.addClose('Cancel');

        
        //
        // The system panel
        //
        this.system = new M.panel('Plant System',
            'ciniki_materiamedica_main', 'system',
            'mc', 'medium', 'sectioned', 'ciniki.materiamedica.main.system');
        this.system.plant_id = 0;
        this.system.system_num = 0;
        this.system.data = null;
        this.system.sections = {
            '_system':{'label':'', 'fields':{
                'system_num':{'label':'System', 'active':'no', 'type':'select', 'options':[]},
            }},
            '_primary_actions':{'label':'Primary Actions', 'fields':{
                'primary_actions':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new primary action:'},
            }},
            '_secondary_actions':{'label':'Secondary Actions', 'fields':{
                'secondary_actions':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new secondary action:'},
            }},
			'_buttons':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_materiamedica_main.systemSave();'},
            }},
        };
		this.system.fieldValue = function(s, i, d) { 
			return this.data[i]; 
		}
		this.system.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.materiamedica.plantSystemHistory', 'args':{'business_id':M.curBusinessID, 
				'system_num':this.system_num, 'field':i}};
		}
		this.system.addButton('save', 'Save', 'M.ciniki_materiamedica_main.systemSave();');
		this.system.addClose('Cancel');

		//
		// The panel to display the action form
		//
		this.action = new M.panel('Plant Action',
			'ciniki_materiamedica_main', 'action',
			'mc', 'medium', 'sectioned', 'ciniki.materiamedica.main.action');
		this.action.action_id = 0;
		this.action.plant_id = 0;
		this.action.data = null;
		this.action.sections = {
			'info':{'label':'Details', 'aside':'yes', 'type':'simpleform', 'fields':{
				'system':{'label':'System', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
				'action':{'label':'Action', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
			}},
			'_notes':{'label':'Notes', 'aside':'yes', 'type':'simpleform', 'fields':{
				'notes':{'label':'', 'type':'textarea', 'size':'large', 'hidelabel':'yes'},
			}},
			'_buttons':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_materiamedica_main.actionSave();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_materiamedica_main.actionDelete();'},
			}},
		};
		this.action.fieldValue = function(s, i, d) { 
			return this.data[i]; 
		}
		this.action.sectionData = function(s) {
			return this.data[s];
		};
		this.action.liveSearchCb = function(s, i, value) {
			if( i == 'system' || i == 'action' ) {
				var rsp = M.api.getJSONBgCb('ciniki.materiamedica.plantActionSearchField', {'business_id':M.curBusinessID, 'field':i, 'start_needle':value, 'limit':15},
					function(rsp) {
						M.ciniki_materiamedica_main.action.liveSearchShow(s, i, M.gE(M.ciniki_materiamedica_main.action.panelUID + '_' + i), rsp.results);
					});
			}
		};
		this.action.liveSearchResultValue = function(s, f, i, j, d) {
			if( (f == 'system' || f == 'action' ) && d.result != null ) { return d.result.name; }
			return '';
		};
		this.action.liveSearchResultRowFn = function(s, f, i, j, d) { 
			if( (f == 'system' || f == 'action' ) && d.result != null ) {
				return 'M.ciniki_materiamedica_main.action.updateField(\'' + s + '\',\'' + f + '\',\'' + escape(d.result.name) + '\');';
			}
		};
		this.action.updateField = function(s, fid, result) {
			M.gE(this.panelUID + '_' + fid).value = unescape(result);
			this.removeLiveSearch(s, fid);
		};
		this.action.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.materiamedica.plantActionHistory', 'args':{'business_id':M.curBusinessID, 
				'action_id':this.action_id, 'field':i}};
		}
		this.action.addButton('save', 'Save', 'M.ciniki_materiamedica_main.actionSave();');
		this.action.addClose('Cancel');
	}

	this.start = function(cb, appPrefix, aG) {
		args = {};
		if( aG != null ) { args = eval(aG); }

		//
		// Create container
		//
		var appContainer = M.createContainer(appPrefix, 'ciniki_materiamedica_main', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		}

		this.menu.tag_name = null;
		this.showMenu(cb);
	}

	this.showMenu = function(cb, tag_type, tag_name) {
		if( tag_type != null ) { this.menu.formtab = tag_type; }
		if( tag_name != null ) { this.menu.tag_name = unescape(tag_name); }
		var args = {'business_id':M.curBusinessID};
		if( this.menu.formtab != null ) { args.tag_type = this.menu.formtab; }
		if( this.menu.tag_name != null ) { 
			this.menu.forms[this.menu.formtab].plants.label = this.menu.tag_name;
			args.tag_name = this.menu.tag_name; 
		}
		if( this.menu.formtab == 'all' ) {
			this.menu.size = 'medium';
		} else {
			this.menu.size = 'medium narrowaside';
		}
		M.api.getJSONCb('ciniki.materiamedica.plantList', args, function(rsp) {
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			var p = M.ciniki_materiamedica_main.menu;
			p.data = rsp;
			p.refresh();
			p.show(cb);
		});
	};

	this.plantShow = function(cb, pid, list) {
		this.plant.reset();
		if( cb != null ) { this.plant.cb = cb; }
		if( pid != null ) { this.plant.plant_id = pid; }
		if( list != null ) { this.plant.list = list; }

		M.api.getJSONCb('ciniki.materiamedica.plantGet', 
			{'business_id':M.curBusinessID, 'plant_id':M.ciniki_materiamedica_main.plant.plant_id,
			'images':'yes', 'tags':'yes', 'systems':'yes', 'notes':'yes'}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				var p = M.ciniki_materiamedica_main.plant;
				p.data = rsp.plant;

				if( rsp.plant.tags != null && rsp.plant.tags != '' ) {
					p.data.tags = rsp.plant.tags.replace(/::/g, ', ');
				}
                p.sections.systems.showadd = 'no';
                if( p.data.systems != null ) {
                    for(var i in M.ciniki_materiamedica_main.systems) {
                        if( p.data.systems == null || p.data.systems[i] == null ) {
                            p.sections.systems.showadd = 'yes';
                            break;
                        }
                    }
                } else {
                    p.sections.systems.showadd = 'yes';
                }

                // Setup systems
				// Setup next/prev buttons
				p.prev_plant_id = 0;
				p.next_plant_id = 0;
				if( p.list != null ) {
					for(i in p.list) {
						if( p.next_plant_id == -1 ) {
							p.next_plant_id = p.list[i].plant.id;
							break;
						} else if( p.list[i].plant.id == p.plant_id ) {
							// Flag to pickup next plant
							p.next_plant_id = -1;
						} else {
							p.prev_plant_id = p.list[i].plant.id;
						}
					}
				}
				p.refresh();
				p.show(cb);
			});
	};

	this.refreshPlantImages = function() {
		if( M.ciniki_materiamedica_main.plant.plant_id > 0 ) {
			var rsp = M.api.getJSONCb('ciniki.materiamedica.plantGet', 
				{'business_id':M.curBusinessID, 'plant_id':M.ciniki_materiamedica_main.plant.plant_id, 
				'images':'yes'}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_materiamedica_main.plant.data.images = rsp.plant.images;
					M.ciniki_materiamedica_main.plant.refreshSection('images');
					M.ciniki_materiamedica_main.plant.show();
				});
		}
	};

	this.plantEdit = function(cb, rid, type, type_name) {
		if( rid != null ) {
			this.edit.plant_id = rid;
		}
		if( this.edit.plant_id == 0 ) {
			this.edit.reset();
			this.edit.sections._buttons.buttons.delete.visible = 'no';
		} else {
			this.edit.sections._buttons.buttons.delete.visible = 'yes';
		}
		M.api.getJSONCb('ciniki.materiamedica.plantGet', {'business_id':M.curBusinessID, 'plant_id':this.edit.plant_id, 'tags':'yes'}, function(rsp) {
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			var p = M.ciniki_materiamedica_main.edit;
			p.data = rsp.plant;
//			for(i in M.ciniki_materiamedica_main.tagTypes) {
//				p.sections['_'+i].fields['tag-'+i].tags = [];
//			}
//			if( rsp.tags != null ) {
//				for(i in rsp.tags) {
//					p.sections['_'+rsp.tags[i].tag_type].fields[i].tags = rsp.tags[i].tag_names.split(/::/);
//				}
//			}
			p.refresh();
			p.show(cb);
		});
	};

	this.plantSave = function() {
		// Check form values
		var nv = this.edit.formFieldValue(this.edit.sections.info.fields.genus, 'genus');
		if( nv != this.edit.fieldValue('info', 'genus') && nv == '' ) {
			alert('You must specifiy a genus');
			return false;
		}
		var nv = this.edit.formFieldValue(this.edit.sections.info.fields.species, 'species');
		if( nv != this.edit.fieldValue('info', 'species') && nv == '' ) {
			alert('You must specifiy a species');
			return false;
		}
		if( this.edit.plant_id > 0 ) {
			var c = this.edit.serializeForm('no');
			if( c != '' ) {
				M.api.postJSONFormData('ciniki.materiamedica.plantUpdate', 
					{'business_id':M.curBusinessID, 'plant_id':this.edit.plant_id}, c,
						function(rsp) {
							if( rsp.stat != 'ok' ) {
								M.api.err(rsp);
								return false;
							} else {
								M.ciniki_materiamedica_main.edit.close();
							}
						});
			} else {
				M.ciniki_materiamedica_main.edit.close();
			}
		} else {
			var c = this.edit.serializeForm('yes');
			M.api.postJSONFormData('ciniki.materiamedica.plantAdd', 
				{'business_id':M.curBusinessID}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} else {
							M.ciniki_materiamedica_main.edit.close();
						}
					});
		}
	};

	this.plantDelete = function() {
		if( confirm('Are you sure you want to delete \'' + this.plant.data.name + '\'?  All information about it will be removed and unrecoverable.') ) {
			var rsp = M.api.getJSONCb('ciniki.materiamedica.plantDelete', 
				{'business_id':M.curBusinessID, 'plant_id':this.edit.plant_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_materiamedica_main.edit.close();
				});
		}
	};

    //
    // Manage the system and actions for a plant
    //
    this.systemEdit = function(cb, pid, snum) {
        if( pid != null ) { this.system.plant_id = pid; }
        if( snum != null ) { this.system.system_num = snum; }
        M.api.getJSONCb('ciniki.materiamedica.plantSystemActionsGet', {'business_id':M.curBusinessID, 'plant_id':this.system.plant_id, 'system_num':this.system.system_num}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_materiamedica_main.system;
            p.data = rsp.system;
            p.sections._primary_actions.fields.primary_actions.tags = rsp.actions.slice(0);
            p.sections._secondary_actions.fields.secondary_actions.tags = rsp.actions.slice(0);
            var options = [];
            for(var i in M.ciniki_materiamedica_main.systems) {
                if( M.ciniki_materiamedica_main.plant.data.systems == null || M.ciniki_materiamedica_main.plant.data.systems[i] == null ) {
                    options[i] = M.ciniki_materiamedica_main.systems[i].name;
                }
            }
            if( options.length > 0 && p.system_num == 0 ) {
                p.sections._system.active = 'yes';
                p.sections._system.fields.system_num.active = 'yes';
                p.sections._system.fields.system_num.options = options;
            } else {
                p.sections._system.active = 'no';
                p.sections._system.fields.system_num.active = 'no';
            }
            p.refresh();
            p.show(cb);
        });
    };

    this.systemSave = function(cb) {
        if( this.system.system_num == 0 ) {
            var c = this.system.serializeForm('yes');
            M.api.postJSONCb('ciniki.materiamedica.plantSystemActionsUpdate', 
                {'business_id':M.curBusinessID, 'plant_id':this.system.plant_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } else {
                            M.ciniki_materiamedica_main.system.close();
                        }
                    });
        } else {
			var c = this.system.serializeForm('yes');
            M.api.postJSONCb('ciniki.materiamedica.plantSystemActionsUpdate', 
                {'business_id':M.curBusinessID, 'plant_id':this.system.plant_id, 'system_num':this.system.system_num}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } else {
                            M.ciniki_materiamedica_main.system.close();
                        }
                    });
        } 
    };

    this.noteEdit = function(cb, nkey, nid) {
        M.startApp('ciniki.materiamedica.notes',null,cb,'mc',{'note_key':nkey, 'note_id':nid});
    };

/*
	//
	// Manage the actions for a plant
	//
	this.actionEdit = function(cb, aid, pid) {
		if( aid != null ) { this.action.action_id = aid; }
		if( pid != null ) { this.action.plant_id = pid; }
		if( this.action.action_id == 0 ) {
			this.action.reset();
			this.action.sections._buttons.buttons.delete.visible = 'no';
		} else {
			this.action.sections._buttons.buttons.delete.visible = 'yes';
		}
		M.api.getJSONCb('ciniki.materiamedica.plantActionGet', {'business_id':M.curBusinessID, 'action_id':this.action.action_id}, function(rsp) {
			if( rsp.stat != 'ok' ) {
				M.api.err(rsp);
				return false;
			}
			var p = M.ciniki_materiamedica_main.action;
			p.data = rsp.action;
			p.refresh();
			p.show(cb);
		});
	};

	this.actionSave = function() {
		// Check form values
		var nv = this.action.formFieldValue(this.action.sections.info.fields.system, 'system');
		if( nv != this.action.fieldValue('info', 'system') && nv == '' ) {
			alert('You must specifiy a system');
			return false;
		}
		var nv = this.action.formFieldValue(this.action.sections.info.fields.action, 'action');
		if( nv != this.action.fieldValue('info', 'action') && nv == '' ) {
			alert('You must specifiy a action');
			return false;
		}
		if( this.action.action_id > 0 ) {
			var c = this.action.serializeForm('no');
			if( c != '' ) {
				var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantActionUpdate', 
					{'business_id':M.curBusinessID, 'action_id':this.action.action_id}, c,
						function(rsp) {
							if( rsp.stat != 'ok' ) {
								M.api.err(rsp);
								return false;
							} else {
								M.ciniki_materiamedica_main.action.close();
							}
						});
			} else {
				M.ciniki_materiamedica_main.action.close();
			}
		} else {
			var c = this.action.serializeForm('yes');
			var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantActionAdd', {'business_id':M.curBusinessID, 'plant_id':this.action.plant_id}, c, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				} else {
					M.ciniki_materiamedica_main.action.close();
				}
			});
		}
	};

	this.actionDelete = function() {
		if( confirm('Are you sure you want to delete this action?') ) {
			var rsp = M.api.getJSONCb('ciniki.materiamedica.plantActionDelete', 
				{'business_id':M.curBusinessID, 'action_id':this.action.action_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_materiamedica_main.action.close();
				});
		}
	};*/
}
