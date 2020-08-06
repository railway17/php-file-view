<?php
//
//  FPDI - Version 1.5.2
//
//    Copyright 2004-2014 Setasign - Jan Slabon
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

require_once('fpdf_tpl.php');
require_once('formatedstring.php');

/**
 * Class FPDI
 */
class FPDI extends FPDF_TPL
{
    /**
     * FPDI version
     *
     * @string
     */
    const VERSION = '1.5.2';

    /**
     * Actual filename
     *
     * @var string
     */
    public $currentFilename;

    /**
     * Parser-Objects
     *
     * @var fpdi_pdf_parser[]
     */
    public $parsers = array();

    /**
     * Current parser
     *
     * @var fpdi_pdf_parser
     */
    public $currentParser;

    /**
     * The name of the last imported page box
     *
     * @var string
     */
    public $lastUsedPageBox;

    /**
     * Object stack
     *
     * @var array
     */
    protected $_objStack;

    /**
     * Done object stack
     *
     * @var array
     */
    protected $_doneObjStack;

    /**
     * Current Object Id.
     *
     * @var integer
     */
    protected $_currentObjId;

    /**
     * Cache for imported pages/template ids
     *
     * @var array
     */
    protected $_importedPages = array();

    /**
     * Set a source-file.
     *
     * Depending on the PDF version of the used document the PDF version of the resulting document will
     * be adjusted to the higher version.
     *
     * @param string $filename A valid path to the PDF document from which pages should be imported from
     * @return int The number of pages in the document
     */
    public function setSourceFile($filename)
    {
        $_filename = realpath($filename);
        if (false !== $_filename) {
            $filename = $_filename;
        }

        $this->currentFilename = $filename;

        if (!isset($this->parsers[$filename])) {
            $this->parsers[$filename] = $this->_getPdfParser($filename);
            $this->setPdfVersion(
                max($this->getPdfVersion(), $this->parsers[$filename]->getPdfVersion())
            );
        }

        $this->currentParser =& $this->parsers[$filename];

        return $this->parsers[$filename]->getPageCount();
    }

    /**
     * Returns a PDF parser object
     *
     * @param string $filename
     * @return fpdi_pdf_parser
     */
    protected function _getPdfParser($filename)
    {
        require_once('fpdi_pdf_parser.php');
        return new fpdi_pdf_parser($filename);
    }

    /**
     * Get the current PDF version.
     *
     * @return string
     */
    public function getPdfVersion()
    {
        return $this->PDFVersion;
    }

    /**
     * Set the PDF version.
     *
     * @param string $version
     */
    public function setPdfVersion($version = '1.3')
    {
        $this->PDFVersion = sprintf('%.1F', $version);
    }

