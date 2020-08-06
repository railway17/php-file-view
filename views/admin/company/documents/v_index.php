<?php
	
	$content = '
	<h1 class="page-title txt-color-blueDark"><i class="fal fa-lg fa-fw fa-files-o"></i> Company File: <strong>Operations Directory</strong></h1>
	<section id="widget-grid">
		<div class="row">
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="jarviswidget" id="wid-id-3" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false">
					<ul class="nav nav-tabs" id="companyDocTypeTab" role="tablist">';
						if($this->getData('compDocTypeArray'))
						{
							foreach($this->getData('compDocTypeArray') as $docType)
							{
								$content .= '
								<li><a style="padding: 9px 4px 10px;" href="#'.strtolower($docType['tag']).'" role="tab" data-toggle="tab">'.$docType['title'].'</a></li>';
							}
						}
					$content .= '
					</ul>
					<div class="tab-content no-padding">';
						if($this->getData('compDocTypeArray'))
						{
							foreach($this->getData('compDocTypeArray') as $docType)
							{						
								$content .= '
								<div class="tab-pane fade" id="'.$docType['tag'].'">
									<div class="dataTables_wrapper table-responsive form-inline no-footer">
										<table id="'.strtolower($docType['tag']).'ListDT"class="table table-condensed table-striped table-bordered table-hover dataTable" width="100%" cellpadding="0" cellspacing="0" role="datatable">
											<thead>
												<tr class="hidden-xs">
													<th class="hasinput">
														<input type="text" id="dt_fltr_0" class="form-control input-sm" placeholder="Column Search ..." />
													</th>
													<th class="hasinput">
														<input type="text" id="dt_fltr_1" class="form-control input-sm" placeholder="Column Search ..." />
													</th>
													<th class="hasinput">
														<input type="text" id="dt_fltr_2" class="form-control input-sm" placeholder="Column Search ..." />
													</th>
													<th class="hasinput">
														<input type="text" id="dt_fltr_4" class="form-control input-sm" placeholder="Column Search ..." />
													</th>
													<th class="hasinput">
														<input type="text" id="dt_fltr_5" class="form-control input-sm maskTime" placeholder="Column Search ..." />
													</th>
													<th class="hasinput">
														<input type="text" id="dt_fltr_6" class="form-control input-sm" placeholder="Column Search ..." />
													</th>
													<th></th>
													<th></th>
												</tr>
												<tr>
													<th>Title</th>
													<th>Category</th>
													<th>Review Date</th>
													<th>Uploaded Date</th>
													<th>Uploaded Time</th>
													<th>Uploaded By</th>
													<th>Download</th>
													<th width="18px;"></th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>';
							}
						}
					$content .= '
					</div>
				</div>
			</article>
			<div id="dlgDeleteDocument" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<!-- dialog Header -->
						<div class="modal-header">
							<h4 class="modal-title txt-color-blueDark"><strong>Delete Document Details</strong></h4>
						</div>
						<!-- dialog body -->
						<div class="modal-body">
							<p>Are you sure you want to DELETE this Document from the System?</p>
						</div>
						<!-- dialog buttons -->
						<div class="modal-footer">
							<button type="button" class="btn btn-default" id="btnNo">No</button>
							<button type="button" class="btn btn-primary" id="btnYes">Yes</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>';

	include(ADMIN_VIEWS.'v_template.php');

?>