
			
				$(function() {
					$(".datepicker").mask("99/99/9999");
					$(".datepicker").datepicker({format:"dd/mm/yyyy",weekStart:1,autoclose:true,clearBtn:true,todayHighlight:true});
					$(".maskTime").mask("99:99");
					$(".maskCurrency").maskMoney({prefix:"£ ",affixesStay:false,allowZero:false});

					$("#quoteTab a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
						var tabType = $(this).attr("href"); tabType = tabType.substr(1);
						if(tabType == "cad") {
							loadCADDetails();
						} else if(tabType == "localauthorities") {
							loadLocalAuthDetails();
						} else if(tabType == "documents") {
							loadDocumentDetails();
						} else if(tabType == "comments") {
							loadCommentDetails();
						} else if(tabType == "status") {
							loadStatusDetails();
						} else if (tabType == "history") {
							loadHistory();
						} else if(tabType == "planners") {
							loadPlannerDetails();
						}
					});	
					
					window.addEventListener("beforeunload", function (e) {
					$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/ajax-recordLock.php",
							data: { "reltypeid" : 10, "recordid" : 20392, "action" : "deleteRecordLock"},
							cache: false,
							success: function(data){}
						});
                        return false;
					});
					
					/*$(window).bind("beforeunload", function(e){
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/ajax-recordLock.php",
							data: { "reltypeid" : 10, "recordid" : 20392, "action" : "deleteRecordLock"},
							cache: false,
							success: function(data){}
						});
                        return false;
					});*/

					$("#tmsFileRef, #postcode").keyup(function() {
						$(this).val($(this).val().toUpperCase().replace(/[^A-Za-z0-9&_\-\+ ]/g,""))
					});
					var lastActiveQuoteTab = sessionStorage.getItem("TMSERP_lastActiveQuoteTab");
					if(lastActiveQuoteTab != null && lastActiveQuoteTab != "undefined") {
						var arrLastActTabURL = lastActiveQuoteTab.split("#");
						var lastActTab = arrLastActTabURL[1];
						$("#quoteMonthYearTab a[href=\"#"+lastActTab+"\"]").tab("show");
					} else {
						$("#quoteMonthYearTab a[href=\"#may2020\"]").tab("show");
						sessionStorage.setItem("TMSERP_lastActiveQuoteTab", $(location).attr("href") + "#may2020");
					}
                    
                    $("#eventStartDate" ).on( "change", function() {
                       $("#editQuote").formValidation("revalidateField", "eventStartDate");
                    });
                    $("#eventEndDate" ).on( "change", function() {
                       $("#editQuote").formValidation("revalidateField", "eventEndDate");
                    });
				});
                
                $("#custContactID, #popup_permitLocationID, .selJobReqDOM, .selCustOrderNum, .selCustomerDOM, #tmTypeIDs, #startPointID, #endPointID, #cwayDirection, #areaCodeID, #popup_startPointID,  #popup_endPointID,  #popup_cwayDirection, #popup_areaCodeID #supplierID, #suppSiteID, #permitLocationID, #singleLocationID, #qdLocationID, #locationID, #quoteDocIDs, #docIDs, #appLocationID, #depotID, #companyID, #docCatID,  #deliveryUnitID, #localAuthAppTypeID, #appliedBy, #statusID, #schemeID, #supplierID").selectpicker({
                    liveSearch : true,
                    liveSearchNormalize: true,
                    dropupAuto: false,
                    width : "100%",
                    size: 10
                });
                
                $("#companyID").on( "change", function() {
                    $("#depotID").empty();
                    $("#depotID").selectpicker("refresh");
                    $.ajax({
                        type: "POST",
                        url: "https://dev-erp.traffic.org.uk/public/ajax/company/ajax-company.php",
                        data: { "action" : "getDepotList", "companyid" : $(this).val() },
                        cache: false,
                        dataType: "json",
                        success: function(data){
                            $.each(data, function (key, val) {
                                $("#depotID").append($("<option>", {
                                    value: val.depotID,
                                    text: val.name+" - "+val.addressLine1+", "+val.addressLine2+", "+val.city+", "+val.county+", "+val.postcode
                                }));
                            });
                            $("#depotID").selectpicker("refresh");
                        }
                    });
                    $("#customerID").empty();
                    $("#customerID").selectpicker("refresh");
                    $.ajax({
                        type: "POST",
                        url: "https://dev-erp.traffic.org.uk/public/ajax/accounts/customers/ajax-cust.php",
                        data: { "action" : "getCompanySpecificCustomers", "companyid" : $(this).val() },
                        dataType: "json",
                        cache: false,
                        success: function(data){
                            $.each(data, function (key, val) {
                                $("#customerID").append($("<option>", {
                                    value: val.customerID,
                                    text: val.name
                                }));
                            });
                            $("#customerID").selectpicker("refresh");
                        }
                    });  
                    $("#supplierID").empty();
                    $("#supplierID").selectpicker("refresh");
                    $.ajax({
                        type: "POST",
                        url: "https://dev-erp.traffic.org.uk/public/ajax/accounts/suppliers/ajax-supplier.php",
                        data: { "action" : "getCompanySpecificSuppliers", "companyid" : $(this).val(), "suppliertypeid" : 6 },
                        dataType: "json",
                        cache: false,
                        success: function(data) {
                             $.each(data, function (key, val) {
                                $("#supplierID").append($("<option>", {
                                    value: val.supplierID,
                                    text: val.name
                                }));
                            });
                            $("#supplierID").selectpicker("refresh");
                        }
                    });  
                    $("#appliedBy").empty();
                    $("#appliedBy").selectpicker("refresh");
                    $.ajax({
                        type: "POST",
                        url: "https://dev-erp.traffic.org.uk/public/ajax/system/users/ajax-user.php",
                        data: { "action" : "getCompanySpecificUsers", "companyid" : $(this).val()},
                        dataType: "json",
                        cache: false,
                        success: function(data) {
                             $.each(data, function (key, val) {
                                $("#appliedBy").append($("<option>", {
                                    value: val.userID,
                                    text: val.fullName
                                }));
                            });
                            $("#appliedBy").selectpicker("refresh");
                        }
                    }); 
                });

				$("#editQuote").on("init.field.fv", function(e, data) {
					// data.fv      --> The FormValidation instance
					// data.field   --> The field name
					// data.element --> The field element

					var $icon      = data.element.data("fv.icon"),
						options    = data.fv.getOptions(),                      // Entire options
						validators = data.fv.getOptions(data.field).validators; // The field validators

					if (validators.notEmpty && options.icon && options.icon.required) {
						// The field uses notEmpty validator
						// Add required icon
						$icon.addClass(options.icon.required).show();
					}
				}).formValidation({
					framework: "bootstrap",
					excluded: ":disabled",
					icon: {
						required: "far fa-asterisk",
						valid: "far fa-check",
						invalid: "far fa-times",
						validating: "far fa-sync-alt"
					},
					fields: {
						eventName: {
							row: ".col-sm-3",
							validators: {
								notEmpty: {
									message: "This field is required"
								}
							}
						},
						venue: {
							row: ".col-sm-3",
							validators: {
								notEmpty: {
									message: "This field is required"
								}
							}
						},
						eventStartDate: {
							row: ".col-sm-3",
							validators: {
								notEmpty: {
									message: "The event start date is required"
								},
								date: {
									format: "DD/MM/YYYY",
									message: "Please enter a valid date"
								}
							}
						},
						eventEndDate: {
							row: ".col-sm-3",
							validators: {
								notEmpty: {
									message: "The event end date is required"
								},
								date: {
									format: "DD/MM/YYYY",
									message: "Please enter a valid date"
								}
							}
						},
						customerID: {
							row: ".col-sm-9",
							validators: {
								notEmpty: {
									message: "The customer is required"
								},
								greaterThan: {
									value: 1,
									message: "The customer is required"
								}
							}
						},
						custContactName: {
							row: ".col-sm-3",
							validators: {
								notEmpty: {
									message: "This field is required"
								}
							}
						},
						custContactEmail: {
							row: ".col-sm-3",
							validators: {
								notEmpty: {
									message: "This field is required"
								}
							}
						},
						"tmTypeIDs[]": {
							row: ".col-md-9",
							validators: {
								choice: {
									min: 1,
									message: "Please choose at least 1 option"
								}
							}
						},
						roadName: {
							row: ".col-sm-5",
							validators: {
								notEmpty: {
									message: "This field is required"
								},
								stringLength: {
									max: 100,
									message: "This field must be less than 100 Characters"
								}
							}
						},
						location: {
							row: ".col-sm-4",
							validators: {
								notEmpty: {
									message: "This field is required"
								},
								stringLength: {
									max: 100,
									message: "This field must be less than 100 Characters"
								}
							}
						},
						councilAppliedBy: {
							row: ".col-sm-9",
							validators: {
								notEmpty: {
									message: "This field is required"
								}
							}
						},
					}
				}).on("err.field.fv", function(e, data) {
					if (data.fv.getInvalidFields().length > 0) {
						$("#saveQuoteStay").prop("disabled", true);
						$("#saveQuoteReturn").prop("disabled", true);
					}
				}).on("success.field.fv", function(e, data) {
					if (data.fv.getInvalidFields().length <= 0) {
						$("#saveQuoteStay").prop("disabled", false);
						$("#saveQuoteReturn").prop("disabled", false);
					}
				});

				if (!$.fn.DataTable.isDataTable("#importantCommentListDT")) {
					var oImportantCommentListDT = $("#importantCommentListDT").DataTable({
						ajax: {
							url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-commentList.php",
							type: "POST",
							data: { "reltypeid" : 10, "relationid" : "20392", "statusid" : "129" }
						},
						processing: true,
						serverSide: false,
						stateSave: true,
						autoWidth: false,
						paging: false,
						lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
						displayLength: 10,
						pagingType: "full_numbers",
						dom: "t",
						buttons: [
						],
						columns: [
							{ data: "dtCreatedDate", type: "date-uk" },
							{ data: "dtCreatedTime" },
							{ data: "dtComment" },
							{ data: "dtUploadedByName" }
						],
						order: [[0, "asc"], [1, "asc"] ],
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
				};

				$("#permitLocationID").on("change", function() {
					var permitLocationID = $(this).val();
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/permitlocations/ajax-permitLocation.php",
						data: { "action" : "getPermitLocationDetails", "permitlocationid" : permitLocationID },
						dataType: "json",
						cache: false,
						success: function(data){
							if(data != false){
								$("#roadName").val(data["addressLine1"]);
								$("#location").val(data["city"]);
								$("#easting").val(data["easting"]);
								$("#northing").val(data["northing"]);
								if(data['isPermitRequired'] == 1 ) {
									$("#permitReq").prop( "checked", true );
									$("#cadReq").prop( "checked", true );
								} else {
									$("#permitReq").prop( "checked", false );
									$("#cadReq").prop( "checked", false );
								}
							} else {
								$("#roadName").val("");
								$("#location").val("");
								$("#easting").val("");
								$("#northing").val("");
								$("#permitReq" ).val( 1 );
								$("#permitReq").prop( "checked", false );
								$("#cadReq" ).val( 1 );
								$("#cadReq").prop( "checked", false );
							}
						}
					});
				});

				$("#btnAddNewContact").on("click", function() {
					$("#dlgNewCustContact").on("init.field.fv", function(e, data) {
						// data.fv      --> The FormValidation instance
						// data.field   --> The field name
						// data.element --> The field element

						var $icon      = data.element.data("fv.icon"),
							options    = data.fv.getOptions(),                      // Entire options
							validators = data.fv.getOptions(data.field).validators; // The field validators

						if (validators.notEmpty && options.icon && options.icon.required) {
							// The field uses notEmpty validator
							// Add required icon
							$icon.addClass(options.icon.required).show();
						}
					}).formValidation({
						framework: "bootstrap",
						excluded: ":disabled",
						icon: {
							required: "far fa-asterisk",
							valid: "far fa-check",
							invalid: "far fa-times",
							validating: "far fa-sync-alt"
						},
						fields: {
							contactName: {
								row: ".col-md-10",
								validators: {
									notEmpty: {
										message: "Contact name is required"
									}
								}
							},
							contactEmailAddress: {
								row: ".col-md-10",
								validators: {
									notEmpty: {
										message: "Contact email is required"
									},
									emailAddress: {}
								}
							},
						}
					}).on("err.field.fv", function(e, data) {
						if (data.fv.getInvalidFields().length > 0) {
							$("#dlgNewCustContact button#btnSave").prop("disabled", true);
						}
					}).on("success.field.fv", function(e, data) {
						if (data.fv.getInvalidFields().length <= 0) {
							$("#dlgNewCustContact button#btnSave").prop("disabled", false);
						}
					});
					$("#dlgNewCustContact" ).modal( "show" ); 
					$("#dlgNewCustContact button#btnSave" ).unbind().on( "click" , function(e) {
						$("#dlgNewCustContact" ).formValidation("validate"); 
                        var formValidation = $("#dlgNewCustContact" ).data("formValidation");
                        if(formValidation.isValid()) {
                            $("#dlgNewCustContact button#btnSave" ).html( "Saving..." );
                            $("#dlgNewCustContact button#btnSave" ).prop( "disabled", true );
                            $.ajax({
                                type: "POST",
                                url: "https://dev-erp.traffic.org.uk/public/ajax/contacts/ajax-contact.php",
                                data: { "action" : "saveContact", "reltypeid" : "43", "relationid" : $("#customerID").val(), "postdata": $("form#updateContact").serialize() },
                                dataType: "json",
                                cache: false,
                                success: function(data){
                                    $("#dlgNewCustContact" ).formValidation("resetForm");
                                    $("#dlgNewCustContact .fa-asterisk" ).css({ 'display' : '',});
                                    $("#dlgNewCustContact .fa-asterisk" ).addClass( "far" );
                                    $("#dlgNewCustContact").modal( "hide" );
                                    $("#dlgNewCustContact button#btnSave" ).html( "Submit" );
                                    $("#dlgNewCustContact button#btnSave" ).prop( "disabled", false );
                                    $("#contactName, #contactPhoneNumber, #contactMobilePhone, #contactPhoneNumber, #contactEmailAddress" ).val( "" );
                                    if( data.state == "success" ) {									
                                        $("#custContactID").append($("<option>", {
                                            value: data.id,
                                            text: data.contactName
                                        }));
                                        $("#custContactID").selectpicker("val", data.id);
                                        $("#custContactID").selectpicker("refresh");
                                        $("#custContactNum").val(data.phoneNumber);
                                        $("#custContactEmail").val(data.emailAddress);
                                    }
                                }
                            });
                        }
					});
					$("#dlgNewCustContact button#btnCancel" ).unbind().on( "click" , function(e) {
						$("#dlgNewCustContact" ).modal( "hide" );
						$("#dlgNewCustContact button#btnSave" ).html( "Save" );
						$("#dlgNewCustContact button#btnSave" ).prop( "disabled", false );
						$("#dlgNewCustContact" ).formValidation("resetForm");
						$("#dlgNewCustContact .fa-asterisk" ).css({ 'display' : '',});
						$("#dlgNewCustContact .fa-asterisk" ).addClass( "far" );
						$("#contactName, #contactPhoneNumber, #contactMobilePhone, #contactPhoneNumber, #contactEmailAddress" ).val( "" );
					});
				});

				$("#popup_permitLocationID").on("change", function() {
					var permitLocationID = $(this).val();
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/permitlocations/ajax-permitLocation.php",
						data: { "action" : "getPermitLocationDetails", "permitlocationid" : permitLocationID },
						dataType: "json",
						cache: false,
						success: function(data){
							if(data != false){
								$("#popup_postcode").val(data["postcode"]);
								$("#popup_roadName").val(data["addressLine1"]);
								$("#popup_location").val(data["city"]);
								$("#popup_easting").val(data["easting"]);
								$("#popup_northing").val(data["northing"]);
							} else {
								$("#popup_postcode").val("");
								$("#popup_roadName").val("");
								$("#popup_location").val("");
								$("#popup_easting").val("");
								$("#popup_northing").val("");
							}
						}
					});
				});

				$("#supplierID").on( "change", function() {
					var supplierID = $(this).val();
					$.ajax({
						type: "POST",
                        url: "https://dev-erp.traffic.org.uk/public/ajax/accounts/suppliers/ajax-supplier.php",
                        data: {"action" : "getSupplierSites", "supplierid" : supplierID},
						cache: false,
						dataType: "json",
						success: function(data){
                            if(data != false) {
                                $("#suppSiteID").prop("disabled", false);
                                $("#suppSiteID").empty();
                                $.each(data, function (key, val) {
                                    if(val.isPrimarySite == null){
                                        $("#suppSiteID").append($("<option>", {
                                            value: val.siteID,
                                            text: val.fullSiteAddress
                                        }));
                                    }

                                });
                                $("#suppSiteID").selectpicker("refresh");
                            } else {
                                $("#suppSiteID").prop("disabled", true);
                                $("#suppSiteID").selectpicker("val", 0);
                                $("#suppSiteID").selectpicker("refresh");
                            }
                        }
					});
				});

				$("#customerID").on("change", function() {		
					var customerID = $(this).val();
					$.ajax({
						type: "POST",
					   url: "https://dev-erp.traffic.org.uk/public/ajax/contacts/ajax-contact.php",
						data: { "action" : "getContactsList", "reltypeid" : 43, "relationid" : customerID },
						cache: false,
						dataType: "json",
						success: function(data){
                            $("#custContactID").empty();
							$.each(data, function (key, val) {
								if(val.isPrimary == 1){
                                    $("#custContactID").append($("<option>", {
                                        value: val.contactID,
                                        text: val.contactName,
                                        "data-subtext": "Primary Contact",
                                        "selected": "selected"
                                    }));
                                } else {
                                    $("#custContactID").append($("<option>", {
                                        value: val.contactID,
                                        text: val.contactName
                                    }));
                                }
							});
							$("#custContactID").selectpicker("refresh");
						}
					});
				});
				$("#custContactID").on("change", function() {
					var contactID = $(this).val();
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/contacts/ajax-contact.php",
						data: { "action" : "getContactData", "contactid" : contactID },
						dataType: "json",
						cache: false,
						success: function(data){
							if(data != false){
								$("#custContactID").val(data["contactID"]);
								$("#custContactName").val(data["contactName"]);
								$("#custContactNum").val(data["phoneNumber"]);
								$("#custContactEmail").val(data["emailAddress"]);
							}
						}
					});
				});
				$("#cadReq").on("change", function() {
					 if($(this).is(":checked")) {

						 //create pop up to get additional info.
						 $("#cadDetails").modal( "show" );

						 $("#btnCadDetailCancel").unbind().on( "click" , function(e) {
							 $("#cadDetails").modal( "hide" );
							 $("#cadReq").prop("checked", false);
							 //clear inputs
							 $("#cadDueDate").val("");
							 $("#cadPriority").val("");
							 $("#cadNotes").val("");
						 });

						 $("#btnCadDetailSave").unbind().on( "click" , function(e) {
							 if ($("#cadDueDate").val() == "") {
								 $("#btnCadDetailSave").prop("disabled", true);
								 $("<p id=\"dueDateError\" style=\"margin-top: 10px; color: red;\">Due Date is Required</p>").hide().insertAfter("#cadDueDate").fadeIn(150);
								 setTimeout(function(e){
									 $("#dueDateError").fadeOut(150);
									 $("#btnCadDetailSave").prop("disabled", false);
								 }, 3000)
							 } else {
								 $("#btnCadDetailSave").html("Saving...");
								 $.ajax({
									 type: "POST",
									 url: "https://dev-erp.traffic.org.uk/public/ajax/works/ajax-saveCadDetails.php",
									 data: { "action" : "add", "dueDate" : $("#cadDueDate").val(), "priority" :  $("#cadPriority").val(), "notes" : $("#cadNotes").val(), "relationid" : 20392, "reltypeid" : 10 },
									 cache: false,
									 success: function(cadData){
										 $("#cadDetails").modal( "hide" );
										 $("#btnCadDetailSave").html("Save");
										 if(cadData){
											 $("<i class=\"fal fa-lg fa-edit\" id=\"editCadDetail\" style=\"margin:0 5px; line-height: 25px; cursor: pointer\"></i>").hide().insertAfter($("#cadReq").closest("label")).fadeIn(300);
											 $.smallBox({title:"Success:",content:"CAD Requested Successfully!",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
										 } else {
											 $.smallBox({title:"Error:",content:"CAD Details already exist for this job",color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
											 $("#cadReq").prop("checked", false);
										 }
									 }
								 });
							 }
						 });
					 } else {
						// check it exists
						 $.ajax({
							 type: "POST",
							 url: "https://dev-erp.traffic.org.uk/public/ajax/works/ajax-saveCadDetails.php",
							 data: { "action" : "checkRequestExist", "relationid" : 20392, "reltypeid" : 2},
							 cache: false,
							 success: function(data){
								if(data == 1){
									 $("#cadRemoveConfirm").modal( "show" );
									 $("#btnCadRemoveConfirmCancel").unbind().on( "click" , function(e) {
										 $("#cadRemoveConfirm").modal( "hide" );
										 $("#cadReq").prop("checked", true);
									 });
									 $("#btnCadRemoveConfirmSave").unbind().on( "click" , function(e) {
										$.ajax({
											type: "POST",
											url: "https://dev-erp.traffic.org.uk/public/ajax/works/ajax-saveCadDetails.php",
											data: { "action" : "remove", "relationid" : 20392, "reltypeid" : 10},
											cache: false,
											success: function(cadRemoveData){
												if(cadRemoveData){
													$("#cadDueDate").val("");
													$("#cadPriority").val("");
													$("#cadNotes").val("");
													$("#editCadDetail").remove();
													$("#cadRemoveConfirm").modal( "hide" );
													$.smallBox({title:"Success:",content:"CAD Details have been removed",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
												} else {
													$("#cadRemoveConfirm").modal( "hide" );
													$.smallBox({title:"Error:",content:"CAD job has already begun, please contact CAD to cancel this request",color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
													$("#cadReq").prop("checked", true);
												}
											}
										});
									}); 
								}
							}
						});
					}
				});

				$("body").unbind().on( "click" , "#editCadDetail", function(e) {
					 $("#cadDetails").modal( "show" );
					 $("#btnCadDetailCancel").unbind().on( "click" , function(e) {
						 $("#cadDetails").modal( "hide" );
					 });
					 $("#btnCadDetailSave").unbind().on( "click" , function(e) {
						 $("#btnCadDetailSave").html("Saving...");
						 $.ajax({
							 type: "POST",
							 url: "https://dev-erp.traffic.org.uk/public/ajax/works/ajax-saveCadDetails.php",
							 data: { "action" : "update", "dueDate" : $("#cadDueDate").val(), "priority" :  $("#cadPriority").val(), "notes" : $("#cadNotes").val(), "relationid" : 20392, "reltypeid" : 10},
							 cache: false,
							 success: function(cadUpdateData){
								 $("#cadDetails").modal( "hide" );
								 $("#btnCadDetailSave").html("Save");
								 if(!cadUpdateData){
									 $.smallBox({title:"Info:",content:"No changes made",color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
								 } else {
									 $.smallBox({title:"Success:",content:"CAD Details updated succesfully",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
								 }
							 }
						 });
					 });
				});

				$("input:radio[name=\"locationType\"]").change(function(){
					if($(this).val() == "multiple"){
						fnConvertLocation();
					} else if($(this).val() == "single"){
						fnRevertLocation();
					}
				});
                
				function fnLoadLocationListDT (){
					if (!$.fn.DataTable.isDataTable("#locationListDT")) {
						var oLocationTable = $("#locationListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/ajax-locationList.php",
								type: "POST",
								data: { "quoteid" : "20392" }
							},
							processing: true,
							serverSide: false,
							stateSave: false,
							autoWidth: true,
							scrollY: "15vh",
							scrollCollapse: true,
							paging: false,
							lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
							displayLength: -1,
							pagingType: "full_numbers",
							dom: "t<\"dt-toolbar-footer\"<\"col-xs-12 col-sm-12\"i>>",
							columns: [
								{ data: "dtDisplayOrder", className: "pointer" },
								{ data: "dtRoadName" },
								{ data: "dtLocation" },
								{ data: "dtPostcode" },
								{ data: "dtLocalAuthName" },
								{ data: "dtDistance" },
								{ data: "dtDuration" },
								{ data: "dtRowTools", orderable: false, searchable: false }
							],
							rowReorder: {
								dataSrc: "dtDisplayOrder"
							},
							order: [[0, "asc"]],
							language: {
								info: "<span class=\"pull-left\">Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span></span><span class=\"pull-right\" style=\"font-style:normal;\"><a href=\"javascript:void(0);\" class=\"addLocation btn btn-sm btn-primary\"><i class=\"fal fa-lg fa-plus-circle\"></i> Add Location</a></span>",
							},
							drawCallback: function( settings ) {
								$("[data-toggle~=\"tooltip\"]").tooltip({
									trigger: "hover",
									html: true
								});
							},
						});
						oLocationTable.on( "row-reorder", function ( e, diff, edit ) {
							for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
								var rowData = oLocationTable.row( diff[i].node ).data();

								//result += rowData["dtRoadName"] +" updated to be in position "+ diff[i].newData+ " (was "+diff[i].oldData+")<br>";

								var saveDistanceOrderData = new FormData();
								saveDistanceOrderData.append("action", "SaveOrdering");
								saveDistanceOrderData.append("locationid", rowData["dtLocationID"]);
								saveDistanceOrderData.append("newdisplayorder", diff[i].newData);

								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
									data: saveDistanceOrderData,
									cache: false,
									processData: false,
									contentType: false,
									success: function(data){
										fnUpdateMarkers();
										var arrData = data.split("|");
										if( arrData[0] == "success" ) {
											$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
											oQuoteExtraDetailTable.ajax.reload(null, false);
										} else if( arrData[0] == "danger" ) {
											$.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
										} else if(arrData[0] == "info") {
											$.smallBox({title:"Info:",content:arrData[1],color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
										}
									}
								});
							}

						});
						$("#dispMultipleLocationDetails ").on("click", "a.addLocation", function () {
							fnSaveLocation();
						});
						$("#locationListDT tbody ").on("click", "a.editLocation", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oLocationTable.row(dtTR).data();
							var id = dtRowData["dtLocationID"];
							fnSaveLocation( id );
						});
						$("#locationListDT tbody").on("click", "a.deleteLocation", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oLocationTable.row(dtTR).data();
							var id = dtRowData["dtLocationID"];
							markerCount = markerGroup.getObjects().length;
                            if (2 == 5){
                                var limit = 2;
                            } else {
                                var limit = 1; 
                            }
							if (markerCount > limit){
								$("#dlgDeleteLocation" ).modal( "show" );
								$("#dlgDeleteLocation button#btnYes" ).unbind().on( "click" , function(e) {
									$.ajax({
										type: "POST",
										url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
										data: { "action" : "deleteLocation", "locationid" : id, "quoteid" : "20392" },
										dataType: "json",
										cache: false,
										success: function(data){
											if(data.state == "success"){
												oLocationTable.row( dtTR ).remove().ajax.reload().draw();
                                                
                                                if (2 == 5){
                                                     markerGroup.removeObject( markerData["m"+data.startPointID] );
                                                     markerGroup.removeObject( markerData["m"+data.endPointID] );
                                                } else {
                                                    markerGroup.removeObject( markerData["m"+id] );
												    fnUpdateMarkers();
                                                }
												
												fnZoomLevel();
												fnRefreshLocationList();
												$("#dlgDeleteLocation" ).modal( "hide" );
											}
										}
									});
									$("#dlgDeleteLocation button#btnNo").unbind().on( "click" , function(e) {
										$("#dlgDeleteLocation").modal( "hide" );
									});
								});
							} else {
								$("#dlgDeleteLocationError" ).modal( "show" );
								$("#dlgDeleteLocationError button#btnRevert" ).unbind().on( "click" , function(e) {
                                    $("#dlgDeleteLocationError button#btnRevert" ).html( "Reverting..." );
                                    $("#dlgDeleteLocationError button#btnRevert" ).prop( "disabled", true );
									$.when(fnRevertLocation(id)).then(function(){
                                        $("#dlgDeleteLocationError" ).modal( "hide" );
                                        $("#dlgDeleteLocationError button#btnRevert" ).html( "Revert" );
                                        $("#dlgDeleteLocationError button#btnRevert" ).prop( "disabled", false ); 
                                    });
								});
								$("#dlgDeleteLocationError button#btnCancel").unbind().on( "click" , function(e) {
									$("#dlgDeleteLocationError").modal( "hide" );
								});
							}
						});
					};
				}

				if (!$.fn.DataTable.isDataTable("#quoteExtraDetailListDT")) {
					var oQuoteExtraDetailTable = $("#quoteExtraDetailListDT").DataTable({
						ajax: {
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteExtraDetailList.php",
							type: "POST",
							data: { "quoteid" : "20392" }
						},
						processing: true,
						serverSide: false,
						stateSave: true,
						autoWidth: false,
						lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
						displayLength: -1,
						pagingType: "full_numbers",
						dom: "<\"dt-toolbar-header\"<\"col-xs-12 col-sm-4\"f><\"col-xs-12 col-sm-4 text-center\"B><\" col-xs-12 col-sm-4\"l>r>t<\"dt-toolbar-footer\"<\"col-sm-6 col-xs-12\"i><\"col-xs-12 col-sm-6\"p>>",
						buttons: [
							{
								text: "<i class=\"fal fa-lg fa-plus\"></i>",
								titleAttr: "Add",
								action: function ( e, dt, node, config ) {
									fnSaveQuoteExtraDetail();
								}
							},
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
									$("#quoteExtraDetailListDT thead input").val("").change();
									$("#quoteExtraDetailListDT thead select").val("").change();
									$("#quoteExtraDetailListDT").DataTable().search("").draw();
								}
							}
						],
						columns: [
							{ data: "dtExtraDetailID", orderable: false, searchable: false, render: function (data, type, full, meta) { return "<label class=\"checkbox-inline\" style=\"padding-top:0px;\"><input type=\"checkbox\" class=\"checkbox style-0\" name=\"batchDel[]\" value=\"" + data + "\"><span></span></label>"; } },
							{ data: "dtDescription" },
							{ data: "dtCost", type: "num-fmt", searchable: false, render: function ( data, type, full, meta ) { return "£ " + data; } },
							{ data: "dtRowTools", orderable: false, searchable: false }
						],
						order: [[1, "asc"]],
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
					var state = oQuoteExtraDetailTable.state.loaded();
					if ( state ) {
						oQuoteExtraDetailTable.columns().eq( 0 ).each( function ( colIdx ) {
						var colSearch = state.columns[colIdx].search;
							if ( colSearch.search ) {
								$("#quoteExtraDetailListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
								$("#quoteExtraDetailListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
							}
						});
					}
					$("#quoteExtraDetailListDT thead th input[type=text]").on( "keyup change", function () {
						oQuoteExtraDetailTable
							.column( $(this).parent().index()+":visible" )
							.search( this.value )
							.draw();
					});
					$("#quoteExtraDetailListDT thead th select").on( "change", function () {
						oQuoteExtraDetailTable
							.column( $(this).parent().index()+":visible" )
							.search( this.value )
							.draw();
					});
					$("#quoteExtraDetailListDT tbody").on("click", "a.editQuoteExtraDetail", function () {
						var dtTR = $(this).parent("td").parent("tr");
						var dtRowData = oQuoteExtraDetailTable.row(dtTR).data();
						var extraDetailID = dtRowData["dtExtraDetailID"];
						fnSaveQuoteExtraDetail( extraDetailID );
					});
					$("#quoteExtraDetailListDT thead th #checkAll2" ).on( "click", function() {
						var rows = oQuoteExtraDetailTable.rows({ "search": "applied" }).nodes();
						$("input[type=\"checkbox\"]", rows).prop("checked", this.checked);
					});
					$("#quoteExtraDetailListDT tbody" ).on("click", "a.deleteQuoteExtraDetail", function () {
						var dtTR = $(this).parent("Label").parent("td").parent("tr");
						var dtRowData = oQuoteExtraDetailTable.row(dtTR).data();
						var extraDetailID = dtRowData["dtExtraDetailID"];
						$("#dlgDeleteQuoteExtraDetail" ).modal( "show" );
						$("#dlgDeleteQuoteExtraDetail button#btnYes" ).unbind().on( "click" , function(e) {
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteExtraDetail.php",
								data: { "action" : "deleteQuoteExtraDetail", "extradetailid" : extraDetailID },
								cache: false,
								success: function(data){
									if(data == true){
										$("#dlgDeleteQuoteExtraDetail" ).modal( "hide" );
										oQuoteExtraDetailTable.row( dtTR ).remove().ajax.reload().draw();
										$("input.checkAll2" ).prop("checked", false);
									}
								}
							});
						});
						$("#dlgDeleteQuoteExtraDetail button#btnNo").unbind().on( "click" , function(e) {
							$("#dlgDeleteQuoteExtraDetail").modal( "hide" );
						});
					});

					function fnSaveQuoteExtraDetail(id)
					{

						if( id > 0 ) {
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteExtraDetail.php",
								data: { "action" : "getQuoteExtraDetailData", "extradetailid" : id },
								dataType: "json",
								cache: false,
								success: function(data){
									if(data != false && data != null){
										$("#qedDescription" ).val( data['description'] );
										if(data['cost'] > 0) { dCost = parseFloat(data['cost']).toFixed(2) } else { dCost = '' }
										$("#qedCost" ).val( dCost );
									} else {
										$("#qedDescription" ).val( "" );
										$("#qedCost" ).val( "" );
									}
								}
							});
						}

						$("#dlgQuoteExtraDetails" ).modal( "show" );
						$("#dlgQuoteExtraDetails button#btnSave" ).unbind().on( "click" , function(e) {
							$("#dlgQuoteExtraDetails button#btnSave" ).html( "Saving..." );
							$("#dlgQuoteExtraDetails button#btnSave" ).prop( "disabled", true );
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteExtraDetail.php",
								data: { "action" : "saveQuoteExtraDetail", "extradetailid" : id, "quoteid" : "20392", "postdata" : $("#updateQuoteExtraDetail").serialize() },
								cache: false,
								success: function(data) {
									var arrData = data.split("|");
									$("#dlgQuoteExtraDetails" ).modal( "hide" );
									$("#dlgQuoteExtraDetails button#btnSave" ).html( "Save" );
									$("#dlgQuoteExtraDetails button#btnSave" ).prop( "disabled", false );
									$("#qedDescription, #qedCost" ).val( "" );
									if( arrData[0] == "success" ) {
										$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
										oQuoteExtraDetailTable.ajax.reload(null, false);
									} else if( arrData[0] == "danger" ) {
										$.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
									} else if(arrData[0] == "info") {
										$.smallBox({title:"Info:",content:arrData[1],color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
									}
								}
							});
							return false;
						});
						$("#dlgQuoteExtraDetails button#btnCancel" ).unbind().on( "click" , function(e) {
							$("#dlgQuoteExtraDetails" ).modal( "hide" );
							$("#dlgQuoteExtraDetails button#btnSave" ).html( "Save" );
							$("#dlgQuoteExtraDetails button#btnSave" ).prop( "disabled", false );
							$("#qedDescription, #qedCost" ).val( "" );
						});
					}
					$("#quoteExtraDetailListDT thead").on("click", "button#btnDelExtraQuoteDetails", function () {
						var chkDelRows = [];
						$("#quoteExtraDetailListDT tbody input[name='batchDel[]']:checked" ).each(function() {
							chkDelRows.push( $(this).val() );
						});
						$("#dlgDeleteQuoteExtraDetail" ).modal( "show" );
						$("#dlgDeleteQuoteExtraDetail button#btnYes" ).unbind().on( "click" , function(e) {
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteExtraDetail.php",
								data: { "action" : "deleteQuoteExtraDetail", "delrows" : chkDelRows },
								cache: false,
								success: function(data){
									if(data == true){
										$("#dlgDeleteQuoteExtraDetail" ).modal( "hide" );
										oQuoteExtraDetailTable.ajax.reload(null, false);
										$.smallBox({title:"Success:",content:"Item Deleted",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
									}
								}
							});
						});
						$("#dlgDeleteQuoteExtraDetail button#btnNo").unbind().on( "click" , function(e) {
							$("#dlgDeleteQuoteExtraDetail").modal( "hide" );
						});
					});
				};

				if (!$.fn.DataTable.isDataTable("#quoteDetailListDT")) {
					var oQuoteDetailListDT = $("#quoteDetailListDT").DataTable({
						ajax: {
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetailList.php",
							type: "POST",
							data: { "quoteid" : "20392" }
						},
						processing: true,
						serverSide: false,
						stateSave: true,
						autoWidth: false,
						lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
						displayLength: -1,
						pagingType: "full_numbers",
						dom: "<\"dt-toolbar-header\"<\"col-xs-12 col-sm-4\"f><\"col-xs-12 col-sm-4 text-center\"B><\" col-xs-12 col-sm-4\"l>r>t<\"dt-toolbar-footer\"<\"col-sm-6 col-xs-12\"i><\"col-xs-12 col-sm-6\"p>>",
						buttons: [
							{
								text: "<i class=\"fal fa-lg fa-plus\"></i>",
								titleAttr: "Add",
								action: function ( e, dt, node, config ) {
									fnSaveQuoteDetail();
								}
							},
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
									$("#quoteDetailListDT thead input").val("").change();
									$("#quoteDetailListDT thead select").val("").change();
									$("#quoteDetailListDT").DataTable().search("").draw();
								}
							},
							/*{
								text: "<i class=\"fal fa-lg fa-refresh\"></i>",
								titleAttr: "Sync Quote to Job",
								action: function ( e, dt, node, config ) {
									fnSyncQuoteToWork();
								}
							}*/
						],
						columns: [
							{ data: "dtQuoteDetailID", orderable: false, searchable: false, render: function (data, type, full, meta) { return "<label class=\"checkbox-inline\" style=\"padding-top:0px;\"><input type=\"checkbox\" class=\"delQuoteDetail checkbox style-0\" name=\"batchDel[]\" value=\"" + data + "\"><span></span></label>"; } },
							{ data: "dtJobCode" },
							{ data: "dtCustOrderNum", type: "natural" },
							{ data: "dtJobDescription", type: "natural" },
							{ data: "dtPrintDescription", type: "natural" },
							{ data: "dtQuoteDetailLocation", type: "natural" },
							{ data: "dtReqMen", className: "text-center", type: "num", orderable: false },
							{ data: "dtReqVehicles", className: "text-center", type: "num", orderable: false },
							{ data: "dtReqIPV", className: "text-center", type: "num", orderable: false },
							{ data: "dtReq7HalfVehicles", className: "text-center", type: "num", orderable: false },
							{ data: "dtNumSigns", className: "text-center", type: "num", orderable: false },
							{ data: "dtNumCones", className: "text-center", type: "num", orderable: false },
							{ data: "dtNumSandbags", className: "text-center", type: "num", orderable: false },
							{ data: "dtNumBarriers", className: "text-center", type: "num", orderable: false },
							{ data: "dtStartDate", type: "date-uk" },
							{ data: "dtSetupTime", type: "num-fmt" },
							{ data: "dtEndDate", type: "date-uk" },
							{ data: "dtEndTime", type: "num-fmt" },
							{ data: "dtQty", type: "num" },
							{ data: "dtCost", type: "num-fmt", searchable: false, render: function ( data, type, full, meta ) { return "£ " + data; } },
							{ data: "dtNetCost", type: "num-fmt", searchable: false, render: function ( data, type, full, meta ) { return "£ " + data; } },
							{ data: "dtDisplayQuote", searchable: false, className: "text-center", render: function (data, type, row, meta) { return "<label class=\"checkbox-inline\" style=\"padding-top:0px;\"><input type=\"checkbox\" name=\"qdDisplayQuote[]\" class=\"displayQuote checkbox style-0\" value=\"1\" " + ((data == 1) ? "checked=\"checked\"" : "") + " /><span></span></label>"; } },
							/*{ data: "dtSyncWorkDetail", searchable: false, className: "text-center", render: function (data, type, row, meta) { return "<label class=\"checkbox-inline\" style=\"padding-top:0px;\"><input type=\"checkbox\" name=\"qdSyncWorkDetail[]\" class=\"syncWorkDetail checkbox style-0\" value=\"1\" " + ((data == 1) ? "checked=\"checked\"" : "") + " /><span></span></label>"; } },*/
							{ data: "dtRowTools", orderable: false, searchable: false }
						],
						rowGroup: {
							startRender: function ( rows, group ) {
								
								if(group == "No group") {
									return "<span class=\"text-left col-md-2 dtGroupData\"><i class=\"fal fa-fw fa-calendar-alt\"></i><a name=\"TBA\"></a><strong>TBA</strong></span>";
								} else {
									moment.locale("en-gb");
									var fGrpDate = group.split("/")
										newGrpDate = fGrpDate[1]+"-"+fGrpDate[0]+"-"+fGrpDate[2];
									return "<span class=\"text-left col-md-2 dtGroupData\"><i class=\"fal fa-fw fa-calendar-alt\"></i> <a name=\"" +group+ "\"></a><strong>" +moment(newGrpDate).format("dddd DD/MM/YYYY")+ "</strong></span>";
								}
							},
							dataSrc: "dtStartDate"
						},
						footerCallback: function ( row, data, start, end, display ) {
							var api = this.api(), data;
							var intVal = function ( i ) { return typeof i === "string" ? i.replace(/[\£, ]/g, "")*1 : typeof i === "number" ? i : 0; };

							// Total over all pages
							if ( api.column( 20 ).data().length ){ all_quoteCostTotal = api.column( 20 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); } ) } else { all_quoteCostTotal = 0 };

							// Total over this page
							if ( api.column( 20 ).data().length ){ page_quoteCostTotal = api.column( 20, { page: "current"} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 ) } else { page_quoteCostTotal = 0 };

							// Update footer
							if(parseInt(page_quoteCostTotal) > 99) { $(api.column( 20 ).footer() ).html( "£ " + page_quoteCostTotal.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); } else { $(api.column( 20 ).footer() ).html( "£ " + page_quoteCostTotal ); }
						},
						order: [[14, "asc"],[16, "asc"],[15, "asc"],[17, "asc"]],
						language: {
							search: "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>_INPUT_</div>",
							searchPlaceholder: "Global Search ...",
							lengthMenu: "Records <span class=\"txt-color-darken\">Per</span> <span class=\"text-primary\">Page</span> _MENU_",
							info: "Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span>"
						}
					});

					if("single" == "multiple") {
						oQuoteDetailListDT.column( 5 ).visible( true );
					} else {
						oQuoteDetailListDT.column( 5 ).visible( false );
					}

					var state = oQuoteDetailListDT.state.loaded();
					if ( state ) {
						oQuoteDetailListDT.columns().eq( 0 ).each( function ( colIdx ) {
						var colSearch = state.columns[colIdx].search;
							if ( colSearch.search ) {
								$("#quoteDetailListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
								$("#quoteDetailListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
							}
						});
					}
					$("#quoteDetailListDT thead th input[type=text]").on( "keyup change", function () {
						oQuoteDetailListDT
							.column( $(this).parent().index()+":visible" )
							.search( this.value )
							.draw();
					});
					$("#quoteDetailListDT thead th select").on( "change", function () {
						oQuoteDetailListDT
							.column( $(this).parent().index()+":visible" )
							.search( this.value )
							.draw();
					});

					$("#quoteDetailListDT thead th #checkAll2" ).on( "click", function() {
						var rows = oQuoteDetailListDT.rows({ "search": "applied" }).nodes();
						$("input.delQuoteDetail", rows).prop("checked", this.checked);
					});

					$("#quoteDetailListDT tbody" ).on("change", "input.displayQuote", function () {
						var dtTR = $(this).parent("label").parent("td").parent("tr");
						var dtRowData = oQuoteDetailListDT.row(dtTR).data();
						var quoteDetailID = dtRowData["dtQuoteDetailID"];
						var chkSalesQuoteState = $(this).is(":checked") ? true : false;
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
							data: { "action" : "saveSalesQuoteDetail", "quotedetailid" : quoteDetailID, "value" : chkSalesQuoteState },
							cache: false,
							success: function(response){
								if(response == true) {
									$.smallBox({title:"Success:",content:"Sales Quote information updated!",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
									oQuoteDetailListDT.ajax.reload(null, false);
								} else {
									$.smallBox({title:"Error:",content:"Sales Quote information failed to update!",color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
									oQuoteDetailListDT.ajax.reload(null, false);
								}
							}
						});
					});
					
					$("#quoteDetailListDT tbody" ).on("change", "input.syncWorkDetail", function () {
						var dtTR = $(this).parent("label").parent("td").parent("tr");
						var dtRowData = oQuoteDetailListDT.row(dtTR).data();
						var quoteDetailID = dtRowData["dtQuoteDetailID"];
						var chkSalesQuoteState = $(this).is(":checked") ? true : false;
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
							data: { "action" : "saveSyncWorkDetail", "quotedetailid" : quoteDetailID, "value" : chkSalesQuoteState },
							cache: false,
							success: function(response){
								if(response == true) {
									$.smallBox({title:"Success:",content:"Sales Quote information updated!",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
									oQuoteDetailListDT.ajax.reload(null, false);
								} else {
									$.smallBox({title:"Error:",content:"Sales Quote information failed to update!",color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
									oQuoteDetailListDT.ajax.reload(null, false);
								}
							}
						});
					});

					$("#quoteDetailListDT tbody" ).on("click", "a.editQuoteDetail", function () {
						var dtTR = $(this).parent("td").parent("tr");
						var dtRowData = oQuoteDetailListDT.row(dtTR).data();
						var quoteDetailID = dtRowData["dtQuoteDetailID"];
						fnSaveQuoteDetail( quoteDetailID );
					});

					var date = $.datepicker.formatDate("dd/mm/yy", new Date());
					$("#qdStartDate").datepicker({
						//startDate: date,
						format: "dd/mm/yyyy",
						weekStart: 1,
						autoclose: true,
						clearBtn: true,
						todayHighlight: true
					}).on("changeDate", function(selected){
						$("#qdEndDate").datepicker("setStartDate", $("#qdStartDate").datepicker("getDate"));
						fnCalcQDJobPricing();
					});
					$("#qdEndDate").datepicker({
						format: "dd/mm/yyyy",
						weekStart: 1,
						autoclose: true,
						clearBtn: true,
						todayHighlight: true
					}).on("changeDate", function(selected){
						$("#qdStartDate").datepicker("setEndDate", $("#qdEndDate").datepicker("getDate"));
						fnCalcQDJobPricing();
					});

					$("#qdJobReqID" ).on("change", function() {
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
							data: { "action" : "getJobReqData", "jobreqid" : $(this ).val() },
							dataType: "json",
							cache: false,
							success: function(data){
								if(data != false && data != null){
									$("#qdDescription" ).val( decodeEntities(data['description']) );
									$("#qdPrintDescription" ).val( decodeEntities(data['printDescription']) );
									$("#qdWorkScope" ).val( decodeEntities(data['scopeOfWorks']) );
									$("#qdHealthSafety" ).val( decodeEntities(data['healthAndSafety']) );
									$("#qdReqMen" ).val( data['totalMenReq'] );
									$("#qdReqSigns" ).val( data['numSigns'] );
									$("#qdReqCones" ).val( data['numCones'] );
									$("#qdReqSandbags" ).val( data['numSandbags'] );
									$("#qdReqBarriers" ).val( data['numBarriers'] );
									if(data['jobCatID'] == 21 || data['jobCatID'] == 6 || data['jobReqID'] == 364 || data['jobReqID'] == 365 || 		data['jobReqID'] == 366 || data['jobReqID'] == 380 || data['jobReqID'] == 381) {
										$("#qdMenOnSite").prop( "checked", true );
									} else {
										$("#qdMenOnSite").prop( "checked", false );
									}
								} else {
									$("#qdDescription" ).val( "" );
									$("#qdPrintDescription" ).val( "" );
									$("#qdReqMen" ).val( "" );
									$("#qdReqSigns" ).val( "" );
									$("#qdReqCones" ).val( "" );
									$("#qdReqSandbags" ).val( "" );
									$("#qdReqBarriers" ).val( "" );
								}
							}
						});

						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
							data: { "action" : "getJobVehReqData", "jobreqid" : $(this ).val() },
							dataType: "json",
							cache: false,
							success: function(data){
								if(data != false && data != null){
									$("#qdReqVehicles" ).val( data['totalVehicles'] );
								} else {
									$("#qdReqVehicles" ).val( "" );
								}
							}
						});
					});

					$("#qdJobReqID, #qdReqMen, #qdReqVehicles, #qdStartTime, #qdEndTime, #qdMenOnSite" ).on("change", function() {
						fnCalcQDJobPricing();
					});

					$("input:radio[name=\"quoteDetailType\"]" ).on( "change" , function(e) {
						if ( $(this ).val() == "1" ) {
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).hide();
							$("#qdJobReqID").prop("disabled", true);
							$("#dispStartTime" ).hide();
							$("#dispEndTime" ).hide();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						} else if ( $(this ).val() == "2" ) {
							$("#dispMaintFreq" ).show();
							$("#dispJobReq" ).hide();
							$("#qdJobReqID").prop("disabled", true);
							$("#dispStartTime" ).hide();
							$("#dispEndTime" ).hide();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						} else if ( $(this ).val() == "3" ) {
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						} else if ( $(this ).val() == "4" ) {
							$("#dispMaintFreq" ).show();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						} else if ( $(this ).val() == "5" ) {
							$("#dispMaintFreq" ).show();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).show();
							$("#dispHireCost" ).show();
							$("#dispHireFreq" ).show();
							$("#lblCost" ).html("Install / Removal Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						} else if ( $(this ).val() == "6" ) {
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).hide();
							$("#qdJobReqID").prop("disabled", true);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).hide();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Install Date:");
							$("#lblEndDate" ).html("Removal Date:");
							$("#lblStartTime" ).html("Install Time:");
							$("#lblEndTime" ).html("Removal Time:");
						} else if ( $(this ).val() == "7" ) {
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).hide();
							$("#qdJobReqID").prop("disabled", true);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).hide();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Install Date:");
							$("#lblEndDate" ).html("Removal Date:");
							$("#lblStartTime" ).html("Install Time:");
							$("#lblEndTime" ).html("Removal Time:");
						} else if ( $(this ).val() == "8" ) {
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						} else {
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
						}
					});
					
					function fnSyncQuoteToWork() {
						
						//load a modal that asks if the user is sure 
						$("#dlgSyncQuoteDetails" ).modal( "show" );
						$("#dlgSyncQuoteDetails button#btnYes" ).unbind().on( "click" , function(e) {
							//ajax request 
							//close modal on finish and maybe open job in new tab
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quote.php",
								data: { "action" : "updateWorkDetails", "quoteid" :  20392 },
								dataType: "json",
								cache: false,
								success: function(data){
									$("#dlgSyncQuoteDetails" ).modal( "hide" );
									//load job in new tab to see changes
									
									if(data['messageType'] == "success") { // redirect to job 
										$.smallBox({title:"Success:",content:data['message'],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
										
										setTimeout(function(){
										  	window.open("https://dev-erp.traffic.org.uk/pages/admin/works/edit.php?workid=" + data['workID'], "_blank");
										}, 2000);
										
									} else if(data['messageType'] == "error") {
										$.smallBox({title:"Error:",content:data['message'],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
									}
								}
							});
						});
						
						$("#dlgSyncQuoteDetails button#btnNo" ).unbind().on( "click" , function(e) {
							$("#dlgSyncQuoteDetails" ).modal( "hide" );
						});
					}

					function fnCalcQDJobPricing()
					{
						var qdJobReqID = $("#qdJobReqID" ).val(),
							qdReqMen = $("#qdReqMen" ).val(),
							qdReqVehicles = $("#qdReqVehicles" ).val(),
							qdStartDate = $("#qdStartDate" ).val(),
							qdEndDate = $("#qdEndDate" ).val(),
							qdStartTime = $("#qdStartTime" ).val(),
							qdEndtime = $("#qdEndTime" ).val(),
							workTool = $("input:radio[name=\"workDetailType\"]:checked").val();

						if($("#qdMenOnSite").is( ":checked") ) {
							var qdMenOnSite = 1
						} else {
							var qdMenOnSite = 0
						}

						if(qdJobReqID > 0) {
							if(($.trim(qdStartDate) != "" && $.trim(qdEndDate) != "") || ($.trim(qdStartDate) != "" && $.trim(qdEndDate) != "" && $.trim(qdStartTime) != "" && $.trim(qdEndtime) != "")) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
									data: { "action" : "getJobPricing", "jobreqid" :  qdJobReqID, "customerid" : "646", "reqmen" : qdReqMen, "reqvehicles" : qdReqVehicles, "startdate" : qdStartDate, "enddate" :  qdEndDate, "starttime" : qdStartTime, "endtime" : qdEndtime, "worktool" : workTool, "menonsite" : qdMenOnSite },
									cache: false,
									success: function(data){
										if(data != false) {
											var arrData = data.split("-|-");
											$("#qdQty" ).val( arrData[0] );
											$("#qdCost" ).val( arrData[1] );
											var qdPrintDesc = $("#qdPrintDescription" ).val();
											qdPrintDesc = qdPrintDesc.split("Pricing is inclusive of")[0];
											$("#qdPrintDescription" ).val( qdPrintDesc + arrData[2] );
										}
									}
								});
							}
						}
					}
					function fnCalcEqipPricing()
					{
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
							data: { "action" : "updateEquipCharge", "customerid" : "646", "quoteid" : "20392" },
							cache: false,
							success: function(data){
								if(data != false) {
									oQuoteDetailListDT.ajax.reload(null, false);
								}
							}
						});
					}

					function fnSaveQuoteDetail(id)
					{
						$("#dlgQuoteDetailDetails").on("init.field.fv", function(e, data) {
							// data.fv      --> The FormValidation instance
							// data.field   --> The field name
							// data.element --> The field element

							var $icon      = data.element.data("fv.icon"),
								options    = data.fv.getOptions(),                      // Entire options
								validators = data.fv.getOptions(data.field).validators; // The field validators

							if (validators.notEmpty && options.icon && options.icon.required) {
								// The field uses notEmpty validator
								// Add required icon
								$icon.addClass(options.icon.required).show();
							}
						}).formValidation({
							framework: "bootstrap",
							excluded: ":disabled",
							icon: {
								required: "far fa-asterisk",
								valid: "far fa-check",
								invalid: "far fa-times",
								validating: "far fa-sync-alt"
							},
							fields: {
								qdLocationID: {
									row: ".col-sm-10",
									validators: {
										notEmpty: {
											message: "The location is required"
										},
										greaterThan: {
											value: 1,
											message: "The location is required"
										}
									}
								}
							}
						}).on("err.field.fv", function(e, data) {
							if (data.fv.getInvalidFields().length > 0) {
								$("#dlgQuoteDetailDetails button#btnSave").prop("disabled", true);
							}
						}).on("success.field.fv", function(e, data) {
							if (data.fv.getInvalidFields().length <= 0) {
								$("#dlgQuoteDetailDetails button#btnSave").prop("disabled", false);
							}
						});

						if( id > 0 ) {
							$("#dispTools" ).hide();
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
								data: { "action" : "getQuoteDetailData", "quotedetailid" : id },
								dataType: "json",
								cache: false,
								success: function(data){
									if(data != false && data != null){
										$("#qdJobReqID" ).selectpicker("val",data['jobReqID']);
										if(data['custOrderNum'] != "") {
											$("#qdCustOrderNum" ).selectpicker("val",data['custOrderNum']);
										} else {
											$("#qdCustOrderNum" ).selectpicker("val","");
										}
										$("#qdLocationID" ).selectpicker("val",data['locationID']);
										$("#qdDescription" ).val( data['jobDescription'] );
										$("#qdPrintDescription" ).val( data['printDescription'] );
										$("#qdWorkScope" ).val( data['scopeOfWorks'] );
										$("#qdHealthSafety" ).val( data['healthAndSafety'] );
										$("#qdReqMen" ).val( ((data['reqMen'] > 0) ? data['reqMen'] : '') );
										$("#qdReqVehicles" ).val( ((data['reqVehicles'] > 0) ? data['reqVehicles'] : '') );
										$("#qdIPV" ).val( ((data['reqIPV'] > 0) ? data['reqIPV'] : '') );
										$("#qdSevenHalf" ).val( ((data['req7HalfVehicles'] > 0) ? data['req7HalfVehicles'] : '') );
										$("#qdReqSigns" ).val( ((data['numSigns'] > 0) ? data['numSigns'] : '') );
										$("#qdReqCones" ).val( ((data['numCones'] > 0) ? data['numCones'] : '') );
										$("#qdReqSandbags" ).val( ((data['numSandbags'] > 0) ? data['numSandbags'] : '') );
										$("#qdReqBarriers" ).val( ((data['numBarriers'] > 0) ? data['numBarriers'] : '') );
										if(data['startDate'] != null) { dStartDate = data['startDate'] } else { dStartDate = '' }
										$("#qdStartDate" ).val( dStartDate );
										if(data['setupTime'] != "00:00") { dSetupTime = data['setupTime'] } else { dSetupTime = '' }
										$("#qdStartTime" ).val( dSetupTime );
										if(data['endDate'] != null) { dEndDate = data['endDate'] } else { dEndDate = '' }
										$("#qdEndDate" ).val( dEndDate );
										if(data['endTime'] != "00:00") { dEndTime = data['endTime'] } else { dEndTime = '' }
										$("#qdEndTime" ).val( dEndTime );
										$("#qdNumberOfShifts" ).val( data['numberOfShifts'] );
										$("#qdQty" ).val( data['qty'] );
										if(data['cost'] > 0) { dCost = parseFloat(data['cost']).toFixed(2) } else { dCost = '' }
										$("#qdCost" ).val( dCost );
										$("#quoteDetailType" ).val( data['quoteDetailType'] );
										if(data['menOnSite'] == 1 ) {
											$("#qdMenOnSite").prop( "checked", true );
										} else {
											$("#qdMenOnSite").prop( "checked", false );
										}
                                        if(data['allowWeekend'] == 1 ) {
											$("#qdAllowWeekend").prop( "checked", true );
										} else {
											$("#qdAllowWeekend").prop( "checked", false );
										}
                                        $("input[name='qdShiftType'][value='"+data['shiftType']+"']").prop("checked", true);
										if( 0 == 1 && $("#qdCustOrderNum").val() == "" ) {
											$("#qdCustOrderNum").selectpicker("val","");
										}

									} else {
										$("#qdJobReqID,#qdCustOrderNum,#qdLocationID" ).selectpicker("val","0");
                                        $("#qdDescription,#qdPrintDescription,#qdWorkScope,#qdHealthSafety,#qdReqMen,#qdReqVehicles,#qdIPV,#qdSevenHalf,#qdReqSigns,#qdReqCones,#qdReqSandbags,#qdReqBarriers,#qdStartDate,#qdStartTime,#qdEndDate,#qdEndTime,#qdNumberOfShifts,#qdQty,#qdCost" ).val( "" );
										$("#quoteDetailType" ).val( "0" );
                                        $("input:radio[name=\"quoteDetailType\"]" ).prop( "checked", false );
										$("#qdMenOnSite" ).val( 1 );
										$("#qdMenOnSite").prop( "checked", false );
										$("#qdAllowWeekend").prop( "checked", false );
                                        $("input[name='qdShiftType']:checked").prop("checked", false);
									}
								}
							});
						} else {
							$("#dispTools" ).show();
							$("#dispMaintFreq" ).hide();
							$("#dispJobReq" ).show();
							$("#qdJobReqID").prop("disabled", false);
							$("#dispStartTime" ).show();
							$("#dispEndTime" ).show();
							$("#dispPrintJobDesc" ).show();
							$("#dispMaintenanceCost" ).hide();
							$("#dispHireCost" ).hide();
							$("#dispHireFreq" ).hide();
							$("#lblCost" ).html("Cost:");
							$("#lblStartDate" ).html("Start Date:");
							$("#lblEndDate" ).html("End Date:");
							$("#lblStartTime" ).html("On:");
							$("#lblEndTime" ).html("Off:");

							$("#qdStartDate, #qdEndDate" ).mask("99/99/9999");
							$("#qdStartDate, #qdEndDate" ).datepicker({format:"dd/mm/yyyy",weekStart:1,autoclose:true,clearBtn:true,todayHighlight:true});

							if( 0 == 1 ) {
								$("#qdCustOrderNum").selectpicker("val","");
							}
						}

						$("#dlgQuoteDetailDetails" ).modal( "show" );
						$("#dlgQuoteDetailDetails button#btnSave" ).unbind().on( "click" , function(e) {

							$(".error-message-start").remove();
							$(".error-message-end").remove();
							$(".error-message-time").remove();

							// if ($("#qdStartDate").val() !== "" && $("#qdEndDate").val() !== "" && $("#qdStartTime").val() !== "") {
								$("#dlgQuoteDetailDetails" ).formValidation("validate"); 
								var formValidation = $("#dlgQuoteDetailDetails" ).data("formValidation");
								if(formValidation.isValid()) {
									$("#dlgQuoteDetailDetails button#btnSave" ).html( "Saving..." );
									$("#dlgQuoteDetailDetails button#btnSave" ).prop( "disabled", true );
									$.ajax({
										type: "POST",
										url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
										data: { "action" : "saveQuoteDetail", "quotedetailid" : id, "quoteid" : "20392", "postdata" : $("#updateQuoteDetail").serialize() },
										cache: false,
										success: function(data) {
											var arrData = data.split("|");
											$("#dlgQuoteDetailDetails" ).modal( "hide" );
											$("#dlgQuoteDetailDetails button#btnSave" ).html( "Save" );
											$("#dlgQuoteDetailDetails button#btnSave" ).prop( "disabled", false );
											$("#dlgQuoteDetailDetails" ).formValidation("resetForm");
											$("#dlgQuoteDetailDetails .fa-asterisk" ).css({ 'display' : '',});
											$("#dlgQuoteDetailDetails .fa-asterisk" ).addClass( "far" );
											$("#qdJobReqID,#qdCustOrderNum,#qdLocationID" ).selectpicker("val","0");
                                            $("#qdDescription,#qdPrintDescription,#qdWorkScope,#qdHealthSafety,#qdReqMen,#qdReqVehicles,#qdIPV,#qdSevenHalf,#qdReqSigns,#qdReqCones,#qdReqSandbags,#qdReqBarriers,#qdStartDate,#qdStartTime,#qdEndDate,#qdEndTime,#qdNumberOfShifts,#qdQty,#qdCost" ).val( "" );
                                            $("#quoteDetailType" ).val( "0" );
                                            $("input:radio[name=\"quoteDetailType\"]" ).prop( "checked", false );
                                            $("#qdMenOnSite" ).val( 1 );
                                            $("#qdMenOnSite").prop( "checked", false );
                                            $("#qdAllowWeekend").prop( "checked", false );
                                            $("input[name='qdShiftType']:checked").prop("checked", false);
											fnCalcEqipPricing();
											oQuoteDetailListDT.ajax.reload(null, false);
											if ($.fn.DataTable.isDataTable("#plannerListDT")) {
												$("#plannerListDT").DataTable().ajax.reload(null, false);
											}
											if( arrData[0] == "success" ) {
												$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
												oQuoteDetailListDT.ajax.reload(null, false);
											} else if( arrData[0] == "danger" ) {
												$.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
											} else if(arrData[0] == "info") {
												$.smallBox({title:"Info:",content:arrData[1],color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
											}
										}
									});
									return false;
								}
							// } else {
							//     if($("#qdStartDate").val() == ""){
							//         $("#qdStartDate").after("<p class=\"error-message-start\" style=\"padding-top:5px; color: red;\">Start Date is Required</p>");
							//     } else {
							//         $(".error-message-start").remove();
							//     }
							//     if($("#qdEndDate").val() == ""){
							//         $("#qdEndDate").after("<p class=\"error-message-end\" style=\"padding-top:5px; color: red;\">End Date is Required</p>");
							//     } else {
							//         $(".error-message-end").remove();
							//     }
							//     if($("#qdStartTime").val() == ""){
							//         $("#qdStartTime").after("<p class=\"error-message-time\" style=\"padding-top:5px; color: red;\">Setup Time is Required</p>");
							//     } else {
							//         $(".error-message-time").remove();
							//     }
							// }

						});
						$("#dlgQuoteDetailDetails button#btnCancel" ).unbind().on( "click" , function(e) {
							$("#dlgQuoteDetailDetails" ).modal( "hide" );
							$("#dlgQuoteDetailDetails button#btnSave" ).html( "Save" );
							$("#dlgQuoteDetailDetails button#btnSave" ).prop( "disabled", false );
							$("#dlgQuoteDetailDetails" ).formValidation("resetForm");
							$("#dlgQuoteDetailDetails .fa-asterisk" ).css({ 'display' : '',});
							$("#dlgQuoteDetailDetails .fa-asterisk" ).addClass( "far" );
							$("#qdJobReqID,#qdCustOrderNum,#qdLocationID" ).selectpicker("val","0");
                            $("#qdDescription,#qdPrintDescription,#qdWorkScope,#qdHealthSafety,#qdReqMen,#qdReqVehicles,#qdIPV,#qdSevenHalf,#qdReqSigns,#qdReqCones,#qdReqSandbags,#qdReqBarriers,#qdStartDate,#qdStartTime,#qdEndDate,#qdEndTime,#qdNumberOfShifts,#qdQty,#qdCost" ).val( "" );
                            $("#quoteDetailType" ).val( "0" );
                            $("input:radio[name=\"quoteDetailType\"]" ).prop( "checked", false );
                            $("#qdMenOnSite" ).val( 1 );
                            $("#qdMenOnSite").prop( "checked", false );
                            $("#qdAllowWeekend").prop( "checked", false );
                            $("input[name='qdShiftType']:checked").prop("checked", false);
						});
					}

					$("#quoteDetailListDT thead").on("click", "button#btnDelQuoteDetails", function () {
						var chkDelRows = [];
						$("#quoteDetailListDT tbody input[name='batchDel[]']:checked" ).each(function() {
							chkDelRows.push( $(this).val() );
						});
						$("#dlgDelQuoteDetails" ).modal( "show" );
						$("#dlgDelQuoteDetails button#btnYes" ).unbind().on( "click" , function(e) {
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
								data: { "action" : "deleteQuoteDetails", "quoteid" : "20392", "delrows" : chkDelRows },
								cache: false,
								success: function(data){
									var arrData = data.split("|");
									$("#dlgDelQuoteDetails" ).modal( "hide" );
									if( arrData[0] == "success" ) {
										$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
										fnCalcEqipPricing();
										oQuoteDetailListDT.ajax.reload(null, false);
										if ($.fn.DataTable.isDataTable("#plannerListDT")) {
											$("#plannerListDT").DataTable().ajax.reload(null, false);
										}
									} else if( arrData[0] == "danger" ) {
										$.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
									} else if(arrData[0] == "info") {
										$.smallBox({title:"Info:",content:arrData[1],color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
									}
								}
							});
						});
						$("#dlgDelQuoteDetails button#btnNo").unbind().on( "click" , function(e) {
							$("#dlgDelQuoteDetails").modal( "hide" );
						});
					});
				};

				$("#genApplication").on( "change", function() {
					if($(this).is(":checked")) {
						$("#genEventSignApp").prop("disabled",true);
					} else {
						$("#genEventSignApp").prop("disabled",false);
					}
				});
				$("#genEventSignApp").on( "change", function() {
					if($(this).is(":checked")) {
						$("#genApplication").prop("disabled",true);
					} else {
						$("#genApplication").prop("disabled",false);
					}
				});

				$("#statusID").on("change", function() {
					var statusID = $(this).val();
					if(statusID == 48) {
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/accounts/customers/ajax-cust.php",
							data: { "action" : "getCustDetails", "customerid" : $("#customerID").val() },
							dataType: "json",
							cache: false,
							success: function(data){
								if(data != false){
									if(data['finPkgAccRef'] != '') {
										$("#statusID" ).val( 48 );
									} else {
										$.smallBox({title:"Error:",content:"The status cant be set to won unless the customer is on sage!",color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
										$("#statusID" ).val( 47 );
									}
								}
							}
						});
					}
				});

				$("#supplierID,#suppSiteID,#localAuthAppTypeID").on( "change", function() {
					checkAppDoc($("#supplierID").val(),$("#suppSiteID").val(), $("#localAuthAppTypeID").val());
				});

				$("input:radio[name=\"worksClassification\"]").on("change", function() {
					if ($(this).val() == "4") {
						$("#dispFaultCode" ).show();
					} else {
						$("#dispFaultCode" ).hide();
						$("#faultCode" ).val("");
					}
				});

				$("input").keypress(function (e) {
					var charCode = e.charCode || e.keyCode;
					if (charCode  == 13) {
						return false;
					}
				});

				Number.prototype.formatMoney = function(c, d, t){
					var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "." : d, t = t == undefined ? "," : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
					return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
				};

				function loadPlannerDetails(){
					 if (!$.fn.DataTable.isDataTable("#plannerListDT")) {
						var oPlannerListDT = $("#plannerListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/works/ajax-plannerList.php",
								type: "POST",
								data: { "quoteid" : "20392" }
							},
							processing: true,
							serverSide: false,
							stateSave: true,
							autoWidth: false,
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
										$("#plannerListDT thead input").val("").change();
										$("#plannerListDT thead select").val("").change();
										$("#plannerListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{ data: "dtDate", type: "date-uk" },
								{ data: "dtEmployeeNames" },
								{ data: "dtVehicleNames" },
								{ data: "dtTrailerNames" },
								{ data: "dtJobDesc"},
								{ data: "dtNotes"},
								{ data: "dtSetupTimeOrder", visible: false },
								{ data: "dtSetupTime", type: "num-fmt" },
								{ data: "dtEndTime", type: "num-fmt" },
								{ data: "dtRowTools", orderable: false, searchable: false }
							],
							drawCallback: function( settings ) {
								$("[data-toggle~=\"tooltip\"]").tooltip({
									trigger: "hover",
									html: true
								});
							},
							order: [[0, "asc"], [6, "asc"]],
							language: {
								search: "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>_INPUT_</div>",
								searchPlaceholder: "Global Search ...",
								lengthMenu: "Records <span class=\"txt-color-darken\">Per</span> <span class=\"text-primary\">Page</span> _MENU_",
								info: "Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span>"
							},
							initComplete: function (settings, json) {
								if($("#plannerListDT tbody tr").find('td.dataTables_empty').length) {
									$("#bdgPlannerCnt").html(0);
								} else {
									var plannerDataCount = $("#plannerListDT tbody tr").length
									$("#bdgPlannerCnt").html(plannerDataCount);
								}
							}
						});

						var state = oPlannerListDT.state.loaded();
						if ( state ) {
							oPlannerListDT.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$("#plannerListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
									$("#plannerListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#plannerListDT thead th input[type=text]").on( "keyup change", function () {
							oPlannerListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#plannerListDT thead th select").on( "change", function () {
							oPlannerListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});

						$("#plannerListDT tbody").on("click", "a.deletePlanner", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oPlannerListDT.row(dtTR).data();
							var plannerID = dtRowData["dtPlannerID"];
							$("#dlgDeletePlanner" ).modal( "show" );
							$("#dlgDeletePlanner button#btnYes" ).unbind().on( "click" , function(e) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/logistics/planners/ajax-planner.php",
									data: { "action" : "deletePlanner", "plannerid" : plannerID },
									cache: false,
									success: function(data){
										if(data == true){
											$.smallBox({title:"Success:",content:"Planner / Timesheet entries have been removed!",color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
											$("#dlgDeletePlanner" ).modal( "hide" );
											oPlannerListDT.ajax.reload(null, false);
										}
									}
								});
							});
							$("#dlgDeletePlanner button#btnNo").unbind().on( "click" , function(e) {
								$("#dlgDeletePlanner").modal( "hide" );
							});
						});
					};
				}

				function loadCADDetails() {
					if (!$.fn.DataTable.isDataTable("#planTable")) {
						var planTable = $("#planTable").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/cad/ajax-cadPlanList.php",
								type: "POST",
								data: {"reltypeid": 10, "relationid": 20392}
							},
							processing: true,
							serverSide: false,
							stateSave: true,
							autoWidth: false,
							lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
							displayLength: -1,
							pagingType: "full_numbers",
							dom: "<\"dt-toolbar-header\"<\"col-xs-12 col-sm-4\"f><\"col-xs-12 col-sm-4 text-center\"B><\" col-xs-12 col-sm-4\"l>r>t<\"dt-toolbar-footer\"<\"col-sm-6 col-xs-12\"i><\"col-xs-12 col-sm-6\"p>>",
							buttons: [
							],
							columns: [
								{ data: "dtPlanNum", width: "60px" },
								{ data: "dtTitle" },
								{ data: "dtCadDrawingDuration", width: "140px"},
								{ data: "dtAuthorisedByName", width: "110px" },
								{ data: "dtAuthorisedDate", type: "date-uk", width: "90px" },
								{ data: "dtAuthorisedTime", width: "90px" },
								{ data: "dtAuthRequestedByName", width: "120px" },
								{ data: "dtAuthRequestedDate", type: "date-uk", width: "120px" },
								{ data: "dtAuthRequestedTime", width: "120px" },
								{ data: "dtCadDrawingCount", width: "90px"},
								{ data: "dtStatusName", width: "120px"},
								{ data: "dtDownload", width: "90px", className: "text-center"}
							],
							drawCallback: function( settings ) {
								$("[data-toggle~=\"tooltip\"]").tooltip({
									animation: true,
									trigger: "hover",
									html: true
								});
							},
							order: [[0, "asc"]],
							language: {
								search: "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>_INPUT_</div>",
								searchPlaceholder: "Global Search ...",
								lengthMenu: "Records <span class=\"txt-color-darken\">Per</span> <span class=\"text-primary\">Page</span> _MENU_",
								info: "Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span>"
							},
							initComplete: function (settings, json) {
								if($("#planTable tbody tr").find('td.dataTables_empty').length) {
									$("#bdgCadCnt").html(0);
								} else {
									var cadDataCount = $("#planTable tbody tr").length
									$("#bdgCadCnt").html(cadDataCount);
								}
							}
						});
					};
				}
				function loadLocalAuthDetails() {
					if (!$.fn.DataTable.isDataTable("#localAuthAppListDT")) {
						var oLocalAuthListDT = $("#localAuthAppListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthAppList.php",
								type: "POST",
								data: { "reltypeid" : 10, "relationid" : "20392" }
							},
							processing: true,
							serverSide: false,
							stateSave: true,
							autoWidth: false,
							lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
							displayLength: -1,
							pagingType: "full_numbers",
							dom: "<\"dt-toolbar-header\"<\"col-xs-12 col-sm-4\"f><\"col-xs-12 col-sm-4 text-center\"B><\" col-xs-12 col-sm-4\"l>r>t<\"dt-toolbar-footer\"<\"col-sm-6 col-xs-12\"i><\"col-xs-12 col-sm-6\"p>>",
							buttons: [
								{
									text: "<i class=\"fal fa-lg fa-plus\"></i>",
									titleAttr: "Add",
									action: function ( e, dt, node, config ) {
										fnSaveLocalAuthApp();
									}
								},
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
										$("#localAuthAppListDT thead input").val("").change();
										$("#localAuthAppListDT thead select").val("").change();
										$("#localAuthAppListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{ data: "dtLocalAuthName" },
								{ data: "dtlocalAuthDistrictName" },
								{ data: "dtLocalAuthAppTypeName" },
								{ data: "dtAppliedDate", type: "date-uk" },
								{ data: "dtReceivedDate", type: "date-uk" },
								{ data: "dtChaseDate", type: "date-uk" },
								{ data: "dtDateChased", type: "date-uk" },
								{ data: "dtComments" },
								{ data: "dtAppliedByName" },
								{ data: "dtCreatedByName" },
								{ data: "dtAmount", render: function ( data, type, full, meta ) { return "£ " + data; }, type: "num-fmt", searchable: false },
								{ data: "dtPONumber" },
								{ data: "dtRowTools", orderable: false, searchable: false }
							],
							order: [[1, "asc"]],
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
							},
							initComplete: function (settings, json) {
								if($("#localAuthAppListDT tbody tr").find('td.dataTables_empty').length) {
									$("#bdgLocalAuthCnt").html(0);
								} else {
									var localAuthDataCount = $("#localAuthAppListDT tbody tr").length
									$("#bdgLocalAuthCnt").html(localAuthDataCount);
								}
							}
						});

						var state = oLocalAuthListDT.state.loaded();
						if ( state ) {
							oLocalAuthListDT.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$("#localAuthAppListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
									$("#localAuthAppListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#localAuthAppListDT thead th input[type=text]").on( "keyup change", function () {
							oLocalAuthListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#localAuthAppListDT thead th select").on( "change", function () {
							oLocalAuthListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#localAuthAppListDT tbody").on("click", "a.editLocalAuthApp", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oLocalAuthListDT.row(dtTR).data();
							var authAppID = dtRowData["dtAuthAppID"];
							fnSaveLocalAuthApp( authAppID );
						});

						$("#localAuthAppListDT tbody").on("click", "a.emailApp", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oLocalAuthListDT.row(dtTR).data();
							var authAppID = dtRowData["dtAuthAppID"];

							var defaultSubject = "TMS Council Application for Main Street, Mattersey, DN10 5DT.";

							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
								data: { "action" : "getContactEmails", "authappid" : authAppID},
								cache: false,
								success: function(data){
									if(data != "") {
										$("#emailAddress").val(data);
									}
								}
							});

							$("#dlgEmailApplication").find(".modal-body").append( "<form class=\"form-horizontal\" method=\"POST\" action=\"\"><div class=\"form-group\"><label for=\"emailAddress\" class=\"col-sm-3 control-label\">Email Address:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"emailAddress\" name=\"emailAddress\" row=\"4\"></textarea></div></div><div class=\"form-group\"><label for=\"subject\" class=\"col-sm-3 control-label\">Subject:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"subject\" name=\"subject\" row=\"4\">" + defaultSubject + "</textarea></div></div><div class=\"form-group\"><label for=\"emailBody\" class=\"col-sm-3 control-label\">Email Body:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"emailBody\" name=\"emailBody\" rows=\"10\">Good morning\n\nPlease find attached Council Application form and any relevant CAD Plans/Schedules.</textarea></div></div><div class=\"form-group\"><label for=\"emailAttachment\" class=\"col-sm-3 control-label\">Attachment(s):</label><div class=\"col-sm-9\"><select class=\"form-control\" id=\"docIDs\" name=\"docIDs[]\" multiple><optgroup label=\"Document List\"></select></div></div></form>" );

							$("#dlgEmailApplication").modal("show");

							$("#docIDs").selectpicker({
								liveSearch : true,
								liveSearchNormalize: true,
								dropupAuto: false,
								width : "100%",
								size: 10
							});

							$("#dlgEmailApplication button#btnSend").unbind().on( "click" , function(e) {
								$("#dlgEmailApplication button#btnSend").html( "Sending..." );
								$("#dlgEmailApplication button#btnSend").prop("disabled", true);
								var emailAddress = $("#emailAddress").val();
								var emailSubject = $("#subject").val();
								var emailBody = $("#emailBody").val();

								if( $("#docIDs :selected").length > 0) {
									var documentIDs = [];
									$("#docIDs :selected").each(function(i, selected) {
										documentIDs[i] = $(selected).val();
									});
								}

								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
									data: { "action" : "emailApplication", "authappid" : authAppID, "emailaddresses" : emailAddress, "emailbody" : emailBody, "emailsubject" : emailSubject, "documentids" : documentIDs },
									cache: false,
									success: function(data){
										$("#dlgEmailApplication button#btnSend").html("Send");
										$("#dlgEmailApplication button#btnSend").prop("disabled", false);
										var arrData = data.split("|");
										if( arrData[0] == "success" ) {
											$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
											$("#dlgEmailApplication").modal("hide");
											$("#dlgEmailApplication").removeData("bs.modal");
											$("#dlgEmailApplication").find(".modal-body").empty();
										} else if( arrData[0] == "danger" ) {
											$.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"}); 
										}
									}
								});
							});
							$("#dlgEmailApplication button#btnCancel").unbind().on( "click" , function(e) {
								$("#dlgEmailApplication button#btnSend").html("Send");
								$("#dlgEmailApplication button#btnSend").prop("disabled", false);
								$("#dlgEmailApplication").modal("hide");
								$("#dlgEmailApplication").removeData("bs.modal");
								$("#dlgEmailApplication").find(".modal-body").empty();
							});
						});

						$("#localAuthAppListDT tbody").on("click", "a.deleteLocalAuthApp", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oLocalAuthListDT.row(dtTR).data();
							var authAppID = dtRowData["dtAuthAppID"];
							$("#dlgDeleteLocalAuthApp" ).modal( "show" );
							$("#dlgDeleteLocalAuthApp button#btnYes" ).unbind().on( "click" , function(e) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
									data: { "action" : "deleteLocalAuthApp", "authappid" : authAppID },
									cache: false,
									success: function(data){
										if(data == true){
											$("#dlgDeleteLocalAuthApp" ).modal( "hide" );
											oLocalAuthListDT.row( dtTR ).remove().ajax.reload().draw();
											if($("#localAuthAppListDT tbody tr").find('td.dataTables_empty').length) {
												$("#bdgLocalAuthCnt").html(0);
											} else {
												var localAuthDataCount = $("#localAuthAppListDT tbody tr").length
												$("#bdgLocalAuthCnt").html(localAuthDataCount);
											}
										}
									}
								});
							});
							$("#dlgDeleteLocalAuthApp button#btnNo").unbind().on( "click" , function(e) {
								$("#dlgDeleteLocalAuthApp").modal( "hide" );
							});
						});
						function fnSaveLocalAuthApp(id)
						{
							$("#dlgLocalAuthAppDetails").on("init.field.fv", function(e, data) {
								// data.fv      --> The FormValidation instance
								// data.field   --> The field name
								// data.element --> The field element

								var $icon      = data.element.data("fv.icon"),
									options    = data.fv.getOptions(),                      // Entire options
									validators = data.fv.getOptions(data.field).validators; // The field validators

								if (validators.notEmpty && options.icon && options.icon.required) {
									// The field uses notEmpty validator
									// Add required icon
									$icon.addClass(options.icon.required).show();
								}
							}).formValidation({
								framework: "bootstrap",
								excluded: ":disabled",
								icon: {
									required: "far fa-asterisk",
									valid: "far fa-check",
									invalid: "far fa-times",
									validating: "far fa-sync-alt"
								},
								fields: {
									supplierID: {
										row: ".col-sm-10",
										validators: {
											notEmpty: {
												message: "The council/highway is required"
											},
											greaterThan: {
												value: 1,
												message: "The council/highway is required"
											}
										}
									},
									localAuthAppTypeID: {
										row: ".col-sm-10",
										validators: {
											notEmpty: {
												message: "The application type is required"
											},
											greaterThan: {
												value: 1,
												message: "The application type is required"
											}
										}
									},
									appLocationID: {
										row: ".col-sm-10",
										validators: {
											notEmpty: {
												message: "The location is required"
											},
											greaterThan: {
												value: 1,
												message: "The location is required"
											}
										}
									},
									/*appliedDate: {
										row: ".col-md-3",
										validators: {
											notEmpty: {
												message: "The applied date is required"
											},
											date: {
												format: "DD/MM/YYYY",
												message: "The applied date is required"
											}
										}
									},*/
									appliedBy: {
										row: ".col-sm-10",
										validators: {
											notEmpty: {
												message: "The applied by field is required"
											},
											greaterThan: {
												value: 1,
												message: "The applied by field is required"
											}
										}
									}
								}
							}).on("err.field.fv", function(e, data) {
								if (data.fv.getInvalidFields().length > 0) {
									$("#dlgLocalAuthAppDetails button#btnSave").prop("disabled", true);
								}
							}).on("success.field.fv", function(e, data) {
								if (data.fv.getInvalidFields().length <= 0) {
									$("#dlgLocalAuthAppDetails button#btnSave").prop("disabled", false);
								}
							});

							/*$("#appliedDate" ).on( "change", function() {
							   $("#dlgLocalAuthAppDetails").formValidation("revalidateField", "appliedDate");
							});*/
							
							
							$("#appUpload").attr("data-content","Drag & Drop (or click) to upload your file");

							$("#appUpload").change(function(){
								var fileName = $(this).val().split("\\");
								$(this).attr("data-content","File: "+fileName[2]+"");
							});

							if( id > 0 ) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
									data: { "action" : "getLocalAuthAppData", "authappid" : id },
									dataType: "json",
									cache: false,
									success: function(data){
										if(data != false && data != null){
											if(data['suppSiteID'] != null && data['suppSiteID'] != 0) {
												
												$.ajax({
                                                    type: "POST",
                                                    url: "https://dev-erp.traffic.org.uk/public/ajax/accounts/suppliers/ajax-supplier.php",
                                                    data: {"action" : "getSupplierSites", "supplierid" : supplierID},
                                                    cache: false,
                                                    dataType: "json",
                                                    success: function(data){
                                                        if(data != false) {
                                                            $("#suppSiteID").prop("disabled", false);
                                                            $("#suppSiteID").empty();
                                                            $.each(data, function (key, val) {
                                                                if(val.isPrimarySite == null){
                                                                    $("#suppSiteID").append($("<option>", {
                                                                        value: val.siteID,
                                                                        text: val.fullSiteAddress
                                                                    }));
                                                                }

                                                            });
                                                            $("#suppSiteID").selectpicker("refresh");
                                                        } else {
                                                            $("#suppSiteID").prop("disabled", true);
                                                            $("#suppSiteID").selectpicker("val", 0);
                                                            $("#suppSiteID").selectpicker("refresh");
                                                        }
                                                    }
                                                });
												
												setTimeout(function(){
													$("#suppSiteID").selectpicker("val", data['suppSiteID'] );
												}, 1500);
											
												$("#supplierID").selectpicker("val", data['supplierID'] );
												
											} else {
												$("#supplierID").selectpicker("val", data['supplierID'] );
											}
											$("#appLocationID").selectpicker("val", data['supplierID'] );
											$("#localAuthAppTypeID" ).selectpicker("val", data['localAuthAppTypeID'] );
											if(data['appliedDate'] != null) { dAppliedDate = data['appliedDate'] } else { dAppliedDate = '' }
											$("#appliedDate" ).val( dAppliedDate );
											if(data['receivedDate'] != null) { dReceivedDate = data['receivedDate'] } else { dReceivedDate = '' }
											$("#receivedDate" ).val( dReceivedDate );
											if(data['chaseDate'] != null) { dChaseDate = data['chaseDate'] } else { dChaseDate = '' }
											$("#chaseDate" ).val( dChaseDate );
											if(data['dateChased'] != null) { dDateChased = data['dateChased'] } else { dDateChased = '' }
											$("#dateChased" ).val( dDateChased );
											$("#localAuthComment" ).val( data['comments'] );
											$("#poNumber" ).val( data['poNumber'] );
											$("#appliedBy" ).selectpicker( "val", data['appliedBy'] );
											if(data['amount'] > 0) { dAmount = parseFloat(data['amount']).toFixed(2) } else { dAmount = '' }
											$("#amount" ).val( dAmount );
											
											if(data['sysGeneratedPO'] == 1) {
												$("#poNumber").prop( "disabled", true );
											} else {
												$("#poNumber").prop( "disabled", false );
											}
											
											if(data['genApplication'] == 1 ) {
												$("#genApplication").prop( "checked", true );
											} else {
												$("#genApplication").prop( "checked", false );
											}
											if(data['genPO'] == 1 ) {
												$("#genPO").prop( "checked", true );
											} else {
												$("#genPO").prop( "checked", false );
											}
											if(data['genEventSignApp'] == 1 ) {
												$("#genEventSignApp").prop( "checked", true );
											} else {
												$("#genEventSignApp").prop( "checked", false );
											}
											$("#appUpload" ).val( "" );
											if(data['poNumber'] != null && data['poNumber'] != "") {
												$("#genPO").prop("disabled", true);
												$("#genPO").attr("title", "PO Number Already Generated");
											}
											if(data['docFileName'] != null && data['docFileName'] != "") {
												$("#appUpload" ).attr("data-content","File: "+data['docFileName']+"");
												$("#genApplication").prop("disabled", true);
												$("#genApplication").attr("title", "Application Already Generated");
												$("#genEventSignApp").prop("disabled", true);
												$("#genEventSignApp").attr("title", "Application Already Generated");
											}
										} else {
											$("#supplierID").selectpicker("val", "0" );
											$("#appliedBy" ).selectpicker( "val", "0" );
											$("#suppSiteID").selectpicker("val", "0" );
											$("#appliedDate" ).val( "" );
											$("#receivedDate" ).val( "" );
											$("#chaseDate" ).val( "" );
											$("#dateChased" ).val( "" );
											$("#appUpload" ).val( "" );
											$("#poNumber" ).val( "" );
											$("#genApplication" ).val( "1" );
											$("#genApplication" ).prop( "checked", false );
											$("#genEventSignApp" ).val( "1" );
											$("#genEventSignApp" ).prop( "checked", false );
											$("#genPO" ).val( "1" );
											$("#genPO" ).prop( "checked", false );
											$("#localAuthComment" ).val( "" );
											$("#appliedBy" ).val( "" );
											$("#amount" ).val( "" );
										}
									}
								});
								$("#dlgUploadApp").show();
								$("#dlgAppEdit").show();
								$("#dlgAppAdd").hide();
								$("#dispCustInvAddress").hide();
							} else {
								checkAppAmount();
								$("#dlgUploadApp").hide();
								$("#dispCustInvAddress").show();
								$("#dlgAppEdit").hide();
								$("#dlgAppAdd").show();
								$("#appliedDate, #receivedDate, #chaseDate, #dateChased" ).mask("99/99/9999");
								$("#appliedDate, #receivedDate, #chaseDate, #dateChased" ).datepicker({format:"dd/mm/yyyy",autoclose:true,clearBtn:true,todayHighlight:true});
							}

							$("#dlgLocalAuthAppDetails" ).modal( "show" );
							setTimeout(function(){
								checkAppDoc($("#supplierID").val(),$("#suppSiteID").val(), $("#localAuthAppTypeID").val());
							}, 500);
							$("#dlgLocalAuthAppDetails button#btnSave" ).unbind().on( "click" , function(e) {
								$("#dlgLocalAuthAppDetails" ).formValidation("validate"); 
								var formValidation = $("#dlgLocalAuthAppDetails" ).data("formValidation");
								if(formValidation.isValid()) {
									var saveAppFormData = new FormData();
										saveAppFormData.append("action", "saveLocalAuthApp");
										saveAppFormData.append("authappid", id);
										saveAppFormData.append("reltypeid", 10);
										saveAppFormData.append("relationid", 20392);
										saveAppFormData.append("postdata", $("form#updateLocalAuthApp").serialize());
										saveAppFormData.append("file", $("#appUpload")[0].files[0]);
									$("#dlgLocalAuthAppDetails button#btnSave" ).html( "Saving..." );
									$("#dlgLocalAuthAppDetails button#btnSave" ).prop( "disabled", true );
									$.ajax({
										type: "POST",
										url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
										data: saveAppFormData,
										cache: false,
										processData: false,
										contentType: false,
										dataType: "json",
										success: function(data) {
											
											$("#dlgLocalAuthAppDetails" ).modal( "hide" );
											$("#dlgLocalAuthAppDetails button#btnSave" ).html( "Save" );
											$("#dlgLocalAuthAppDetails button#btnSave" ).prop( "disabled", false );
											$("#dlgLocalAuthAppDetails" ).formValidation("resetForm");
											$("#dlgLocalAuthAppDetails .fa-asterisk" ).css({ 'display' : '',});
											$("#dlgLocalAuthAppDetails .fa-asterisk" ).addClass( "far" );
											$("#supplierID").selectpicker("val", "0" );
											$("#suppSiteID").selectpicker("val", "0" );
											$("#suppSiteID" ).selectpicker("val", "0" );
											$("#localAuthAppTypeID" ).selectpicker("val", "0" );
											$("#appLocationID").selectpicker("val", "0" );
											$("#amount" ).val( "" );
											$("#poNumber" ).val( "" );
											$("#appUpload" ).val( "" );
											$("#genApplication" ).val( "1" );
											$("#genApplication" ).prop( "checked", false );
											$("#genPO" ).val( "1" );
											$("#genPO" ).prop( "checked", false );
											$("#appliedBy" ).selectpicker( "val", "0" );
											$("#genEventSignApp" ).val( "1" );
											$("#poNumber").prop( "disabled", false );
											$("#suppSiteID").empty();
											$("#genEventSignApp" ).prop( "checked", false );
											$("#genPO").prop("disabled", false);
											$("#genEventSignApp").prop("disabled", false);
											$("#genApplication").prop("disabled", false);
											$(".error-message-application-missing").remove();
											$(".error-message-application-completed").remove();
											$(".error-message-po").remove();
											$(".error-message-po-noSage").remove();
											$("#localAuthComment, #appliedDate, #receivedDate, #chaseDate, #dateChased" ).val( "" );
											
											var sageInUse = false;
											$.each(data, function(i, data) {
												if(data.state == "success") {
													$.smallBox({
														title: "Success:",
														content: data.message,
														color: "#739E73",
														timeout: 8000,
														iconSmall: "fal fa-check shake animated"
													});
												} else if (data.state == "danger") {
													$.smallBox({
														title: "Error: "+data.title,
														content: data.message,
														color: "#C46A69",
														iconSmall: "fa fa-exclamation shake animated"
													});
													if(data.type == "email"){
														emailError = true;
													}
												} else if (data.state == "info") {
													$.smallBox({
														title: "Info: ",
														content: data.message,
														color: "#3276B1",
														iconSmall: "fa fa-info shake animated"
													});
												}
												if(data.type == "sageInUse"){
													sageInUse = true;
												}
											});
											oLocalAuthListDT.ajax.reload(null, false);
											setTimeout(function(){
												if($("#localAuthAppListDT tbody tr").find('td.dataTables_empty').length) {
													$("#bdgLocalAuthCnt").html(0);
												} else {
													var localAuthDataCount = $("#localAuthAppListDT tbody tr").length
													$("#bdgLocalAuthCnt").html(localAuthDataCount);
												}
											}, 800);
										}
									});
									return false;
								}
							});
							$("#dlgLocalAuthAppDetails button#btnCancel" ).unbind().on( "click" , function(e) {
								$("#dlgLocalAuthAppDetails" ).modal( "hide" );
								$("#dlgLocalAuthAppDetails button#btnSave" ).html( "Save" );
								$("#dlgLocalAuthAppDetails button#btnSave" ).prop( "disabled", false );
								$("#dlgLocalAuthAppDetails" ).formValidation("resetForm");
								$("#dlgLocalAuthAppDetails .fa-asterisk" ).css({ 'display' : '',});
								$("#dlgLocalAuthAppDetails .fa-asterisk" ).addClass( "far" );
								$(".error-message-application-missing").remove();
								$(".error-message-application-compleated").remove();
								$(".error-message-po").remove();
								$(".error-message-po-noSage").remove();
								$("#supplierID").selectpicker("val", "0" );
								$("#suppSiteID").selectpicker("val", "0" );
								$("#poNumber" ).val( "" );
								$("#appUpload" ).val( "" );
								$("#suppSiteID" ).selectpicker("val", "0" );
								$("#appLocationID").selectpicker("val", "0" );
								$("#amount" ).val( "" );
								$("#appliedBy" ).selectpicker( "val", "0" );
								$("#genPO" ).val( "1" );
								$("#poNumber").prop( "disabled", false );
								$("#suppSiteID").empty();
								$("#genPO" ).prop( "checked", false );
								$("#genApplication" ).val( "1" );
								$("#genApplication" ).prop( "checked", false );
								$("#genPO").prop("disabled", false);
								$("#genApplication").prop("disabled", false);
								$("#genEventSignApp").prop("disabled", false);
								$("#genEventSignApp" ).val( "1" );
								$("#genEventSignApp" ).prop( "checked", false );
								$("#localAuthComment, #appliedDate, #receivedDate, #chaseDate, #dateChased" ).val( "" );
								$("#localAuthAppTypeID" ).selectpicker("val", "0" );
							});
						}
					};
				}
				function loadDocumentDetails() {
					if (!$.fn.DataTable.isDataTable("#documentListDT")) {
						var oDocumentListDT = $("#documentListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/documents/ajax-documentList.php",
								type: "POST",
								data: { "reltypeid" : 10, "relationid" : "20392" }
							},
							processing: true,
							serverSide: false,
							stateSave: true,
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
										$("#documentListDT thead input").val("").change();
										$("#documentListDT thead select").val("").change();
										$("#documentListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{ data: "dtTitle" },
								{ data: "dtDocCatName" },
								{ data: "dtIncludeVanPack" },
								{ data: "dtIsCADPlan" },
								{ data: "dtUploadedDate", type: "date-uk" },
								{ data: "dtUploadedTime" },
								{ data: "dtUploadedByName" },
								{ data: "dtExtension" },
								{ data: "dtRowTools", orderable: false }
							],
							drawCallback: function( settings ) {
								$("[data-toggle~=\"tooltip\"]").tooltip({
									animation: true,
									trigger: "hover",
									html: true
								});
							},
							order: [[3, "asc"]],
							language: {
								search: "<div class=\"input-group\"><span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-search\"></i></span>_INPUT_</div>",
								searchPlaceholder: "Global Search ...",
								lengthMenu: "Records <span class=\"txt-color-darken\">Per</span> <span class=\"text-primary\">Page</span> _MENU_",
								info: "Showing <span class=\"txt-color-darken\">_START_</span> to <span class=\"txt-color-darken\">_END_</span> of <span class=\"text-primary\">_TOTAL_</span>"
							},
							initComplete: function (settings, json) {
								if($("#documentListDT tbody tr").find('td.dataTables_empty').length) {
									$("#bdgDocCnt").html(0);
								} else {
									var docDataCount = $("#documentListDT tbody tr").length
									$("#bdgDocCnt").html(docDataCount);
								}
							}
						});

						var state = oDocumentListDT.state.loaded();
						if ( state ) {
							oDocumentListDT.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$("#documentListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
									$("#documentListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#documentListDT thead th input[type=text]").on( "keyup change", function () {
							oDocumentListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#documentListDT thead th select").on( "change", function () {
							oDocumentListDT
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});

						$("#documentListDT thead th #dt_fltr_4").daterangepicker({
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
						$("#documentListDT thead th #dt_fltr_4").on("apply.daterangepicker", function(ev, picker) {
							  $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
							  oDocumentListDT.draw();
						});
						$("#documentListDT thead th #dt_fltr_4").on("cancel.daterangepicker", function(ev, picker) {
							  $(this).val("");
							  oDocumentListDT.draw();
						});
						$.fn.dataTableExt.afnFiltering.push(
							function( oSettings, aData, iDataIndex ) {
								var str_dateRange = $("#documentListDT thead th #dt_fltr_4").val();
								if(str_dateRange != "") {
									var arr_dateRange = str_dateRange.split(" - ");
									var f_startDate = arr_dateRange[0].split("/");
									var filterStartDate = f_startDate[2] + "-" + f_startDate[1] + "-" + f_startDate[0];
									var f_endDate = arr_dateRange[1].split("/");
									var filterEndDate = f_endDate[2] + "-" + f_endDate[1] + "-" + f_endDate[0];

									var iDateRangeCol = 3;
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

						$("#documentListDT tbody").on("click", "a.editDocument", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oDocumentListDT.row(dtTR).data();
							var documentID = dtRowData["dtDocumentID"];
							fnSaveDocument( documentID );
						});
						$("#documentListDT tbody").on("click", "a.deleteDocument", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oDocumentListDT.row(dtTR).data();
							var dtRowIndex = dtRowData["dtDocumentID"];
							$("#dlgDeleteDocument" ).modal( "show" );
							$("#dlgDeleteDocument button#btnYes" ).unbind().on( "click" , function(e) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/documents/ajax-document.php",
									data: { "action" : "deleteFile", "docid" : dtRowIndex },
									cache: false,
									success: function(data){
										if(data == true){
											oDocumentListDT.row( dtTR ).remove().ajax.reload().draw();
											$("#dlgDeleteDocument").modal( "hide" );
											if($("#documentListDT tbody tr").find('td.dataTables_empty').length) {
												$("#bdgDocCnt").html(0);
											} else {
												var docDataCount = $("#documentListDT tbody tr").length
												$("#bdgDocCnt").html(docDataCount);
											}
										}
									}
								});
							});
							$("#dlgDeleteDocument button#btnNo").unbind().on( "click" , function(e) {
								$("#dlgDeleteDocument").modal( "hide" );
							});
						});

						function fnSaveDocument(id)
						{
							 $("#dlgDocumentDetails").on("init.field.fv", function(e, data) {
								// data.fv      --> The FormValidation instance
								// data.field   --> The field name
								// data.element --> The field element

								var $icon      = data.element.data("fv.icon"),
									options    = data.fv.getOptions(),                      // Entire options
									validators = data.fv.getOptions(data.field).validators; // The field validators

								if (validators.notEmpty && options.icon && options.icon.required) {
									// The field uses notEmpty validator
									// Add required icon
									$icon.addClass(options.icon.required).show();
								}
							}).formValidation({
								framework: "bootstrap",
								excluded: ":disabled",
								icon: {
									required: "far fa-asterisk",
									valid: "far fa-check",
									invalid: "far fa-times",
									validating: "far fa-sync-alt"
								},
								fields: {
									title: {
										row: ".col-sm-10",
										validators: {
											notEmpty: {
												message: "The title is required"
											}
										}
									},
									docCatID: {
										row: ".col-sm-10",
										validators: {
											notEmpty: {
											},
											greaterThan: {
												value: 1,
												message: "The document category is required"
											}
										}
									},
								}
							}).on("err.field.fv", function(e, data) {
								if (data.fv.getInvalidFields().length > 0) {
									$("#dlgDocumentDetails button#btnSave").prop("disabled", true);
								}
							}).on("success.field.fv", function(e, data) {
								if (data.fv.getInvalidFields().length <= 0) {
									$("#dlgDocumentDetails button#btnSave").prop("disabled", false);
								}
							});

							if( id > 0 ) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/documents/ajax-document.php",
									data: { "action" : "getFileData", "docid" : id },
									dataType: "json",
									cache: false,
									success: function(data){
										if(data != false && data != null){
											$("#title" ).val( data['title'] );
											$("#docCatID" ).val( data['docCatID'] );
											$("#docFileName" ).html( data['docFileName'] );
											$("#uploadedDate" ).html( data['uploadedDate'] );
											$("#uploadedBy" ).html( data['uploadedByName'] );
											if(data['isCADPlan'] == 1 ) {
												$("#isCADPlan").prop( "checked", true );
											} else {
												$("#isCADPlan").prop( "checked", false );
											}
											if(data['includeVanPack'] == 1 ) {
												$("#includeVanPack").prop( "checked", true );
											} else {
												$("#includeVanPack").prop( "checked", false );
											}
										} else {
											$("#title" ).val( "" );
											$("#docCatID" ).val( "0" );
											$("#docFileName" ).val( "" );
											$("#uploadedDate" ).val( "" );
											$("#uploadedBy" ).val( "" );
											$("#isCADPlan" ).val( "1" );
											$("#isCADPlan" ).prop( "checked", false );
											$("#includeVanPack" ).val( "1" );
											$("#includeVanPack" ).prop( "checked", false );
										}
									}
								});
							}

							$("#dlgDocumentDetails" ).modal( "show" );
							$("#dlgDocumentDetails button#btnSave" ).unbind().on( "click" , function(e) {
								$("#dlgDocumentDetails" ).formValidation("validate"); 
								var formValidation = $("#dlgDocumentDetails" ).data("formValidation");
								if(formValidation.isValid()) {
									$("#dlgDocumentDetails button#btnSave" ).html( "Saving..." );
									$("#dlgDocumentDetails button#btnSave" ).prop( "disabled", true );
									$.ajax({
										type: "POST",
										url: "https://dev-erp.traffic.org.uk/public/ajax/documents/ajax-document.php",
										data: { "action" : "saveFile", "docid" : id, "postdata" : $("#updateDocument").serialize() },
										cache: false,
										success: function(data) {
											var arrData = data.split("|");
											$("#dlgDocumentDetails" ).modal( "hide" );
											$("#dlgDocumentDetails button#btnSave" ).html( "Save" );
											$("#dlgDocumentDetails button#btnSave" ).prop( "disabled", false );
											$("#dlgDocumentDetails" ).formValidation("resetForm");
											$("#dlgDocumentDetails .fa-asterisk" ).css({ 'display' : '',});
											$("#dlgDocumentDetails .fa-asterisk" ).addClass( "far" );
											$("#isConfidential" ).val( "1" );
											$("#isConfidential" ).prop( "checked", false );
											$("#docCatID" ).val( "0" );
											$("#title, #docFileName, #uploadedDate, #uploadedBy" ).val( "" );
											if( arrData[0] == "success" ) {
												$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
												oDocumentListDT.ajax.reload(null, false);
												if($("#documentListDT tbody tr").find('td.dataTables_empty').length) {
														$("#bdgDocCnt").html(0);
												} else {
													var docDataCount = $("#documentListDT tbody tr").length
													$("#bdgDocCnt").html(docDataCount);
												}
												$("#bdgDocCnt").html(oDocumentListDT.data().count());
											} else if( arrData[0] == "danger" ) {
												$.smallBox({title:"Error:",content:arrData[1],color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
											} else if(arrData[0] == "info") {
												$.smallBox({title:"Info:",content:arrData[1],color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
											}
										}
									});
									return false;
								}
							});
							$("#dlgDocumentDetails button#btnCancel" ).unbind().on( "click" , function(e) {
								$("#dlgDocumentDetails" ).modal( "hide" );
								$("#dlgDocumentDetails button#btnSave" ).html( "Save" );
								$("#dlgDocumentDetails button#btnSave" ).prop( "disabled", false );
								$("#dlgDocumentDetails" ).formValidation("resetForm");
								$("#dlgDocumentDetails .fa-asterisk" ).css({ 'display' : '',});
								$("#dlgDocumentDetails .glyphicon-asterisk" ).addClass( "glyphicon" )
								$("#isConfidential" ).val( "1" );
								$("#isConfidential" ).prop( "checked", false );
								$("#docCatID" ).val( "0" );
								$("#title, #docFileName, #uploadedDate, #uploadedBy" ).val( "" );
							});
						}

						$("#file-uploads").uploadifive({
							debug    			: true,
							auto      			: true,
							uploadScript      	: "https://dev-erp.traffic.org.uk/public/ajax/documents/ajax-document.php",
							method   			: "POST",
							formData 			: { "action" : "uploadFiles", "reltypeid" : 10, "relid" : "20392" },
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
							onFallback			: function()
							{
								alert("HTML5 is not supported in this browser.");
							},
							onQueueComplete 	: function(queueData)
							{
								var dataString = { "action" : "refreshUploadedFiles", "reltypeid" : 10, "relid" : "20392" };
								$.ajax(
								{
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/documents/ajax-document.php",
									data: dataString,
									cache: false,
									success: function(result)
									{
										oDocumentListDT.ajax.reload(null, false);
										setTimeout(function(){
											if($("#documentListDT tbody tr").find('td.dataTables_empty').length) {
												$("#bdgDocCnt").html(0);
											} else {
												var docDataCount = $("#documentListDT tbody tr").length
												$("#bdgDocCnt").html(docDataCount);
											}
										}, 800);
									}
								});
							}
						});
					}
				}
				function loadCommentDetails() {
					if (!$.fn.DataTable.isDataTable("#commentListDT")) {
						var oCommentTable = $("#commentListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-commentList.php",
								type: "POST",
								data: { "reltypeid" : 10, "relationid" : "20392" }
							},
							processing: true,
							serverSide: false,
							stateSave: true,
							autoWidth: false,
							lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
							displayLength: -1,
							pagingType: "full_numbers",
							dom: "<\"dt-toolbar-header\"<\"col-xs-12 col-sm-4\"f><\"col-xs-12 col-sm-4 text-center\"B><\" col-xs-12 col-sm-4\"l>r>t<\"dt-toolbar-footer\"<\"col-sm-6 col-xs-12\"i><\"col-xs-12 col-sm-6\"p>>",
							buttons: [
								{
									text: "<i class=\"fal fa-lg fa-plus\"></i>",
									titleAttr: "Add",
									action: function ( e, dt, node, config ) {
										fnSaveComment();
									}
								},
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
										$("#commentListDT thead input").val("").change();
										$("#commentListDT thead select").val("").change();
										$("#commentListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{ data: "dtCreatedDate" },
								{ data: "dtCreatedTime" },
								{ data: "dtStatusName" },
								{ data: "dtComment" },
								{ data: "dtUploadedByName" },
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
							},
							initComplete: function (settings, json) {
								if($("#commentListDT tbody tr").find('td.dataTables_empty').length) {
									$("#bdgCommentCnt").html(0);
								} else {
									var commentDataCount = $("#commentListDT tbody tr").length
									$("#bdgCommentCnt").html(commentDataCount);
								}
							}
						});
						var state = oCommentTable.state.loaded();
						if ( state ) {
							oCommentTable.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$("#commentListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
									$("#commentListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#commentListDT thead th input[type=text]").on( "keyup change", function () {
							oCommentTable
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#commentListDT thead th select").on( "change", function () {
							oCommentTable
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#commentListDT thead th #dt_fltr_0").daterangepicker({
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
						$("#commentListDT thead th #dt_fltr_0").on("apply.daterangepicker", function(ev, picker) {
							  $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
							  oCommentTable.draw();
						});
						$("#commentListDT thead th #dt_fltr_0").on("cancel.daterangepicker", function(ev, picker) {
							  $(this).val("");
							  oCommentTable.draw();
						});
						$.fn.dataTableExt.afnFiltering.push(
							function( oSettings, aData, iDataIndex ) {
								var str_dateRange = $("#commentListDT thead th #dt_fltr_0").val();
								if(str_dateRange != "") {
									var arr_dateRange = str_dateRange.split(" - ");
									var f_startDate = arr_dateRange[0].split("/");
									var filterStartDate = f_startDate[2] + "-" + f_startDate[1] + "-" + f_startDate[0];
									var f_endDate = arr_dateRange[1].split("/");
									var filterEndDate = f_endDate[2] + "-" + f_endDate[1] + "-" + f_endDate[0];

									var iDateRangeCol = 0;
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
						$("#commentListDT tbody").on("click", "a.editComment", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oCommentTable.row(dtTR).data();
							var commentID = dtRowData["dtCommentID"];
							fnSaveComment( commentID );
						});
						$("#commentListDT tbody").on("click", "a.deleteComment", function () {
							var dtTR = $(this).parent("td").parent("tr");
							var dtRowData = oCommentTable.row(dtTR).data();
							var commentID = dtRowData["dtCommentID"];
							$("#dlgDeleteComment" ).modal( "show" );
							$("#dlgDeleteComment button#btnYes" ).unbind().on( "click" , function(e) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-comment.php",
									data: { "action" : "deleteComment", "commentid" : commentID },
									cache: false,
									success: function(data){
										if(data == true){
											oCommentTable.row( dtTR ).remove().ajax.reload().draw();
											$("#dlgDeleteComment" ).modal( "hide" );
											if($("#commentListDT tbody tr").find('td.dataTables_empty').length) {
												$("#bdgCommentCnt").html(0);
											} else {
												var commentDataCount = $("#commentListDT tbody tr").length
												$("#bdgCommentCnt").html(commentDataCount);
											}
										}
									}
								});
							});
							$("#dlgDeleteComment button#btnNo").unbind().on( "click" , function(e) {
								$("#dlgDeleteComment").modal( "hide" );
							});
						});
						function fnSaveComment(id)
						{
							 $("#dlgCommentDetails").on("init.field.fv", function(e, data) {
								// data.fv      --> The FormValidation instance
								// data.field   --> The field name
								// data.element --> The field element

								var $icon      = data.element.data("fv.icon"),
									options    = data.fv.getOptions(),                      // Entire options
									validators = data.fv.getOptions(data.field).validators; // The field validators

								if (validators.notEmpty && options.icon && options.icon.required) {
									// The field uses notEmpty validator
									// Add required icon
									$icon.addClass(options.icon.required).show();
								}
							}).formValidation({
								framework: "bootstrap",
								excluded: ":disabled",
								icon: {
									required: "far fa-asterisk",
									valid: "far fa-check",
									invalid: "far fa-times",
									validating: "far fa-sync-alt"
								},
								fields: {
									commentDesc: {
										row: ".col-md-10",
										validators: {
											notEmpty: {
												message: "The comment is required"
											}
										}
									},
								}
							}).on("err.field.fv", function(e, data) {
								if (data.fv.getInvalidFields().length > 0) {
									$("#dlgCommentDetails button#btnSave").prop("disabled", true);
								}
							}).on("success.field.fv", function(e, data) {
								if (data.fv.getInvalidFields().length <= 0) {
									$("#dlgCommentDetails button#btnSave").prop("disabled", false);
								}
							});

							if( id > 0 ) {
								$.ajax({
									type: "POST",
									url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-comment.php",
									data: { "action" : "getCommentData", "commentid" : id },
									dataType: "json",
									cache: false,
									success: function(data){
										if(data != false && data != null){
											$("#commentDesc" ).val( data['comment'] );
											$("#commentStatusID" ).selectpicker( "val", data['statusID'] );
										} else {
											$("#commentDesc" ).val( "" );
										}
									}
								});
							} else {
								$("#commentStatusID" ).selectpicker( "val", 128 );
							}

							$("#dlgCommentDetails" ).modal( "show" );
							$("#dlgCommentDetails button#btnSave" ).unbind().on( "click" , function(e) {
								$("#dlgCommentDetails" ).formValidation("validate"); 
								var formValidation = $("#dlgCommentDetails" ).data("formValidation");
								if(formValidation.isValid()) {
									$("#dlgCommentDetails button#btnSave" ).html( "Saving..." );
									$("#dlgCommentDetails button#btnSave" ).prop( "disabled", true );
									$.ajax({
										type: "POST",
										url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-comment.php",
										data: { "action" : "saveComment", "commentid" : id, "reltypeid" : 10, "relationid" : "20392", "postdata" : $("#updateComment").serialize() },
										cache: false,
										success: function(data) {
											var arrData = data.split("|");
											$("#dlgCommentDetails" ).modal( "hide" );
											$("#dlgCommentDetails button#btnSave" ).html( "Save" );
											$("#dlgCommentDetails button#btnSave" ).prop( "disabled", false );
											$("#dlgCommentDetails" ).formValidation("resetForm");
											$("#dlgCommentDetails .fa-asterisk" ).css({ 'display' : '',});
											$("#dlgCommentDetails .fa-asterisk" ).addClass( "far" );
											$("#commentDesc" ).val( "" );
											$("#commentStatusID" ).selectpicker( "val", 0 );
											if( arrData[0] == "success" ) {
												$.smallBox({title:"Success:",content:arrData[1],color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
												oCommentTable.ajax.reload(null, false);
												$("#importantCommentListDT").DataTable().ajax.reload();

												setTimeout(function(){
													if($("#commentListDT tbody tr").find('td.dataTables_empty').length) {
														$("#bdgCommentCnt").html(0);
													} else {
														var commentDataCount = $("#commentListDT tbody tr").length
														$("#bdgCommentCnt").html(commentDataCount);
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
								}
							});
							$("#dlgCommentDetails button#btnCancel" ).unbind().on( "click" , function(e) {
								$("#dlgCommentDetails" ).modal( "hide" );
								$("#dlgCommentDetails button#btnSave" ).html( "Save" );
								$("#dlgCommentDetails button#btnSave" ).prop( "disabled", false );
								$("#dlgCommentDetails" ).formValidation("resetForm");
								$("#dlgCommentDetails .fa-asterisk" ).css({ 'display' : '',});
								$("#dlgCommentDetails .glyphicon-asterisk" ).addClass( "glyphicon" )
								$("#commentDesc" ).val( "" );
								$("#commentStatusID" ).selectpicker( "val", 0 );
							});
						}
					};
				}
				function loadHistory() {
					if (!$.fn.DataTable.isDataTable("#quoteListDT")) {
						var selDateQuotesTable = $("#quoteListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteList.php",
								type: "POST",
								data: {
									"workTypeid" : "2",
									"tmsfileref" : ""
								}
							},
							processing: true,
							serverSide: false,
							fixedHeader: { headerOffset: $("#left-panel").outerHeight() + $("#ribbon").outerHeight() - 2 },
							stateSave: true,
							autoWidth: false,
							lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
							displayLength: 100,
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
										$("#quoteListDT thead input").val("").change();
										$("#quoteListDT thead select").val("").change();
										$("#quoteListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{
									class:          "details-control",
									orderable:      false,
									searchable: 	false,
									data:           null,
									defaultContent: ""
								},
								{ data: "dtTMSFileRef" },
								{ data: "dtQuoteRef" },
								{ data: "dtQuoteDate", type: "date-uk" },
								{ data: "dtCustName" },
								{ data: "dtLocation" },
								{ data: "dtEventVenue" },
								{ data: "dtJobStartDate", type: "date-uk" },
								{ data: "dtEventStartDate", type: "date-uk" },
								{ data: "dtEventEndDate", type: "date-uk" },
								{ data: "dtJobEndDate", type: "date-uk" },
								{ data: "dtTotalNumSigns" },
								{ data: "dtTotalNetCost", render: function ( data, type, full, meta ) { return "£ " + data; }, type: "num-fmt", searchable: false },
								{ data: "dtWorkRef" },
								{ data: "dtStatusName" },
								{ data: "dtCreatedByName" }
							],
							createdRow: function( row, data, index ) {
								if (data["dtStatusID"] == 47) {
									$("td", row).css("background-color", "#A9E2F3 !important");
								} else if (data["dtStatusID"] == 49 || data["dtStatusID"] == 50) {
									$("td", row).css("background-color", "#F5A9A9 !important");
								} else if (data["dtStatusID"] == 48) {
									$("td", row).css("background-color", "#BCF5A9 !important");
								}

								if (data["dtWorkTypeID"] == 1) { //  EVENT SIGN
									selDateQuotesTable.column( 0 ).visible( true ); // details
									selDateQuotesTable.column( 1 ).visible( true ); // tms file ref
									selDateQuotesTable.column( 2 ).visible( true ); // quote ref
									selDateQuotesTable.column( 3 ).visible( true ); // quote date
									selDateQuotesTable.column( 4 ).visible( true ); // cust name
									selDateQuotesTable.column( 5 ).visible( true ); // location
									selDateQuotesTable.column( 6 ).visible( true ); // event / venue
									selDateQuotesTable.column( 7 ).visible( true ); // job start date
									selDateQuotesTable.column( 8 ).visible( true ); // event start date
									selDateQuotesTable.column( 9 ).visible( true ); // event end date
									selDateQuotesTable.column( 10 ).visible( true ); // job end date
									selDateQuotesTable.column( 11 ).visible( true ); // no. signs
									selDateQuotesTable.column( 12 ).visible( true ); // total net cost
									selDateQuotesTable.column( 13 ).visible( true ); // won order no
									selDateQuotesTable.column( 14 ).visible( true ); // status
									selDateQuotesTable.column( 15 ).visible( true ); // created by
								} else if (data["dtWorkTypeID"] == 2) { // WORKS
									selDateQuotesTable.column( 0 ).visible( true ); // details
									selDateQuotesTable.column( 1 ).visible( true ); // tms file ref
									selDateQuotesTable.column( 2 ).visible( true ); // quote ref
									selDateQuotesTable.column( 3 ).visible( true ); // quote date
									selDateQuotesTable.column( 4 ).visible( true ); // cust name
									selDateQuotesTable.column( 5 ).visible( true ); // location
									selDateQuotesTable.column( 6 ).visible( false ); // event / venue
									selDateQuotesTable.column( 7 ).visible( true ); // job start date
									selDateQuotesTable.column( 8 ).visible( false ); // event start date
									selDateQuotesTable.column( 9 ).visible( false ); // event end date
									selDateQuotesTable.column( 10 ).visible( true ); // job end date
									selDateQuotesTable.column( 11 ).visible( true ); // no. signs
									selDateQuotesTable.column( 12 ).visible( true ); // total net cost
									selDateQuotesTable.column( 13 ).visible( true ); // won order no
									selDateQuotesTable.column( 14 ).visible( true ); // status
									selDateQuotesTable.column( 15 ).visible( true ); // created by
								} else if (data["dtWorkTypeID"] == 3) { // MAJOR EVENTS
									selDateQuotesTable.column( 0 ).visible( true ); // details
									selDateQuotesTable.column( 1 ).visible( true ); // tms file ref
									selDateQuotesTable.column( 2 ).visible( true ); // quote ref
									selDateQuotesTable.column( 3 ).visible( true ); // quote date
									selDateQuotesTable.column( 4 ).visible( true ); // cust name
									selDateQuotesTable.column( 5 ).visible( false ); // location
									selDateQuotesTable.column( 6 ).visible( true ); // event / venue
									selDateQuotesTable.column( 7 ).visible( true ); // job start date
									selDateQuotesTable.column( 8 ).visible( true ); // event start date
									selDateQuotesTable.column( 9 ).visible( true ); // event end date
									selDateQuotesTable.column( 10 ).visible( true ); // job end date
									selDateQuotesTable.column( 11 ).visible( true ); // no. signs
									selDateQuotesTable.column( 12 ).visible( true ); // total net cost
									selDateQuotesTable.column( 13 ).visible( true ); // won order no
									selDateQuotesTable.column( 14 ).visible( true ); // status
									selDateQuotesTable.column( 15 ).visible( true ); // created by
								}
							},
							footerCallback: function ( row, data, start, end, display ) {
								var api = this.api(), data;
								var intVal = function ( i ) { return typeof i === "string" ? i.replace(/[\£, ]/g, "")*1 : typeof i === "number" ? i : 0; };

								// Total over all pages
								if ( api.column( 12 ).data().length ){ grandTotal = api.column( 12 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); } ) } else { grandTotal = 0 };

								// Total over this page
								if ( api.column( 12 ).data().length ){ pageTotal = api.column( 12, { page: "current"} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 ) } else { pageTotal = 0 };

								// Update footer
								if(parseInt(pageTotal) > 999) { $(api.column( 12 ).footer() ).html( "£ " + pageTotal.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); } else { $(api.column( 12 ).footer() ).html( "£ " + pageTotal ); }
							},
							order: [[3, "desc"]],
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
							},
							initComplete: function (settings, json) {
								if($("#quoteListDT tbody tr").find('td.dataTables_empty').length) {
									$("#bdgHistoryCnt").html(0);
								} else {
									var historyDataCount = $("#quoteListDT tbody tr").length
									$("#bdgHistoryCnt").html(historyDataCount);
								}
							}
						});
						var state = selDateQuotesTable.state.loaded();
						if ( state ) {
							selDateQuotesTable.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$("#quoteListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#quoteListDT thead th input[type=text]").on( "keyup change", function () {
							selDateQuotesTable
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#quoteListDT tbody").on("click", "td.details-control", function () {
							var dtTR = $(this).parent("tr");
							var dtRow = selDateQuotesTable.row(dtTR);
							var dtRowData = selDateQuotesTable.row(dtTR).data();
							var dtRowIndex = dtRowData["dtQuoteID"];
							if ( dtRow.child.isShown() ) {
								dtRow.child.hide();
								dtTR.removeClass("shown");
							} else {
								dtTR.addClass("shown");
								$.ajax({
									type: "GET",
									url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteListDetails.php",
									data: { "id" : dtRowIndex },
									success:function(result)
									{
										dtRow.child(result).show();
										$("[data-toggle~=\"tooltip\"]").tooltip({
											trigger: "hover",
											html: true
										});
									}
								});
							}
						});
					};
				}
				function loadStatusDetails() {
					if (!$.fn.DataTable.isDataTable("#statusListDT")) {
						var oStatusTable = $("#statusListDT").DataTable({
							ajax: {
								url: "https://dev-erp.traffic.org.uk/public/ajax/works/ajax-statusLogList.php",
								type: "POST",
								data: { "reltypeid" : 10, "relationid" : "20392" }
							},
							processing: true,
							serverSide: false,
							stateSave: false,
							autoWidth: false,
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
										$("#statusListDT thead input").val("").change();
										$("#statusListDT thead select").val("").change();
										$("#statusListDT").DataTable().search("").draw();
									}
								}
							],
							columns: [
								{ data: "dtUpdatedDate" },
								{ data: "dtUpdatedTime" },
								{ data: "dtOldStatusName" },
								{ data: "dtNewStatusName" },
								{ data: "dtUploadedByName"}
							],
							order: [[0, "desc"]],
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
							},
							initComplete: function (settings, json) {
								if($("#statusListDT tbody tr").find('td.dataTables_empty').length) {
									$("#bdgStatusLogCnt").html(0);
								} else {
									var statusLogDataCount = $("#statusListDT tbody tr").length
									$("#bdgStatusLogCnt").html(statusLogDataCount);
								}
							}
						});
						var state = oStatusTable.state.loaded();
						if ( state ) {
							oStatusTable.columns().eq( 0 ).each( function ( colIdx ) {
							var colSearch = state.columns[colIdx].search;
								if ( colSearch.search ) {
									$("#statusListDT thead th input#dt_fltr_" + colIdx ).val( colSearch.search );
									$("#statusListDT thead th select#dt_fltr_" + colIdx ).val( colSearch.search );
								}
							});
						}
						$("#statusListDT thead th input[type=text]").on( "keyup change", function () {
							oStatusTable
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#statusListDT thead th select").on( "change", function () {
							oStatusTable
								.column( $(this).parent().index()+":visible" )
								.search( this.value )
								.draw();
						});
						$("#statusListDT thead th #dt_fltr_0").daterangepicker({
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
						$("#statusListDT thead th #dt_fltr_0").on("apply.daterangepicker", function(ev, picker) {
							  $(this).val(picker.startDate.format("DD/MM/YYYY") + " - " + picker.endDate.format("DD/MM/YYYY"));
							  oStatusTable.draw();
						});
						$("#statusListDT thead th #dt_fltr_0").on("cancel.daterangepicker", function(ev, picker) {
							  $(this).val("");
							  oStatusTable.draw();
						});
						$.fn.dataTableExt.afnFiltering.push(
							function( oSettings, aData, iDataIndex ) {
								var str_dateRange = $("#statusListDT thead th #dt_fltr_0").val();
								if(str_dateRange != "") {
									var arr_dateRange = str_dateRange.split(" - ");
									var f_startDate = arr_dateRange[0].split("/");
									var filterStartDate = f_startDate[2] + "-" + f_startDate[1] + "-" + f_startDate[0];
									var f_endDate = arr_dateRange[1].split("/");
									var filterEndDate = f_endDate[2] + "-" + f_endDate[1] + "-" + f_endDate[0];

									var iDateRangeCol = 0;
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
				}

				$(document).on("change",".selJobReqDOM",function(){
					oRowID = $(this).parent().parent().get(0).id;
					var selText = $("#jobReqID_" + oRowID + " option:selected").text();
					var jobReqText = selText.split(" | ");
					$("#jobDescription_" + oRowID).focus();
					$("#jobDescription_" + oRowID).val($.trim(jobReqText[1]));
				});
				function decodeEntities(encodedString) {
					var textArea = document.createElement("textarea");
					textArea.innerHTML = encodedString;
					return textArea.value;
				}
				function emailReport(reportName){
					var defaultEmailAddresses = "";
					if(reportName == "SalesQuotation") {
						defaultEmailAddresses = "tracy@minstersurfacing.com";$("#dlgEmailReport").find(".modal-body").append( "<form class=\"form-horizontal\" method=\"POST\" action=\"\"><div class=\"form-group\"><label for=\"emailAddress\" class=\"col-sm-3 control-label\">Email Address:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"emailAddress\" name=\"emailAddress\" row=\"4\">" + defaultEmailAddresses + "</textarea></div></div><div class=\"form-group\"><label for=\"emailBody\" class=\"col-sm-3 control-label\">Email Body:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"emailBody\" name=\"emailBody\" rows=\"10\">Good morning Gez\n\nPlease find attached Quote Ref: TM-Q16892 as requested.\n\nPlease note, to confirm this order, we will require a valid purchase order number from yourselves.\n\nShould you have any queries, please do not hesitate to contact us.</textarea></div></div><div class=\"form-group\"><label for=\"emailAttachment\" class=\"col-sm-3 control-label\">Attachment(s):</label><div class=\"col-sm-9\"><select class=\"form-control\" id=\"quoteDocIDs\" name=\"quoteDocIDs[]\" multiple><optgroup label=\"Document List\"></select></div></div></form>" );
						$("#quoteDocIDs").selectpicker({
							liveSearch : true,
							liveSearchNormalize: true,
							dropupAuto: false,
							width : "100%",
							size: 10
						});

					}
					$("#dlgEmailReport").modal("show");
					$("#dlgEmailReport button#btnSend").unbind().on( "click" , function(e) {
						$("#dlgEmailReport button#btnSend").html("Sending...");
						$("#dlgEmailReport button#btnSend").prop("disabled", true);
						var emailAddress = $("#emailAddress").val();
						var emailAddress = $("#emailAddress").val();
						var emailBody = $("#emailBody").val();

						if( $("#quoteDocIDs :selected").length > 0) {
							var documentIDs = [];
							$("#quoteDocIDs :selected").each(function(i, selected) {
								documentIDs[i] = $(selected).val();
							});
						}

						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-Quote.php",
							data: { "action" : "emailReport", "report" : reportName, "quoteid" : "20392", "email" : emailAddress, "emailbody" : emailBody, "documentids" : documentIDs },
                            dataType: "json",
							cache: false,
							success: function(data){
								$("#dlgEmailReport button#btnSend").html("Send");
								$("#dlgEmailReport button#btnSend").prop("disabled", false);
								if( data.state == "success" ) {
									$.smallBox({title:"Success:",content:data.message,color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
									$("#dlgEmailReport").modal("hide");
									$("#dlgEmailReport").removeData("bs.modal");
									$("#dlgEmailReport").find(".modal-body").empty();
								} else if( data.state  == "danger" ) {
									$.smallBox({title:"Error:",content:data.message,color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"}); 
								}
							}
						});
					});
					$("#dlgEmailReport button#btnCancel").unbind().on( "click" , function(e) {
						$("#dlgEmailReport button#btnSend").html("Send");
						$("#dlgEmailReport button#btnSend").prop("disabled", false);
						$("#dlgEmailReport").modal("hide");
						$("#dlgEmailReport").removeData("bs.modal");
						$("#dlgEmailReport").find(".modal-body").empty();
					});
				}
				function emailNotification(notifyType){
					var defaultEmailAddresses = "", defaultEmailSubject = "";
					if(notifyType == "NewCustomer") {
						defaultEmailAddresses = "accounts@traffic.org.uk";
						defaultEmailSubject = "New Customer Account Request";
						$("#dlgEmailNotification").find(".modal-body").append( "<form class=\"form-horizontal\" method=\"POST\" action=\"\"><div class=\"form-group\"><label for=\"emailAddress\" class=\"col-sm-3 control-label\">Email Address:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"emailAddress\" name=\"emailAddress\" row=\"4\">" + defaultEmailAddresses + "</textarea></div></div><div class=\"form-group\"><label for=\"emailSubject\" class=\"col-sm-3 control-label\">Email Subject:</label><div class=\"col-sm-9\"><input type=\"text\" class=\"form-control\" id=\"emailSubject\" name=\"emailSubject\" value=\"" + defaultEmailSubject + "\" /></div></div><div class=\"form-group\"><label for=\"emailBody\" class=\"col-sm-3 control-label\">Email Body:</label><div class=\"col-sm-9\"><textarea class=\"form-control\" id=\"emailBody\" name=\"emailBody\" rows=\"10\">Mason Draper has requested a new account to be setup within Sage, please could you add the following details and check the credit application.\n\nName: " + $("#newCustName").val() + "\nAddress Line 1: " + $("#newCustAddLine1").val() + "\nAddress Line 2: " + $("#newCustAddLine2").val() + "\nCity: " + $("#newCustCity").val() + "\nCounty: " + $("#newCustCounty").val() + "\nPostcode: " + $("#newCustPostcode").val() + "\nTelephone: " + $("#newCustPhone").val() + "\nEmail:" + $("#newCustEmail").val() + "\nContact: " + $("#custContactName").val() + "\n\nShould you have any queries, please do not hesitate to contact us.</textarea></div></div></form>" );
					}
					$("#dlgEmailNotification").modal("show");
					$("#dlgEmailNotification button#btnSend").unbind().on( "click" , function(e) {
						$("#dlgEmailNotification button#btnSend").html("Notifying Accounts...");
						$("#dlgEmailNotification button#btnSend").prop("disabled", true);
						var emailAddress = $("#emailAddress").val();
						var emailSubject = $("#emailSubject").val();
						var emailBody = $("#emailBody").val();
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/notifications/ajax-notify.php",
							data: { "action" : "emailNotification", "email" : emailAddress, "emailsubject" : emailSubject, "emailbody" : emailBody },
							cache: false,
							success: function(data){
								$("#dlgEmailNotification button#btnSend").html("Send Notification");
								$("#dlgEmailNotification button#btnSend").prop("disabled", false);
								$("#dlgEmailNotification").modal("hide");
								$("#dlgEmailNotification").removeData("bs.modal");
								$("#dlgEmailNotification").find(".modal-body").empty();
								if(data != 1) {
									alert(data);
								}
							}
						});
					});
					$("#dlgEmailNotification button#btnCancel").unbind().on( "click" , function(e) {
						$("#dlgEmailNotification button#btnSend").html("Send Notification");
						$("#dlgEmailNotification button#btnSend").prop("disabled", false);
						$("#dlgEmailNotification").modal("hide");
						$("#dlgEmailNotification").removeData("bs.modal");
						$("#dlgEmailNotification").find(".modal-body").empty();
					});
				}
				function previewReport(reportName){
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quote.php",
						data: { "action" : "previewReport", "report" : reportName, "quoteid" : "20392" },
						cache: false,
						success: function(url){
							openReportWindow("https://dev-erp.traffic.org.uk/pages/admin/documents/download.php?location=reports&filename="+url+"", "_blank", 1000, 700);
						}
					});
				}
				function openReportWindow(url, title, w, h){
					var left = (screen.width/2)-(w/2);
					var top = (screen.height/2)-(h/2);
					return window.open(url, title, "location=no, resizable=yes, directories=no, toolbar=no, width="+w+", height="+h+", top="+top+", left="+left);
				}
				var commentRefreshTime = 1000 * 60 * 5;
				function addCommentToQuote(){
					var commentDesc = $("textarea#comment").val();
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-comment.php",
						data: { "action" : "addComment", "reltypeid" : 10, "relationid" : "20392", "comment" : commentDesc },
						cache: false,
						success: function(url){
							refreshCommentsTable();
							$("textarea#comment" ).val( "" );
						}
					});
				}
				function refreshCommentsTable()
				{
					$.ajax(
					{
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/comments/ajax-comment.php",
						data: { "action" : "refreshComments", "reltypeid" : 10, "relationid" : "20392" },
						cache: false,
						success: function( data ){
							$("table#commentList tbody#ajaxCommentData" ).html( data );
						}
					});
				}
				function checkAppAmount() {
					$.ajax(
					{
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
						data: { "action" : "checkApplicationAmount", "reltypeid" : 10, "relationid" : 20392},
						dataType: "json",
						cache: false,
						success: function( data ){
							if(data['cost'] > 0) { dAmount = parseFloat(data['cost']).toFixed(2) } else { dAmount = '' }
							$("#amount" ).val( dAmount );
						}
					});
				}
				function checkAppDoc(supplierID, suppSiteID, applicationTypeID) {
					if(supplierID > 0 && applicationTypeID > 0) {
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/localauthorities/ajax-localAuthApp.php",
							data: { "action" : "checkApplicationExists", "reltypeid" : 10, "relationid" : 20392, "supplierid" : supplierID, "suppsiteid" : suppSiteID, "applicationtypeid" : applicationTypeID },
                            dataType: "json",
							cache: false,
							success: function( data ){
								$(".error-message-application-missing").remove();
								$(".error-message-application-compleated").remove();
								$(".error-message-po").remove();
								$(".error-message-po-noSage").remove();
								$("#genApplication").prop("disabled", false);
								$("#genPO").prop("disabled", false);
								if(data.templateExists == 0) { // no template found
									$("#genApplication").prop("disabled", true);
									$("#genApplication").prop("checked",false);
									$("#genEventSignApp").prop("disabled", false);
									$("#appError").html("<p class=\"error-message-application-missing\" style=\"padding-top:5px; color: red;\">Generate Application Disabled: Template File does not exist.</p>");
									$("#appErrorRow").show();
								} else {
									$("#genApplication").prop("disabled", false);
									$(".error-message-application-missing").remove();
									$("#appErrorRow").hide();
								}
								if(data.duplicateExists == 1) { // duplicate Application
									$("#genApplication").prop("disabled", true);
									$("#genApplication").prop("checked",false);
									$("#genEventSignApp").prop("disabled", true);
									$("#genEventSignApp").prop("checked",false);
									$("#appErrorRow").show();
									$("#appError").html("<p class=\"error-message-application-compleated\" style=\"padding-top:5px; color: red;\">Application has already been compleated.</p>");
								} else {
									if(data.templateExists == 1) {
										$("#genApplication").prop("disabled", false);
										$("#appErrorRow").hide();
									}
									$("#genEventSignApp").prop("disabled", false);
									$(".error-message-application-compleated").remove();
								}
								if(data.poExists == 1) { // duplicate PO
									$("#genPO").prop("disabled", true);
									$("#genPO").prop("checked",false);
									$("#poErrorRow").show();
									$("#poError").html("<p class=\"error-message-po\" style=\"padding-top:5px; color: red;\">PO has already been generated.</p>");
								} else {
									$("#genPO").prop("disabled", false);
									$(".error-message-po").remove();
									$("#poErrorRow").hide();
								}
								if(data.sageExists == 1) { // sage ref true
									if(data.poExists == 0) {
										$("#genPO").prop("disabled", false);
									}
									$(".error-message-po-noSage").remove();
									$("#poErrorRow").hide();
								} else {
									$("#genPO").prop("disabled", true);
									$("#poError").html("<p class=\"error-message-po-noSage\" style=\"padding-top:5px; color: red;\">Generate PO Disabled: Local Authority is missing the Sage Account Ref.</p>");
									$("#poErrorRow").show();
								}
							}
						});
					}
				}

				var platform = new H.service.Platform({
					app_id: "XxCLjkoeOkuqDqIaKyWX" ,
					app_code: "Vu9ujuKjm0wnvljftJodGQ",
					useHTTPS: true
				});

				var pixelRatio = window.devicePixelRatio || 1;
				var defaultLayers = platform.createDefaultLayers({
				  tileSize: pixelRatio === 1 ? 256 : 512,
				  ppi: pixelRatio === 1 ? undefined : 320
				});

				var map = new H.Map(document.getElementById("map"),
					defaultLayers.normal.map,{
						pixelRatio: pixelRatio
					}
				);

				var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
				behavior.disable(H.mapevents.Behavior.WHEELZOOM);
				var ui = H.ui.UI.createDefault(map, defaultLayers);
				var markerData = { };
				markerGroup = new H.map.Group();
                
                var depotMarkerData = { };
                var depots = new H.map.Group();
                var tmsIcon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map-tms-icon.png"); 
                        var coords4 = {lat:53.7805378, lng:-1.50111};
                        depotMarkerData["m"+4] = new H.map.Marker(coords4, {icon: tmsIcon});
                        depots.addObject(depotMarkerData["m"+4]); 
                        var coords1 = {lat:53.335657, lng:-0.952829};
                        depotMarkerData["m"+1] = new H.map.Marker(coords1, {icon: tmsIcon});
                        depots.addObject(depotMarkerData["m"+1]);
                map.addObject(depots);
                map.setViewBounds(depots.getBounds()); 
                            var coords20392 = {lat:53.396439, lng:-0.964131};
                            var icon20392 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/map_marker.png");
                            markerData["m20392"] = new H.map.Marker(coords20392, {icon: icon20392});
                            markerData["m20392"].draggable = true;
                            markerData["m20392"].setData("Main Street,Mattersey,DN10 5DT")
                            var id = 20392
                            fnCreateListener(map, behavior, markerData["m20392"], id);
                            markerGroup.addObject(markerData["m20392"]);
                            fnShowSingleLocationHTML();

				map.addObject(markerGroup);
                
                if(markerGroup.getObjects().length > 0){
                    fnZoomLevel();
                };
                
                $("#roadName,#popup_roadName").on("change", function() {		
					
                    var roadName = $(this).val();
                    var formName = $(this).closest("form").prop("id");
                    
                    if(formName == "updateLocation"){;
                        var startPointID = "#popup_startPointID",
                            endPointID = "#popup_endPointID";
                    } else if (formName == "editQuote"){
                        markerGroup.removeAll();
                        var startPointID = "#startPointID",
                            endPointID = "#endPointID";
                    }
                    $(startPointID).empty();
                    $(startPointID).selectpicker("refresh");
                    $(endPointID).empty();
                    $(endPointID).selectpicker("refresh");

					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/markerposts/ajax-markerpost.php",
						data: { "action" : "getMarkerPostOptions", "roadname" : roadName},
						cache: false,
						success: function(data){
                            $(startPointID).html(data);
                            $(endPointID).html(data);
                            $(startPointID).selectpicker("refresh");
							$(endPointID).selectpicker("refresh");
                            $("#startPointID, #endPointID").selectpicker("val", 0);
						}
					});
				});
                var prevStartPoint;
                var prevEndPoint;
                
                $("#startPointID").on("shown.bs.select", function(e) {
                    var markerID = $(this).val();
                    if(markerID > 0){
                        prevStartPoint = markerID
                    } else {
                        prevStartPoint = null;
                    }
                }).on("change", function(e) {
                    if(prevStartPoint != null){
                        if(prevStartPoint > 0){  
                            $("#endPointID option[value='"+prevStartPoint+"']").prop("disabled", false);
                            markerGroup.removeObject(markerData["m"+prevStartPoint]);
                        }
                    }
                    var markerID = $(this).val();
                    $("#endPointID option[value='"+markerID+"']").prop("disabled", true);
                    $("#endPointID").selectpicker("refresh");
                    
                    $.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/markerposts/ajax-markerpost.php",
						data: { "action" : "getMarkerPostData", "markerid" : markerID},
						dataType: "json",
						cache: false,
						success: function(data){
                            var coords = {lat:data['latitude'], lng:data['longitude']};
                            var icon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redS.png");
                            fnCreateMarker(markerID, coords, icon, data['name'], data['junctions'], "", false);
						}
					});
                });
                
                $("#endPointID").on("shown.bs.select", function(e) {
                    var markerID = $(this).val();
                    if(markerID > 0){
                        prevEndPoint = markerID
                    } else {
                        prevEndPoint = null;
                    }
                }).on("change", function(e) {
                    if(prevEndPoint != null){
                        if(prevEndPoint > 0){
                            $("#startPointID option[value='"+prevEndPoint+"']").prop("disabled", false); 
                            markerGroup.removeObject(markerData["m"+prevEndPoint]);
                        }
                    }
                    var markerID = $(this).val();
                    $("#startPointID option[value='"+markerID+"']").prop("disabled", true);  
                    $("#startPointID").selectpicker("refresh");
                    
                    $.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/markerposts/ajax-markerpost.php",
						data: { "action" : "getMarkerPostData", "markerid" : markerID},
						dataType: "json",
						cache: false,
						success: function(data){
                            var coords = {lat:data['latitude'], lng:data['longitude']};
                            var icon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redE.png");
                            fnCreateMarker(markerID, coords, icon, data['name'], data['junctions'], "", false);
						}
					});
                });
                
                $("#endPointID").on("change", function() {		
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/markerposts/ajax-markerpost.php",
						data: { "action" : "getBearing", "startpointid" : $("#startPointID").val(), "endpointid" : $(this).val()},
						cache: false,
						success: function(data){
                            $("#cwayDirection").selectpicker("val", data);
						}
					});
				});
                
				function fnConvertLocation(){
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
						data: { "action" : "convertLocationType", "quoteid" : "20392" },
                        dataType: "json",
						cache: false,
						success: function(data){
							$("#roadName, #location, #postcode, #easting, #northing" ).val( "" );
							if( data.state == "success" ) {
								$.smallBox({title:"Success:",content:data.message,color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
								fnLoadLocationListDT();
								fnShowMultiLocationDetailHTML();
								$("#editQuote" ).formValidation("resetForm");
								if ($.fn.DataTable.isDataTable("#locationListDT")) {
									$("#locationListDT").DataTable().ajax.reload();
								}
								$("#locationDistanceDT").DataTable().ajax.reload();
								markerGroup.removeAll();
								if( data.locationID > 0 ) {
									var locationID = data.locationID;
									var locationData = data.locationData;
                                    
                                    if(2 == 5){
                                    
                                        var roadName = locationData['roadName'];
                                        
                                        var latitude1 = locationData['latitude'];
                                        var longitude1 = locationData['longitude'];
                                        var coords1 = {lat:latitude1, lng:longitude1};
                                        var icon1 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redS.png");
                                        fnCreateMarker(locationData['startPointID'], coords1, icon1, roadName, "", ""); 
                                        
                                        var latitude2 = locationData['latitude2'];
                                        var longitude2 = locationData['longitude2'];
                                        var coords2 = {lat:latitude2, lng:longitude2};
                                        var icon2 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redE.png");
                                        fnCreateMarker(locationData['endPointID'], coords2, icon2, roadName, "", ""); 
                                        
                                    } else {
                                        var latitude = locationData['latitude'];
                                        var longitude = locationData['longitude'];
                                        var roadName = locationData['roadName'];
                                        var location = locationData['location'];
                                        var postcode = locationData['postcode'];
                                        var displayOrder = locationData['displayOrder'];
                                        var coords = {lat:latitude, lng:longitude};
                                        var icon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/numberedmarkers/marker_red"+ displayOrder +".png");
                                        fnCreateMarker(locationID, coords, icon, roadName, location, postcode);
                                    }
                                    fnRefreshLocationList();
								} 
							} else if( data.state == "danger" ) {
								$.smallBox({title:"Error:",content:data.message,color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
							} else if(data.state == "info") {
								$.smallBox({title:"Info:",content:data.message,color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
							}
						}
					});
				}
                
				function fnRevertLocation(id){
					if (id > 0){
					   	$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
							data: { "action" : "revertLocationType", "locationid" : id, "quoteid" : "20392", "worktypeid" : "2"},
                            dataType: "json",
							cache: false,
							success: function(data){
								if( data.state == "success" ) {
									$.smallBox({title:"Success:",content:data.message,color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
									$("#locationTypeSingle").prop("checked", true);
									$("#locationDistanceDT").DataTable().ajax.reload();
									if ($.fn.DataTable.isDataTable("#locationListDT")) {
										$("#locationListDT").DataTable().ajax.reload();
									}
									fnShowSingleLocationHTML();
									markerGroup.removeAll();
									var markerData = { };
                                    $("#locationTypeSingle").prop("checked", true);
									var locationData = data.locationData;
                                    if(2 == 5){
                                    
                                        var markerPostList = data['markerPostList'];
                                        $("#startPointID, #endPointID").html(markerPostList);
                                        $("#startPointID, #endPointID").selectpicker("refresh");
                                        
                                        $("#roadName").selectpicker("refresh");
                                    
                                        $("#roadName" ).selectpicker("val", locationData['roadName'] );
                                        $("#startPointID" ).selectpicker("val", locationData['startPointID'] );
                                        $("#endPointID" ).selectpicker("val", locationData['endPointID'] );
                                        $("#cwayDirection" ).val( locationData['cwayDirection'] );
                                        $("#areaCodeID" ).selectpicker("val", locationData['areaCodeID'] );
                                        
                                        $("#editQuote").formValidation("revalidateField", "roadName");
                                        $("#editQuote").formValidation("revalidateField", "startPointID");
                                        $("#editQuote").formValidation("revalidateField", "endPointID");
                                    
                                        var roadName = locationData['roadName'];
                                        var startPointID = locationData['startPointID'];
                                        var endPointID = locationData['endPointID'];
                                    
                                        var latitude1 = locationData['latitude'];
                                        var longitude1 = locationData['longitude'];
                                        var coords1 = {lat:latitude1, lng:longitude1};
                                        var icon1 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redS.png");
                                        fnCreateMarker(startPointID, coords1, icon1, roadName, "", ""); 
                                        
                                        var latitude2 = locationData['latitude2'];
                                        var longitude2 = locationData['longitude2'];
                                        var coords2 = {lat:latitude2, lng:longitude2};
                                        var icon2 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redE.png");
                                        fnCreateMarker(endPointID, coords2, icon2, roadName, "", ""); 
                                        
                                    } else {
                                    
                                        $("#roadName" ).val( locationData['roadName'] );
                                        $("#location" ).val( locationData['location'] );
                                        $("#postcode" ).val( locationData['postcode'] );
                                        $("#easting" ).val( locationData['easting'] );
                                        $("#northing" ).val( locationData['northing'] );
                                        
                                        $("#editQuote").formValidation("revalidateField", "roadName");
                                        $("#editQuote").formValidation("revalidateField", "location");
                                        $("#editQuote").formValidation("revalidateField", "postcode");
                                    
                                        var latitude = locationData['latitude'];
                                        var longitude = locationData['longitude'];
                                        var roadName = locationData['roadName'];
                                        var location = locationData['location'];
                                        var postcode = locationData['postcode'];
                                        var coords = {lat:latitude, lng:longitude};
                                        var icon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/map_marker.png")
                                        fnCreateMarker(20392, coords, icon, roadName, location, postcode);
                                    }
                                    
                                    $("#locationDuration" ).text( locationData['duartion'] );
                                    $("#locationDistance" ).text( locationData['distance'] );
								} else if( data.state == "danger" ) {
                                    $.smallBox({title:"Error:",content:data.message,color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
                                } else if(data.state == "info") {
                                    $.smallBox({title:"Info:",content:data.message,color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
                                }
							}
						});
					} else {
						markerCount = markerGroup.getObjects().length;
						if (markerCount >= 1){
							$("#dlgRevertSingleLocation" ).modal( "show" );
                            $("#dlgRevertSingleLocation button#btnYes" ).unbind().on( "click" , function(e) {
                                $("#dlgRevertSingleLocation button#btnYes" ).html( "Reverting..." );
                                $("#dlgRevertSingleLocation button#btnYes" ).prop( "disabled", true );
                                $.when(fnRevertLocation($("#singleLocationID").val())).then(function(){
                                    $("#dlgRevertSingleLocation" ).modal( "hide" );
                                    $("#dlgRevertSingleLocation button#btnYes" ).html( "Revert" );
                                    $("#dlgRevertSingleLocation button#btnYes" ).prop( "disabled", false ); 
                                });
                            });
                            $("#dlgRevertSingleLocation button#btnNo").unbind().on( "click" , function(e) {
                                $("#singleLocationID").selectpicker("val", "0");
                                $("#dlgRevertSingleLocation").modal( "hide" );
                                $("#locationTypeMulti").prop("checked", true);
                                fnShowMultiLocationDetailHTML();
                            });
						} else {
							fnShowSingleLocationHTML();
							$("#locationTypeSingle").prop("checked", true);
						}
					}
				}
				function fnUpdateLocation(id, lat, lng){
					if( id > 0 ) {
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
							data: { "action" : "updLatLon", "locationid" : id, "lat" : lat, "lng": lng, "quoteid" : "20392" },
                            dataType: "json",
							cache: false,
							success: function(data) {
								if( data.state == "success" ) {
									$.smallBox({title:"Success:",content:data.message,color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
                                    $("#locationDuration" ).text( data.strDuration );
                                    $("#locationDistance" ).text( data.strDistance );
                                    var locationData = data.locationData
                                    var roadName = locationData['roadName'];
                                    var location = locationData['location'];
                                    var postcode = locationData['postcode'];
									if( id == "20392" ){
                                        //$("#roadName" ).val( roadName );
                                        //$("#location" ).val( location );
                                        //$("#postcode" ).val( postcode );
                                        //$("#easting" ).val( locationData['easting'] );
                                        //$("#northing" ).val( locationData['northing'] );
                                        //$("#editQuote").formValidation("revalidateField", "roadName");
                                        //$("#editQuote").formValidation("revalidateField", "location");
                                        //$("#editQuote").formValidation("revalidateField", "postcode");
                                        //markerData["m"+id].setData(roadName+","+location+","+postcode)
									} else {
                                        //markerData["m"+id].setData(roadName+","+location+","+postcode);
                                        $("#locationListDT").DataTable().ajax.reload();
                                        fnRefreshLocationList();
									}
								} else if( data.state == "danger" ) {
									$.smallBox({title:"Error:",content:data.message,color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
								} else if(data.state == "info") {
									$.smallBox({title:"Info:",content:data.message,color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
								}
							}
						});
					}
				}
                
				function fnSaveLocation(id){
					$("#dlgLocationDetails").on("init.field.fv", function(e, data) {
						// data.fv      --> The FormValidation instance
						// data.field   --> The field name
						// data.element --> The field element

						var $icon      = data.element.data("fv.icon"),
							options    = data.fv.getOptions(),                      // Entire options
							validators = data.fv.getOptions(data.field).validators; // The field validators

						if (validators.notEmpty && options.icon && options.icon.required) {
							// The field uses notEmpty validator
							// Add required icon
							$icon.addClass(options.icon.required).show();
						}
					}).formValidation({
						framework: "bootstrap",
						excluded: ":disabled",
						icon: {
							required: "far fa-asterisk",
							valid: "far fa-check",
							invalid: "far fa-times",
							validating: "far fa-sync-alt"
						},
						fields: {
							popup_roadName: {
								row: ".col-sm-10",
								validators: {
									notEmpty: {
										message: "The road name is required"
									}
								}
							},
							popup_location: {
								row: ".col-sm-10",
								validators: {
									notEmpty: {
										message: "The location is required"
									}
								}
							}
						}
					}).on("err.field.fv", function(e, data) {
						if (data.fv.getInvalidFields().length > 0) {
							$("#dlgLocationDetails button#btnSave").prop("disabled", true);
						}
					}).on("success.field.fv", function(e, data) {
						if (data.fv.getInvalidFields().length <= 0) {
							$("#dlgLocationDetails button#btnSave").prop("disabled", false);
						}
					});

					$("#postcodeLookup").on("click", "a.postcodeLookup", function () {
						var roadName = $("#popup_roadName").val(),
							location = $("#popup_location").val(),
							easting = $("#popup_easting").val(),
							northing = $("#popup_northing").val();
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/postcode/ajax-postcodeFunctions.php",
							data: { "action" : "nearestPostcode", "roadName" : roadName, "location" : location, "easting" : easting, "northing" : northing },
							cache: false,
							success: function(data){
								var arrData = data.split("|");
								if( arrData[0] == "success" ) {
									$("#popup_postcode").val(arrData[1]);
									$("#inputPostcodeLookup").css("border", "1px solid green");
									$.ajax({
										type: "POST",
										url: "https://dev-erp.traffic.org.uk/public/ajax/postcode/ajax-postcodeFunctions.php",
										data: { "action" : "postcodeLookup", "postcode" : arrData[1] },
										dataType: "json",
										cache: false,
										success: function(data){
											$("#councilLookup" ).text( data['admin_district'] );
										}
									});
								} else if( arrData[0] == "danger" ) {
									$("#popup_postcode").val(arrData[1]);
									$("#inputPostcodeLookup").css("border", "1px solid red");
									$("#councilLookupDiv").hide();
								}
							}
						});
					});

					if( id > 0 ) {
						$.ajax({
							type: "POST",
							url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
							data: { "action" : "getLocationData", "locationid" : id },
							dataType: "json",
							cache: false,
							success: function(data){
								if(data != false && data != null){
                                        $("#popup_roadName" ).val( data['roadName'] );
                                        $("#popup_permitLocationID").selectpicker("val", data['permitLocationID']);
                                        $("#popup_location" ).val( data['location'] );
                                        $("#popup_postcode" ).val( data['postcode'] );
                                        $("#popup_easting" ).val( data['easting'] );
                                        $("#popup_northing" ).val( data['northing'] );
									
								} else {
									$("#popup_roadName, #popup_location, #popup_postcode, #popup_easting, #popup_northing" ).val( "" );
									$("#popup_permitLocationID,#popup_startPointID,#popup_endPointID,#popup_cwayDirection,#popup_areaCodeID").selectpicker("val", "0");
								}
							}
						});
					}
					$("#dlgLocationDetails" ).modal( "show" );
					$("#dlgLocationDetails button#btnSave" ).unbind().on( "click" , function(e) {

						$("#dlgLocationDetails" ).formValidation("validate"); 
						var formValidation = $("#dlgLocationDetails" ).data("formValidation");
						if(formValidation.isValid()) {
							$("#dlgLocationDetails button#btnSave" ).html( "Saving..." );
							$("#dlgLocationDetails button#btnSave" ).prop( "disabled", true );
							$.ajax({
								type: "POST",
								url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
								data: { "action" : "addLocation", "locationid" : id, "quoteid" : "20392", "postdata" : $("#updateLocation").serialize() },
								dataType: "json",
								cache: false,
								success: function(data) {
									
                                    $("#dlgLocationDetails" ).modal( "hide" );
									$("#dlgLocationDetails button#btnSave" ).html( "Save" );
									$("#dlgLocationDetails button#btnSave" ).prop( "disabled", false );
									$("#dlgLocationDetails" ).formValidation("resetForm");
									$("#dlgLocationDetails .fa-asterisk" ).css({ 'display' : '',});
									$("#dlgLocationDetails .fa-asterisk" ).addClass( "far" );
									
                                    $("#popup_permitLocationID,#popup_startPointID,#popup_endPointID,#popup_cwayDirection,#popup_areaCodeID").selectpicker("val", "0");
									$("#popup_roadName, #popup_location, #popup_postcode, #popup_easting, #popup_northing" ).val( "" );
									$("#councilLookup").html("");
									$("#inputPostcodeLookup").removeAttr("style");
									
                                    if( data.state == "success" ) {
                                    
										$.smallBox({title:"Success:",content:data.message,color:"#739E73",timeout:8000,iconSmall:"fal fa-check shake animated"});
										
                                        $("#locationListDT").DataTable().ajax.reload(null,false);

										if (data.action == "UPDATE"){
                                            if(2 == 5){
                                                markerGroup.removeObject( markerData["m"+data.prevStartPointID] );  
                                                markerGroup.removeObject( markerData["m"+data.prevEndPointID] );  
                                            } else {
                                                markerGroup.removeObject( markerData["m"+data.locationID] );   
                                            }
											
										}
                                        var locationData = data.locationData;
                                        
                                        if(2 == 5){

                                            var startPointID = locationData['startPointID'];
                                            var endPointID = locationData['endPointID'];
                                            var roadName = locationData['roadName'];
                                            
                                            var latitude1 = locationData['latitude'];
                                            var longitude1 = locationData['longitude'];
                                            var coords1 = {lat:latitude1, lng:longitude1};
                                            var icon1 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redS.png");
                                            fnCreateMarker(startPointID, coords1, icon1, roadName, "", ""); 

                                            var latitude2 = locationData['latitude2'];
                                            var longitude2 = locationData['longitude2'];
                                            var coords2 = {lat:latitude2, lng:longitude2};
                                            var icon2 = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/marker_redE.png");
                                            fnCreateMarker(endPointID, coords2, icon2, roadName, "", ""); 

                                        } else {

                                            var latitude = locationData['latitude'];
                                            var longitude = locationData['longitude'];
                                            var roadName = locationData['roadName'];
                                            var location = locationData['location'];
                                            var postcode = locationData['postcode'];
                                            var displayOrder = locationData['displayOrder'];
                                            var coords = {lat:latitude, lng:longitude};
                                            var icon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/numberedmarkers/marker_red"+ displayOrder +".png");
                                            fnCreateMarker(data['locationID'], coords, icon, roadName, location, postcode);
                                        }
                                        
                                        fnRefreshLocationList();
                                        
									} else if( data.state == "danger" ) {
										$.smallBox({title:"Error:",content:data.message,color:"#C46A69",iconSmall:"fal fa-exclamation shake animated"});
									} else if(data.state == "info") {
										$.smallBox({title:"Info:",content:data.message,color:"#3276B1",timeout:8000,iconSmall:"fal fa-info shake animated"});
									}
								}
							});
							return false;
						}
					});
					$("#dlgLocationDetails button#btnCancel" ).unbind().on( "click" , function(e) {
						$("#dlgLocationDetails" ).modal( "hide" );
						$("#dlgLocationDetails button#btnSave" ).html( "Save" );
						$("#dlgLocationDetails button#btnSave" ).prop( "disabled", false );
						$("#dlgLocationDetails" ).formValidation("resetForm");
						$("#dlgLocationDetails .fa-asterisk" ).css({ 'display' : '',});
						$("#dlgLocationDetails .fa-asterisk" ).addClass( "far" );
						$("#popup_permitLocationID,#popup_startPointID,#popup_endPointID,#popup_cwayDirection,#popup_areaCodeID").selectpicker("val", "0");
						$("#popup_roadName, #popup_location, #popup_postcode, #popup_easting, #popup_northing" ).val( "" );
						$("#councilLookup").html("");
						$("#inputPostcodeLookup").removeAttr("style");
					});
				}
				function fnCreateMarker(id, coords, icon, roadName, location, postcode, draggable = true){
					markerData["m"+id] = new H.map.Marker(coords, {icon: icon});markerData["m"+id].draggable = draggable;
					markerData["m"+id].setData(roadName+","+location+","+postcode)
					fnCreateListener(map, behavior, markerData["m"+id], id);
					markerGroup.addObject(markerData["m"+id]);
					fnZoomLevel();
				}
				function fnUpdateMarkers(){
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/workLocations/quote/ajax-workLocation.php",
						data: { "action" : "getLocationList", "quoteid" : "20392" },
						dataType: "json",
						cache: false,
						success: function(data){
                            $.each(data, function (key, val) {
                                var icon = new H.map.Icon("https://dev-erp.traffic.org.uk/public/img/icons/map/numberedmarkers/marker_red"+val.displayOrder+".png");
                                markerData["m"+val.locationID].setIcon(icon);
                            });
						}
					});
				}
				function fnZoomLevel(){
					markerCount = markerGroup.getObjects().length;
					if (markerCount > 1) {
						map.setViewBounds(markerGroup.getBounds());
					} else if (markerCount <= 1){
						map.setViewBounds(markerGroup.getBounds());
						map.setZoom(15);
					}
				}
				function fnRefreshLocationList(){
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quoteDetail.php",
						data: { "action" : "refreshLocationList", "quoteid" : "20392" },
						cache: false,
						dataType: "json",
						success: function(data){
                                $("#quoteDetailListDT").DataTable().ajax.reload(); 
                                $("#qdLocationID").empty();
                                $("#qdLocationID").append($("<option>", {
                                    value: 0,
                                    text: "Please Select From The Below List"
                                }));
                                $.each(data, function (key, val) {
                                    $("#qdLocationID").append($("<option>", {
                                        value: val.locationID,
                                        text: val.roadName
                                    }));
                                });
                                $("#qdLocationID").selectpicker("refresh");
                                $("#singleLocationID").empty();
                                $("#singleLocationID").append($("<option>", {
                                    value: 0,
                                    text: "Please Select From The Below List"
                                }));
                                $.each(data, function (key, val) {
                                    $("#singleLocationID").append($("<option>", {
                                        value: val.locationID,
                                        text: val.roadName+" "+val.startPointName+" "+val.endPointName
                                    }));
                                });
                                $("#singleLocationID").selectpicker("refresh");
                                $("#appLocationID").empty();
                                $("#appLocationID").append($("<option>", {
                                    value: 0,
                                    text: "Please Select From The Below List"
                                }));
                                $.each(data, function (key, val) {
                                    $("#appLocationID").append($("<option>", {
                                        value: val.locationID,
                                        text: val.roadName
                                    }));
                                });
                                $("#appLocationID").selectpicker("refresh");
							
						}
					});
				}
				function fnCreateListener(map, behavior, object, id){

				  object.addEventListener("dragstart", function(ev) {
					  behavior.disable();
				  }, false);

				  object.addEventListener("dragend", function(ev) {
					  fnZoomLevel();
					  var lat = object.getPosition().lat;
					  var lng = object.getPosition().lng;
					  fnUpdateLocation(id, lat, lng)
					  behavior.enable();
					  behavior.disable(H.mapevents.Behavior.WHEELZOOM);
				  }, false);

				   object.addEventListener("drag", function(ev) {
					var target = ev.target,
						pointer = ev.currentPointer;
					if (target instanceof mapsjs.map.Marker) {
					  target.setPosition(map.screenToGeo(pointer.viewportX, pointer.viewportY));
					}
				  }, false);

				  object.addEventListener("tap", function (evt) {
					// event target is the marker itself, group is a parent event target
					// for all objects that it contains
					var bubble =  new H.ui.InfoBubble(evt.target.getPosition(), {
					  // read custom data
					  content: evt.target.getData()
					});
					// show info bubble
					ui.addBubble(bubble);
				  }, false);
				}
				function fnShowSingleLocationHTML(){
					$("#dispSingleLocationDetails").show();
					$("#dispPermitLocationList").show();
					$("#dispSingleLocationMapDetails").show();
					$("#dispMultipleLocationDetails").hide();
					$("#dispMultipleLocationDropdown").hide();
					$("#dispMultipleLocationMapDetails").hide();
					$("#dispAuthAppLocation").hide();
					$("#roadName").prop("disabled", false);
					$("#location").prop("disabled", false);
					$("#postcode").prop("disabled", false);
					$("#startPointID").prop("disabled", false);
					$("#startPointID").selectpicker("refresh");
					$("#endPointID").prop("disabled", false);
                    $("#endPointID").selectpicker("refresh");
                    $("#cwayDirection").prop("disabled", false);
                    $("#cwayDirection").selectpicker("refresh");
                    $("#areaCodeID").prop("disabled", false);
                    $("#areaCodeID").selectpicker("refresh");
					$("#appLocationID").prop("disabled", true);
					$("#qdLocationID").prop("disabled", true);
				}
				function fnShowMultiLocationDetailHTML(){
					$("#dispMultipleLocationDetails").show();
					$("#dispMultipleLocationDropdown").show();
					$("#dispMultipleLocationMapDetails").show();
					$("#dispSingleLocationDetails").hide();
					$("#dispPermitLocationList").hide();
					$("#dispSingleLocationMapDetails").hide();
					$("#dispAuthAppLocation").show();
					$("#roadName").prop("disabled", true);
					$("#location").prop("disabled", true);
					$("#postcode").prop("disabled", true);
                    $("#startPointID").prop("disabled", true);
					$("#startPointID").selectpicker("refresh");
					$("#endPointID").prop("disabled", true);
                    $("#endPointID").selectpicker("refresh");
                    $("#cwayDirection").prop("disabled", true);
                    $("#cwayDirection").selectpicker("refresh");
                    $("#areaCodeID").prop("disabled", true);
                    $("#areaCodeID").selectpicker("refresh");
					$("#appLocationID").prop("disabled", false);
					$("#qdLocationID").prop("disabled", false);
				}
				$("#btnConvQuoteToOrder").click(function(e){
					e.preventDefault();
					$.ajax({
						type: "POST",
						url: "https://dev-erp.traffic.org.uk/public/ajax/quotes/ajax-quote.php",
						data: { "action" : "checkQuoteDetails", "quoteid" : 20392},
						cache: false,
						success: function( data ){
							if( data != "passed" ) {
								$("#ajaxCheckQuoteDetailsResponse" ).html(data);
								$("#dlgCheckQuoteDetails" ).modal( "show" );
							} else {
								$("#convertQuoteToOrder").prop("disabled", true);
								$("form#editQuote").append("<input type='hidden' name='convertQuoteToOrder' value='Convert Quote To Order'>");
								$("form#editQuote").submit();
							}
						}
					});
				});
			