<?php
    
	require_once('../../../library/config.php');
    $relTypeID = isset($_REQUEST['reltypeid']) ? $_REQUEST['reltypeid'] : NULL;
    $docType = isset($_REQUEST['doctype']) ? $_REQUEST['doctype'] : "";

	if ($oAuth->checkLoginStatus() == true) {
		
		$companyDocsRelTypes = array(14, 15, 16, 17, 18, 19, 20, 21, 22, 36, 37, 38, 39, 41, 69);
		$auditRelTypes = array(30, 31, 32, 33, 34, 35);
		
		if($relTypeID != NULL){
			if (in_array($relTypeID, $companyDocsRelTypes)){
				$permission = $oAuth->checkPermissionAccess('adminCompanyDocuments'.$relTypeID, 'index') == true;
                $permissionName = 'adminCompanyDocuments'.$relTypeID;
			} elseif (in_array($relTypeID, $auditRelTypes)){
				$permission = $oAuth->checkPermissionAccess('adminCompanyAudits'.$relTypeID, 'index') == true;
                $permissionName = 'adminCompanyAudits'.$relTypeID;
			}	
		} else if($docType != "") {
			if($docType == 'company'){
				$permission = $oAuth->checkPermissionAccess('adminCompanyDocuments', 'index');
				$permissionName = 'adminCompanyDocuments';
				$oRelType = new RelType();
				$relTypeList = $oRelType->getAll("AND relTypeID IN (".implode(", ",$companyDocsRelTypes).")");
			 	$oTemplate->setData('relTypeList', $relTypeList);
			} elseif($docType == 'audits'){
				$permission = $oAuth->checkPermissionAccess('adminCompanyAudits', 'index');	
                $permissionName = 'adminCompanyAudits';
				$oRelType = new RelType();
				$relTypeList = $oRelType->getAll("AND relTypeID IN (".implode(", ",$auditRelTypes).")");
			 	$oTemplate->setData('relTypeList', $relTypeList);
			}	
		}
		$oTemplate->setData('permissionName', $permissionName);
        if ($permission == true) {

            $y2dMonthArray[] = array('yearTag' => (date('Y') - 3), 'yearTitle' => date('Y') - 3);
            $y2dMonthArray[] = array('yearTag' => (date('Y') - 2), 'yearTitle' => date('Y') - 2);
            $y2dMonthArray[] = array('yearTag' => (date('Y') - 1), 'yearTitle' => date('Y') - 1);
            $y2dMonthArray[] = array('yearTag' => date('Y'), 'yearTitle' => date('Y'));
            $y2dMonthArray[] = array('yearTag' => (date('Y') + 1), 'yearTitle' => date('Y') + 1);
            $y2dMonthArray[] = array('yearTag' => 'all', 'yearTitle' => 'All');
            $oTemplate->setData('y2dMonthArray', $y2dMonthArray);
            
            $oCompany = new Company();
            $companyList = $oCompany->getAll();
            $oTemplate->setData('companyList', $companyList);
            
			if($relTypeID != NULL){
				$oRelType = new RelType();
				$dbRelType = $oRelType->getOne($relTypeID);
				$dbRelType = $dbRelType[0];
				$oTemplate->setData('relTypeID', $relTypeID);
				$oTemplate->setData('relTypeName', $dbRelType['name']);	
				$oTemplate->setMetaData($dbRelType['name'].': Documents Directory', 'pageTitle');
			} elseif($docType != "") {
				if($docType == 'company'){
					$oTemplate->setData('relTypeName', 'All Company');	
					$oTemplate->setMetaData('All Documents: Documents Directory', 'pageTitle');
				} elseif($docType == 'audits'){
					$oTemplate->setData('relTypeName', 'All Audits');	
					$oTemplate->setMetaData('All Audits: Documents Directory', 'pageTitle');	
				}	
			}
        
            $oTemplate->setInclude(ASSETS_JS_URL.'plugin/uploadifive/jquery.uploadifive.min.js', 'jsInclude');
            $oTemplate->setInclude(ASSETS_JS_URL.'plugin/datatables/datatables.min.js', 'jsInclude');
            $oTemplate->setInclude(ASSETS_JS_URL.'plugin/datatables/date-uk.js', 'jsInclude');
            $oTemplate->setInclude(ASSETS_JS_URL.'plugin/datatables/natural-sort.js', 'jsInclude');

            $footerCode = '
            <script type="text/javascript">											
                $(document).ready(function(){				

                    $(".datepicker").datepicker({ format: "dd/mm/yyyy", autoclose: true, clearBtn: true, todayHighlight: true });
                    $( "#uploadCompanyID, #upoadDocCatID, #docCatID" ).selectpicker({
                        liveSearch : true,
                        liveSearchNormalize: true,
                        dropupAuto: false,
                        width : "100%",
                        size: 10
                    });';
            
                    if($_SESSION['hasMultiCompanyAccess'] == true) {
                        $footerCode .= '
                        if($("#multiCompanyID").val().length == 0){
                            $("#multiCompanyID").selectpicker("val", $("#multiCompanyID option:first").val());
                            $("#companyTabs a[href=\"#"+$("#multiCompanyID option:first").val()+"\"]").tab("show");
                            $("#company"+$("#multiCompanyID option:first").val()+"Tab").show();
                            $("#multiCompanyID option:first").prop("disabled", true);
                            $("#multiCompanyID").selectpicker("refresh");
                        } else {
                            $("#companyTabs a[href=\"#'.$_SESSION['companyID'].'\"]").tab("show"); 
                            $("#multiCompanyID option[value=\"'.$_SESSION['companyID'].'\"]").prop("disabled", true);
                            $("#multiCompanyID").selectpicker("refresh");
                        }';
                    }  else {
                        $footerCode .= '
                        $("#companyTabs a[href=\"#'.$_SESSION['companyID'].'\"]").tab("show"); 
                        $("#multiCompanyID option[value=\"'.$_SESSION['companyID'].'\"]").prop("disabled", true);
                        $("#multiCompanyID").selectpicker("refresh");';    
                    }
                    $footerCode .= '
				});
                
                $("#multiCompanyID").selectpicker({
                    liveSearch: true,
                    liveSearchStyle: "startsWith",
                    liveSearchNormalize: true,
                    actionsBox: false,
                    dropupAuto: true,
                    width: "300px",
                    size: 10
                }).on("shown.bs.select", function(e) {
                    prevVal = $(this).val();
                }).on("changed.bs.select", function(e) {
                   var currentVal = $(this).val();
                   if(prevVal){
                        $.each( prevVal, function (key, val) {
                            if($.inArray(val,currentVal) == -1){
                                $("#company"+val+"Tab").hide();
                            }
                        });
                    }
                    $.each( currentVal, function (key, val) {
                        if($.inArray(val,prevVal) == -1){
                            var multiCompanyID = val;
                            if(multiCompanyID > 0) {
                                $("#company"+val+"Tab").show();
                            }    
                        }
                    });
                    var firstVisibleTab = $("#companyTabs").find("li:visible:first").find("a:first").attr("href");
                    $("#companyTabs a[href=\""+firstVisibleTab+"\"]").tab("show");
                    prevVal = currentVal;
                });
                
                $("#companyTabs a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
                    var companyID = $(this).attr("href"); companyID = companyID.substr(1);

                    $("#documentMonthYearTab"+companyID+" a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
                        sessionStorage.setItem("TMSERP_lastActiveDocumentTab"+companyID, e.target);
                    });
                
                    $("#documentMonthYearTab" + companyID + " a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
                        var selDate = $(this).attr("href"); selDate = selDate.substr(1);
                        $.ajax({
                            type: "POST",
                            url: "'.AJAX_URL.'auth/ajax-userpermission.php",
                            data: { "action" : "getUserPermissionData", "companyid" : companyID, "permissionname" : [ "'.$permissionName.'" ] },
                            dataType: "json",
                            cache: false,
                            success: function(userPermission) {
                                if (!$.fn.DataTable.isDataTable("#" + selDate + "documentListDT")) {
                                    var oDocumentListDT = $("#" + selDate + "documentListDT").DataTable({
                                    ajax: {
                                            url: "'.AJAX_URL.'documents/ajax-documentList.php",
                                            type: "POST",
                                            data: {
                                                "reltypeid" : "'.$relTypeID.'",
                                                "doctype" : "'.$docType.'",
                                                "fYear": selDate.replace("comp"+companyID+"_","").substr(selDate.replace("comp"+companyID+"_","").length - 4),
                                                 "companyid": companyID
                                            }
                                        },
                                        processing: true,
                                        serverSide: false,
                                        stateSave: false,
                                        fixedHeader: {
                                            headerOffset: $("#left-panel").outerHeight() + $("#ribbon").outerHeight() - 2
                                        }, 
                                        autoWidth: true,
                                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                                        displayLength: -1,
                                        pagingType: "full_numbers",
                                        dom: "<\"dt-toolbar-header\"<\"col-xs-12 col-sm-4\"f><\"col-xs-12 col-sm-4 text-center\"B><\" col-xs-12 col-sm-4\"l>r>t<\"dt-toolbar-footer\"<\"col-sm-6 col-xs-12\"i><\"col-xs-12 col-sm-6\"p>>",
                                        buttons: [ 
                                            {
                                                extend: "colvis",
                                                text: "<i class=\"fal fa-lg fa-bars\"></i>",
                                                titleAttr: "Display Options",
                                            },
                                            {
                                                extend: "copyHtml5",
                                                text: "<i class=\"fal fa-lg fa-copy\"></i>",
                                                titleAttr: "Copy",
                                            },
                                            {
                                                extend: "collection",
                                                text: "<i class=\"fal fa-lg fa-save\"></i>",
                                                titleAttr: "Save",
                                                buttons: [ "csvHtml5", "excelHtml5", { extend: "pdfHtml5", orientation: "landscape" } ]
                                            },
                                            {
                                                extend: "print",
                                                text: "<i class=\"fal fa-lg fa-print\"></i>",
                                                titleAttr: "Print",
                                            },
                                            { 
                                                text: "<i class=\"fal fa-lg fa-undo\"></i>",
                                                titleAttr: "Clear / Reset Filters",
                                                className: "buttons-reset",
                                                action: function ( e, dt, node, config ) {
                                                    $("#" + selDate + "documentListDT thead input").val("").change();
                                                    $("#" + selDate + "documentListDT thead select").val("").change();
                                                    $("#" + selDate + "documentListDT").DataTable().search("").draw();
                                                }
                                            }
                                        ],
                                        columns: [
                                            { data: "dtRelTypeName"},
                                            { data: "dtTitle", type: "natural" },
                                            { data: "dtDocCatName" },
                                            { data: "dtExpiryDate", type: "date-uk" },
                                            { data: "dtUploadedDate", type: "date-uk" },
                                            { data: "dtUploadedTime" },
                                            { data: "dtUploadedByName" },
                                            { data: "dtExtension"},
                                            { 
                                                class:          "dtRowTools text-center align-middle",
                                                orderable:      false,
                                                searchable: 	false,
                                                data:           "dtDocumentID",
                                                render: 		function (data, type, row) {
                                                    var tdToolsHTML = "", btnDownloadHTML = "", btnEditHTML = "", btnDeleteHTML = "";

                                                    if(userPermission.'.$permissionName.'.edit == 1){
                                                        btnEditHTML = "<a href=\"javascript:void(0);\" class=\"editDocument\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"Edit Document\"><i class=\"fal fa-lg fa-edit\"></i></a>";
                                                    } else {
                                                        btnEditHTML = "<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"You do not have Permission to Edit this Document\"><i class=\"fal fa-lg fa-edit disabled\"></i></a>";
                                                    }

                                                    if(userPermission.'.$permissionName.'.delete == 1){
                                                        btnDeleteHTML = "<a href=\"javascript:void(0)\" class=\"deleteDocument\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"Delete Document\"><i class=\"fal fa-lg fa-trash-alt\"></i></a>";
                                                    } else {
                                                        btnDeleteHTML = "<a href=\"javascript:void(0)\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"You do not have Permission to Delete this Document\"><i class=\"fal fa-lg fa-trash-alt disabled\"></i></a>";
                                                    }
                                                    
                                                    if(userPermission.'.$permissionName.'.view == 1){
                                                        btnDownloadHTML = "<a href=\"'.ADMIN_URL.'documents/download.php?docid="+data+"\" "+((row[\'dtExtension\'] == "pdf") ? "target=\"_blank\"" : "")+" data-toggle=\"tooltip\" data-placement=\"left\" title=\"Download Document\"><i class=\"fal fa-lg fa-download\"></i></a>";
                                                        
                                                    } else {
                                                        btnDownloadHTML = "<a href=\"javascript:void(0);\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"You do not have Permission to Download this Document\"><i class=\"fal fa-lg fa-download disabled\"></i></a>";
                                                    }

                                                    tdToolsHTML = btnDownloadHTML + " " +btnEditHTML + " " + btnDeleteHTML;
                                                    return tdToolsHTML;
                                                }
                                            }
                                        ],
                                        drawCallback: function( settings ) {
                                            $("[data-toggle~=\"tooltip\"]").tooltip({
                                                animation: true,
                                                trigger: "hover",
                                                html: true
                                            });
                                        },
                                        order: [[1, "asc"]],
                                        language: {
                                            search: "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>_INPUT_</div>",
                                            searchPlaceholder: "Global Search ...",
                                            lengthMenu: "Records <span class=\"txt-color-darken\">Per</span> <span class=\"text-primary\">Page</span> _MENU_",
                                            info: "Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span>"
                                        }
                                    });
                                };
                                
                                if(selDate.indexOf("all") >= 0) { 
                                    oDocumentListDT.page.len(100).draw();
                                } else { 
                                    oDocumentListDT.page.len(-1).draw();
                                }
                                var state = oDocumentListDT.state.loaded();
                                if ( state ) {
                                    oDocumentListDT.columns().eq( 0 ).each( function ( colIdx ) {
                                    var colSearch = state.columns[colIdx].search;
                                        if ( colSearch.search ) {
                                            $( "#" + selDate + "documentListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
                                            $( "#" + selDate + "documentListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
                                        }
                                    });
                                }										
                                $( "#" + selDate + "documentListDT thead th input[type=text]").on( "keyup change", function () {
                                    oDocumentListDT
                                        .column( $(this).parent().index()+":visible" )
                                        .search( this.value )
                                        .draw();
                                });
                                $( "#" + selDate + "documentListDT thead th select").on( "change", function () {
                                    oDocumentListDT
                                        .column( $(this).parent().index()+":visible" )
                                        .search( this.value )
                                        .draw();
                                });

                                $("#" + selDate + "documentListDT thead th #dt_fltr_4").daterangepicker({ 
                                    ranges: {
                                        "Today": [moment(), moment()],
                                       "Last 7 Days": [moment().subtract(6, "days"), moment()],
                                       "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                                       "Month to Date": [moment().startOf("month"), moment()],
                                       "Year to Date": [moment().startOf("year"), moment()]
                                    },
                                    autoUpdateInput: false,
                                    locale: { 
                                        format: "DD/MM/YYYY", 
                                        cancelLabel: "Clear" 
                                    }
                                });
                                $("#" + selDate + "documentListDT thead th #dt_fltr_4").on("apply.daterangepicker", function(ev, picker) {
                                      $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
                                      oDocumentListDT.draw();
                                });
                                $("#" + selDate + "documentListDT thead th #dt_fltr_4").on("cancel.daterangepicker", function(ev, picker) {
                                      $(this).val("");
                                      oDocumentListDT.draw();
                                });
                                $.fn.dataTableExt.afnFiltering.push(
                                    function( oSettings, aData, iDataIndex ) {
                                        var str_dateRange = $("#" + selDate + "documentListDT thead th #dt_fltr_4").val();
                                        if(str_dateRange != "") {
                                            var arr_dateRange = str_dateRange.split(" - ");
                                            var f_startDate = arr_dateRange[0].split("/");
                                            var filterStartDate = f_startDate[2] + "-" + f_startDate[1] + "-" + f_startDate[0];
                                            var f_endDate = arr_dateRange[1].split("/");
                                            var filterEndDate = f_endDate[2] + "-" + f_endDate[1] + "-" + f_endDate[0];

                                            var iDateRangeCol = 4;
                                            var str_dtDateCol = aData[iDateRangeCol];
                                            var f_dtDateCol = str_dtDateCol.split("/");
                                            var filterDTDateCol = f_dtDateCol[2] + "-" + f_dtDateCol[1] + "-" + f_dtDateCol[0];

                                            if ( filterStartDate === "" && filterEndDate === "" )
                                            {
                                                return true;
                                            }
                                            else if ( filterStartDate <= filterDTDateCol && filterEndDate === "")
                                            {
                                                return true;
                                            }
                                            else if ( filterEndDate >= filterDTDateCol && filterStartDate === "")
                                            {
                                                return true;
                                            }
                                            else if (filterStartDate <= filterDTDateCol && filterEndDate >= filterDTDateCol)
                                            {
                                                return true;
                                            }
                                            return false;
                                        } else {
                                            return true;	
                                        }
                                    }
                                );

                                $( "#" + selDate + "documentListDT tbody").on("click", "a.editDocument", function () {
                                    var dtTR = $(this).parent("td").parent("tr");
                                    var dtRowData = oDocumentListDT.row(dtTR).data();
                                    var documentID = dtRowData["dtDocumentID"];	
                                    fnSaveDocument( documentID );
                                });

                                $( "#" + selDate + "documentListDT tbody").on("click", "a.deleteDocument", function () {
                                    var dtTR = $(this).parent("td").parent("tr");
                                    var dtRowData = oDocumentListDT.row(dtTR).data();
                                    var dtRowIndex = dtRowData["dtDocumentID"];	
                                    $( "#dlgDeleteDocument" ).modal( "show" );
                                    $( "#dlgDeleteDocument button#btnYes" ).unbind().on( "click" , function(e) {
                                        $.ajax({
                                            type: "POST",
                                            url: "'.AJAX_URL.'documents/ajax-document.php",
                                            data: { "action" : "deleteFile", "docid" : dtRowIndex },
                                            cache: false,
                                            success: function(data){
                                                if(data == true){
                                                    $("#dlgDeleteDocument").modal( "hide" );
                                                    oDocumentListDT.row( dtTR ).remove().ajax.reload().draw();
                                                    if($("#" + selDate + "documentListDT tbody tr").find(\'td.dataTables_empty\').length) {
                                                        $("#bdgDocCnt").html(0);
                                                    } else {
                                                        var docDataCount = $("#" + selDate + "documentListDT tbody tr").length
                                                        $("#bdgDocCnt").html(docDataCount);
                                                    }
                                                }
                                            }
                                        });
                                    });
                                    $("#dlgDeleteDocument button#btnNo").unbind().on( "click" , function(e) {
                                        $("#dlgDeleteDocument").modal( "hide" );
                                    });
                                });';
                                if($docType != ""){
                                    $footerCode.= '
                                    oDocumentListDT.column( 0 ).visible( true );';
                                } else {
                                    $footerCode.= '
                                    oDocumentListDT.column( 0 ).visible( false );';
                                }
                                $footerCode.= '
                                function fnSaveDocument(id) {
                                    if( id > 0 ) {

                                        $.ajax({
                                            type: "POST",
                                            url: "'.AJAX_URL.'documents/ajax-document.php",
                                            data: { "action" : "getCategoryList", "docid" : id },
                                            cache: false,
                                            dataType: "json",
                                            success: function(data){
                                                $("#docCatID").empty();
                                                $("#docCatID").append($("<option>", {
                                                    value: 0,
                                                    text: "Please Select From The Below List"
                                                }));
                                                $.each(data, function (key, val) {
                                                    $("#docCatID").append($("<option>", {
                                                        value: val.docCatID,
                                                        text: val.name
                                                    }));
                                                });
                                                $("#docCatID").selectpicker("refresh");
                                            }
                                        });

                                        $.ajax({
                                            type: "POST",
                                            url: "'.AJAX_URL.'documents/ajax-document.php",
                                            data: { "action" : "getFileData", "docid" : id },
                                            dataType: "json",
                                            cache: false,
                                            success: function(data){
                                                if(data != false && data != null){
                                                    $( "#title" ).val( data[\'title\'] );
                                                    $( "#docCatID" ).selectpicker("val", data[\'docCatID\'] );
                                                    $( "#expiryDate" ).val(data[\'expiryDate\']);
                                                    $( "#localAuthAppTypeID" ).val( data[\'localAuthAppTypeID\'] );
                                                    $( "#docFileName" ).html( data[\'docFileName\'] );
                                                    $( "#uploadedDate" ).html( data[\'uploadedDate\'] );
                                                    $( "#uploadedBy" ).html( data[\'uploadedByName\'] );
                                                    if(data[\'isCADPlan\'] == 1 ) {
                                                        $("#isCADPlan").prop( "checked", true );
                                                    } else {
                                                        $("#isCADPlan").prop( "checked", false );
                                                    }
                                                    if(data[\'includeVanPack\'] == 1 ) {
                                                        $("#includeVanPack").prop( "checked", true );
                                                    } else {
                                                        $("#includeVanPack").prop( "checked", false );
                                                    }
                                                } else {
                                                    $( "#title" ).val( "" );
                                                    $( "#docCatID" ).val( "0" );
                                                    $( "#localAuthAppTypeID" ).val( "0" );
                                                    $( "#docFileName" ).val( "" );
                                                    $( "#uploadedDate" ).val( "" );
                                                    $( "#uploadedBy" ).val( "" );
                                                    $( "#isCADPlan" ).val( "1" );
                                                    $( "#isCADPlan" ).prop( "checked", false );
                                                    $( "#includeVanPack" ).val( "1" );
                                                    $( "#includeVanPack" ).prop( "checked", false );
                                                }
                                            }
                                        });
                                    } 

                                    $( "#dlgDocumentDetails" ).modal( "show" );			
                                    $( "#dlgDocumentDetails button#btnSave" ).unbind().on( "click" , function(e) {
                                        $( "#dlgDocumentDetails button#btnSave" ).html( "Saving..." );
                                        $( "#dlgDocumentDetails button#btnSave" ).prop( "disabled", true );
                                        $.ajax({
                                            type: "POST",
                                            url: "'.AJAX_URL.'documents/ajax-document.php",
                                            data: { "action" : "saveFile", "docid" : id, "postdata" : $("#updateDocument").serialize() },
                                            cache: false,
                                            success: function(data) {
                                                var arrData = data.split("|");
                                                $( "#dlgDocumentDetails" ).modal( "hide" );
                                                $( "#dlgDocumentDetails button#btnSave" ).html( "Save" );
                                                $( "#dlgDocumentDetails button#btnSave" ).prop( "disabled", false );	
                                                $( "#isConfidential" ).val( "1" );
                                                $( "#isConfidential" ).prop( "checked", false );
                                                $( "#docCatID" ).val( "0" );
                                                $( "#localAuthAppTypeID" ).val( "0" );
                                                $( "#title, #docFileName, #uploadedDate, #uploadedBy" ).val( "" );
                                                if( arrData[0] == "success" ) {									
                                                    $.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
                                                    oDocumentListDT.ajax.reload(null, false);
                                                    setTimeout(function(){
                                                        if($("#" + selDate + "documentListDT tbody tr").find(\'td.dataTables_empty\').length) {
                                                            $("#bdgDocCnt").html(0);
                                                        } else {
                                                            var docDataCount = $("#" + selDate + "documentListDT tbody tr").length
                                                            $("#bdgDocCnt").html(docDataCount);
                                                        }
                                                    }, 800);
                                                } else if( arrData[0] == "danger" ) {
                                                    $.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
                                                } else if(arrData[0] == "info") {
                                                    $.smallBox({title:"Info:",content:arrData[1],color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
                                                }
                                            }
                                        });
                                        return false;
                                    });
                                    $( "#dlgDocumentDetails button#btnCancel" ).unbind().on( "click" , function(e) {
                                        $( "#dlgDocumentDetails" ).modal( "hide" );
                                        $( "#dlgDocumentDetails button#btnSave" ).html( "Save" );
                                        $( "#dlgDocumentDetails button#btnSave" ).prop( "disabled", false );
                                        $( "#isConfidential" ).val( "1" );
                                        $( "#isConfidential" ).prop( "checked", false );
                                        $( "#docCatID" ).val( "0" );
                                        $( "#localAuthAppTypeID" ).val( "0" );
                                        $( "#title, #docFileName, #uploadedDate, #uploadedBy" ).val( "" );
                                    });
                                }	
                            }
                        });
                    });
                    
                    var lastActiveDocumentTab = sessionStorage.getItem("TMSERP_lastActiveDocumentTab"+companyID);
                    if(lastActiveDocumentTab != null && lastActiveDocumentTab != "undefined") {
                        var arrLastActTabURL = lastActiveDocumentTab.split("#");
                        var lastActTab = arrLastActTabURL[1];
                        $("#documentMonthYearTab"+companyID+" a[href=\"#"+lastActTab+"\"]").tab("show");
                    } else {
                        $("#documentMonthYearTab"+companyID+" a[href=\"#comp"+companyID+"_'.strtolower(date('Y')).'\"]").tab("show");
                        sessionStorage.setItem("TMSERP_lastActiveDocumentTab"+companyID, $(location).attr("href") + "#comp"+companyID+"_'.strtolower(date('Y')).'");
                    }
                });
                
                $("#file-uploads").uploadifive({
                    debug    			: true,
                    auto      			: true,
                    uploadScript      	: "'.AJAX_URL.'documents/ajax-document.php",
                    method   			: "POST",
                    onUpload : function(){
                        $("#file-uploads").data("uploadifive").settings.formData = { 
                            "action" : "uploadFiles", 
                            "reltypeid" : "'.$relTypeID.'",
                            "companyid" : $("#uploadCompanyID").val(),
                            "doccatid" : $("#uploadDocCatID").val()
                        }
                    },
                    fileSizeLimit		: "20MB",
                    fileType  			: "image/* | audio/wav | audio/mpeg | application/msword | application/vnd.openxmlformats-officedocument.wordprocessingml.document | application/vnd.ms-excel | application/vnd.openxmlformats-officedocument.spreadsheetml.sheet | application/pdf|application/vnd.msoutlook | application/octet-stream",
                    buttonClass			: "",
                    buttonText			: "Drop files to upload (Or Click)",
                    width				: "100%",
                    height				: "70px",
                    queueID          	: "file-uploads-queue",
                    removeCompleted 	: true,
                    multi				: true,
                    dnd					: true,
                    onFallback			: function() {
                        alert("HTML5 is not supported in this browser.");
                    },
                    onQueueComplete : function(queueData) {
                        var companyID = $("#uploadCompanyID").val();
                        var lastActiveDocumentTab = sessionStorage.getItem("TMSERP_lastActiveDocumentTab"+companyID);
                        if(lastActiveDocumentTab != null){
                            var arrLastActTabURL = lastActiveDocumentTab.split("#");
                            var lastActTab = arrLastActTabURL[1];
                            $("#"+lastActTab+"documentListDT").DataTable().ajax.reload(null, false); 
                        }
                        $("#uploadDocCatID").selectpicker("val", 0);
                    }
                });
            </script>';
			
            if($relTypeID){
				$oDocCat = new DocumentCategory();
				$docCatList = $oDocCat->getAll( "AND relTypeID = ".$relTypeID );
				$oTemplate->setData('docCatList', $docCatList);
			}
            
            $oTemplate->setInclude($footerCode,'footerCode');
            $oTemplate->load(ADMIN_VIEWS.'documents/v_index.php');
        } else {
			$oTemplate->setMetaData('Permission Denied!','pageTitle');
			$oTemplate->load(ADMIN_VIEWS.'v_403.php');
			exit();
		}
    }
?>