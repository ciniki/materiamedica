//
// The materiamedica app to manage an artists collection
//
function ciniki_materiamedica_main() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		};
	this.tagTypes = {
		'30':{'name':'Uses', 'arg':'uses', 'visible':'no'},
		'40':{'name':'Actions', 'arg':'actions', 'visible':'no'},
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
	this.init = function() {
		//
		// Setup the main panel to list the collection
		//
		this.menu = new M.panel('Materia Media',
			'ciniki_materiamedica_main', 'menu',
			'mc', 'medium narrowaside', 'sectioned', 'ciniki.materiamedica.main.menu');
		this.menu.data = {};
		this.menu.formtab = '30';
		this.menu.tag_name = '';
		this.menu.formtabs = {'label':'', 'tabs':{
			'30':{'label':'Uses', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,30,\'\');'},
			'40':{'label':'Actions', 'visible':'yes', 'fn':'M.ciniki_materiamedica_main.showMenu(null,40,\'\');'},
			}},
		this.menu.forms = {};
		this.menu.forms['30'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':1,
				'headerValues':['Genus-Species'],
				'cellClasses':['multiline'],
				'sortable':'yes', 'sortTypes':['text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.forms['40'] = {
			'tags':{'label':'', 'aside':'yes', 'type':'simplegrid', 'num_cols':1},
			'search':{'label':'', 'type':'livesearchgrid', 'livesearchcols':1, 'hint':'plant name', 
				'noData':'No plants found',
				},
			'plants':{'label':'Latest Plants', 'type':'simplegrid', 'num_cols':1,
				'headerValues':['Genus-Species'],
				'cellClasses':['multiline'],
				'sortable':'yes', 'sortTypes':['text'],
				'addTxt':'Add Plant',
				'addFn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.showMenu();\', 0);',
				},
			};
		this.menu.sections = this.menu.forms['30'];	
		this.menu.cellValue = function(s, i, j, d) {
			if( s == 'tags' ) {
				return d.tag.tag_name + ' <span class="count">' + d.tag.num_plants + '</span>';
			} 
			else if( s == 'plants' || s == 'search' ) {
				return '<span class="maintext">' + d.plant.genus + '-' + d.plant.species + '</span>'
					+ '<span class="subtext">' + d.plant.family + '</span>';
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
		this.plant.sections = {
			'_image':{'label':'Image', 'aside':'yes', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'history':'no'},
			}},
			'info':{'label':'Public Information', 'aside':'yes', 'list':{
				'name':{'label':'Name', 'type':'text'},
				'common_name':{'label':'Common', 'type':'text'},
				'type_growth':{'label':'Type', 'type':'text'},
				'tag-30':{'label':'Uses', 'visible':'yes'},
				'tag-40':{'label':'Actions', 'visible':'yes'},
			}},
			'warnings':{'label':'Warnings', 'aside':'yes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.warnings!=null&&M.ciniki_materiamedica_main.plant.data.warnings!='')?'yes':'no');
				}},
			'contraindications':{'label':'Contraindications', 'aside':'yes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.contraindications!=null&&M.ciniki_materiamedica_main.plant.data.contraindications!='')?'yes':'no');
				}},
			'quick_id':{'label':'Quick ID', 'aside':'yes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.quick_id!=null&&M.ciniki_materiamedica_main.plant.data.quick_id!='')?'yes':'no');
				}},
			// Add tabs for additional information sections
			'habitat':{'label':'Habitat', 'type':'htmlcontent'},
			'cultivation':{'label':'Cultivation', 'type':'htmlcontent'},
			'history':{'label':'History', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.history!=null&&M.ciniki_materiamedica_main.plant.data.history!='')?'yes':'no');
				}},
			'notes':{'label':'Notes', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.notes!=null&&M.ciniki_materiamedica_main.plant.data.notes!='')?'yes':'no');
				}},
			'reference_notes':{'label':'References', 'type':'htmlcontent',
				'visible':function() {
					return ((M.ciniki_materiamedica_main.plant.data.reference_notes!=null&&M.ciniki_materiamedica_main.plant.data.reference_notes!='')?'yes':'no');
				}},
			'images':{'label':'Additional Images', 'type':'simplethumbs'},
			'_images':{'label':'', 'type':'simplegrid', 'num_cols':1,
				'addTxt':'Add Image',
				'addFn':'M.startApp(\'ciniki.materiamedica.plantimages\',null,\'M.ciniki_materiamedica_main.plantShow();\',\'mc\',{\'plant_id\':M.ciniki_materiamedica_main.plant.plant_id,\'add\':\'yes\'})',
				},
			'_buttons':{'label':'', 'buttons':{
				'edit':{'label':'Edit', 'fn':'M.ciniki_materiamedica_main.plantEdit(\'M.ciniki_materiamedica_main.plantShow();\',M.ciniki_materiamedica_main.plant.plant_id);'},
			}},
			};
		this.plant.sectionData = function(s) {
			if( this.sections[s].type != null && this.sections[s].type == 'htmlcontent' ) {
				return this.data[s].replace(/\n/g, '<br/>'); 
			}
			if( s == 'info' ) { return this.sections[s].list; }
			return this.data[s];
			};
		this.plant.listLabel = function(s, i, d) {
			switch (s) {
				case 'info': return d.label;
			}
		};
		this.plant.listValue = function(s, i, d) {
			if( s == 'info' && i == 'name' ) { return this.data.family + ' ' + this.data.genus + ' ' + this.data.species; }
			if( s == 'info' && i == 'type_growth' ) { return this.data.plant_type_text + ' - ' + this.data.growth_pattern_text; }
			if( i.match(/tag-/) ) { 
				if( this.data[i] != null ) {
					return this.data[i].replace(/::/g, ', '); 
				}
				return '';
			}
			return this.data[i];
		};
		this.plant.fieldValue = function(s, i, d) {
			return this.data[i];
		};
		this.plant.noData = function(s) {
			return '';
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
			return 'M.startApp(\'ciniki.materiamedica.plantimages\',null,\'M.ciniki_materiamedica_main.plantShow();\',\'mc\',{\'plant_image_id\':\'' + d.image.id + '\'});';
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
			'_image':{'label':'Image', 'aside':'yes', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'controls':'all', 'hidelabel':'yes', 'history':'no'},
			}},
			'info':{'label':'Public Information', 'aside':'yes', 'type':'simpleform', 'fields':{
				'family':{'label':'Family', 'type':'text'},
				'genus':{'label':'Genus', 'type':'text'},
				'species':{'label':'Species', 'type':'text'},
				'common_name':{'label':'Common', 'type':'text'},
				'plant_type':{'label':'Type', 'type':'toggle', 'toggles':this.plantTypes},
				'growth_pattern':{'label':'Growth', 'type':'toggle', 'toggles':this.growthPatterns},
				'parts_used':{'label':'Parts Used', 'type':'flags', 'join':'yes', 'flags':this.partsUsed},
			}},
			'_warnings':{'label':'Warnings', 'aside':'yes', 'type':'simpleform', 'fields':{
				'warnings':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_30':{'label':'Uses', 'aside':'yes', 'fields':{
				'tag-30':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new use:'},
				}},
			'_40':{'label':'Actions', 'aside':'yes', 'fields':{
				'tag-40':{'label':'', 'hidelabel':'yes', 'type':'tags', 'tags':[], 'hint':'Enter a new action:'},
				}},
			'_contraindications':{'label':'Contraindications', 'aside':'yes', 'type':'simpleform', 'fields':{
				'contraindications':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_quick_id':{'label':'Quick ID', 'type':'simpleform', 'fields':{
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
			'_reference_notes':{'label':'References', 'type':'simpleform', 'fields':{
				'reference_notes':{'label':'', 'type':'textarea', 'hidelabel':'yes'},
			}},
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
			if( i == 'family' || i == 'genus' || i == 'species' ) {
				var rsp = M.api.getJSONBgCb('ciniki.materiamedica.searchField', {'business_id':M.curBusinessID, 'field':i, 'start_needle':value, 'limit':15},
					function(rsp) {
						M.ciniki_materiamedica_main.edit.liveSearchShow(s, i, M.gE(M.ciniki_materiamedica_main.edit.panelUID + '_' + i), rsp.results);
					});
			}
		};
		this.edit.liveSearchResultValue = function(s, f, i, j, d) {
			if( (f == 'family' || f == 'genus' || f == 'species' ) && d.result != null ) { return d.result.name; }
			return '';
		};
		this.edit.liveSearchResultRowFn = function(s, f, i, j, d) { 
			if( (f == 'family' || f == 'genus' || f == 'species' )
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

		var rsp = M.api.getJSONCb('ciniki.materiamedica.plantGet', 
			{'business_id':M.curBusinessID, 'plant_id':M.ciniki_materiamedica_main.plant.plant_id,
			'images':'yes', 'tags':'yes'}, function(rsp) {
				if( rsp.stat != 'ok' ) {
					M.api.err(rsp);
					return false;
				}
				var p = M.ciniki_materiamedica_main.plant;
				p.data = rsp.plant;

				if( rsp.plant.tags != null && rsp.plant.tags != '' ) {
					p.data.tags = rsp.plant.tags.replace(/::/g, ', ');
				}
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
				p.show();
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
			for(i in M.ciniki_materiamedica_main.tagTypes) {
				p.sections['_'+i].fields['tag-'+i].tags = [];
			}
			if( rsp.tags != null ) {
				for(i in rsp.tags) {
					p.sections['_'+rsp.tags[i].tag_type].fields[i].tags = rsp.tags[i].tag_names.split(/::/);
				}
			}
			p.refresh();
			p.show(cb);
		});
	};

	this.plantSave = function() {
		// Check form values
		var nv = this.edit.formFieldValue(this.edit.sections.info.fields.family, 'family');
		if( nv != this.edit.fieldValue('info', 'family') && nv == '' ) {
			alert('You must specifiy a family');
			return false;
		}
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
				var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantUpdate', 
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
			var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantAdd', 
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
}
