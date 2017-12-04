//
// The app to add/edit materiamedica plant images
//
function ciniki_materiamedica_plantimages() {
    this.webFlags = {
        '1':{'name':'Hidden'},
        };
    this.Flags1 = {
        '1':{'name':'Photo'},
        '2':{'name':'Illustration'},
        };
    this.Flags2 = {
        '3':{'name':'Mature'},
        '4':{'name':'Immature'},
        };
    this.Flags3 = {
        '5':{'name':'Spring'},
        '6':{'name':'Summer'},
        '7':{'name':'Fall'},
        '8':{'name':'Winter'},
        };
    this.Parts = {
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
        // The panel to display the edit form
        //
        this.edit = new M.panel('Edit Image',
            'ciniki_materiamedica_plantimages', 'edit',
            'mc', 'medium mediumaside', 'sectioned', 'ciniki.materiamedica.plantimages.edit');
        this.edit.default_data = {};
        this.edit.data = {};
        this.edit.plant_id = 0;
        this.edit.sections = {
            '_image':{'label':'Photo', 'aside':'yes', 'type':'imageform', 'fields':{
                'image_id':{'label':'', 'type':'image_id', 'size':'large', 'hidelabel':'yes', 'controls':'all', 'history':'no'},
            }},
            'info':{'label':'Information', 'type':'simpleform', 'fields':{
//              'name':{'label':'Title', 'type':'text'},
                'primary_image':{'label':'Primary', 'type':'toggle', 'default':'no', 'toggles':{'no':'No', 'yes':'Yes'}},
                'flags1':{'label':'Type', 'type':'flagspiece', 'field':'flags', 'mask':0x03, 'join':'yes', 'flags':this.Flags1},
                'flags2':{'label':'Age', 'type':'flagspiece', 'field':'flags', 'mask':0x0c, 'join':'yes', 'flags':this.Flags2},
                'flags3':{'label':'Season', 'type':'flagspiece', 'field':'flags', 'mask':0xf0, 'join':'yes', 'flags':this.Flags3},
                'parts':{'label':'Parts', 'type':'flags', 'join':'yes', 'flags':this.Parts},
//              'webflags':{'label':'Website', 'type':'flags', 'join':'yes', 'flags':this.webFlags},
                'location':{'label':'Location', 'type':'text', 'livesearch':'yes', 'livesearchempty':'yes'},
                'date_taken':{'label':'Date', 'type':'date'},
            }},
            '_description':{'label':'Description', 'type':'simpleform', 'fields':{
                'description':{'label':'', 'type':'textarea', 'size':'medium', 'hidelabel':'yes'},
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
            return {'method':'ciniki.materiamedica.plantImageHistory', 'args':{'tnid':M.curTenantID, 
                'plant_image_id':this.plant_image_id, 'field':i}};
        };
        this.edit.liveSearchCb = function(s, i, value) {
            if( i == 'location' ) {
                var rsp = M.api.getJSONBgCb('ciniki.materiamedica.plantImageSearchField', {'tnid':M.curTenantID, 'field':i, 'start_needle':value, 'limit':15},
                    function(rsp) {
                        M.ciniki_materiamedica_plantimages.edit.liveSearchShow(s, i, M.gE(M.ciniki_materiamedica_plantimages.edit.panelUID + '_' + i), rsp.results);
                    });
            }
        };
        this.edit.liveSearchResultValue = function(s, f, i, j, d) {
            if( (f == 'location' ) && d.result != null ) { return d.result.name; }
            return '';
        };
        this.edit.liveSearchResultRowFn = function(s, f, i, j, d) { 
            if( (f == 'location' ) && d.result != null ) {
                return 'M.ciniki_materiamedica_plantimages.edit.updateField(\'' + s + '\',\'' + f + '\',\'' + escape(d.result.name) + '\');';
            }
        };
        this.edit.updateField = function(s, fid, result) {
            M.gE(this.panelUID + '_' + fid).value = unescape(result);
            this.removeLiveSearch(s, fid);
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

        if( args.title != null ) {
            this.edit.title = '<i>' + args.title + '</i>';
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
                {'tnid':M.curTenantID, 'plant_image_id':this.edit.plant_image_id}, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    var p = M.ciniki_materiamedica_plantimages.edit;
                    p.data = rsp.image;
//                  p.data.flags1 = (rsp.image.flags&0x03);
//                  p.data.flags2 = (rsp.image.flags&0x0c);
//                  p.data.flags3 = (rsp.image.flags&0xf0);
                    p.refresh();
                    p.show(cb);
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
//          var flags = this.edit.formValue('flags1') | this.edit.formValue('flags2') | this.edit.formValue('flags3');
//          if( flags != this.edit.data.flags ) {
//              if( c == null ) { c = new FormData; }
//              c.append('flags', flags);
//          }
            if( c != null ) {
                var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantImageUpdate', 
                    {'tnid':M.curTenantID, 
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
            var flags = this.edit.formValue('flags1') | this.edit.formValue('flags2') | this.edit.formValue('flags3');
            c.append('flags', flags);
            var rsp = M.api.postJSONFormData('ciniki.materiamedica.plantImageAdd', 
                {'tnid':M.curTenantID, 'plant_id':this.edit.plant_id}, c,
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
            var rsp = M.api.getJSONCb('ciniki.materiamedica.plantImageDelete', {'tnid':M.curTenantID, 
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
