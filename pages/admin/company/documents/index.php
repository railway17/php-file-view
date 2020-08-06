<?php

	require_once('../../../../library/config.php');
	
	if ($oAuth->checkLoginStatus() == true)
	{	 
		if($oAuth->checkPermissionAccess('adminCompanyDocuments_All', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_PoliciesProcedures', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_RiskAssessments', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_MethodStatements', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_ToolboxTalks', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_Compliments', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_COSHHAssessments', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_EquipmentRegister', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_QualityManagementSystems', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_AttendanceDocumentSignatures', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_OperationalBibliography', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_Certification', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_MeetingMinutes', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_Insurances', 'index') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_StaffMemos', 'index') == true ) {
			
			$compDocTypeArray = array();
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_PoliciesProcedures', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'policy', 'title' => 'Policies & Procedures');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_RiskAssessments', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'risk', 'title' => 'Risk Assessments');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_MethodStatements', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'method', 'title' => 'Method Statements');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_ToolboxTalks', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'toolbox', 'title' => 'Toolbox Talks');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_Compliments', 'index') == true) {	
				$compDocTypeArray[] = array('tag' => 'compliment', 'title' => 'Compliments');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_COSHHAssessments', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'coshh', 'title' => 'COSHH Assessments');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_EquipmentRegister', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'equipment', 'title' => 'Equipment Register');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_QualityManagementSystems', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'quality', 'title' => 'Quality Management Systems');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_AttendanceDocumentSignatures', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'attendance', 'title' => 'Attendance / Document Signatures');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_OperationalBibliography', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'operation', 'title' => 'Operational Bibliography');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_Certification', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'certificate', 'title' => 'Certification');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_MeetingMinutes', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'meeting', 'title' => 'Meetings Minutes');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_Insurances', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'insurance', 'title' => 'Insurances');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_StaffMemos', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'staffmemo', 'title' => 'Staff Memos');
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_All', 'index') == true) {
				$compDocTypeArray[] = array('tag' => 'all', 'title' => 'All Files');
			}
			$oTemplate->setData('compDocTypeArray', $compDocTypeArray);

			$oTemplate->setInclude(ASSETS_JS_URL.'plugin/datatables/dataTables.min.js', 'jsInclude');
			$oTemplate->setInclude(ASSETS_JS_URL.'plugin/datatables/date-uk.js', 'jsInclude');
			$oTemplate->setInclude(ASSETS_JS_URL.'plugin/datatables/natural-sort.js', 'jsInclude');
			$footerCode = '
			<script type="text/javascript">												
				$(document).ready(function(){			
					$("#companyDocTypeTab a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
						sessionStorage.setItem("TMSERP_lastActiveCompanyDocTypeTab", e.target);
					});

					$(".daterangepicker").daterangepicker({
						opens: "left",
						startDate: moment(),
						endDate: moment().add(6, "days"),
						ranges: {
						   "Last 7 Days": [moment().subtract(6, "days"), moment()],
						   "Next 7 Days": [moment(), moment().add(6, "days")],
						   "Next 10 Days": [moment(), moment().add(9, "days")],
						   "This Month": [moment().startOf("month"), moment().endOf("month")],
						   "Next Month": [moment().add(1, "month").startOf("month"), moment().add(1, "month").endOf("month")],
						   "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
						},
						locale: { format: "DD/MM/YYYY", cancelLabel: "Clear" }	
					});
					$(".daterangepicker").on("apply.daterangepicker", function(ev, picker) {
						if(picker.startDate == picker.endDate) {
							$(this).val(picker.startDate.format("DD/MM/YYYY"));
						} else {
							$(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
						}
					});
					$(".daterangepicker").on("cancel.daterangepicker", function(ev, picker) {
						$(this).val( "" );
					});

				});
				$(function() {
					var lastActiveCompDocTypeTab = sessionStorage.getItem("TMSERP_lastActiveCompanyDocTypeTab");
					if(lastActiveCompDocTypeTab != null && lastActiveCompDocTypeTab != "undefined") {
						var arrLastActTabURL = lastActiveCompDocTypeTab.split("#");
						var lastActTab = arrLastActTabURL[1];
						$("#companyDocTypeTab a[href=\"#"+lastActTab+"\"]").tab("show");
					} else {
						$("#companyDocTypeTab a[href=\"#policy\"]").tab("show");
						sessionStorage.setItem("TMSERP_lastActiveCompanyDocTypeTab", $(location).attr("href") + "#policy");
					}
				});
				$("#companyDocTypeTab a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
					var docType = $(this).attr("href"); docType = docType.substr(1);
					if (!$.fn.DataTable.isDataTable("#" + docType + "ListDT")) {
						var oCompDocListDT = $("#" + docType + "ListDT").DataTable({
							ajax: {
								url: "'.AJAX_URL.'company/documents/ajax-documentList.php",
								type: "POST",
								data: { "docType": docType }
							},
							processing: true,
							serverSide: false,
							fixedHeader: { headerOffset: $("#left-panel").outerHeight() + $("#ribbon").outerHeight() - 2 },
							stateSave: true,
							autoWidth: false,
							deferRender: true,
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
										$("#" + docType + "ListDT thead input").val("").change();
										$("#" + docType + "ListDT thead select").val("").change();
										$("#" + docType + "ListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{ data: "dtTitle", type: "natural" },
								{ data: "dtDocCatName" },
								{ data: "dtExpiryDate", type: "date-uk" },
								{ data: "dtUploadedDate", type: "date-uk" },
								{ data: "dtUploadedTime" },
								{ data: "dtUploadedByName" },
								{ data: "dtDownloadFile", orderable: false, searchable: false },
								{ data: "dtRowTools", orderable: false, searchable: false }			
							],
							order: [[0, "asc"]],
							drawCallback: function( settings ) {
								$("[data-toggle~=\"tooltip\"]").tooltip({
									trigger: "hover",
									html: true
								});
							},
							language: {
								search: "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>_INPUT_</div>",
								searchPlaceholder: "Global Search ...",
								lengthMenu: "Records <span class=\"txt-color-darken\">Per</span> <span class=\"text-primary\">Page</span> _MENU_",
								info: "Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span>"
							}
						});
						if(docType.indexOf("all") >= 0) { 
							oCompDocListDT.page.len(100).draw(); 
						} else { 
							oCompDocListDT.page.len(-1).draw(); 
						}
						var state = oCompDocListDT.state.loaded();
						if ( state ) {
							oCompDocListDT.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$( "#" + docType + "ListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#" + docType + "ListDT thead th input[type=text]").on( "keyup change", function () {
							oCompDocListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#" + docType + "ListDT thead th select").on( "change", function () {
							oCompDocListDT
							.column( $(this).parent().index()+":visible" )
							.search( this.value )
							.draw();
						});
						
						$( "#" + docType + "ListDT tbody").on("click", "a.deleteDocument", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oCompDocListDT.row(dtTR).data();
							var documentID = dtRowData["dtDocumentID"];	
							$( "#dlgDeleteDocument" ).modal( "show" );
							$( "#dlgDeleteDocument button#btnYes" ).unbind().on( "click" , function(e) {
								$.ajax({
									type: "POST",
									url: "'.AJAX_URL.'documents/ajax-document.php",
									data: { "action" : "deleteFile", "docid" : documentID },
									cache: false,
									success: function(data){
										if(data == true){
											oCompDocListDT.row( dtTR ).remove().ajax.reload().draw();
											$( "#dlgDeleteDocument" ).modal( "hide" );
										}
									}
								});
							});
							$("#dlgDeleteDocument button#btnNo").unbind().on( "click" , function(e) {
								$("#dlgDeleteDocument").modal( "hide" );
							});
						});
						$("#" + docType + "ListDT thead th #dt_fltr_3").daterangepicker({ 
							ranges: {
							   "Last 7 Days": [moment().subtract(6, "days"), moment()],
							   "Next 7 Days": [moment(), moment().add(6, "days")],
							   "Next 10 Days": [moment(), moment().add(9, "days")],
							   "This Month": [moment().startOf("month"), moment().endOf("month")],
							   "Next Month": [moment().add(1, "month").startOf("month"), moment().add(1, "month").endOf("month")],
							   "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
							},
							autoUpdateInput: false,
							locale: { 
								format: "DD/MM/YYYY", 
								cancelLabel: "Clear" 
							}
						});
						$("#" + docType + "ListDT thead th #dt_fltr_3").on("apply.daterangepicker", function(ev, picker) {
							  $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
							  oCompDocListDT.draw();
						});
						$("#" + docType + "ListDT thead th #dt_fltr_3").on("cancel.daterangepicker", function(ev, picker) {
							  $(this).val("");
							  oCompDocListDT.draw();
						});
						$.fn.dataTableExt.afnFiltering.push(
							function( oSettings, aData, iDataIndex ) {
								var str_dateRange = $("#" + docType + "ListDT thead th #dt_fltr_3").val();
								if(str_dateRange != "") {
									var arr_dateRange = str_dateRange.split(" - ");
									var f_startDate = arr_dateRange[0].split("/");
									var filterStartDate = f_startDate[2] + "-" + f_startDate[1] + "-" + f_startDate[0];
									var f_endDate = arr_dateRange[1].split("/");
									var filterEndDate = f_endDate[2] + "-" + f_endDate[1] + "-" + f_endDate[0];

									var iDateRangeCol =3;
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
						$("#" + docType + "ListDT thead th #dt_fltr_4").daterangepicker({ 
							ranges: {
							   "Last 7 Days": [moment().subtract(6, "days"), moment()],
							   "Next 7 Days": [moment(), moment().add(6, "days")],
							   "Next 10 Days": [moment(), moment().add(9, "days")],
							   "This Month": [moment().startOf("month"), moment().endOf("month")],
							   "Next Month": [moment().add(1, "month").startOf("month"), moment().add(1, "month").endOf("month")],
							   "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
							},
							autoUpdateInput: false,
							locale: { 
								format: "DD/MM/YYYY", 
								cancelLabel: "Clear" 
							}
						});
						$("#" + docType + "ListDT thead th #dt_fltr_4").on("apply.daterangepicker", function(ev, picker) {
							  $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
							  oCompDocListDT.draw();
						});
						$("#" + docType + "ListDT thead th #dt_fltr_4").on("cancel.daterangepicker", function(ev, picker) {
							  $(this).val("");
							  oCompDocListDT.draw();
						});
						$.fn.dataTableExt.afnFiltering.push(
							function( oSettings, aData, iDataIndex ) {
								var str_dateRange = $("#" + docType + "ListDT thead th #dt_fltr_4").val();
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
					};
				});
				function openReportWindow(url, title, w, h)
				{
					var left = (screen.width/2)-(w/2);
					var top = (screen.height/2)-(h/2);
					return window.open(url, title, "location=no, resizable=yes, directories=no, toolbar=no, width="+w+", height="+h+", top="+top+", left="+left);
				}
			</script>';
			$oTemplate->setInclude($footerCode,'footerCode');

			$oTemplate->setMetaData('Operations Document Directory','pageTitle');

			$oTemplate->load(ADMIN_VIEWS.'company/documents/v_index.php');
		} else {
			$oTemplate->setMetaData('Permission Denied!','pageTitle');
			$oTemplate->load(ADMIN_VIEWS.'v_403.php');
			exit();
		}
	}

?>