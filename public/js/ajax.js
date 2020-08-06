$('#add_new_folder').unbind().bind('click', function(e) {
    e.preventDefault();
    var folderName = $("#sub_folder_name").val();
    if(folderName.trim() == '') {
        showToastWarning('Folder name should not be empty!')
    }
    var parentId = $("#parent_folder_id").val()
    var url = `${BASE_URL}pages/documents/api.php?endpoint=addNewSub`
    var data = {
        folderName: folderName,
        parentId: parentId
    }
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess(`Folder:  ${folderName} created successfully`)    
            setTimeout(function(){
                location.href = `${BASE_URL}folders`
            }, 2000)
        }
    })
})

$('#edit_folder').unbind().bind('click', function(e) {
    e.preventDefault();
    var folderName = $("#edit_folder_name").val();
    var curFolderId = $("#cur_folder_id").val();
    if(folderName.trim() == '') {
        showToastWarning('Folder name should not be empty!')
    }

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editFolder`
    var data = {
        folderId: curFolderId,
        folderName: folderName
    }
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Folder: ' + folderName + ' changed successfully')    
            setTimeout(function(){
                location.href =`${BASE_URL}folders`
            }, 2000)
        }
    })
})

$('#btn_edit_folder_owner').unbind().bind('click', function(e) {
    e.preventDefault();
    var selectedUserId = $("#owner_selector").val()
    var curFolderId = $("#cur_folder_id").val();

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editFolderOwner`
    var data = {
        folderId: curFolderId,
        userId: selectedUserId
    }
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Folder owner changed successfully')    
        }
    })
})

$('#btn_default_access_save').unbind().bind('click', function(e) {
    e.preventDefault();
    var selectedMode = $("#select_default_access").val()
    var curFolderId = $("#cur_folder_id").val();

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editFolderDefaultAccess`
    var data = {
        folderId: curFolderId,
        defaultAccessMode: selectedMode
    }
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Folder owner changed successfully')    
        }
    })
})

$('#btn_access_mode').unbind().bind('click', function(e) {
    e.preventDefault();
    var selectedMode = $("#select_access_mode").val()
    var selectedUserId = $("#select_access_user").val()
    var selectedGroupId = $("#select_access_group").val()
    var curFolderId = $("#cur_folder_id").val();

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editFolderAccess`
    var data = {
        folderId: curFolderId,
        accessMode: selectedMode,
    }
    if(selectedUserId != 0) data['userId'] = selectedUserId
    if(selectedGroupId != 0) data['groupId'] = selectedGroupId
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Folder owner changed successfully')    
            setTimeout(function(){
                location.reload()
            }, 2000)
        }
    })
})

$(".access-item-save").unbind().bind('click', function(e) {
    e.preventDefault()
    var id = $(this).data('id');
    var changedAccessMode = $($(this).parent().parent().find('select.access-info-item-select')[0]).val()
    var url = `${BASE_URL}pages/documents/api.php?endpoint=changeFolderAccess`
    var data = {
        id: id,
        accessMode: changedAccessMode
    }
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Folder owner changed successfully')    
        }
    })
})

$(".access-item-delete").unbind().bind('click', function(e) {
    e.preventDefault()
    var id = $(this).data('id');
    var url = `${BASE_URL}pages/documents/api.php?endpoint=deleteFolderAccess`
    var data = {
        id: id
    }
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Folder accessMode removed successfully')  
            setTimeout(function(){
                location.reload()
            }, 2000)
        }
    })
})

$("#update_doc_file_dlg").on('change', function(e){
    var file = this.files[0]
    $("#file_name_display").val(file.name)
})

$("#btn_update_doc").unbind().bind('click', function(e) {
    e.preventDefault()
    var fileData = document.getElementById("update_doc_file_dlg").files
    if(!fileData) return

    var docmentId = $("#cur_doc_id").val()
    var form_data = new FormData();                  
    form_data.append('file', fileData[0]);
    
    var url = `${BASE_URL}pages/documents/api.php?documentId=${docmentId}&endpoint=updateDocument`
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
                showToastSuccess('File updated successfully') 
                // location.href = `${BASE_URL}folders`
            }
        }
    });
})

$("#btn_edit_doc").unbind().bind('click', function(e) {
    e.preventDefault()
    var newName = $("#txt_rename").val()
    var documentId = $("#cur_doc_id").val()
    var keepExt = $("#keep_ext").prop('checked')
    
    var data = {
        documentId: documentId,
        docName: newName,
        keepExt: keepExt
    }
    
    var url = `${BASE_URL}pages/documents/api.php?endpoint=documentRename`
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document renamed successfully')  
            setTimeout(function(){
                location.reload()
            }, 2000)
        }
    })
})

$("#btn_move_doc").unbind().bind('click', function(e) {
    e.preventDefault()
    var documentId = $("#cur_doc_id").val()
    var folderId = $("#selected_folder_id").val()
    var data = {
        documentId: documentId,
        folderId: folderId
    }
    
    var url = `${BASE_URL}pages/documents/api.php?endpoint=moveDocument`
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document renamed successfully')  
            setTimeout(function(){
                location.reload()
            }, 2000)
        }
    })
})

$("#btn_remove_doc").unbind().bind('click', function(e) {
    e.preventDefault()
    var documentId = $("#cur_doc_id").val()
    
    var data = {
        documentId: documentId,
    }
    
    var url = `${BASE_URL}pages/documents/api.php?endpoint=removeDocument`
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document removed successfully')  
            setTimeout(function(){
                location.reload()
            }, 2000)
        }
    })
})

$('#btn_doc_owner').unbind().bind('click', function(e) {
    e.preventDefault();
    var selectedUserId = $("#owner_selector").val()
    var documentId = $("#cur_doc_id").val()

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editDocOwner`
    var data = {
        documentId: documentId,
        userId: selectedUserId
    }
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document owner changed successfully')    
        }
    })
})

$('#btn_doc_default_access_save').unbind().bind('click', function(e) {
    e.preventDefault();
    var selectedMode = $("#select_default_access").val()
    var documentId = $("#cur_doc_id").val();

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editDocDefaultAccess`
    var data = {
        documentId: documentId,
        defaultAccessMode: selectedMode
    }
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document owner changed successfully')    
        }
    })
})

$('#btn_doc_access_mode').unbind().bind('click', function(e) {
    e.preventDefault();
    var selectedMode = $("#select_access_mode").val()
    var selectedUserId = $("#select_access_user").val()
    var selectedGroupId = $("#select_access_group").val()
    var documentId = $("#cur_doc_id").val();

    var url = `${BASE_URL}pages/documents/api.php?endpoint=editDocumentAccess`
    var data = {
        documentId: documentId,
        accessMode: selectedMode,
    }
    if(selectedUserId != 0) data['userId'] = selectedUserId
    if(selectedGroupId != 0) data['groupId'] = selectedGroupId
    
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document owner changed successfully')    
            setTimeout(function(){
                location.reload()
            }, 2000)
        }
    })
})

$(".doc-access-item-save").unbind().bind('click', function(e) {
    e.preventDefault()
    var id = $(this).data('id');
    var documentId = $("#cur_doc_id").val();

    var changedAccessMode = $($(this).parent().parent().find('select.access-info-item-select')[0]).val()
    var url = `${BASE_URL}pages/documents/api.php?endpoint=changeDocumentAccess`
    var data = {
        id: id,
        documentId: documentId,
        accessMode: changedAccessMode
    }
    ajaxPOSTRequest(url, data, function(res) {
        if(res > 0) {
            showToastSuccess('Document owner changed successfully')    
        }
    })
})
