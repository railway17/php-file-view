<?php global $oAuth; ?>
<aside id="left-panel">
    <div class="login-info">
        <span>
            <a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
                <img src="<?php echo ASSETS_IMG_URL; ?>avatars/sunny.png" alt="me" class="online" />
                <span>
                    <?php echo $_SESSION['fullname']; ?>
                </span>
                <i class="fal fa-angle-down"></i>
            </a>
        </span>
    </div>
    <nav>
        <ul>
			<?php if($oAuth->checkPermissionAccess('adminDashboard', 'index') == true) { ?>
            <li>
                <a href="<?php echo ADMIN_URL.'dashboard/index.php'; ?>" title="Dashboard">
                    <i class="fal fa-fw fa-home"></i>
                    <span class="menu-item-parent">Dashboard</span>
                </a>
            </li>
			<?php } ?>
			<?php if(($oAuth->checkSectorPermissionAccess('documents') == true) || ($oAuth->checkSectorPermissionAccess('audits') == true)) { ?>
            <li>
                <a href="javascript:void(0);" title="Documents">
                    <i class="fal fa-cabinet-filing"></i>
                    <span class="menu-item-parent">Documents</span>
                </a>
                <ul>
					<?php if($oAuth->checkSectorPermissionAccess('documents') == true) { ?>
                    <?php if(($oAuth->checkPermissionAccess('adminCompanyDocuments', 'index') == true) ||
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments14', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments15', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments16', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments17', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments18', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments19', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments20', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments21', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments36', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments38', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments39', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments37', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments22', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments41', 'index') == true) ||
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments69', 'index') == true)) { ?>
					<li class="dropdown-header">Documents</li>
					<?php if(($oAuth->checkPermissionAccess('adminCompanyDocuments',   'index') == true) ||
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments14', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments15', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments16', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments17', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments18', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments19', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments20', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments21', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments36', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments38', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments39', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments37', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments22', 'index') == true) || 
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments41', 'index') == true) ||
							 ($oAuth->checkPermissionAccess('adminCompanyDocuments69', 'index') == true)) { ?>
                    <li>
						<?php if($oAuth->checkPermissionAccess('adminCompanyDocuments21', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=21';?>">Quality Management Systems</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments14', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=14';?>">QMS: Policies &amp; Procedures</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments15', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=15';?>">Risk Assessments</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments16', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=16';?>">Method Statements</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments17', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=17';?>">Toolbox Talks</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments18', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=18';?>">Compliments</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments19', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=19';?>">COSHH Assessments</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments20', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=20';?>">Equipment Register &amp; Inspections</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments36', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=36';?>">Attendance / Document Signatures</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments38', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=38';?>">Operational Bibliography</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments39', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=39';?>">Registration &amp; Certification</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments22', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=22';?>">Insurances</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyDocuments41', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=41';?>">Staff Memos</a></li>
                        <?php } ?>
					 	<?php if($oAuth->checkPermissionAccess('adminCompanyDocuments69', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=69';?>">Employee Handbook & Policies</a></li>
                        <?php } ?>
						<?php if($oAuth->checkPermissionAccess('adminCompanyDocuments', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?doctype=company';?>">All Company Documents</a></li>
                        <?php } ?>
                    </li>
					<?php } ?>
					<?php } ?>
					<?php } ?>
					<?php if($oAuth->checkSectorPermissionAccess('audits') == true) { ?>
                    <li class="dropdown-header">Audits</li>
					<?php if(($oAuth->checkPermissionAccess('adminCompanyAudits', 'index') == true) ||($oAuth->checkPermissionAccess('adminCompanyAudits30', 'index') == true) || ($oAuth->checkPermissionAccess('adminCompanyAudits31', 'index') == true) || ($oAuth->checkPermissionAccess('adminCompanyAudits32', 'index') == true) || ($oAuth->checkPermissionAccess('adminCompanyAudits35', 'index') == true) || ($oAuth->checkPermissionAccess('adminCompanyAudits33', 'index') == true) || ($oAuth->checkPermissionAccess('adminCompanyAudits34', 'index') == true)) { ?>
                    <li>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyAudits30', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=30';?>">Fire Safety</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyAudits31', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=31';?>">Health &amp; Safety</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyAudits32', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=32';?>">Human Resources</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyAudits35', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=35';?>">ISO</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyAudits33', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=33';?>">Sector Schemes</a></li>
                        <?php } ?>
                        <?php if($oAuth->checkPermissionAccess('adminCompanyAudits34', 'index') == true) { ?>
                        <li><a href="<?php echo ADMIN_URL.'documents/index.php?reltypeid=34';?>">Site Audits</a></li>
                        <?php } ?>
						<?php if($oAuth->checkPermissionAccess('adminCompanyAudits', 'index') == true) { ?>
						<li><a href="<?php echo ADMIN_URL.'documents/index.php?doctype=audits';?>">All Audits</a></li>
                        <?php } ?>
                    </li>
					<?php } ?>
					<?php } ?>
                </ul>
            </li>
			<?php } ?>
        </ul>
        <div class="company-logo hidden-xs" style="float:right;padding-right:8px;padding-top:9px;">
            <img src="<?php echo ASSETS_IMG_URL.'logos/tms-nav-logo.png'; ?>" alt="<?php echo SITE_NAME.' Nav Logo' ?>" />
        </div>
    </nav>
</aside>
