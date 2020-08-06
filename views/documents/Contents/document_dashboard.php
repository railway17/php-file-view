        <div class='row'>
            <div class='col-md-4'>
                <div class='content'>
                    <div class='row tree-view area'>
                        <div class='area-title'><h3>Document Information</h3></div>
                        <div class='info-area'>
                            <div class='row'><div class='col-md-6'>ID:</div><div class='col-md-6'><?php echo $this->getData('data')['curDoc']['documentID'];?></div></div>
                            <div class='row'><div class='col-md-6'>Name:</div><div class='col-md-6'><?php echo $this->getData('data')['curDoc']['folderName'];?></div></div>
                            <div class='row'><div class='col-md-6'>Owner:</div><div class='col-md-6'><?php echo $this->getData('data')['curDoc']['OwnerName'];?></div></div>
                            <div class='row'><div class='col-md-6'>Default Access Mode:</div><div class='col-md-6'><?php echo $this->getData('data')['accessModes'][$this->getData('data')['curDoc']['defaultAccessMode']];?></div></div>
                            <div class='row'><div class='col-md-6'>Access Mode:</div><div class='col-md-6'><?php echo isset($this->getData('data')['accessMode']) ? $this->getData('data')['accessModes'][$this->getData('data')['accessMode']] : '';?></div></div>
                            <div class='row'><div class='col-md-6'>Used Disk Space:</div><div class='col-md-6'><?php echo $this->getData('data')['curDoc']['fileSize'];?></div></div>
                            <div class='row'><div class='col-md-6'>Created:</div><div class='col-md-6'><?php echo $this->getData('data')['curDoc']['uploadedDate'];?></div></div>
                        </div>
                    </div>
                    <div class='row clipboard area'>
                        <div class='area-title'><h3>Clipboard</h3></div>
                        
                    </div>
                </div>
            </div>
            <div class='col-md-8'>
                <div class='content'>
                    <div class='row tree-view area'>
                        <div class='area-title'><h3>Current Version</h3></div>
                        <div class='info-area'>
                            <div class='row panel-title'>
                                <div class='col-md-6 bold-text'><?php echo $this->getData('data')['curDoc']['docFileName'];?></div>
                                <div class='col-md-3 bold-text'>Status</div>
                                <div class='col-md-3'></div>
                            </div>
                            <div class='row panel-content'>
                                <div class='col-md-6 file-overview'>
                                    <div class='file-thumbnail' id='file_thumb'>
                                        <?php
                                            $doc = $this->getData('data')['curDoc'];
                                            $ext = end((explode(".", $doc['docFileName'])));
                                            if($ext == 'pdf') {
                                                echo '<i class="fa fa-file-pdf-o fa-5x"></i>';
                                            } else if(in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                                echo '<img width="100%" src="'.$this->getData('data')['realPath'].'"/>';
                                            } else {
                                                echo '<i class="fa fa-file-o fa-5x"></i>';
                                            }
                                        ?>
                                    </div>
                                    <div class='detail'>
                                        <div>Version: 1</div>
                                        <div><?php echo $this->getData('data')['curDoc']['fileSize'];?></div>
                                        <div>Uploaded by <?php echo $this->getData('data')['curDoc']['OwnerName'];?></div>
                                        <div><?php echo $this->getData('data')['curDoc']['uploadedDate'];?></div>
                                    </div>
                                </div>
                                <div class='col-md-3'><?php echo count($this->getData('data')['revisions']) > 0 ? $this->getData('data')['revisions'][0]['revisedAction'] : ''; ?></div>
                                <div class='col-md-3'>
                                    <div class='action-item'><i class='fa fa-download'></i>
                                        <a href="<?php echo $this->getData('data')['realPath'];?>" download="<?php echo $this->getData('data')['curDoc']['docFileName']?>">
                                            Download
                                        </a>
                                    </div>
                                    <div class='action-item disabled-text'><i class='fa fa-star'></i>View Online</div>
                                    <div class='action-item disabled-text'><i class='fa fa-times'></i>Remove Version</div>
                                    <div class='action-item disabled-text'><i class='fa fa-check-square-o'></i>Set Recipient</div>
                                    <div class='action-item disabled-text'><i class='fa fa-list-ul'></i>Add to Transmittal</div>
                                    <div class='action-item disabled-text'><i class='fa fa-comment'></i>Edit Comment</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='row tree-view area'>
                        <div class='area-title'><h3>Status</h3></div>
                        <div class='content-table'>
                            <div class='info-area'>
                                <div class='row panel-title'>
                                    <div class='col-md-3 bold-text'>Date</div>            
                                    <div class='col-md-2 bold-text'>Status</div>            
                                    <div class='col-md-2 bold-text'>User</div>            
                                    <div class='col-md-5 bold-text'>Comment</div>            
                                </div>
                                <?php 
                                    $revisions = $this->getData('data')['revisions'];
                                    $content = '';
                                    foreach($revisions as $value) {
                                        $content .= "<div class='row panel-content'>
                                            <div class='col-md-3'>".$value['revisedAt']."</div>            
                                            <div class='col-md-2'>".$value['revisedAction']."</div>            
                                            <div class='col-md-2'>".$value['revisedUser']."</div>            
                                            <div class='col-md-5'>".$value['comment']."</div>            
                                        </div>";
                                    }
                                    echo $content;
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>