<div class='documents-container'>
    <div class='nav-container'>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo SITE_URL.'folders';?>">TMS</a>
                    <input type='hidden' name='cur_doc_id' id='cur_doc_id' value='<?php echo $this->getData('data')['curDocId'];?>' />
                    <input type='hidden' name='view_type' id='view_type' value='DOCUMENT' />
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <!-- <li><a>Update Document</a></li> -->
                        <li><a href="<?php echo SITE_URL.'documents?viewType=Edit&documentId='.$this->getData('data')['curDocId'];?>">Edit Document</a></li>
                        <!-- <li><a>Move Document</a></li>
                        <li><a>Remove Document</a></li>
                        <li><a>Edit Access</a></li> -->
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <?php if(!empty($this->getData('data')['curDoc'])) {?>
    <div class='content-wrapper'>
        <?php
            if($this->getData('data')['viewType'] == 'Edit') {
                include('edit_document.php');
            } else {
                include('document_dashboard.php');
            }
        ?>
    </div>
    <?php }?>
</div>
