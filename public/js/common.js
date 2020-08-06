function ajaxGETRequest(url, successCallback) {
    return $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: successCallback
    })
}

function ajaxPOSTRequest(url, data, successCallback) {
    return $.ajax({
        url: url,
        method: 'POST',
        data: data,
        dataType: 'json',
        success: successCallback
    })
}

function showToastSuccess(text) {
    $.toast({
        heading: 'Success',
        text: text,
        showHideTransition: 'slide',
        icon: 'success',
        position: 'top-right'
    })
}

function showToastWarning(text) {
    $.toast({
        heading: 'Warning',
        text: text,
        showHideTransition: 'slide',
        icon: 'warning',
        position: 'top-right'
    })
}

function isPreviewable(doc, ext) {
    var realFilePath = doc['docFilePath']
    var splitter = realFilePath.split('/')
    if(!splitter) return null
    var fileName = splitter[splitter.length - 1]
    var extArr = ['jpg', 'jpeg', 'png', 'pdf']
    if(extArr.indexOf(ext) == -1) return null
    return doc['folderPath'] == '' ? '/' + fileName : doc['folderPath'] + fileName
}