    /**
     * Import a page.
     *
     * The second parameter defines the bounding box that should be used to transform the page into a
     * form XObject.
     *
     * Following values are available: MediaBox, CropBox, BleedBox, TrimBox, ArtBox.
     * If a box is not especially defined its default box will be used:
     *
     * <ul>
     *   <li>CropBox: Default -> MediaBox</li>
     *   <li>BleedBox: Default -> CropBox</li>
     *   <li>TrimBox: Default -> CropBox</li>
     *   <li>ArtBox: Default -> CropBox</li>
     * </ul>
     *
     * It is possible to get the used page box by the {@link getLastUsedPageBox()} method.
     *
     * @param int $pageNo The page number
     * @param string $boxName The boundary box to use when transforming the page into a form XObject
     * @param boolean $groupXObject Define the form XObject as a group XObject to support transparency (if used)
     * @return int An id of the imported page/template to use with e.g. fpdf_tpl::useTemplate()
     * @throws LogicException|InvalidArgumentException
     * @see getLastUsedPageBox()
     */
    public function importPage($pageNo, $boxName = 'CropBox', $groupXObject = true)
    {
        if ($this->_inTpl) {
            throw new LogicException('Please import the desired pages before creating a new template.');
        }

        $fn = $this->currentFilename;
        $boxName = '/' . ltrim($boxName, '/');

        // check if page already imported
        $pageKey = $fn . '-' . ((int)$pageNo) . $boxName;
        if (isset($this->_importedPages[$pageKey])) {
            return $this->_importedPages[$pageKey];
        }

        $parser = $this->parsers[$fn];
        $parser->setPageNo($pageNo);

        if (!in_array($boxName, $parser->availableBoxes)) {
            throw new InvalidArgumentException(sprintf('Unknown box: %s', $boxName));
        }

        $pageBoxes = $parser->getPageBoxes($pageNo, $this->k);

        /**
         * MediaBox
         * CropBox: Default -> MediaBox
         * BleedBox: Default -> CropBox
         * TrimBox: Default -> CropBox
         * ArtBox: Default -> CropBox
         */
        if (!isset($pageBoxes[$boxName]) && ($boxName == '/BleedBox' || $boxName == '/TrimBox' || $boxName == '/ArtBox')) {
            $boxName = '/CropBox';
        }
        if (!isset($pageBoxes[$boxName]) && $boxName == '/CropBox') {
            $boxName = '/MediaBox';
        }

        if (!isset($pageBoxes[$boxName])) {
            return false;
        }

        $this->lastUsedPageBox = $boxName;

        $box = $pageBoxes[$boxName];

        $this->tpl++;
        $this->_tpls[$this->tpl] = array();
        $tpl =& $this->_tpls[$this->tpl];
        $tpl['parser'] = $parser;
        $tpl['resources'] = $parser->getPageResources();
        $tpl['buffer'] = $parser->getContent();
        $tpl['box'] = $box;
        $tpl['groupXObject'] = $groupXObject;
        if ($groupXObject) {
            $this->setPdfVersion(max($this->getPdfVersion(), 1.4));
        }

        // To build an array that can be used by PDF_TPL::useTemplate()
        $this->_tpls[$this->tpl] = array_merge($this->_tpls[$this->tpl], $box);

        // An imported page will start at 0,0 all the time. Translation will be set in _putformxobjects()
        $tpl['x'] = 0;
        $tpl['y'] = 0;

        // handle rotated pages
        $rotation = $parser->getPageRotation($pageNo);
        $tpl['_rotationAngle'] = 0;
        if (isset($rotation[1]) && ($angle = $rotation[1] % 360) != 0) {
            $steps = $angle / 90;

            $_w = $tpl['w'];
            $_h = $tpl['h'];
            $tpl['w'] = $steps % 2 == 0 ? $_w : $_h;
            $tpl['h'] = $steps % 2 == 0 ? $_h : $_w;

            if ($angle < 0) {
                $angle += 360;
            }

            $tpl['_rotationAngle'] = $angle * -1;
        }

        $this->_importedPages[$pageKey] = $this->tpl;

        return $this->tpl;
    }

    /**
     * Returns the last used page boundary box.
     *
     * @return string The used boundary box: MediaBox, CropBox, BleedBox, TrimBox or ArtBox
     */
    public function getLastUsedPageBox()
    {
        return $this->lastUsedPageBox;
    }

