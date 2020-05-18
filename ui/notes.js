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
                'plant_id':{'label':'Plant', 'type':'select', 'options':{}},
                'ailment_id':{'label':'Plant', 'type':'select', 'options':{}},
                'preparation_id':{'label':'Plant', 'type':'select', 'options':{}},
                'flags':{'label':'Options', 'type':'flags', 'join':'yes', 'flags':this.flags},
                'note_date':{'label':'Date', 'type':'date'},
            }},
            '_content':{'label':'', 'type':'simpleform', 'fields':{
                'content':{'label':'', 'type':'textarea', 'size':'large', 'hidelabel':'yes'},
            }},
            'citations':{'label':'References', 'type':'simplegrid', 'num_cols':1,
                'cellClasses':['multiline'],
                'addTxt':'Add Reference',
                'addFn':'M.ciniki_materiamedica_notes.citationEdit(0);',
                },
            '_save':{'label':'', 'buttons':{
                'save':{'label':'Save', 'fn':'M.ciniki_materiamedica_notes.noteSave();'},
                'delete':{'label':'Delete', 'visible':function() { return (M.ciniki_materiamedica_notes.edit.note_id>0?'yes':'no');}, 'fn':'M.ciniki_materiamedica_notes.noteDelete();'},
            }},
        };
        this.edit.sectionData = function(s) { return this.data[s]; }
        this.edit.fieldValue = function(s, i, d) { 
            if( this.data[i] != null ) {
                return this.data[i]; 
            } 
            return ''; 
        };
        this.edit.cellValue = function(s, i, j, d) {
            return '<span class="maintext">' + d.citation_text + '</span><span class="subtext">' + d.notes + '</span>';
        };
        this.edit.rowFn = function(s, i, d) {
            return 'M.ciniki_materiamedica_notes.citationEdit(' + d.id + ');';
        };
        this.edit.fieldHistoryArgs = function(s, i) {
            return {'method':'ciniki.materiamedica.noteHistory', 'args':{'tnid':M.curTenantID, 
                'note_id':this.note_id, 'field':i}};
        };
        this.edit.refreshCitations = function() {
            M.api.getJSONCb('ciniki.materiamedica.noteGet', {'tnid':M.curTenantID, 'note_id':this.note_id, 'citations':'yes'}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                var p = M.ciniki_materiamedica_notes.edit;
                p.data.citations = rsp.note.citations;
                p.refreshSection('citations');
                p.show();
            });
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
            M.alert('App Error');
            return false;
        }

        this.edit.sections.citations.visible = (M.curTenant.modules['ciniki.citations'] != null?'yes':'no');

        this.noteEdit(cb, args.note_key, (args.note_id==null?0:args.note_id));
    }

    this.noteEdit = function(cb, nkey, nid) {
        if( nkey != null ) { this.edit.note_key = nkey; }
        if( nid != null ) { this.edit.note_id = nid; }
        M.api.getJSONCb('ciniki.materiamedica.noteGet', {'tnid':M.curTenantID, 'note_id':this.edit.note_id}, function(rsp) {
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

    this.citationEdit = function(cid) {
        if( this.edit.note_id == 0 ) {
            var c = this.edit.serializeForm('yes');
            M.api.postJSONCb('ciniki.materiamedica.noteAdd', {'tnid':M.curTenantID, 'note_key':this.edit.note_key}, c,
                function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    } 
                    M.ciniki_materiamedica_notes.edit.note_id = rsp.id;
                    M.startApp('ciniki.citations.edit',null,'M.ciniki_materiamedica_notes.edit.refreshCitations();','mc',
                        {'object':'ciniki.materiamedica.note','object_id':rsp.id,'citation_id':cid});
                });
        } else {
            M.startApp('ciniki.citations.edit',null,'M.ciniki_materiamedica_notes.edit.refreshCitations();','mc',
                {'object':'ciniki.materiamedica.note','object_id':this.edit.note_id,'citation_id':cid});
        }
    };

    this.noteSave = function() {
        if( this.edit.note_id > 0 ) {
            var c = this.edit.serializeForm('no');
            if( c != null ) {
                var rsp = M.api.postJSONCb('ciniki.materiamedica.noteUpdate', {'tnid':M.curTenantID, 'note_id':this.edit.note_id}, c,
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
            M.api.postJSONCb('ciniki.materiamedica.noteAdd', {'tnid':M.curTenantID, 'note_key':this.edit.note_key}, c,
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
        M.confirm('Are you sure you want to delete this note?',null,function() {
            M.api.getJSONCb('ciniki.materiamedica.noteDelete', {'tnid':M.curTenantID, 
                'note_id':M.ciniki_materiamedica_notes.edit.note_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    M.ciniki_materiamedica_notes.edit.close();
                });
        });
    };
}
