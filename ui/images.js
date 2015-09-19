//
// The app to add/edit materiamedica plant images
//
function ciniki_materiamedica_plantimages() {
	this.webFlags = {
		'1':{'name':'Hidden'},
		};
	this.init = function() {
		//
		// The panel to display the edit form
		//
		this.edit = new M.panel('Edit Image',
			'ciniki_materiamedica_plantimages', 'edit',
			'mc', 'medium', 'sectioned', 'ciniki.materiamedica.plantimages.edit');
		this.edit.default_data = {};
		this.edit.data = {};
		this.edit.plant_id = 0;
		this.edit.sections = {
			'_image':{'label':'Photo', 'fields':{
				'image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
			}},
			'info':{'label':'Information', 'type':'simpleform', 'fields':{
				'name':{'label':'Title', 'type':'text'},
				'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
			}},
			'_description':{'label':'Description', 'type':'simpleform', 'fields':{
				'description':{'label':'', 'type':'textarea', 'size':'small', 'hidelabel':'yes'},
			}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_materiamedica_plantimages.imageSave();'},
				'delete':{'label':'Delete', 'fn':'M.ciniki_materiamedica_plantimages.imageDelete();'},
			}},
		};
		this.edit.fieldValue = function(s, i, d) { 
			if( this.data[i] != null ) {
				return this.data[i]; 
			} 
			return ''; 
		};
		this.edit.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.materiamedica.plantImageHistory', 'args':{'business_id':M.curBusinessID, 
				'plant_image_id':this.plant_image_id, 'field':i}};
		};
		this.edit.addDropImage = function(iid) {
			M.ciniki_materiamedica_plantimages.edit.setFieldValue('image_id', iid, null, null);
			return true;
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_materiamedica_plantimages.imageSave();');
		this.edit.addClose('Cancel');
	};

	this.start = function(cb, appPrefix, aG) {
		args = {};
		if( aG != null ) {
			args = eval(aG);
		}

		//
		// Create container
		//
		var appContainer = M.createContainer(appPrefix, 'ciniki_materiamedica_plantimages', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		}

		if( args.add != null && args.add == 'yes' ) {
			this.imageEdit(cb, 0, args.plant_id);
		} else if( args.plant_image_id != null && args.plant_image_id > 0 ) {
			this.imageEdit(cb, args.plant_image_id);
		}
		return false;
	}

	this.imageEdit = function(cb, iid, eid) {
		if( iid != null ) {
			this.edit.plant_image_id = iid;
		}
		if( eid != null ) {
			this.edit.plant_id = eid;
		}
		if( this.edit.plant_image_id > 0 ) {
			var rsp = M.api.getJSONCb('ciniki.materiamedica.plantImageGet', 
				{'business_id':M.curBusinessID, 'plant_image_id':this.edit.plant_image_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_materiamedica_plantimages.edit.data = rsp.image;
					M.ciniki_materiamedica_plantimages.edit.refresh();
					M.ciniki_materiamedica_plantimages.edit.show(cb);
				});
		} else {
			this.edit.reset();
			this.edit.data = {};
			this.edit.refresh();
			this.edit.show(cb);
		}
	};

	this.imageSave = function() {
		if( this.edit.plant_image_id > 0 ) {
			var c = this.edit.serializeFormData('no');
			if( c != '' ) {
				var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantImageUpdate', 
					{'business_id':M.curBusinessID, 
					'plant_image_id':this.edit.plant_image_id}, c,
						function(rsp) {
							if( rsp.stat != 'ok' ) {
								M.api.err(rsp);
								return false;
							} else {
								M.ciniki_materiamedica_plantimages.edit.close();
							}
						});
			} else {
				this.edit.close();
			}
		} else {
			var c = this.edit.serializeFormData('yes');
			var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantImageAdd', 
				{'business_id':M.curBusinessID, 'plant_id':this.edit.plant_id}, c,
					function(rsp) {
						if( rsp.stat != 'ok' ) {
							M.api.err(rsp);
							return false;
						} else {
							M.ciniki_materiamedica_plantimages.edit.close();
						}
					});
		}
	};

	this.imageDelete = function() {
		if( confirm('Are you sure you want to delete this image?') ) {
			var rsp = M.api.getJSONCb('ciniki.materiamedica.plantImageDelete', {'business_id':M.curBusinessID, 
				'plant_image_id':this.edit.plant_image_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_materiamedica_plantimages.edit.close();
				});
		}
	};
}
