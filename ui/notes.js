//
// The app to add/edit materiamedica plant images
//
function ciniki_materiamedica_notes() {
	this.flags = {
		'1':{'name':'Favourite'},
		};
	this.init = function() {
		//
		// The panel to display the edit form
		//
		this.edit = new M.panel('Note',
			'ciniki_materiamedica_notes', 'edit',
			'mc', 'medium', 'sectioned', 'ciniki.materiamedica.notes.edit');
		this.edit.default_data = {};
		this.edit.data = {};
		this.edit.note_id = 0;
		this.edit.note_key = '';
		this.edit.sections = {
			'info':{'label':'Note', 'type':'simpleform', 'fields':{
				'flags':{'label':'Options', 'type':'flags', 'join':'yes', 'flags':this.flags},
				'note_date':{'label':'Date', 'type':'date'},
			}},
			'_content':{'label':'', 'type':'simpleform', 'fields':{
				'content':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
			}},
			'_save':{'label':'', 'buttons':{
				'save':{'label':'Save', 'fn':'M.ciniki_materiamedica_notes.noteSave();'},
				'delete':{'label':'Delete', 'visible':'no', 'fn':'M.ciniki_materiamedica_notes.noteDelete();'},
			}},
		};
		this.edit.fieldValue = function(s, i, d) { 
			if( this.data[i] != null ) {
				return this.data[i]; 
			} 
			return ''; 
		};
		this.edit.fieldHistoryArgs = function(s, i) {
			return {'method':'ciniki.materiamedica.noteHistory', 'args':{'business_id':M.curBusinessID, 
				'note_id':this.note_id, 'field':i}};
		};
		this.edit.addButton('save', 'Save', 'M.ciniki_materiamedica_notes.noteSave();');
		this.edit.addClose('Cancel');
	};

	this.start = function(cb, appPrefix, aG) {
		args = {};
		if( aG != null ) { args = eval(aG); }

		//
		// Create container
		//
		var appContainer = M.createContainer(appPrefix, 'ciniki_materiamedica_notes', 'yes');
		if( appContainer == null ) {
			alert('App Error');
			return false;
		}

        this.noteEdit(cb, args.note_key, (args.note_id==null?0:args.note_id));
	}

	this.noteEdit = function(cb, nkey, nid) {
		if( nkey != null ) { this.edit.note_key = nkey; }
		if( nid != null ) { this.edit.note_id = nid; }
        M.api.getJSONCb('ciniki.materiamedica.noteGet', {'business_id':M.curBusinessID, 'note_id':this.edit.note_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_materiamedica_notes.edit;
            p.data = rsp.note;
            p.refresh();
            p.show(cb);
        });
	};

	this.noteSave = function() {
		if( this.edit.note_id > 0 ) {
			var c = this.edit.serializeForm('no');
			if( c != null ) {
				var rsp = M.api.postJSONCb('ciniki.materiamedica.noteUpdate', {'business_id':M.curBusinessID, 'note_id':this.edit.note_id}, c,
                    function(rsp) {
                        if( rsp.stat != 'ok' ) {
                            M.api.err(rsp);
                            return false;
                        } else {
                            M.ciniki_materiamedica_notes.edit.close();
                        }
                    });
			} else {
				this.edit.close();
			}
		} else {
			var c = this.edit.serializeForm('yes');
			M.api.postJSONCb('ciniki.materiamedica.noteAdd', {'business_id':M.curBusinessID, 'note_key':this.edit.note_key}, c,
                function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } else {
                        M.ciniki_materiamedica_notes.edit.close();
                    }
                });
		}
	};

	this.noteDelete = function() {
		if( confirm('Are you sure you want to delete this note?') ) {
			M.api.getJSONCb('ciniki.materiamedica.noteDelete', {'business_id':M.curBusinessID, 
				'note_id':this.edit.note_id}, function(rsp) {
					if( rsp.stat != 'ok' ) {
						M.api.err(rsp);
						return false;
					}
					M.ciniki_materiamedica_notes.edit.close();
				});
		}
	};
}