    /**
     * Use a template or imported page in current page or other template.
     *
     * You can use a template in a page or in another template.
     * You can give the used template a new size. All parameters are optional.
     * The width or height is calculated automatically if one is given. If no
     * parameter is given the origin size as defined in beginTemplate() or of
     * the imported page is used.
     *
     * The calculated or used width and height are returned as an array.
     *
     * @param int $tplIdx A valid template-id
     * @param int $x The x-position
     * @param int $y The y-position
     * @param int $w The new width of the template
     * @param int $h The new height of the template
     * @param boolean $adjustPageSize If set to true the current page will be resized to fit the dimensions
     *                                of the template
     *
     * @return array The height and width of the template (array('w' => ..., 'h' => ...))
     * @throws LogicException|InvalidArgumentException
     */
    public function useTemplate($tplIdx, $x = null, $y = null, $w = 0, $h = 0, $adjustPageSize = false)
    {
        if ($adjustPageSize == true && is_null($x) && is_null($y)) {
            $size = $this->getTemplateSize($tplIdx, $w, $h);
            $orientation = $size['w'] > $size['h'] ? 'L' : 'P';
            $size = array($size['w'], $size['h']);

            if (is_subclass_of($this, 'TCPDF')) {
                $this->setPageFormat($size, $orientation);
            } else {
                $size = $this->_getpagesize($size);

                if ($orientation != $this->CurOrientation ||
                    $size[0] != $this->CurPageSize[0] ||
                    $size[1] != $this->CurPageSize[1]
                ) {
                    // New size or orientation
                    if ($orientation=='P') {
                        $this->w = $size[0];
                        $this->h = $size[1];
                    } else {
                        $this->w = $size[1];
                        $this->h = $size[0];
                    }
                    $this->wPt = $this->w * $this->k;
                    $this->hPt = $this->h * $this->k;
                    $this->PageBreakTrigger = $this->h - $this->bMargin;
                    $this->CurOrientation = $orientation;
                    $this->CurPageSize = $size;
                    $this->PageSizes[$this->page] = array($this->wPt, $this->hPt);
                }
            }
        }

        $this->_out('q 0 J 1 w 0 j 0 G 0 g'); // reset standard values
        $size = parent::useTemplate($tplIdx, $x, $y, $w, $h);
        $this->_out('Q');

        return $size;
    }

    /**
     * Copy all imported objects to the resulting document.
     */
    protected function _putimportedobjects()
    {
        foreach ($this->parsers as $filename => $p) {
            $this->currentParser =& $p;
            if (!isset($this->_objStack[$filename]) || !is_array($this->_objStack[$filename])) {
                continue;
            }
            while (($n = key($this->_objStack[$filename])) !== null) {
                try {
                    $nObj = $this->currentParser->resolveObject($this->_objStack[$filename][$n][1]);
                } catch (Exception $e) {
                    $nObj = array(pdf_parser::TYPE_OBJECT, pdf_parser::TYPE_NULL);
                }

                $this->_newobj($this->_objStack[$filename][$n][0]);

                if ($nObj[0] == pdf_parser::TYPE_STREAM) {
                    $this->_writeValue($nObj);
                } else {
                    $this->_writeValue($nObj[1]);
                }

                $this->_out("\nendobj");
                $this->_objStack[$filename][$n] = null; // free memory
                unset($this->_objStack[$filename][$n]);
                reset($this->_objStack[$filename]);
            }
        }
    }

