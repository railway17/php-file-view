//tree global
var paths = []
var folders = []
var documents = []
var key = 0;
var rootNode = [{title: 'Documents', key: 0, expanded: true, folder: true, path: "", id: 0, parentId: 0, children: []}]
var selectedPath = ''
var selectedId = 0

//datatable global
var table = null;

$(function(){  // on page load
    initFileUpload()
    
    
    /**
     * initialize and handle data table events
     */
    
    $("#folder_navigation li").on('click', function(e) {
        selectedId = $("#cur_folder_id").val();
        if($(this).hasClass('addFolder')) {
            location.href = `${BASE_URL}folders?parentId=${selectedId}&viewType=AddFolder`
        } else if($(this).hasClass('editFolder')) {
            location.href = `${BASE_URL}folders?folderId=${selectedId}&viewType=Edit`
        } else if($(this).hasClass('addDoc')) {
            location.href = `${BASE_URL}folders?folderId=${selectedId}&viewType=AddDoc`
        } else if($(this).hasClass('editFolderAccess')) {
            location.href = `${BASE_URL}folders?folderId=${selectedId}&viewType=EditFolderAccess`
        }
    })

    

    function initiDataTable(data) {
        table = $('#content_table').DataTable( {
            dom: 'rtip',
                data: data,
                options: [],
                files: [],
                searchPanes: {
                    options: []
                },
            columns: [
                { data: 'isFolder', render: function ( data, type, row, meta ) {
                    if(data) {
                        return '<i class="fa fa-folder"></i>'
                    } else {
                        return getFileThumb(row.DT_RowId)
                    }
                }, width: 30},
                { data: 'name' },
                { data: 'status' },
                { data: 'DT_RowId' , render : function ( data, type, row, meta ) {
                    return '<div class="action-button-wrapper">' +
                            '<i class="fa fa-times btn-close"></i>' +
                            '<i class="fa fa-pencil btn-edit"></i>' +
                        '</div>'
                  }, width: 80},
            
            ],
            columnDefs: [
                { orderable: false, targets: [ 1,2,3 ] }
            ],
            select: true,
        } );

        $('#content_table tbody').on('click', 'td:not(:last-child)', function () {
            var data = table.row( $(this).parents('tr') ).data();
            selectedId = data.DT_RowId
            if(data.isFolder) {
                $("#cur_folder_id").val(selectedId);
            } else {
                $("#cur_doc_id").val(selectedId);
            }
            reloadOrRedirect(selectedPath, data.path)
            setFolderDetail(selectedId)
        } );

        $('#content_table tbody').on( 'click', '.action-button-wrapper .btn-close', function (e) {
            var data = table.row( $(this).parents('tr') ).data();
            var children = initTableDataForSub(data.path)
            console.log(children)
            if(children.length > 0) {
                $.confirm({
                    title: 'Warning!',
                    content: 'Folder is not empty. Are you sure want to delete all of the contents?',
                    buttons: {
                        confirm: function () {
                            deleteFolder(data)
                        },
                        cancel: function () {
                            
                        }
                    }
                })
            } else {
                deleteFolder(data)
            }
        } );

        $('#content_table tbody').on( 'click', '.action-button-wrapper .btn-edit', function (e) {
            var data = table.row( $(this).parents('tr') ).data();
            selectedId = data.DT_RowId
            location.href = `${BASE_URL}folders?folderId=${selectedId}&viewType=Edit`
        } );
    }

    function getFileThumb(documentId) {
        var html = '<i class="fa fa-file-o"></i>'
        var doc = documents.find(el=>el['documentID'] == documentId)
        var extSplit = doc['docFileName'].split('.')
        if(extSplit.length == 2) {
            var path = isPreviewable(doc, extSplit[1])
            if(path) {
                if(extSplit[1] == 'pdf') {
                    html = '<i class="fa fa-file-pdf-o"></i>'
                } else {
                    html = `<img width='20px' src='${BASE_URL}uploads/Documents${path}'/>`
                }
            }
        }
        console.log(html)
        return html;
    }
    
    function initTableDataForSub(path) {
        let subpaths = findSubPaths(path)
        let result = []
        var f = null
        console.log(documents)
        subpaths.forEach(el => {
            var nodeName = getFilename(el);
            var isFolder = false;
            var rowId = -1
            console.log(el)
            if (validateFolder(el)) {
                isFolder = true
                f = folders.find(fo=>fo['folderPath'] == el)
                if(!f) return
                rowId = f['folderId']
            } else {
                f = documents.find(d=>d['folderPath'] + d['docFileName'] == el)
                if(!f) return
                rowId = f['documentID']

            }
            if(rowId != -1) {
                result.push({
                    DT_RowId: rowId,
                    name: nodeName,
                    status: "",
                    isFolder: isFolder,
                    path: el
                })
            }
        });
        return result
    }

    function reloadDataTable (data) {
        if(table) {
            table.clear();
            table.rows.add(data);
            table.draw();
        }
    }

    function deleteFolder(data) {
        var url = `${BASE_URL}pages/documents/api.php?endpoint=deleteFolder`
        var post = {
            folderId: data['DT_RowId']
        }
        ajaxPOSTRequest(url, post, function(res) {
            console.log(res+'test')
            if(res > 0) {
                showToastSuccess('Folder: ' + data.name + ' deleted successfully')    
                setTimeout(function(){
                    location.href = `${BASE_URL}folders`
                }, 2000)
            }
        })
    }

    /**
     * initialize and handle tree events
     */
    
    ajaxGETRequest(`${BASE_URL}pages/documents/api.php?endpoint=getDocumentsInfo`, function(data) {
        folders = data['folders']
        documents = data['docs']
        paths = getPaths(data);
        key = 0;
        var tree = buildTree();
        
        if (tree.length == 1 && (typeof tree[0] === 'object')) {
            tree = tree[0];
        }
        var rootFolder = folders.find(el=>el['folderPath'] == '')
        if(rootFolder) {
            rootNode[0].id = rootFolder['folderId']
            selectedId = rootNode[0].id  
        }
        rootNode[0].children = tree;
        initTree();

        //init data table
        const subArr = initTableDataForSub("");
        initiDataTable(subArr);
        selectedId = $("#cur_folder_id").val();
        
        setFolderDetail(selectedId)
    })

    function setFolderDetail(selectedId) {
        var folder = folders.find(el=>el['folderId']==selectedId)
        if(!folder) return
        $("#folder_id").html(folder['folderId'])
        $("#folder_owner").html(folder['OwnerName'])
        $("#folder_created").html(folder['createdAt'])
        $("#folder_dam").html(folder['defaultAccessMode'])
    
        var url = `${BASE_URL}pages/documents/api.php?endpoint=getAccessMode`
        var data = {
            docId: folder['folderId'],
            isFolder: 1
        }
        console.log('here')
        ajaxPOSTRequest(url, data, function(res){
            console.log(res)
            $("#folder_am").html(res)
        })
    }

    function getPaths(data) {
        var folderPaths = [] 
        var docPaths = [];
        if(data['folders'] && data['folders'].length > 0) {
            folderPaths = data['folders'].map(function(el){
                return el['folderPath'];
            })
        }
        if(data['docs'] && data['docs'].length > 0) {
            docPaths = data['docs'].map(function(el){
                return el['folderPath'] + el['docFileName'];
            })
        }
        var p = folderPaths.concat(docPaths)
        return p;
    }
   
    function initTree() {
        var tree = null
        console.log($("#tree").length)
        console.log($("#move_tree").length)
        if($("#tree").length > 0)
            tree = $("#tree")
        else
            tree = $("#move_tree")
        
        tree.fancytree({
            extensions: ["edit", "filter"],
            source: rootNode,
            selectMode: 3,
            activate: function(event, data){
                selectedId = data.node.data.id
                if($("#move_tree").length > 0 && data.node.folder) {
                    $("#move_target_path").html(data.node.data.path)
                    $("#selected_folder_id").val(selectedId)
                } else if($("#move_tree").length > 0 && !data.node.folder) {
                    $("#cur_doc_id").val(selectedId);
                } else {
                    $("#cur_folder_id").val(selectedId);
                    reloadOrRedirect(selectedPath, data.node.data.path)
                    setFolderDetail(selectedId)
                }
              },
        });
    }

    function validateFolder(path) {
        if (path == '' || /\/$/.test(path)) {
            return true
        } else {
            return false
        }
    }

    function reloadOrRedirect(beforePath, afterPath) {
        if(validateFolder(beforePath) && validateFolder(afterPath)) {
            selectedPath = afterPath
            let rows = initTableDataForSub(selectedPath)
            reloadDataTable(rows)
        } else if(!validateFolder(beforePath) && validateFolder(afterPath)){
            location.href = `${BASE_URL}folders`
        } else if(validateFolder(beforePath) && !validateFolder(afterPath)){
            location.href = `${BASE_URL}documents?documentId=${selectedId}`
        }
    }

    function getFilename(path) {
        return path.split("/").filter(function(value) {
            return value && value.length;
        }).reverse()[0];
    }
    
    // Find sub paths
    function findSubPaths(path) {
        
        // slashes need to be escaped when part of a regexp
        var rePath = path.replace("/", "\\/");
        var re = new RegExp("^[\\/]?" + rePath + "[^\\/]*\\/?$");
        return paths.filter(function(i) {
            return i !== path && re.test(i);
        });
    }
    
    // Build tree recursively
    function buildTree(path) {
        path = path || "";
        var nodeList = [];
        var f = null;
        if(path == '') {
            console.log(findSubPaths(path))
        }
        findSubPaths(path).forEach(function(subPath) {
            var nodeName = getFilename(subPath);
            if (validateFolder(subPath)) {
                f = folders.find(el=>el['folderPath'] == subPath)
            } else {
                f = documents.find(el=>el['folderPath'] + el['docFileName'] == subPath)
            }
            
            
            if(!f) return
            key ++;
            var nodeName = getFilename(subPath);
            if (validateFolder(subPath)) {
                var node = {};
                node['children'] = buildTree(subPath);
                node['title'] = nodeName;
                node['key'] = key;
                node['folder'] = true;
                node['expanded'] = false;
                node['path'] = subPath;
                node['id'] = f && f['folderId'] ? f['folderId'] : -1
                node['parentId'] = f && f['parentId'] ? f['parentId'] : -1
                nodeList.push(node);
            } else {
                var node = {};
                node['title'] = nodeName;
                node['key'] = key;
                node['expanded'] = false;
                node['path'] = subPath;
                node['folder'] = false;
                node['id'] = f && f['documentID'] ? f['documentID'] : -1
                node['folderId'] = f && f['folderId'] ? f['folderId'] : -1
                nodeList.push(node);
            }
        });
        return nodeList;
    }

    function initFileUpload() {
        window.supportDrag = function() {
            let div = document.createElement('div');
            return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
         }();
         
         if (!supportDrag) {
            $(".has-drag")[0].classList.remove("has-drag");
         }
      
         $("#js-file-input").on('change', function(e){
            $("#js-file-name").html(this.files[0].name);
            $(".file-input").removeClass("file-input--active");
             uploadFile(this.files[0])
         })
          
         if (supportDrag) {
            $("#js-file-input").on("dragenter", function (e) {
               $(".file-input").addClass("file-input--active");
            });
      
            $("#js-file-input").on("dragleave", function (e) {
               $(".file-input").removeClass("file-input--active");
            });
         }
    }

    function uploadFile(data) {
        var form_data = new FormData();                  
        form_data.append('file', data);
        var curFolderId = $("#cur_folder_id").val();
        var url = `${BASE_URL}pages/documents/api.php?folderId=${curFolderId}&endpoint=upload`
        $.ajax({
            url: url, // point to server-side PHP script 
            dataType: 'text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(php_script_response){
                if(php_script_response) {
                    showToastSuccess('File uploaded successfully') 
                }
            }
        });
    }
    // Note: Loading and initialization may be asynchronous, so the nodes may not be accessible yet.
});