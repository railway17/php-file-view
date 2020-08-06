<?php

	class Template
	{
		var $data;
		var $pageID;
		var $alertTypes;
		var $includeTypes;
		var $arrIncludes;
		var $metaTypes;
		var $arrMetaData;
		var $metaData;
		var $pageOperations;

		function load($url)
		{
			include($url);
		}

		function redirect($url)
		{
			header('Location: '.$url);
		}
		
		function setMetaTypes($metaTypes)
		{
			$this->metaTypes = $metaTypes;
		}
		
		function setMetaData($value, $metaType = null)
		{
			$this->arrMetaData[] = array($value, $metaType);
		}
		
		function getMetaData($metaType)
		{
			$data = '';
			if(isset($this->arrMetaData)){
				foreach($this->arrMetaData as $meta){		
					if($meta[1] == 'pageTitle' && $metaType == 'pageTitle'){
						$data .= $meta[0];
					}elseif($meta[1] == 'pageDesc' && $metaType == 'pageDesc'){
						$data .= $meta[0];
					}elseif($meta[1] == 'pageTags' && $metaType == 'pageTags'){
						$data .= $meta[0];
					}
				}
				return $data;
			}
		}
		
		function setIncludeTypes($includeTypes)
		{
			$this->includeTypes = $includeTypes;
		}
		
		function setInclude($value, $includeType = null)
		{
			$this->arrIncludes[] = array($value, $includeType);
		}
		
		function getIncludes($includeType)
		{
			$data = '';
            if(DEV_MODE == false) { $htmlFormatter = ""; } else { $htmlFormatter = "\n"; }
			if(isset($this->arrIncludes)){
				foreach($this->arrIncludes as $include){
					if($include[1] == 'jsInclude' && $includeType == 'jsInclude'){
						$data .= "<script src=\"{$include[0]}\"></script>{$htmlFormatter}";
					}else if($include[1] == 'cssInclude' && $includeType == 'cssInclude'){
						$data .=  "<link rel=\"stylesheet\" href=\"{$include[0]}\" />{$htmlFormatter}";	
					}else if($include[1] == 'metaCode' && $includeType == 'metaCode'){
						$data .=  "{$include[0]}{$htmlFormatter}";	
					}else if($include[1] == 'headerCode' && $includeType == 'headerCode'){
						$data .=  "{$include[0]}{$htmlFormatter}";		
					}else if($include[1] == 'footerCode' && $includeType == 'footerCode'){     
						
						$data .=  "{$include[0]}{$htmlFormatter}";
						
                        /*if(DEV_MODE == false) {
                            $dynamicJSContent = $include[0];
                            //$dynamicJSContent = preg_replace("/[\r\n]+/", "\n", $dynamicJSContent);
                            //$dynamicJSContent = preg_replace("/\s+/", ' ', $dynamicJSContent);
                            $dynamicJSRootFile = ASSETS_JS_ROOT.'dynamic-page.js';
                            $dynamicJSPublicFile = ASSETS_JS_URL.'dynamic-page.js';
                            file_put_contents($dynamicJSRootFile, '');
                            file_put_contents($dynamicJSRootFile, str_replace(array('<script type="text/javascript">','</script>'), '', $dynamicJSContent));
                            $data .=  "<script src=\"".$dynamicJSPublicFile."?u".$_SESSION['userID'].time()."\"></script>".$htmlFormatter;
                        } else {
                            $data .=  "{$include[0]}{$htmlFormatter}";
                        }*/
					}
				}
				return $data;
			}
		}
		
		function setData($name, $value)
		{
			if(is_array($value)){
				$value = multiArrayMap('santizeHTML', $value);
				$this->data[$name] = $value;
			} else {
				$value = santizeHTML($value);
				$this->data[$name] = $value;
			}
		}

		function getData($name)
		{
			if(isset($this->data[$name])){
				return $this->data[$name];
			} else {
				return false;
			}
		}
		
		function setPageID($id)
		{
			$this->pageID = $id;
		}
		
		function getPageID()
		{
			return $this->pageID;
		}

		function setAlertTypes($types)
		{
			$this->alertTypes = $types;
		}
		
		function setAlert($value, $type = null)
		{
			if($type == ''){$type = $this->alertTypes[0];}
			if(is_array($value)){
				foreach($value as $val){
					$_SESSION[$type][] = $val;
				}
			} else {
				$_SESSION[$type][] = $value;
			}
		}
		
		function getAlerts()
		{
			$data = '';
			foreach($this->alertTypes as $alert){			
				if(isset($_SESSION[$alert])){
					foreach($_SESSION[$alert] as $value){
						$data .= '
						<div class="alert alert-'.$alert.' alert-auto alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert">
								<span aria-hidden="true">&times;</span>
								<span class="sr-only">Close</span>
							</button>
							<h4 class="alert-heading">'.ucwords(($alert == 'danger' ? 'Error' : $alert)).'!</h4>
							'.$value.'
						</div>';						
					}
					unset($_SESSION[$alert]);
				}
			}
			return $data;
		}
	}