    /**
     * Writes the form XObjects to the PDF document.
     */
    protected function _putformxobjects()
    {
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        reset($this->_tpls);
        foreach ($this->_tpls as $tplIdx => $tpl) {
            $this->_newobj();
            $currentN = $this->n; // TCPDF/Protection: rem current "n"

            $this->_tpls[$tplIdx]['n'] = $this->n;
            $this->_out('<<' . $filter . '/Type /XObject');
            $this->_out('/Subtype /Form');
            $this->_out('/FormType 1');

            $this->_out(sprintf(
                '/BBox [%.2F %.2F %.2F %.2F]',
                (isset($tpl['box']['llx']) ? $tpl['box']['llx'] : $tpl['x']) * $this->k,
                (isset($tpl['box']['lly']) ? $tpl['box']['lly'] : -$tpl['y']) * $this->k,
                (isset($tpl['box']['urx']) ? $tpl['box']['urx'] : $tpl['w'] + $tpl['x']) * $this->k,
                (isset($tpl['box']['ury']) ? $tpl['box']['ury'] : $tpl['h'] - $tpl['y']) * $this->k
            ));

            $c = 1;
            $s = 0;
            $tx = 0;
            $ty = 0;

            if (isset($tpl['box'])) {
                $tx = -$tpl['box']['llx'];
                $ty = -$tpl['box']['lly'];

                if ($tpl['_rotationAngle'] <> 0) {
                    $angle = $tpl['_rotationAngle'] * M_PI/180;
                    $c = cos($angle);
                    $s = sin($angle);

                    switch ($tpl['_rotationAngle']) {
                        case -90:
                           $tx = -$tpl['box']['lly'];
                           $ty = $tpl['box']['urx'];
                           break;
                        case -180:
                            $tx = $tpl['box']['urx'];
                            $ty = $tpl['box']['ury'];
                            break;
                        case -270:
                            $tx = $tpl['box']['ury'];
                            $ty = -$tpl['box']['llx'];
                            break;
                    }
                }
            } elseif ($tpl['x'] != 0 || $tpl['y'] != 0) {
                $tx = -$tpl['x'] * 2;
                $ty = $tpl['y'] * 2;
            }

            $tx *= $this->k;
            $ty *= $this->k;

            if ($c != 1 || $s != 0 || $tx != 0 || $ty != 0) {
                $this->_out(sprintf(
                    '/Matrix [%.5F %.5F %.5F %.5F %.5F %.5F]',
                    $c,
                    $s,
                    -$s,
                    $c,
                    $tx,
                    $ty
                ));
            }

            $this->_out('/Resources ');

            if (isset($tpl['resources'])) {
                $this->currentParser = $tpl['parser'];
                $this->_writeValue($tpl['resources']); // "n" will be changed
            } else {
                $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
                if (isset($this->_res['tpl'][$tplIdx])) {
                    $res = $this->_res['tpl'][$tplIdx];

                    if (isset($res['fonts']) && count($res['fonts'])) {
                        $this->_out('/Font <<');
                        foreach ($res['fonts'] as $font) {
                            $this->_out('/F' . $font['i'] . ' ' . $font['n'] . ' 0 R');
                        }
                        $this->_out('>>');
                    }
                    if (isset($res['images']) && count($res['images']) ||
                       isset($res['tpls']) && count($res['tpls'])) {
                        $this->_out('/XObject <<');
                        if (isset($res['images'])) {
                            foreach ($res['images'] as $image) {
                                $this->_out('/I' . $image['i'] . ' ' . $image['n'] . ' 0 R');
                            }
                        }
                        if (isset($res['tpls'])) {
                            foreach ($res['tpls'] as $i => $_tpl) {
                                $this->_out($this->tplPrefix . $i . ' ' . $_tpl['n'] . ' 0 R');
                            }
                        }
                        $this->_out('>>');
                    }
                    $this->_out('>>');
                }
            }

            if (isset($tpl['groupXObject']) && $tpl['groupXObject']) {
                $this->_out('/Group <</Type/Group/S/Transparency>>');
            }

            $newN = $this->n; // TCPDF: rem new "n"
            $this->n = $currentN; // TCPDF: reset to current "n"

            $buffer = ($this->compress) ? gzcompress($tpl['buffer']) : $tpl['buffer'];

            if (is_subclass_of($this, 'TCPDF')) {
                $buffer = $this->_getrawstream($buffer);
                $this->_out('/Length ' . strlen($buffer) . ' >>');
                $this->_out("stream\n" . $buffer . "\nendstream");
            } else {
                $this->_out('/Length ' . strlen($buffer) . ' >>');
                $this->_putstream($buffer);
            }
            $this->_out('endobj');
            $this->n = $newN; // TCPDF: reset to new "n"
        }

        $this->_putimportedobjects();
    }

    /**
     * Creates and optionally write the object definition to the document.
     *
     * Rewritten to handle existing own defined objects
     *
     * @param bool $objId
     * @param bool $onlyNewObj
     * @return bool|int
     */
    public function _newobj($objId = false, $onlyNewObj = false)
    {
        if (!$objId) {
            $objId = ++$this->n;
        }

        //Begin a new object
        if (!$onlyNewObj) {
            $this->offsets[$objId] = is_subclass_of($this, 'TCPDF') ? $this->bufferlen : strlen($this->buffer);
            $this->_out($objId . ' 0 obj');
            $this->_currentObjId = $objId; // for later use with encryption
        }

        return $objId;
    }

    /**
     * Writes a PDF value to the resulting document.
     *
     * Needed to rebuild the source document
     *
     * @param mixed $value A PDF-Value. Structure of values see cases in this method
     */
    protected function _writeValue(&$value)
    {
        if (is_subclass_of($this, 'TCPDF')) {
            parent::_prepareValue($value);
        }

        switch ($value[0]) {

            case pdf_parser::TYPE_TOKEN:
                $this->_straightOut($value[1] . ' ');
                break;
            case pdf_parser::TYPE_NUMERIC:
            case pdf_parser::TYPE_REAL:
                if (is_float($value[1]) && $value[1] != 0) {
                    $this->_straightOut(rtrim(rtrim(sprintf('%F', $value[1]), '0'), '.') . ' ');
                } else {
                    $this->_straightOut($value[1] . ' ');
                }
                break;

            case pdf_parser::TYPE_ARRAY:

                // An array. Output the proper
                // structure and move on.

                $this->_straightOut('[');
                for ($i = 0; $i < count($value[1]); $i++) {
                    $this->_writeValue($value[1][$i]);
                }

                $this->_out(']');
                break;

            case pdf_parser::TYPE_DICTIONARY:

                // A dictionary.
                $this->_straightOut('<<');

                reset($value[1]);

                while (list($k, $v) = each($value[1])) {
                    $this->_straightOut($k . ' ');
                    $this->_writeValue($v);
                }

                $this->_straightOut('>>');
                break;

            case pdf_parser::TYPE_OBJREF:

                // An indirect object reference
                // Fill the object stack if needed
                $cpfn =& $this->currentParser->filename;
                if (!isset($this->_doneObjStack[$cpfn][$value[1]])) {
                    $this->_newobj(false, true);
                    $this->_objStack[$cpfn][$value[1]] = array($this->n, $value);
                    $this->_doneObjStack[$cpfn][$value[1]] = array($this->n, $value);
                }
                $objId = $this->_doneObjStack[$cpfn][$value[1]][0];

                $this->_out($objId . ' 0 R');
                break;

            case pdf_parser::TYPE_STRING:

                // A string.
                $this->_straightOut('(' . $value[1] . ')');

                break;

            case pdf_parser::TYPE_STREAM:

                // A stream. First, output the
                // stream dictionary, then the
                // stream data itself.
                $this->_writeValue($value[1]);
                $this->_out('stream');
                $this->_out($value[2][1]);
                $this->_straightOut("endstream");
                break;

            case pdf_parser::TYPE_HEX:
                $this->_straightOut('<' . $value[1] . '>');
                break;

            case pdf_parser::TYPE_BOOLEAN:
                $this->_straightOut($value[1] ? 'true ' : 'false ');
                break;

            case pdf_parser::TYPE_NULL:
                // The null object.

                $this->_straightOut('null ');
                break;
        }
    }


    /**
     * Modified _out() method so not each call will add a newline to the output.
     */
    protected function _straightOut($s)
    {
        if (!is_subclass_of($this, 'TCPDF')) {
            if ($this->state == 2) {
                $this->pages[$this->page] .= $s;
            } else {
                $this->buffer .= $s;
            }
        } else {
            if ($this->state == 2) {
                if ($this->inxobj) {
                    // we are inside an XObject template
                    $this->xobjects[$this->xobjid]['outdata'] .= $s;
                } elseif ((!$this->InFooter) and isset($this->footerlen[$this->page]) and ($this->footerlen[$this->page] > 0)) {
                    // puts data before page footer
                    $pagebuff = $this->getPageBuffer($this->page);
                    $page = substr($pagebuff, 0, -$this->footerlen[$this->page]);
                    $footer = substr($pagebuff, -$this->footerlen[$this->page]);
                    $this->setPageBuffer($this->page, $page . $s . $footer);
                    // update footer position
                    $this->footerpos[$this->page] += strlen($s);
                } else {
                    // set page data
                    $this->setPageBuffer($this->page, $s, true);
                }
            } elseif ($this->state > 0) {
                // set general data
                $this->setBuffer($s);
            }
        }
    }

    /**
     * Ends the document
     *
     * Overwritten to close opened parsers
     */
    public function _enddoc()
    {
        parent::_enddoc();
        $this->_closeParsers();
    }

    /**
     * Close all files opened by parsers.
     *
     * @return boolean
     */
    protected function _closeParsers()
    {
        if ($this->state > 2) {
            $this->cleanUp();
            return true;
        }

        return false;
    }

    /**
     * Removes cycled references and closes the file handles of the parser objects.
     */
    public function cleanUp()
    {
        while (($parser = array_pop($this->parsers)) !== null) {
            /**
             * @var fpdi_pdf_parser $parser
             */
            $parser->closeFile();
        }
    }

    public function PageBreak()
    {
        return $this->PageBreakTrigger;
    }
    public function current_font($c)
    {
        if ($c=='family') {
            return $this->FontFamily;
        } elseif ($c=='style') {
            return $this->FontStyle;
        } elseif ($c=='size') {
            return $this->FontSizePt;
        }
    }
    public function get_color($c)
    {
        if ($c=='fill') {
            return $this->FillColor;
        } elseif ($c=='text') {
            return $this->TextColor;
        }
    }
    public function get_page_width()
    {
        return $this->w;
    }
    public function get_margin($c)
    {
        if ($c=='l') {
            return $this->lMargin;
        } elseif ($c=='r') {
            return $this->rMargin;
        } elseif ($c=='t') {
            return $this->tMargin;
        }
    }
    public function get_linewidth()
    {
        return $this->LineWidth;
    }
    public function get_orientation()
    {
        return $this->CurOrientation;
    }
    private static $hex=array('0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,
   'A'=>10,'B'=>11,'C'=>12,'D'=>13,'E'=>14,'F'=>15);
    public function is_rgb($str)
    {
        $a=true;
        $tmp=explode(',', trim($str, ','));
        foreach ($tmp as $color) {
            if (!is_numeric($color) || $color<0 || $color>255) {
                $a=false;
                break;
            }
        }
        return $a;
    }
    public function is_hex($str)
    {
        $a=true;
        $str=strtoupper($str);
        $n=strlen($str);
        if (($n==7 || $n==4) && $str[0]=='#') {
            for ($i=1; $i<$n; $i++) {
                if (!isset(self::$hex[$str[$i]])) {
                    $a=false;
                    break;
                }
            }
        } else {
            $a=false;
        }
        return $a;
    }
    public function hextodec($str)
    {
        $result=array();
        $str=strtoupper(substr($str, 1));
        $n=strlen($str);
        for ($i=0; $i<3; $i++) {
            if ($n==6) {
                $result[$i]=self::$hex[$str[2*$i]]*16+self::$hex[$str[2*$i+1]];
            } else {
                $result[$i]=self::$hex[$str[$i]]*16+self::$hex[$str[$i]];
            }
        }
        return $result;
    }
    private static $options=array('F'=>'', 'T'=>'', 'D'=>'');
    public function resetColor($str, $p='F')
    {
        if (isset(self::$options[$p]) && self::$options[$p]!=$str) {
            self::$options[$p]=$str;
            $array=array();
            if ($this->is_hex($str)) {
                $array=$this->hextodec($str);
            } elseif ($this->is_rgb($str)) {
                $array=explode(',', trim($str, ','));
                for ($i=0; $i<3; $i++) {
                    if (!isset($array[$i])) {
                        $array[$i]=0;
                    }
                }
            } else {
                $array=array(null, null, null);
                $i=0;
                $tmp=explode(' ', $str);
                foreach ($tmp as $c) {
                    if (is_numeric($c)) {
                        $array[$i]=$c*256;
                        $i++;
                    }
                }
            }
            if ($p=='T') {
                $this->SetTextColor($array[0], $array[1], $array[2]);
            } elseif ($p=='D') {
                $this->SetDrawColor($array[0], $array[1], $array[2]);
            } elseif ($p=='F') {
                $this->SetFillColor($array[0], $array[1], $array[2]);
            }
        }
    }
    private static $font_def='';
    public function resetFont($font_family, $font_style, $font_size)
    {
        if (self::$font_def!=$font_family .'-' . $font_style . '-' .$font_size) {
            self::$font_def=$font_family .'-' . $font_style . '-' .$font_size;
            $this->SetFont($font_family, $font_style, $font_size);
        }
    }
    public function resetStaticData()
    {
        self::$font_def='';
        self::$options=array('F'=>'', 'T'=>'', 'D'=>'');
    }
    /***********************************************************************
    *
    * Based on FPDF method SetFont
    *
    ************************************************************************/
    private function &FontData($family, $style, $size)
    {
        if ($family=='') {
            $family = $this->FontFamily;
        } else {
            $family = strtolower($family);
        }
        $style = strtoupper($style);
        if (strpos($style, 'U')!==false) {
            $style = str_replace('U', '', $style);
        }
        if ($style=='IB') {
            $style = 'BI';
        }
        $fontkey = $family.$style;
        if (!isset($this->fonts[$fontkey])) {
            if ($family=='arial') {
                $family = 'helvetica';
            }
            if (in_array($family, $this->CoreFonts)) {
                if ($family=='symbol' || $family=='zapfdingbats') {
                    $style = '';
                }
                $fontkey = $family.$style;
                if (!isset($this->fonts[$fontkey])) {
                    $this->AddFont($family, $style);
                }
            } else {
                $this->Error('Undefined font: '.$family.' '.$style);
            }
        }
        $result['FontSize'] = $size/$this->k;
        $result['CurrentFont']=&$this->fonts[$fontkey];
        return $result;
    }

    private function setLines(&$fstring, $p, $q)
    {
        $parced_str=& $fstring->parced_str;
        $lines=& $fstring->lines;
        $linesmap=& $fstring->linesmap;
        $cfty=$fstring->get_current_style($p);
        $ffs=$cfty['font-family'] . $cfty['style'];
        if (!isset($fstring->used_fonts[$ffs])) {
            $fstring->used_fonts[$ffs]=& $this->FontData($cfty['font-family'], $cfty['style'], $cfty['font-size']);
        }
        $cw=& $fstring->used_fonts[$ffs]['CurrentFont']['cw'];
        $wmax = $fstring->width*1000*$this->k;
        $j=count($lines)-1;
        $k=strlen($lines[$j]);
        if (!isset($linesmap[$j][0])) {
            $linesmap[$j]=array($p,$p, 0);
        }
        $sl=$cw[' ']*$cfty['font-size'];
        $x=$a=$linesmap[$j][2];
        if ($k>0) {
            $x+=$sl;
            $lines[$j].=' ';
            $linesmap[$j][2]+=$sl;
        }
        $u=$p;
        $t='';
        $l=$p+$q;
        $ftmp='';
        for ($i=$p; $i<$l; $i++) {
            if ($ftmp!=$ffs) {
                $cfty=$fstring->get_current_style($i);
                $ffs=$cfty['font-family'] . $cfty['style'];
                if (!isset($fstring->used_fonts[$ffs])) {
                    $fstring->used_fonts[$ffs]=& $this->FontData($cfty['font-family'], $cfty['style'], $cfty['font-size']);
                }
                $cw=& $fstring->used_fonts[$ffs]['CurrentFont']['cw'];
                $ftmp=$ffs;
            }
            $x+=$cw[$parced_str[$i]]*$cfty['font-size'];
            if ($x>$wmax) {
                if ($a>0) {
                    $t=substr($parced_str, $p, $i-$p);
                    $lines[$j]=substr($lines[$j], 0, $k);
                    $linesmap[$j][1]=$p-1;
                    $linesmap[$j][2]=$a;
                    $x-=($a+$sl);
                    $a=0;
                    $u=$p;
                } else {
                    $x=$cw[$parced_str[$i]]*$cfty['font-size'];
                    $t='';
                    $u=$i;
                }
                $j++;
                $lines[$j]=$t;
                $linesmap[$j]=array();
                $linesmap[$j][0]=$u;
                $linesmap[$j][2]=0;
            }
            $lines[$j].=$parced_str[$i];
            $linesmap[$j][1]=$i;
            $linesmap[$j][2]=$x;
        }
        return;
    }
    public function &extMultiCell($font_family, $font_style, $font_size, $font_color, $w, $txt)
    {
        $result=array();
        if ($w==0) {
            return $result;
        }
        $this->current_font=array('font-family'=>$font_family, 'style'=>$font_style, 'font-size'=>$font_size, 'font-color'=>$font_color);
        $fstring=new formatedString($txt, $w, $this->current_font);
        $word='';
        $p=0;
        $i=0;
        $n=strlen($fstring->parced_str);
        while ($i<$n) {
            $word.=$fstring->parced_str[$i];
            if ($fstring->parced_str[$i]=="\n" || $fstring->parced_str[$i]==' ' || $i==$n-1) {
                $word=trim($word);
                $this->setLines($fstring, $p, strlen($word));
                $p=$i+1;
                $word='';
                if ($fstring->parced_str[$i]=="\n" && $i<$n-1) {
                    $z=0;
                    $j=count($fstring->lines);
                    $fstring->lines[$j]='';
                    $fstring->linesmap[$j]=array();
                }
            }
            $i++;
        }
        if ($n==0) {
            return $result;
        }
        $n=count($fstring->lines);
        for ($i=0; $i<$n; $i++) {
            $result[$i]=$fstring->break_by_style($i);
        }
        return $result;
    }
    private function GetMixStringWidth($line)
    {
        $w = 0;
        foreach ($line['chunks'] as $i=>$chunk) {
            $t=0;
            $cf=& $this->FontData($line['style'][$i]['font-family'], $line['style'][$i]['style'], $line['style'][$i]['font-size']);
            $cw=& $cf['CurrentFont']['cw'];
            $s=implode('', explode(' ', $chunk));
            $l = strlen($s);
            for ($j=0;$j<$l;$j++) {
                $t+=$cw[$s[$j]];
            }
            $w+=$t*$line['style'][$i]['font-size'];
        }
        return $w;
    }
    public function CellBlock($w, $lh, &$lines, $align='J')
    {
        if ($w==0) {
            return;
        }
        $ctmp='';
        $ftmp='';
        foreach ($lines as $i=>$line) {
            $k = $this->k;
            if ($this->y+$lh*$line['height']>$this->PageBreakTrigger) {
                break;
            }
            $dx=0;
            $dw=0;
            if ($line['width']!=0) {
                if ($align=='R') {
                    $dx = $w-$line['width']/($this->k*1000);
                } elseif ($align=='C') {
                    $dx = ($w-$line['width']/($this->k*1000))/2;
                }
                if ($align=='J') {
                    $tmp=explode(' ', implode('', $line['chunks']));
                    $ns=count($tmp);
                    if ($ns>1) {
                        $sx=implode('', $tmp);
                        $delta=$this->GetMixStringWidth($line)/($this->k*1000);
                        $dw=($w-$delta)*(1/($ns-1));
                    }
                }
            }
            $xx=$this->x+$dx;
            foreach ($line['chunks'] as $tj=>$txt) {
                $this->resetFont($line['style'][$tj]['font-family'], $line['style'][$tj]['style'], $line['style'][$tj]['font-size']);
                $this->resetColor($line['style'][$tj]['font-color'], 'T');
                $y=$this->y+0.5*$lh*$line['height'] +0.3*$line['height']/$this->k;
                if ($dw) {
                    $tmp=explode(' ', $txt);
                    foreach ($tmp as $e=>$tt) {
                        if ($e>0) {
                            $xx+=$dw;
                            if ($tt=='') {
                                continue;
                            }
                        }
                        $this->Text($xx, $y, $tt);
                        if ($line['style'][$tj]['href']) {
                            $yr=$this->y+0.5*($lh*$line['height']-$line['height']/$this->k);
                            $this->Link($xx, $yr, $this->GetStringWidth($txt), $line['height']/$this->k, $line['style'][$tj]['href']);
                        }
                        $xx+=$this->GetStringWidth($tt);
                    }
                } else {
                    $this->Text($xx, $y, $txt);
                    if ($line['style'][$tj]['href']) {
                        $yr=$this->y+0.5*($lh*$line['height']-$line['height']/$this->k);
                        $this->Link($xx, $yr, $this->GetStringWidth($txt), $line['height']/$this->k, $line['style'][$tj]['href']);
                    }
                    $xx+=$this->GetStringWidth($txt);
                }
            }
            unset($lines[$i]);
            $this->y += $lh*$line['height'];
        }
    }
}
