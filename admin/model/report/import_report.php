<?php

class ModelReportImportReport extends PT_Model {

    private $error = array();
    protected $null_array = array();
    protected $use_table_seo_url = false;
    protected $posted_categories = '';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->use_table_seo_url = version_compare(VERSION, '3.0', '>=') ? true : false;
    }

    protected function clean(&$str, $allowBlanks = false) {
        $result = "";
        $n = strlen($str);
        for ($m = 0; $m < $n; $m++) {
            $ch = substr($str, $m, 1);
            if (($ch == " ") && (!$allowBlanks) || ($ch == "\n") || ($ch == "\r") || ($ch == "\t") || ($ch == "\0") || ($ch == "\x0B")) {
                continue;
            }
            $result .= $ch;
        }
        return $result;
    }

    protected function multiquery($sql) {
        foreach (explode(";\n", $sql) as $sql) {
            $sql = trim($sql);
            if ($sql) {
                $this->db->query($sql);
            }
        }
    }

    protected function startsWith($haystack, $needle) {
        if (strlen($haystack) < strlen($needle)) {
            return false;
        }
        return (substr($haystack, 0, strlen($needle)) == $needle);
    }

    protected function endsWith($haystack, $needle) {
        if (strlen($haystack) < strlen($needle)) {
            return false;
        }
        return (substr($haystack, strlen($haystack) - strlen($needle), strlen($needle)) == $needle);
    }

    protected function getDefaultLanguageId() {
        $code = $this->config->get('config_language');
        $sql = "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '$code'";
        $result = $this->db->query($sql);
        $language_id = 1;
        if ($result->rows) {
            foreach ($result->rows as $row) {
                $language_id = $row['language_id'];
                break;
            }
        }
        return $language_id;
    }

    protected function getLanguages() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE `status`=1 ORDER BY `code`");
        return $query->rows;
    }

    protected function getDefaultWeightUnit() {
        $weight_class_id = $this->config->get('config_weight_class_id');
        $language_id = $this->getDefaultLanguageId();
        $sql = "SELECT unit FROM `" . DB_PREFIX . "weight_class_description` WHERE language_id='" . (int) $language_id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row['unit'];
        }
        $sql = "SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = 'en'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            $language_id = $query->row['language_id'];
            $sql = "SELECT unit FROM `" . DB_PREFIX . "weight_class_description` WHERE language_id='" . (int) $language_id . "'";
            $query = $this->db->query($sql);
            if ($query->num_rows > 0) {
                return $query->row['unit'];
            }
        }
        return 'kg';
    }



    // function for reading additional cells in class extensions
    protected function moreProductSEOKeywordCells($i, &$j, &$worksheet, &$product_seo_keyword) {
        return;
    }

    protected function uploadProductSEOKeywords(&$reader, $incremental, &$available_product_ids) {
        // get worksheet, if not there return immediately
        $data = $reader->getSheetByName('ProductSEOKeywords');
        if ($data == null) {
            return;
        }

        // if DB table 'seo_url' doesn't exist (OpenCart 1.5.x, 2.x versions) then return immediately
        if (!$this->use_table_seo_url) {
            return;
        }

        // if incremental then find current product IDs else delete all old product SEO keywords
        if ($incremental) {
            $unlisted_product_ids = $available_product_ids;
        } else {
            $this->deleteProductSEOKeywords();
        }

        // load the worksheet cells and store them to the database
        $old_seo_url_ids = array();
        $languages = $this->getLanguages();
        $previous_product_id = 0;
        $first_row = array();
        $i = 0;
        $k = $data->getHighestRow();
        for ($i = 0; $i < $k; $i += 1) {
            if ($i == 0) {
                $max_col = PHPExcel_Cell::columnIndexFromString($data->getHighestColumn());
                for ($j = 1; $j <= $max_col; $j += 1) {
                    $first_row[] = $this->getCell($data, $i, $j);
                }
                continue;
            }
            $j = 1;
            $product_id = trim($this->getCell($data, $i, $j++));
            if ($product_id == '') {
                continue;
            }
            $store_id = trim($this->getCell($data, $i, $j++));
            if ($store_id == '') {
                continue;
            }
            $keywords = array();
            while (($j <= $max_col) && $this->startsWith($first_row[$j - 1], "keyword(")) {
                $language_code = substr($first_row[$j - 1], strlen("keyword("), strlen($first_row[$j - 1]) - strlen("keyword(") - 1);
                $keyword = trim($this->getCell($data, $i, $j++, ''));
                $keyword = htmlspecialchars($keyword);
                $keywords[$language_code] = $keyword;
            }
            $product_seo_keyword = array();
            $product_seo_keyword['product_id'] = $product_id;
            $product_seo_keyword['store_id'] = $store_id;
            $product_seo_keyword['keywords'] = $keywords;
            if (($incremental) && ($product_id != $previous_product_id)) {
                $old_seo_url_ids = $this->deleteProductSEOKeyword($product_id);
                if (isset($unlisted_product_ids[$product_id])) {
                    unset($unlisted_product_ids[$product_id]);
                }
            }
            $this->moreProductSEOKeywordCells($i, $j, $data, $product_seo_keyword);
            $this->storeProductSEOKeywordIntoDatabase($product_seo_keyword, $languages, $old_seo_url_ids);
            $previous_product_id = $product_id;
        }
        if ($incremental) {
            $this->deleteUnlistedProductSEOKeywords($unlisted_product_ids);
        }
    }

    protected function storeOptionIntoDatabase(&$option, &$languages) {
        $option_id = $option['option_id'];
        $type = $option['type'];
        $sort_order = $option['sort_order'];
        $names = $option['names'];
        $sql = "INSERT INTO `" . DB_PREFIX . "option` (`option_id`,`type`,`sort_order`) VALUES ";
        $sql .= "( $option_id, '" . $this->db->escape($type) . "', $sort_order );";
        $this->db->query($sql);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            $language_id = $language['language_id'];
            $name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';
            $sql = "INSERT INTO `" . DB_PREFIX . "option_description` (`option_id`, `language_id`, `name`) VALUES ";
            $sql .= "( $option_id, $language_id, '$name' );";
            $this->db->query($sql);
        }
    }

    protected function deleteOptions() {
        $sql = "TRUNCATE TABLE `" . DB_PREFIX . "option`";
        $this->db->query($sql);
        $sql = "TRUNCATE TABLE `" . DB_PREFIX . "option_description`";
        $this->db->query($sql);
    }

    protected function deleteOption($option_id) {
        $sql = "DELETE FROM `" . DB_PREFIX . "option` WHERE option_id='" . (int) $option_id . "'";
        $this->db->query($sql);
        $sql = "DELETE FROM `" . DB_PREFIX . "option_description` WHERE option_id='" . (int) $option_id . "'";
        $this->db->query($sql);
    }

    // function for reading additional cells in class extensions
    protected function moreOptionCells($i, &$j, &$worksheet, &$option) {
        return;
    }

    protected function storeDsrInfoIntoDatabase(&$dsrinfo, &$languages) {

        $dsr_id = $dsrinfo['dsr_id'];
        $job_no = $dsrinfo['job_no'];
        $igm_no = $dsrinfo['igm_no'];
        
        $igm_date1 = $dsrinfo['igm_date'];
        $UNIX_igm_date = ($igm_date1 - 25569) * 86400;
        $igm_date= gmdate("d-m-Y", $UNIX_igm_date);
        
        $po_no = $dsrinfo['po_no'];
        $shipper = $dsrinfo['shipper'];
        $be_heading = $dsrinfo['be_heading'];
        $no_of_package = $dsrinfo['no_of_package'];
        $unit = $dsrinfo['unit'];
        $net_wt = $dsrinfo['net_wt'];
        $mode = $dsrinfo['mode'];
        
        $org_eta_date1 = $dsrinfo['org_eta_date'];
        $UNIX_org_eta_date = ($org_eta_date1 - 25569) * 86400;
        $org_eta_date= gmdate("d-m-Y", $UNIX_org_eta_date);
        
        $shipping_line_date1 = $dsrinfo['shipping_line_date'];
        $UNIX_shipping_line_date = ($shipping_line_date1 - 25569) * 86400;
        $shipping_line_date= gmdate("d-m-Y", $UNIX_shipping_line_date);
        
        $tentative_eta_date1 = $dsrinfo['tentative_eta_date'];
        $UNIX_tentative_eta_date = ($tentative_eta_date1 - 25569) * 86400;
        $tentative_eta_date= gmdate("d-m-Y", $UNIX_tentative_eta_date);
        
        $expected_date1 = $dsrinfo['expected_date'];
        $UNIX_expected_date = ($expected_date1 - 25569) * 86400;
        $expected_date= gmdate("d-m-Y", $UNIX_expected_date);
        
        $invoice_no = $dsrinfo['invoice_no'];
        
        $invoice_date1 = $dsrinfo['invoice_date'];
        $UNIX_invoice_date = ($invoice_date1 - 25569) * 86400;
        $invoice_date= gmdate("d-m-Y", $UNIX_invoice_date);
        
        $mawb_no = $dsrinfo['mawb_no'];
        
        $mawb_date1 = $dsrinfo['mawb_date'];
        $UNIX_mawb_date = ($mawb_date1 - 25569) * 86400;
        $mawb_date= gmdate("d-m-Y", $UNIX_mawb_date);
        
        $hawb_no = $dsrinfo['hawb_no'];
        
        $hawb_date1 = $dsrinfo['hawb_date'];
        $UNIX_hawb_date = ($hawb_date1 - 25569) * 86400;
        $hawb_date= gmdate("d-m-Y", $UNIX_hawb_date);
        
        $be_no = $dsrinfo['be_no'];
        
        $be_date1 = $dsrinfo['be_date'];
        $UNIX_be_date = ($be_date1 - 25569) * 86400;
        $be_date= gmdate("d-m-Y", $UNIX_be_date);
        
        $airline = $dsrinfo['airline'];
        
        $n_document_date1 = $dsrinfo['n_document_date'];
        $UNIX_n_document_date = ($n_document_date1 - 25569) * 86400;
        $n_document_date= gmdate("d-m-Y", $UNIX_n_document_date);
        
        $org_doc_date1 = $dsrinfo['org_doc_date'];
        $UNIX_org_doc_date = ($org_doc_date1 - 25569) * 86400;
        $org_doc_date= gmdate("d-m-Y", $UNIX_org_doc_date);
        
        $duty_inform_date1 = $dsrinfo['duty_inform_date'];
        $UNIX_duty_inform_date = ($duty_inform_date1 - 25569) * 86400;
        $duty_inform_date= gmdate("d-m-Y", $UNIX_duty_inform_date);
        
        $duty_received_date1 = $dsrinfo['duty_received_date'];
        $UNIX_duty_received_date = ($duty_received_date1 - 25569) * 86400;
        $duty_received_date= gmdate("d-m-Y", $UNIX_duty_received_date);
        
        $duty_paid_date1 = $dsrinfo['duty_paid_date'];
        $UNIX_duty_paid_date = ($duty_paid_date1 - 25569) * 86400;
        $duty_paid_date= gmdate("d-m-Y", $UNIX_duty_paid_date);
        
        $total_duty = $dsrinfo['total_duty'];
        
        $container_cleared_date1 = $dsrinfo['container_cleared_date'];
        $UNIX_container_cleared_date = ($container_cleared_date1 - 25569) * 86400;
        $container_cleared_date= gmdate("d-m-Y", $UNIX_container_cleared_date);
        
        $detention_amt = $dsrinfo['detention_amt'];
        $customer_remark = $dsrinfo['customer_remark'];
        $delivery_location_remark = $dsrinfo['delivery_location_remark'];
        $container_no = $dsrinfo['container_no'];
        
        $free_period_shipping_date1 = $dsrinfo['free_period_shipping_date'];
        $UNIX_free_period_shipping_date = ($free_period_shipping_date1 - 25569) * 86400;
        $free_period_shipping_date= gmdate("d-m-Y", $UNIX_free_period_shipping_date);
        
        $expected_free_dt_date1 = $dsrinfo['expected_free_dt_date'];
        $UNIX_expected_free_dt_date = ($expected_free_dt_date1 - 25569) * 86400;
        $expected_free_dt_date= gmdate("d-m-Y", $UNIX_expected_free_dt_date);
        
        $expected_free_dt_remark = $dsrinfo['expected_free_dt_remark'];
        $customer_id = $dsrinfo['customer_id'];


        $names = $dsrinfo['names'];
//		$sql  = "INSERT INTO `".DB_PREFIX."dsr` (`attribute_group_id`,`sort_order`) VALUES ";
//		$sql .= "( $attribute_group_id, $sort_order );"; 
//		$this->db->query( $sql );
        foreach ($languages as $language) {
            $language_code = $language['code'];
            $language_id = $language['language_id'];
            $name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';
            $sql = "INSERT INTO `" . DB_PREFIX . "temp_dsr` (`dsr_id`,`job_no`, `igm_no`, `igm_date`, `po_no`, `shipper`, `be_heading`, `no_of_package`, `unit`, `net_wt`, `mode`,"
                    . " `org_eta_date`, `shipping_line_date`, `tentative_eta_date`, `expected_date`, `invoice_no`, `invoice_date`, `mawb_no`, `mawb_date`,"
                    . "`hawb_no`, `hawb_date`, `be_no`, `be_date`, `airline`, `n_document_date`, `org_doc_date`, `duty_inform_date`, `duty_received_date`, `duty_paid_date`,"
                    . " `total_duty`, `container_cleared_date`, `detention_amt`, `customer_remark`, `delivery_location_remark`, `container_no`, `free_period_shipping_date`, "
                    . "`expected_free_dt_date`, `expected_free_dt_remark`, `customer_id`) VALUES ";
            $sql .= "( '$dsr_id','$job_no', '$igm_no', '$igm_date', '$po_no', '$shipper', '$be_heading', '$no_of_package', '$unit',"
                    . " '$net_wt', '$mode', '$org_eta_date', '$shipping_line_date', '$tentative_eta_date', '$expected_date', '$invoice_no', '$invoice_date', "
                    . "'$mawb_no', '$mawb_date', '$hawb_no', '$hawb_date', '$be_no', '$be_date', '$airline', '$n_document_date', '$org_doc_date',"
                    . " '$duty_inform_date', '$duty_received_date', '$duty_paid_date', '$total_duty', '$container_cleared_date', '$detention_amt', '$customer_remark', "
                    . " '$delivery_location_remark', '$container_no', '$free_period_shipping_date', '$expected_free_dt_date', '$expected_free_dt_remark','$customer_id');";
            $this->db->query($sql);
        }
    }

//
//	protected function storeAttributeGroupIntoDatabase( &$attribute_group, &$languages ) {
//		$attribute_group_id = $attribute_group['attribute_group_id'];
//		$sort_order = $attribute_group['sort_order'];
//		$names = $attribute_group['names'];
//		$sql  = "INSERT INTO `".DB_PREFIX."attribute_group` (`attribute_group_id`,`sort_order`) VALUES ";
//		$sql .= "( $attribute_group_id, $sort_order );"; 
//		$this->db->query( $sql );
//		foreach ($languages as $language) {
//			$language_code = $language['code'];
//			$language_id = $language['language_id'];
//			$name = isset($names[$language_code]) ? $this->db->escape($names[$language_code]) : '';
//			$sql  = "INSERT INTO `".DB_PREFIX."attribute_group_description` (`attribute_group_id`, `language_id`, `name`) VALUES ";
//			$sql .= "( $attribute_group_id, $language_id, '$name' );";
//			$this->db->query( $sql );
//		}
//	}



    // function for reading additional cells in class extensions
    protected function moreDsrInfoCells($i, &$j, &$worksheet, &$dsrinfo) {
        return;
    }

//	protected function moreAttributeGroupCells( $i, &$j, &$worksheet, &$attribute_group ) {
//		return;
//	}


    protected function uploadDsrInfo(&$reader, $incremental) {
        // get worksheet, if not there return immediately
        $data = $reader->getSheetByName('dsr');
        if ($data == null) {
            return;
        }

        // find the installed languages
        $languages = $this->getLanguages();

        // if not incremental then delete all old attribute groups
//		if (!$incremental) {
//			$this->deleteAttributeGroups();
//		}
        // load the worksheet cells and store them to the database
        $first_row = array();
        $i = 0;
        $k = $data->getHighestRow();
        for ($i = 0; $i < $k; $i += 1) {
            if ($i == 0) {
                $max_col = PHPExcel_Cell::columnIndexFromString($data->getHighestColumn());
                for ($j = 1; $j <= $max_col; $j += 1) {
                    $first_row[] = $this->getCell($data, $i, $j);
                }
                continue;
            }
            $j = 1;
            $dsr_id = trim($this->getCell($data, $i, $j++));
            if ($dsr_id == '') {
                continue;
            }
            $job_no = $this->getCell($data, $i, $j++);
            $igm_no = $this->getCell($data, $i, $j++);
            $igm_date = $this->getCell($data, $i, $j++, '0000-00-00');
            $po_no = $this->getCell($data, $i, $j++);
            $shipper = $this->getCell($data, $i, $j++);
            $be_heading = $this->getCell($data, $i, $j++);
            $no_of_package = $this->getCell($data, $i, $j++);
            $unit = $this->getCell($data, $i, $j++);
            $net_wt = $this->getCell($data, $i, $j++);
            $mode = $this->getCell($data, $i, $j++);
            $org_eta_date = $this->getCell($data, $i, $j++);
            $shipping_line_date = $this->getCell($data, $i, $j++);
            $tentative_eta_date = $this->getCell($data, $i, $j++);
            $expected_date = $this->getCell($data, $i, $j++);
            $invoice_no = $this->getCell($data, $i, $j++);
            $invoice_date = $this->getCell($data, $i, $j++);
            $mawb_no = $this->getCell($data, $i, $j++);
            $mawb_date = $this->getCell($data, $i, $j++);
            $hawb_no = $this->getCell($data, $i, $j++);
            $hawb_date = $this->getCell($data, $i, $j++);
            $be_no = $this->getCell($data, $i, $j++);
            $be_date = $this->getCell($data, $i, $j++);
            $airline = $this->getCell($data, $i, $j++);
            $n_document_date = $this->getCell($data, $i, $j++);
            $org_doc_date = $this->getCell($data, $i, $j++);
            $duty_inform_date = $this->getCell($data, $i, $j++);
            $duty_received_date = $this->getCell($data, $i, $j++);
            $duty_paid_date = $this->getCell($data, $i, $j++);
            $total_duty = $this->getCell($data, $i, $j++);
            $container_cleared_date = $this->getCell($data, $i, $j++);
            $detention_amt = $this->getCell($data, $i, $j++);
            $customer_remark = $this->getCell($data, $i, $j++);
            $delivery_location_remark = $this->getCell($data, $i, $j++);
            $container_no = $this->getCell($data, $i, $j++);
            $free_period_shipping_date = $this->getCell($data, $i, $j++);
            $expected_free_dt_date = $this->getCell($data, $i, $j++);
            $expected_free_dt_remark = $this->getCell($data, $i, $j++);
            $names = array();
            while (($j <= $max_col) && $this->startsWith($first_row[$j - 1], "name(")) {
                $language_code = substr($first_row[$j - 1], strlen("name("), strlen($first_row[$j - 1]) - strlen("name(") - 1);
                $name = $this->getCell($data, $i, $j++);
                $name = htmlspecialchars($name);
                $names[$language_code] = $name;
            }
            $dsrinfo = array();
            $dsrinfo['dsr_id'] = $dsr_id;
            $dsrinfo['job_no'] = $job_no;
            $dsrinfo['igm_no'] = $igm_no;
            $dsrinfo['igm_date'] = $igm_date;
            $dsrinfo['po_no'] = $po_no;
            $dsrinfo['shipper'] = $shipper;
            $dsrinfo['be_heading'] = $be_heading;
            $dsrinfo['no_of_package'] = $no_of_package;
            $dsrinfo['unit'] = $unit;
            $dsrinfo['net_wt'] = $net_wt;
            $dsrinfo['mode'] = $mode;
            $dsrinfo['org_eta_date'] = $org_eta_date;
            $dsrinfo['shipping_line_date'] = $shipping_line_date;
            $dsrinfo['tentative_eta_date'] = $tentative_eta_date;
            $dsrinfo['expected_date'] = $expected_date;
            $dsrinfo['invoice_no'] = $invoice_no;
            $dsrinfo['invoice_date'] = $invoice_date;
            $dsrinfo['mawb_no'] = $mawb_no;
            $dsrinfo['mawb_date'] = $mawb_date;
            $dsrinfo['hawb_no'] = $hawb_no;
            $dsrinfo['hawb_date'] = $hawb_date;
            $dsrinfo['be_no'] = $be_no;
            $dsrinfo['be_date'] = $be_date;
            $dsrinfo['airline'] = $airline;
            $dsrinfo['n_document_date'] = $n_document_date;
            $dsrinfo['org_doc_date'] = $org_doc_date;
            $dsrinfo['duty_inform_date'] = $duty_inform_date;
            $dsrinfo['duty_received_date'] = $duty_received_date;
            $dsrinfo['duty_paid_date'] = $duty_paid_date;
            $dsrinfo['total_duty'] = $total_duty;
            $dsrinfo['container_cleared_date'] = $container_cleared_date;
            $dsrinfo['detention_amt'] = $detention_amt;
            $dsrinfo['customer_remark'] = $customer_remark;
            $dsrinfo['delivery_location_remark'] = $delivery_location_remark;
            $dsrinfo['container_no'] = $container_no;
            $dsrinfo['free_period_shipping_date'] = $free_period_shipping_date;
            $dsrinfo['expected_free_dt_date'] = $expected_free_dt_date;
            $dsrinfo['expected_free_dt_remark'] = $expected_free_dt_remark;
            $dsrinfo['customer_id'] = $incremental;
//			if ($incremental) {
//				$this->deleteAttributeGroup( $dsr_id );
//			}
            $this->moreDsrInfoCells($i, $j, $data, $dsrinfo);
            $this->storeDsrInfoIntoDatabase($dsrinfo, $languages);
        }
    }


    protected function getCell(&$worksheet, $row, $col, $default_val = '') {
        $col -= 1; // we use 1-based, PHPExcel uses 0-based column index
        $row += 1; // we use 0-based, PHPExcel uses 1-based row index
        $val = ($worksheet->cellExistsByColumnAndRow($col, $row)) ? $worksheet->getCellByColumnAndRow($col, $row)->getValue() : $default_val;
        if ($val === null) {
            $val = $default_val;
        }
        return $val;
    }

    protected function validateHeading(&$data, &$expected, &$multilingual) {
        $default_language_code = $this->config->get('config_language');
        $heading = array();
        $k = PHPExcel_Cell::columnIndexFromString($data->getHighestColumn());
        $i = 0;
        for ($j = 1; $j <= $k; $j += 1) {
            $entry = $this->getCell($data, $i, $j);
            $bracket_start = strripos($entry, '(', 0);
            if ($bracket_start === false) {
                if (in_array($entry, $multilingual)) {
                    return false;
                }
                $heading[] = strtolower($entry);
            } else {
                $name = strtolower(substr($entry, 0, $bracket_start));
                if (!in_array($name, $multilingual)) {
                    return false;
                }
                $bracket_end = strripos($entry, ')', $bracket_start);
                if ($bracket_end <= $bracket_start) {
                    return false;
                }
                if ($bracket_end + 1 != strlen($entry)) {
                    return false;
                }
                $language_code = strtolower(substr($entry, $bracket_start + 1, $bracket_end - $bracket_start - 1));
                if (count($heading) <= 0) {
                    return false;
                }
                if ($heading[count($heading) - 1] != $name) {
                    $heading[] = $name;
                }
            }
        }
        for ($i = 0; $i < count($expected); $i += 1) {
            if (!isset($heading[$i])) {
                return false;
            }
            if ($heading[$i] != $expected[$i]) {
                return false;
            }
        }
        return true;
    }

    protected function validateCategories(&$reader) {
        $data = $reader->getSheetByName('Categories');
        if ($data == null) {
            return true;
        }

        // Opencart versions from 2.0 onwards also have category_description.meta_title
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "category_description` LIKE 'meta_title'";
        $query = $this->db->query($sql);
        $exist_meta_title = ($query->num_rows > 0) ? true : false;

        if ($this->use_table_seo_url) {
            if ($exist_meta_title) {
                $expected_heading = array
                    ("category_id", "parent_id", "name", "top", "columns", "sort_order", "image_name", "date_added", "date_modified", "description", "meta_title", "meta_description", "meta_keywords", "store_ids", "layout", "status");
            } else {
                $expected_heading = array
                    ("category_id", "parent_id", "name", "top", "columns", "sort_order", "image_name", "date_added", "date_modified", "description", "meta_description", "meta_keywords", "store_ids", "layout", "status");
            }
        } else {
            if ($exist_meta_title) {
                $expected_heading = array
                    ("category_id", "parent_id", "name", "top", "columns", "sort_order", "image_name", "date_added", "date_modified", "seo_keyword", "description", "meta_title", "meta_description", "meta_keywords", "store_ids", "layout", "status");
            } else {
                $expected_heading = array
                    ("category_id", "parent_id", "name", "top", "columns", "sort_order", "image_name", "date_added", "date_modified", "seo_keyword", "description", "meta_description", "meta_keywords", "store_ids", "layout", "status");
            }
        }
        if ($exist_meta_title) {
            $expected_multilingual = array("name", "description", "meta_title", "meta_description", "meta_keywords");
        } else {
            $expected_multilingual = array("name", "description", "meta_description", "meta_keywords");
        }
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateCategoryFilters(&$reader) {
        $data = $reader->getSheetByName('CategoryFilters');
        if ($data == null) {
            return true;
        }
        if (!$this->existFilter()) {
            throw new Exception($this->language->get('error_filter_not_supported'));
        }
        if ($this->config->get('export_import_settings_use_filter_group_id')) {
            if ($this->config->get('export_import_settings_use_filter_id')) {
                $expected_heading = array("category_id", "filter_group_id", "filter_id");
            } else {
                $expected_heading = array("category_id", "filter_group_id", "filter");
            }
        } else {
            if ($this->config->get('export_import_settings_use_filter_id')) {
                $expected_heading = array("category_id", "filter_group", "filter_id");
            } else {
                $expected_heading = array("category_id", "filter_group", "filter");
            }
        }
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateCategorySEOKeywords(&$reader) {
        $data = $reader->getSheetByName('CategorySEOKeywords');
        if ($data == null) {
            return true;
        }
        if (!$this->use_table_seo_url) {
            throw new Exception(str_replace('%1', 'CategorySEOKeywords', $this->language->get('error_seo_keywords_not_supported')));
        }
        $expected_heading = array("category_id", "store_id", "keyword");
        $expected_multilingual = array("keyword");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProducts(&$reader) {
        $data = $reader->getSheetByName('Products');
        if ($data == null) {
            return true;
        }

        // get list of the field names, some are only available for certain OpenCart versions
        $query = $this->db->query("DESCRIBE `" . DB_PREFIX . "product`");
        $product_fields = array();
        foreach ($query->rows as $row) {
            $product_fields[] = $row['Field'];
        }

        // Opencart versions from 2.0 onwards also have product_description.meta_title
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "product_description` LIKE 'meta_title'";
        $query = $this->db->query($sql);
        $exist_meta_title = ($query->num_rows > 0) ? true : false;

        $expected_heading = array
            ("product_id", "name", "categories", "sku", "upc");
        if (in_array("ean", $product_fields)) {
            $expected_heading[] = "ean";
        }
        if (in_array("jan", $product_fields)) {
            $expected_heading[] = "jan";
        }
        if (in_array("isbn", $product_fields)) {
            $expected_heading[] = "isbn";
        }
        if (in_array("mpn", $product_fields)) {
            $expected_heading[] = "mpn";
        }
        if ($this->use_table_seo_url) {
            $expected_heading = array_merge($expected_heading, array("location", "quantity", "model", "manufacturer", "image_name", "shipping", "price", "points", "date_added", "date_modified", "date_available", "weight", "weight_unit", "length", "width", "height", "length_unit", "status", "tax_class_id", "description"));
        } else {
            $expected_heading = array_merge($expected_heading, array("location", "quantity", "model", "manufacturer", "image_name", "shipping", "price", "points", "date_added", "date_modified", "date_available", "weight", "weight_unit", "length", "width", "height", "length_unit", "status", "tax_class_id", "seo_keyword", "description"));
        }
        if ($exist_meta_title) {
            $expected_heading[] = "meta_title";
        }
        $expected_heading = array_merge($expected_heading, array("meta_description", "meta_keywords", "stock_status_id", "store_ids", "layout", "related_ids", "tags", "sort_order", "subtract", "minimum"));
        if ($exist_meta_title) {
            $expected_multilingual = array("name", "description", "meta_title", "meta_description", "meta_keywords", "tags");
        } else {
            $expected_multilingual = array("name", "description", "meta_description", "meta_keywords", "tags");
        }
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateAdditionalImages(&$reader) {
        $data = $reader->getSheetByName('AdditionalImages');
        if ($data == null) {
            return true;
        }
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "product_image` LIKE 'sort_order'";
        $query = $this->db->query($sql);
        $exist_sort_order = ($query->num_rows > 0) ? true : false;
        if ($exist_sort_order) {
            $expected_heading = array("product_id", "image", "sort_order");
        } else {
            $expected_heading = array("product_id", "image");
        }
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateSpecials(&$reader) {
        $data = $reader->getSheetByName('Specials');
        if ($data == null) {
            return true;
        }
        $expected_heading = array("product_id", "customer_group", "priority", "price", "date_start", "date_end");
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateDiscounts(&$reader) {
        $data = $reader->getSheetByName('Discounts');
        if ($data == null) {
            return true;
        }
        $expected_heading = array("product_id", "customer_group", "quantity", "priority", "price", "date_start", "date_end");
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateRewards(&$reader) {
        $data = $reader->getSheetByName('Rewards');
        if ($data == null) {
            return true;
        }
        $expected_heading = array("product_id", "customer_group", "points");
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProductOptions(&$reader) {
        $data = $reader->getSheetByName('ProductOptions');
        if ($data == null) {
            return true;
        }
        if ($this->config->get('export_import_settings_use_option_id')) {
            $expected_heading = array("product_id", "option_id", "default_option_value", "required");
        } else {
            $expected_heading = array("product_id", "option", "default_option_value", "required");
        }
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProductOptionValues(&$reader) {
        $data = $reader->getSheetByName('ProductOptionValues');
        if ($data == null) {
            return true;
        }
        if ($this->config->get('export_import_settings_use_option_id')) {
            if ($this->config->get('export_import_settings_use_option_value_id')) {
                $expected_heading = array("product_id", "option_id", "option_value_id", "quantity", "subtract", "price", "price_prefix", "points", "points_prefix", "weight", "weight_prefix");
            } else {
                $expected_heading = array("product_id", "option_id", "option_value", "quantity", "subtract", "price", "price_prefix", "points", "points_prefix", "weight", "weight_prefix");
            }
        } else {
            if ($this->config->get('export_import_settings_use_option_value_id')) {
                $expected_heading = array("product_id", "option", "option_value_id", "quantity", "subtract", "price", "price_prefix", "points", "points_prefix", "weight", "weight_prefix");
            } else {
                $expected_heading = array("product_id", "option", "option_value", "quantity", "subtract", "price", "price_prefix", "points", "points_prefix", "weight", "weight_prefix");
            }
        }
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProductAttributes(&$reader) {
        $data = $reader->getSheetByName('ProductAttributes');
        if ($data == null) {
            return true;
        }
        if ($this->config->get('export_import_settings_use_attribute_group_id')) {
            if ($this->config->get('export_import_settings_use_attribute_id')) {
                $expected_heading = array("product_id", "attribute_group_id", "attribute_id", "text");
            } else {
                $expected_heading = array("product_id", "attribute_group_id", "attribute", "text");
            }
        } else {
            if ($this->config->get('export_import_settings_use_attribute_id')) {
                $expected_heading = array("product_id", "attribute_group", "attribute_id", "text");
            } else {
                $expected_heading = array("product_id", "attribute_group", "attribute", "text");
            }
        }
        $expected_multilingual = array("text");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProductFilters(&$reader) {
        $data = $reader->getSheetByName('ProductFilters');
        if ($data == null) {
            return true;
        }
        if (!$this->existFilter()) {
            throw new Exception($this->language->get('error_filter_not_supported'));
        }
        if ($this->config->get('export_import_settings_use_filter_group_id')) {
            if ($this->config->get('export_import_settings_use_filter_id')) {
                $expected_heading = array("product_id", "filter_group_id", "filter_id");
            } else {
                $expected_heading = array("product_id", "filter_group_id", "filter");
            }
        } else {
            if ($this->config->get('export_import_settings_use_filter_id')) {
                $expected_heading = array("product_id", "filter_group", "filter_id");
            } else {
                $expected_heading = array("product_id", "filter_group", "filter");
            }
        }
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProductSEOKeywords(&$reader) {
        $data = $reader->getSheetByName('ProductSEOKeywords');
        if ($data == null) {
            return true;
        }
        if (!$this->use_table_seo_url) {
            throw new Exception(str_replace('%1', 'ProductSEOKeywords', $this->language->get('error_seo_keywords_not_supported')));
        }
        $expected_heading = array("product_id", "store_id", "keyword");
        $expected_multilingual = array("keyword");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateOptions(&$reader) {
        $data = $reader->getSheetByName('Options');
        if ($data == null) {
            return true;
        }
        $expected_heading = array("option_id", "type", "sort_order", "name");
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateOptionValues(&$reader) {
        $data = $reader->getSheetByName('OptionValues');
        if ($data == null) {
            return true;
        }
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "option_value` LIKE 'image'";
        $query = $this->db->query($sql);
        $exist_image = ($query->num_rows > 0) ? true : false;
        if ($exist_image) {
            $expected_heading = array("option_value_id", "option_id", "image", "sort_order", "name");
        } else {
            $expected_heading = array("option_value_id", "option_id", "sort_order", "name");
        }
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateAttributeGroups(&$reader) {
        $data = $reader->getSheetByName('AttributeGroups');
        if ($data == null) {
            return true;
        }
        $expected_heading = array("attribute_group_id", "sort_order", "name");
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }
    
    protected function validateDsrInfo(&$reader) {
        $data = $reader->getSheetByName('dsr');
        if ($data == null) {
            return true;
        }
//        $expected_heading = array("attribute_group_id", "sort_order", "name");
        $expected_heading = array("dsr_id","job_no", "igm_no", "igm_date", "po_no", "shipper", "be_heading", "no_of_package", "unit", "net_wt", "mode",
                        "org_eta_date", "shipping_line_date", "tentative_eta_date", "expected_date", "invoice_no", "invoice_date", "mawb_no", "mawb_date",
                        "hawb_no", "hawb_date", "be_no", "be_date", "airline", "n_document_date", "org_doc_date", "duty_inform_date", "duty_received_date", "duty_paid_date",
                        "total_duty", "container_cleared_date", "detention_amt", "customer_remark", "delivery_location_remark", "container_no", "free_period_shipping_date",
                        "expected_free_dt_date", "expected_free_dt_remark");
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateAttributes(&$reader) {
        $data = $reader->getSheetByName('Attributes');
        if ($data == null) {
            return true;
        }
        $expected_heading = array("attribute_id", "attribute_group_id", "sort_order", "name");
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateFilterGroups(&$reader) {
        $data = $reader->getSheetByName('FilterGroups');
        if ($data == null) {
            return true;
        }
        if (!$this->existFilter()) {
            throw new Exception($this->language->get('error_filter_not_supported'));
        }
        $expected_heading = array("filter_group_id", "sort_order", "name");
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateFilters(&$reader) {
        $data = $reader->getSheetByName('Filters');
        if ($data == null) {
            return true;
        }
        if (!$this->existFilter()) {
            throw new Exception($this->language->get('error_filter_not_supported'));
        }
        $expected_heading = array("filter_id", "filter_group_id", "sort_order", "name");
        $expected_multilingual = array("name");
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateCustomers(&$reader) {
        $data = $reader->getSheetByName('Customers');
        if ($data == null) {
            return true;
        }

        // Some fields are only available in newer Opencart versions
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'custom_field'";
        $query = $this->db->query($sql);
        $exist_custom_field = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'salt'";
        $query = $this->db->query($sql);
        $exist_salt = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'safe'";
        $query = $this->db->query($sql);
        $exist_safe = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'token'";
        $query = $this->db->query($sql);
        $exist_token = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'code'";
        $query = $this->db->query($sql);
        $exist_code = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'approved'";
        $query = $this->db->query($sql);
        $exist_approved = ($query->num_rows > 0) ? true : false;

        $expected_heading = array("customer_id", "customer_group", "store_id", "firstname", "lastname", "email", "telephone", "fax", "password");
        if ($exist_salt) {
            $expected_heading[] = "salt";
        }
        $expected_heading = array_merge($expected_heading, array("cart", "wishlist", "newsletter"));
        if ($exist_custom_field) {
            $expected_heading[] = "custom_field";
        }
        $expected_heading = array_merge($expected_heading, array("ip", "status"));
        if ($exist_approved) {
            $expected_heading[] = "approved";
        }
        if ($exist_safe) {
            $expected_heading[] = "safe";
        }
        if ($exist_token) {
            $expected_heading[] = "token";
        }
        if ($exist_code) {
            $expected_heading[] = "code";
        }
        $expected_heading[] = "date_added";
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateAddresses(&$reader) {
        $data = $reader->getSheetByName('Addresses');
        if ($data == null) {
            return true;
        }

        // Some Opencart 1.5.x versions also have company_id
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "address` LIKE 'company_id'";
        $query = $this->db->query($sql);
        $exist_company_id = ($query->num_rows > 0) ? true : false;

        // Some Opencart 1.5.x versions also have tax_id
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "address` LIKE 'tax_id'";
        $query = $this->db->query($sql);
        $exist_tax_id = ($query->num_rows > 0) ? true : false;

        // Opencart 2.x versions have custom_field
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "address` LIKE 'custom_field'";
        $query = $this->db->query($sql);
        $exist_custom_field = ($query->num_rows > 0) ? true : false;

        $expected_heading = array("customer_id", "firstname", "lastname", "company");
        if ($exist_company_id) {
            $expected_heading[] = "company_id";
        }
        if ($exist_tax_id) {
            $expected_heading[] = "tax_id";
        }
        $expected_heading = array_merge($expected_heading, array("address_1", "address_2", "city", "postcode", "zone", "country"));
        if ($exist_custom_field) {
            $expected_heading[] = "custom_field";
        }
        $expected_heading[] = "default";
        $expected_multilingual = array();
        return $this->validateHeading($data, $expected_heading, $expected_multilingual);
    }

    protected function validateProductIdColumns(&$reader) {
        $data = $reader->getSheetByName('Products');
        if ($data == null) {
            return true;
        }
        $ok = true;

        // only unique numeric product_ids can be used, in ascending order, in worksheet 'Products'
        $previous_product_id = 0;
        $has_missing_product_ids = false;
        $product_ids = array();
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $product_id = $this->getCell($data, $i, 1);
            if ($product_id == "") {
                if (!$has_missing_product_ids) {
                    $msg = str_replace('%1', 'Products', $this->language->get('error_missing_product_id'));
                    $this->log->write($msg);
                    $has_missing_product_ids = true;
                }
                $ok = false;
                continue;
            }
            if (!$this->isInteger($product_id)) {
                $msg = str_replace('%2', $product_id, str_replace('%1', 'Products', $this->language->get('error_invalid_product_id')));
                $this->log->write($msg);
                $ok = false;
                continue;
            }
            if (in_array($product_id, $product_ids)) {
                $msg = str_replace('%2', $product_id, str_replace('%1', 'Products', $this->language->get('error_duplicate_product_id')));
                $this->log->write($msg);
                $ok = false;
            }
            $product_ids[] = $product_id;
            if ($product_id < $previous_product_id) {
                $msg = str_replace('%2', $product_id, str_replace('%1', 'Products', $this->language->get('error_wrong_order_product_id')));
                $this->log->write($msg);
                $ok = false;
            }
            $previous_product_id = $product_id;
        }

        // make sure product_ids are numeric entries and are also mentioned in worksheet 'Products'
        $worksheets = array('AdditionalImages', 'Specials', 'Discounts', 'Rewards', 'ProductOptions', 'ProductOptionValues', 'ProductAttributes', 'ProductFilters', 'ProductSEOKeywords');
        foreach ($worksheets as $worksheet) {
            $data = $reader->getSheetByName($worksheet);
            if ($data == null) {
                continue;
            }
            $previous_product_id = 0;
            $has_missing_product_ids = false;
            $unlisted_product_ids = array();
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $product_id = $this->getCell($data, $i, 1);
                if ($product_id == "") {
                    if (!$has_missing_product_ids) {
                        $msg = str_replace('%1', $worksheet, $this->language->get('error_missing_product_id'));
                        $this->log->write($msg);
                        $has_missing_product_ids = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!$this->isInteger($product_id)) {
                    $msg = str_replace('%2', $product_id, str_replace('%1', $worksheet, $this->language->get('error_invalid_product_id')));
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if (!in_array($product_id, $product_ids)) {
                    if (!in_array($product_id, $unlisted_product_ids)) {
                        $unlisted_product_ids[] = $product_id;
                        $msg = str_replace('%2', $product_id, str_replace('%1', $worksheet, $this->language->get('error_unlisted_product_id')));
                        $this->log->write($msg);
                        $ok = false;
                    }
                }
                if ($product_id < $previous_product_id) {
                    $msg = str_replace('%2', $product_id, str_replace('%1', $worksheet, $this->language->get('error_wrong_order_product_id')));
                    $this->log->write($msg);
                    $ok = false;
                }
                $previous_product_id = $product_id;
            }
        }

        return $ok;
    }

    protected function validateCategoryIdColumns(&$reader) {
        $data = $reader->getSheetByName('Categories');
        if ($data == null) {
            return true;
        }
        $ok = true;

        // only unique numeric category_ids can be used, in ascending order, in worksheet 'Categories'
        $previous_category_id = 0;
        $has_missing_category_ids = false;
        $category_ids = array();
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $category_id = $this->getCell($data, $i, 1);
            if ($category_id == "") {
                if (!$has_missing_category_ids) {
                    $msg = str_replace('%1', 'Categories', $this->language->get('error_missing_category_id'));
                    $this->log->write($msg);
                    $has_missing_category_ids = true;
                }
                $ok = false;
                continue;
            }
            if (!$this->isInteger($category_id)) {
                $msg = str_replace('%2', $category_id, str_replace('%1', 'Categories', $this->language->get('error_invalid_category_id')));
                $this->log->write($msg);
                $ok = false;
                continue;
            }
            if (in_array($category_id, $category_ids)) {
                $msg = str_replace('%2', $category_id, str_replace('%1', 'Categories', $this->language->get('error_duplicate_category_id')));
                $this->log->write($msg);
                $ok = false;
            }
            $category_ids[] = $category_id;
            if ($category_id < $previous_category_id) {
                $msg = str_replace('%2', $category_id, str_replace('%1', 'Categories', $this->language->get('error_wrong_order_category_id')));
                $this->log->write($msg);
                $ok = false;
            }
            $previous_category_id = $category_id;
        }

        // make sure category_ids are numeric entries and are also mentioned in worksheet 'Categories'
        $worksheets = array('CategoryFilters', 'CategorySEOKeywords');
        foreach ($worksheets as $worksheet) {
            $data = $reader->getSheetByName($worksheet);
            if ($data == null) {
                continue;
            }
            $previous_category_id = 0;
            $has_missing_category_ids = false;
            $unlisted_category_ids = array();
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $category_id = $this->getCell($data, $i, 1);
                if ($category_id == "") {
                    if (!$has_missing_category_ids) {
                        $msg = str_replace('%1', $worksheet, $this->language->get('error_missing_category_id'));
                        $this->log->write($msg);
                        $has_missing_category_ids = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!$this->isInteger($category_id)) {
                    $msg = str_replace('%2', $category_id, str_replace('%1', $worksheet, $this->language->get('error_invalid_category_id')));
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if (!in_array($category_id, $category_ids)) {
                    if (!in_array($category_id, $unlisted_category_ids)) {
                        $unlisted_category_ids[] = $category_id;
                        $msg = str_replace('%2', $category_id, str_replace('%1', $worksheet, $this->language->get('error_unlisted_category_id')));
                        $this->log->write($msg);
                        $ok = false;
                    }
                }
                if ($category_id < $previous_category_id) {
                    $msg = str_replace('%2', $category_id, str_replace('%1', $worksheet, $this->language->get('error_wrong_order_category_id')));
                    $this->log->write($msg);
                    $ok = false;
                }
                $previous_category_id = $category_id;
            }
        }

        return $ok;
    }

    protected function validateCustomerIdColumns(&$reader) {
        $data = $reader->getSheetByName('Customers');
        if ($data == null) {
            return true;
        }
        $ok = true;

        // only unique numeric customer_ids can be used, in ascending order, in worksheet 'Customers'
        $previous_customer_id = 0;
        $has_missing_customer_ids = false;
        $customer_ids = array();
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $customer_id = $this->getCell($data, $i, 1);
            if ($customer_id == "") {
                if (!$has_missing_customer_ids) {
                    $msg = str_replace('%1', 'Customers', $this->language->get('error_missing_customer_id'));
                    $this->log->write($msg);
                    $has_missing_customer_ids = true;
                }
                $ok = false;
                continue;
            }
            if (!$this->isInteger($customer_id)) {
                $msg = str_replace('%2', $customer_id, str_replace('%1', 'Customers', $this->language->get('error_invalid_customer_id')));
                $this->log->write($msg);
                $ok = false;
                continue;
            }
            if (in_array($customer_id, $customer_ids)) {
                $msg = str_replace('%2', $customer_id, str_replace('%1', 'Customers', $this->language->get('error_duplicate_customer_id')));
                $this->log->write($msg);
                $ok = false;
            }
            $customer_ids[] = $customer_id;
            if ($customer_id < $previous_customer_id) {
                $msg = str_replace('%2', $customer_id, str_replace('%1', 'Customers', $this->language->get('error_wrong_order_customer_id')));
                $this->log->write($msg);
                $ok = false;
            }
            $previous_customer_id = $customer_id;
        }

        // make sure customer_ids are numeric entries and are also mentioned in worksheet 'Customers'
        $worksheets = array('Addresses');
        foreach ($worksheets as $worksheet) {
            $data = $reader->getSheetByName($worksheet);
            if ($data == null) {
                continue;
            }
            $previous_customer_id = 0;
            $has_missing_customer_ids = false;
            $unlisted_customer_ids = array();
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $customer_id = $this->getCell($data, $i, 1);
                if ($customer_id == "") {
                    if (!$has_missing_customer_ids) {
                        $msg = str_replace('%1', $worksheet, $this->language->get('error_missing_customer_id'));
                        $this->log->write($msg);
                        $has_missing_customer_ids = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!$this->isInteger($customer_id)) {
                    $msg = str_replace('%2', $customer_id, str_replace('%1', $worksheet, $this->language->get('error_invalid_customer_id')));
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if (!in_array($customer_id, $customer_ids)) {
                    if (!in_array($customer_id, $unlisted_customer_ids)) {
                        $unlisted_customer_ids[] = $customer_id;
                        $msg = str_replace('%2', $customer_id, str_replace('%1', $worksheet, $this->language->get('error_unlisted_customer_id')));
                        $this->log->write($msg);
                    }
                    $ok = false;
                }
                if ($customer_id < $previous_customer_id) {
                    $msg = str_replace('%2', $customer_id, str_replace('%1', $worksheet, $this->language->get('error_wrong_order_customer_id')));
                    $this->log->write($msg);
                    $ok = false;
                }
                $previous_customer_id = $customer_id;
            }
        }

        return $ok;
    }

    protected function validateAddressCountriesAndZones(&$reader) {
        $data = $reader->getSheetByName('Addresses');
        if ($data == null) {
            return true;
        }
        $ok = true;

        $country_col = 0;
        $zone_col = 0;
        $k = PHPExcel_Cell::columnIndexFromString($data->getHighestColumn());
        $i = 0;
        for ($j = 1; $j <= $k; $j += 1) {
            $entry = $this->getCell($data, $i, $j);
            if ($entry == 'country') {
                $country_col = $j;
            } else if ($entry == 'zone') {
                $zone_col = $j;
            }
        }
        if ($country_col == 0) {
            $msg = $this->language->get('error_missing_country_col');
            $msg = str_replace('%1', 'Addresses', $msg);
            $this->log->write($msg);
            $ok = false;
        }
        if ($zone_col == 0) {
            $msg = $this->language->get('error_missing_zone_col');
            $msg = str_replace('%1', 'Addresses', $msg);
            $this->log->write($msg);
            $ok = false;
        }
        if (!$ok) {
            return false;
        }

        $available_country_ids = $this->getAvailableCountryIds();
        $available_zone_ids = $this->getAvailableZoneIds();
        $undefined_countries = array();
        $undefined_zones = array();
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $country = $this->getCell($data, $i, $country_col);
            $zone = $this->getCell($data, $i, $zone_col);
            if (!isset($available_country_ids[$country])) {
                $country = html_entity_decode($country, ENT_QUOTES, 'UTF-8');
                if (!isset($available_country_ids[$country])) {
                    if (!in_array($country, $undefined_countries)) {
                        $undefined_countries[] = $country;
                        $msg = $this->language->get('error_undefined_country');
                        $msg = str_replace('%1', $country, $msg);
                        $msg = str_replace('%2', 'Addresses', $msg);
                        $this->log->write($msg);
                        $ok = false;
                    }
                    continue;
                }
            }
            if ($zone != '') {
                if (!isset($available_zone_ids[$country][$zone])) {
                    $zone = html_entity_decode($zone, ENT_QUOTES, 'UTF-8');
                    if (!isset($available_zone_ids[$country][$zone])) {
                        $zone = htmlentities($zone, ENT_NOQUOTES, 'UTF-8');
                        if (!isset($available_zone_ids[$country][$zone])) {
                            $zone = html_entity_decode($zone, ENT_QUOTES, 'UTF-8');
                            $zone = htmlentities($zone, ENT_QUOTES, 'UTF-8');
                            if (!isset($available_zone_ids[$country][$zone])) {
                                $zone = html_entity_decode($zone, ENT_QUOTES, 'UTF-8');
                                $zone = htmlentities($zone, ENT_NOQUOTES, 'UTF-8');
                                $zone = str_replace("'", "&#39;", $zone);
                                if (!isset($available_zone_ids[$country][$zone])) {
                                    if (!isset($undefined_zones[$country])) {
                                        $undefined_zones[$country] = array();
                                    }
                                    if (!in_array($zone, $undefined_zones[$country])) {
                                        $undefined_zones[$country][] = $zone;
                                        $msg = $this->language->get('error_undefined_zone');
                                        $msg = str_replace('%1', $country, $msg);
                                        $msg = str_replace('%2', $zone, $msg);
                                        $msg = str_replace('%3', 'Addresses', $msg);
                                        $this->log->write($msg);
                                        $ok = false;
                                    }
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ok;
    }

    protected function validateCustomerGroupColumns(&$reader) {
        // all customer_groups mentioned in the worksheets must be defined
        $worksheets = array('Specials', 'Discounts', 'Rewards', 'Customers');
        $ok = true;
        $customer_groups = array();
        $customer_group_ids = $this->getCustomerGroupIds();
        foreach ($worksheets as $worksheet) {
            $data = $reader->getSheetByName($worksheet);
            if ($data == null) {
                continue;
            }
            $has_missing_customer_groups = false;
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $customer_group = trim($this->getCell($data, $i, 2));
                if ($customer_group == "") {
                    if (!$has_missing_customer_groups) {
                        $msg = $this->language->get('error_missing_customer_group');
                        $msg = str_replace('%1', $worksheet, $msg);
                        $this->log->write($msg);
                        $has_missing_customer_groups = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!in_array($customer_group, $customer_groups)) {
                    if (!isset($customer_group_ids[$customer_group])) {
                        $msg = $this->language->get('error_invalid_customer_group');
                        $msg = str_replace('%1', $worksheet, str_replace('%2', $customer_group, $msg));
                        $this->log->write($msg);
                        $ok = false;
                    }
                    $customer_groups[] = $customer_group;
                }
            }
        }
        return $ok;
    }

    protected function validateOptionColumns(&$reader) {
        // get all existing options and option values
        $ok = true;
        $export_import_settings_use_option_id = $this->config->get('export_import_settings_use_option_id');
        $export_import_settings_use_option_value_id = $this->config->get('export_import_settings_use_option_value_id');
        $language_id = $this->getDefaultLanguageId();
        $sql = "SELECT od.option_id, od.name AS option_name, ovd.option_value_id, ovd.name AS option_value_name ";
        $sql .= "FROM `" . DB_PREFIX . "option_description` od ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd ON ovd.option_id=od.option_id AND ovd.language_id='" . (int) $language_id . "' ";
        $sql .= "WHERE od.language_id='" . (int) $language_id . "'";
        $query = $this->db->query($sql);
        $options = array();
        foreach ($query->rows as $row) {
            if ($export_import_settings_use_option_id) {
                $option_id = $row['option_id'];
                if (!isset($options[$option_id])) {
                    $options[$option_id] = array();
                }
                if ($export_import_settings_use_option_value_id) {
                    $option_value_id = $row['option_value_id'];
                    if (!is_null($option_value_id)) {
                        $options[$option_id][$option_value_id] = true;
                    }
                } else {
                    $option_value_name = htmlspecialchars_decode($row['option_value_name']);
                    if (!is_null($option_value_name)) {
                        $options[$option_id][$option_value_name] = true;
                    }
                }
            } else {
                $option_name = htmlspecialchars_decode($row['option_name']);
                if (!isset($options[$option_name])) {
                    $options[$option_name] = array();
                }
                if ($export_import_settings_use_option_value_id) {
                    $option_value_id = $row['option_value_id'];
                    if (!is_null($option_value_id)) {
                        $options[$option_name][$option_value_id] = true;
                    }
                } else {
                    $option_value_name = htmlspecialchars_decode($row['option_value_name']);
                    if (!is_null($option_value_name)) {
                        $options[$option_name][$option_value_name] = true;
                    }
                }
            }
        }

        // only existing options can be used in 'ProductOptions' worksheet
        $product_options = array();
        $data = $reader->getSheetByName('ProductOptions');
        if ($data == null) {
            return $ok;
        }
        $has_missing_options = false;
        $i = 0;
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $product_id = trim($this->getCell($data, $i, 1));
            if ($product_id == "") {
                continue;
            }
            if ($export_import_settings_use_option_id) {
                $option_id = trim($this->getCell($data, $i, 2));
                if ($option_id == "") {
                    if (!$has_missing_options) {
                        $msg = str_replace('%1', 'ProductOptions', $this->language->get('error_missing_option_id'));
                        $this->log->write($msg);
                        $has_missing_options = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!isset($options[$option_id])) {
                    $msg = $this->language->get('error_invalid_option_id');
                    $msg = str_replace('%1', 'ProductOptions', $msg);
                    $msg = str_replace('%2', $option_id, $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                $product_options[$product_id][$option_id] = true;
            } else {
                $option_name = trim($this->getCell($data, $i, 2));
                if ($option_name == "") {
                    if (!$has_missing_options) {
                        $msg = str_replace('%1', 'ProductOptions', $this->language->get('error_missing_option_name'));
                        $this->log->write($msg);
                        $has_missing_options = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!isset($options[$option_name])) {
                    $msg = $this->language->get('error_invalid_option_name');
                    $msg = str_replace('%1', 'ProductOptions', $msg);
                    $msg = str_replace('%2', $option_name, $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                $product_options[$product_id][$option_name] = true;
            }
        }

        // only existing options and option values can be used in 'ProductOptionValues' worksheet
        $data = $reader->getSheetByName('ProductOptionValues');
        if ($data == null) {
            return $ok;
        }
        $has_missing_options = false;
        $has_missing_option_values = false;
        $i = 0;
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $product_id = trim($this->getCell($data, $i, 1));
            if ($product_id == "") {
                continue;
            }
            if ($export_import_settings_use_option_id) {
                $option_id = trim($this->getCell($data, $i, 2));
                if ($option_id == "") {
                    if (!$has_missing_options) {
                        $msg = str_replace('%1', 'ProductOptionValues', $this->language->get('error_missing_option_id'));
                        $this->log->write($msg);
                        $has_missing_options = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!isset($options[$option_id])) {
                    $msg = $this->language->get('error_invalid_option_id');
                    $msg = str_replace('%1', 'ProductOptionValues', $msg);
                    $msg = str_replace('%2', $option_id, $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if (!isset($product_options[$product_id][$option_id])) {
                    $msg = $this->language->get('error_invalid_product_id_option_id');
                    $msg = str_replace('%1', 'ProductOptionValues', $msg);
                    $msg = str_replace('%2', $product_id, $msg);
                    $msg = str_replace('%3', $option_id, $msg);
                    $msg = str_replace('%4', 'ProductOptions', $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if ($export_import_settings_use_option_value_id) {
                    $option_value_id = trim($this->getCell($data, $i, 3));
                    if ($option_value_id == "") {
                        if (!$has_missing_option_values) {
                            $msg = str_replace('%1', 'ProductOptionValues', $this->language->get('error_missing_option_value_id'));
                            $this->log->write($msg);
                            $has_missing_option_values = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($options[$option_id][$option_value_id])) {
                        $msg = $this->language->get('error_invalid_option_id_option_value_id');
                        $msg = str_replace('%1', 'ProductOptionValues', $msg);
                        $msg = str_replace('%2', $option_id, $msg);
                        $msg = str_replace('%3', $option_value_id, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                } else {
                    $option_value_name = trim($this->getCell($data, $i, 3));
                    if ($option_value_name == "") {
                        if (!$has_missing_option_values) {
                            $msg = str_replace('%1', 'ProductOptionValues', $this->language->get('error_missing_option_value_name'));
                            $this->log->write($msg);
                            $has_missing_option_values = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($options[$option_id][$option_value_name])) {
                        $msg = $this->language->get('error_invalid_option_id_option_value_name');
                        $msg = str_replace('%1', 'ProductOptionValues', $msg);
                        $msg = str_replace('%2', $option_id, $msg);
                        $msg = str_replace('%3', $option_value_name, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                }
            } else {
                $option_name = trim($this->getCell($data, $i, 2));
                if ($option_name == "") {
                    if (!$has_missing_options) {
                        $msg = str_replace('%1', 'ProductOptionValues', $this->language->get('error_missing_option_name'));
                        $this->log->write($msg);
                        $has_missing_options = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!isset($options[$option_name])) {
                    $msg = $this->language->get('error_invalid_option_name');
                    $msg = str_replace('%1', 'ProductOptionValues', $msg);
                    $msg = str_replace('%2', $option_name, $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if (!isset($product_options[$product_id][$option_name])) {
                    $msg = $this->language->get('error_invalid_product_id_option_name');
                    $msg = str_replace('%1', 'ProductOptionValues', $msg);
                    $msg = str_replace('%2', $product_id, $msg);
                    $msg = str_replace('%3', $option_name, $msg);
                    $msg = str_replace('%4', 'ProductOptions', $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if ($export_import_settings_use_option_value_id) {
                    $option_value_id = trim($this->getCell($data, $i, 3));
                    if ($option_value_id == "") {
                        if (!$has_missing_option_values) {
                            $msg = str_replace('%1', 'ProductOptionValues', $this->language->get('error_missing_option_value_id'));
                            $this->log->write($msg);
                            $has_missing_option_values = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($options[$option_name][$option_value_id])) {
                        $msg = $this->language->get('error_invalid_option_name_option_value_id');
                        $msg = str_replace('%1', 'ProductOptionValues', $msg);
                        $msg = str_replace('%2', $option_name, $msg);
                        $msg = str_replace('%3', $option_value_id, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                } else {
                    $option_value_name = trim($this->getCell($data, $i, 3));
                    if ($option_value_name == "") {
                        if (!$has_missing_option_values) {
                            $msg = str_replace('%1', 'ProductOptionValues', $this->language->get('error_missing_option_value_name'));
                            $this->log->write($msg);
                            $has_missing_option_values = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($options[$option_name][$option_value_name])) {
                        $msg = $this->language->get('error_invalid_option_name_option_value_name');
                        $msg = str_replace('%1', 'ProductOptionValues', $msg);
                        $msg = str_replace('%2', $option_name, $msg);
                        $msg = str_replace('%3', $option_value_name, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                }
            }
        }

        return $ok;
    }

    protected function validateAttributeColumns(&$reader) {
        // get all existing attribute_groups and attributes
        $ok = true;
        $export_import_settings_use_attribute_group_id = $this->config->get('export_import_settings_use_attribute_group_id');
        $export_import_settings_use_attribute_id = $this->config->get('export_import_settings_use_attribute_id');
        $language_id = $this->getDefaultLanguageId();
        $sql = "SELECT agd.attribute_group_id, agd.name AS attribute_group_name, ad.attribute_id, ad.name AS attribute_name ";
        $sql .= "FROM `" . DB_PREFIX . "attribute_group_description` agd ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "attribute` a ON a.attribute_group_id=agd.attribute_group_id ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "attribute_description` ad ON ad.attribute_id=a.attribute_id AND ad.language_id='" . (int) $language_id . "' ";
        $sql .= "WHERE agd.language_id='" . (int) $language_id . "'";
        $query = $this->db->query($sql);
        $attribute_groups = array();
        foreach ($query->rows as $row) {
            if ($export_import_settings_use_attribute_group_id) {
                $attribute_group_id = $row['attribute_group_id'];
                if (!isset($attribute_groups[$attribute_group_id])) {
                    $attribute_groups[$attribute_group_id] = array();
                }
                if ($export_import_settings_use_attribute_id) {
                    $attribute_id = $row['attribute_id'];
                    if (!is_null($attribute_id)) {
                        $attribute_groups[$attribute_group_id][$attribute_id] = true;
                    }
                } else {
                    $attribute_name = htmlspecialchars_decode($row['attribute_name']);
                    if (!is_null($attribute_name)) {
                        $attribute_groups[$attribute_group_id][$attribute_name] = true;
                    }
                }
            } else {
                $attribute_group_name = htmlspecialchars_decode($row['attribute_group_name']);
                if (!isset($attribute_groups[$attribute_group_name])) {
                    $attribute_groups[$attribute_group_name] = array();
                }
                if ($export_import_settings_use_attribute_id) {
                    $attribute_id = $row['attribute_id'];
                    if (!is_null($attribute_id)) {
                        $attribute_groups[$attribute_group_name][$attribute_id] = true;
                    }
                } else {
                    $attribute_name = htmlspecialchars_decode($row['attribute_name']);
                    if (!is_null($attribute_name)) {
                        $attribute_groups[$attribute_group_name][$attribute_name] = true;
                    }
                }
            }
        }

        // only existing attribute_groups and attributes can be used in 'ProductAttributes' worksheet
        $data = $reader->getSheetByName('ProductAttributes');
        if ($data == null) {
            return $ok;
        }
        $has_missing_attribute_groups = false;
        $has_missing_attributes = false;
        $i = 0;
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $product_id = trim($this->getCell($data, $i, 1));
            if ($product_id == "") {
                continue;
            }
            if ($export_import_settings_use_attribute_group_id) {
                $attribute_group_id = trim($this->getCell($data, $i, 2));
                if ($attribute_group_id == "") {
                    if (!$has_missing_attribute_groups) {
                        $msg = str_replace('%1', 'ProductAttributes', $this->language->get('error_missing_attribute_group_id'));
                        $this->log->write($msg);
                        $has_missing_attribute_groups = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!isset($attribute_groups[$attribute_group_id])) {
                    $msg = $this->language->get('error_invalid_attribute_group_id');
                    $msg = str_replace('%1', 'ProductAttributes', $msg);
                    $msg = str_replace('%2', $attribute_group_id, $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if ($export_import_settings_use_attribute_id) {
                    $attribute_id = trim($this->getCell($data, $i, 3));
                    if ($attribute_id == "") {
                        if (!$has_missing_attributes) {
                            $msg = str_replace('%1', 'ProductAttributes', $this->language->get('error_missing_attribute_id'));
                            $this->log->write($msg);
                            $has_missing_attributes = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($attribute_groups[$attribute_group_id][$attribute_id])) {
                        $msg = $this->language->get('error_invalid_attribute_group_id_attribute_id');
                        $msg = str_replace('%1', 'ProductAttributes', $msg);
                        $msg = str_replace('%2', $attribute_group_id, $msg);
                        $msg = str_replace('%3', $attribute_id, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                } else {
                    $attribute_name = trim($this->getCell($data, $i, 3));
                    if ($attribute_name == "") {
                        if (!$has_missing_attributes) {
                            $msg = str_replace('%1', 'ProductAttributes', $this->language->get('error_missing_attribute_name'));
                            $this->log->write($msg);
                            $has_missing_attributes = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($attribute_groups[$attribute_group_id][$attribute_name])) {
                        $msg = $this->language->get('error_invalid_attribute_group_id_attribute_name');
                        $msg = str_replace('%1', 'ProductAttributes', $msg);
                        $msg = str_replace('%2', $attribute_group_id, $msg);
                        $msg = str_replace('%3', $attribute_name, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                }
            } else {
                $attribute_group_name = trim($this->getCell($data, $i, 2));
                if ($attribute_group_name == "") {
                    if (!$has_missing_attribute_groups) {
                        $msg = str_replace('%1', 'ProductAttributes', $this->language->get('error_missing_attribute_group_name'));
                        $this->log->write($msg);
                        $has_missing_attribute_groups = true;
                    }
                    $ok = false;
                    continue;
                }
                if (!isset($attribute_groups[$attribute_group_name])) {
                    $msg = $this->language->get('error_invalid_attribute_group_name');
                    $msg = str_replace('%1', 'ProductAttributes', $msg);
                    $msg = str_replace('%2', $attribute_group_name, $msg);
                    $this->log->write($msg);
                    $ok = false;
                    continue;
                }
                if ($export_import_settings_use_attribute_id) {
                    $attribute_id = trim($this->getCell($data, $i, 3));
                    if ($attribute_id == "") {
                        if (!$has_missing_attributes) {
                            $msg = str_replace('%1', 'ProductAttributes', $this->language->get('error_missing_attribute_id'));
                            $this->log->write($msg);
                            $has_missing_attributes = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($attribute_groups[$attribute_group_name][$attribute_id])) {
                        $msg = $this->language->get('error_invalid_attribute_group_name_attribute_id');
                        $msg = str_replace('%1', 'ProductAttributes', $msg);
                        $msg = str_replace('%2', $attribute_group_name, $msg);
                        $msg = str_replace('%3', $attribute_id, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                } else {
                    $attribute_name = trim($this->getCell($data, $i, 3));
                    if ($attribute_name == "") {
                        if (!$has_missing_attributes) {
                            $msg = str_replace('%1', 'ProductAttributes', $this->language->get('error_missing_attribute_name'));
                            $this->log->write($msg);
                            $has_missing_attributes = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($attribute_groups[$attribute_group_name][$attribute_name])) {
                        $msg = $this->language->get('error_invalid_attribute_group_name_attribute_name');
                        $msg = str_replace('%1', 'ProductAttributes', $msg);
                        $msg = str_replace('%2', $attribute_group_name, $msg);
                        $msg = str_replace('%3', $attribute_name, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                }
            }
        }

        return $ok;
    }

    protected function validateFilterColumns(&$reader) {
        // get all existing filter_groups and filters
        $ok = true;
        $export_import_settings_use_filter_group_id = $this->config->get('export_import_settings_use_filter_group_id');
        $export_import_settings_use_filter_id = $this->config->get('export_import_settings_use_filter_id');
        $language_id = $this->getDefaultLanguageId();
        $sql = "SELECT fgd.filter_group_id, fgd.name AS filter_group_name, fd.filter_id, fd.name AS filter_name ";
        $sql .= "FROM `" . DB_PREFIX . "filter_group_description` fgd ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "filter` f ON f.filter_group_id=fgd.filter_group_id ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "filter_description` fd ON fd.filter_id=f.filter_id AND fd.language_id='" . (int) $language_id . "' ";
        $sql .= "WHERE fgd.language_id='" . (int) $language_id . "'";
        $query = $this->db->query($sql);
        $filter_groups = array();
        foreach ($query->rows as $row) {
            if ($export_import_settings_use_filter_group_id) {
                $filter_group_id = $row['filter_group_id'];
                if (!isset($filter_groups[$filter_group_id])) {
                    $filter_groups[$filter_group_id] = array();
                }
                if ($export_import_settings_use_filter_id) {
                    $filter_id = $row['filter_id'];
                    if (!is_null($filter_id)) {
                        $filter_groups[$filter_group_id][$filter_id] = true;
                    }
                } else {
                    $filter_name = htmlspecialchars_decode($row['filter_name']);
                    if (!is_null($filter_name)) {
                        $filter_groups[$filter_group_id][$filter_name] = true;
                    }
                }
            } else {
                $filter_group_name = htmlspecialchars_decode($row['filter_group_name']);
                if (!isset($filter_groups[$filter_group_name])) {
                    $filter_groups[$filter_group_name] = array();
                }
                if ($export_import_settings_use_filter_id) {
                    $filter_id = $row['filter_id'];
                    if (!is_null($filter_id)) {
                        $filter_groups[$filter_group_name][$filter_id] = true;
                    }
                } else {
                    $filter_name = htmlspecialchars_decode($row['filter_name']);
                    if (!is_null($filter_name)) {
                        $filter_groups[$filter_group_name][$filter_name] = true;
                    }
                }
            }
        }

        // only existing filter_groups and filters can be used in the 'ProductFilters' and 'CategoryFilters' worksheets
        $worksheet_names = array('ProductFilters', 'CategoryFilters');
        foreach ($worksheet_names as $worksheet_name) {
            $data = $reader->getSheetByName('ProductFilters');
            if ($data == null) {
                return $ok;
            }
            $has_missing_filter_groups = false;
            $has_missing_filters = false;
            $i = 0;
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $id = trim($this->getCell($data, $i, 1));
                if ($id == "") {
                    continue;
                }
                if ($export_import_settings_use_filter_group_id) {
                    $filter_group_id = trim($this->getCell($data, $i, 2));
                    if ($filter_group_id == "") {
                        if (!$has_missing_filter_groups) {
                            $msg = str_replace('%1', $worksheet_name, $this->language->get('error_missing_filter_group_id'));
                            $this->log->write($msg);
                            $has_missing_filter_groups = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($filter_groups[$filter_group_id])) {
                        $msg = $this->language->get('error_invalid_filter_group_id');
                        $msg = str_replace('%1', $worksheet_name, $msg);
                        $msg = str_replace('%2', $filter_group_id, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                    if ($export_import_settings_use_filter_id) {
                        $filter_id = trim($this->getCell($data, $i, 3));
                        if ($filter_id == "") {
                            if (!$has_missing_filters) {
                                $msg = str_replace('%1', $worksheet_name, $this->language->get('error_missing_filter_id'));
                                $this->log->write($msg);
                                $has_missing_filters = true;
                            }
                            $ok = false;
                            continue;
                        }
                        if (!isset($filter_groups[$filter_group_id][$filter_id])) {
                            $msg = $this->language->get('error_invalid_filter_group_id_filter_id');
                            $msg = str_replace('%1', $worksheet_name, $msg);
                            $msg = str_replace('%2', $filter_group_id, $msg);
                            $msg = str_replace('%3', $filter_id, $msg);
                            $this->log->write($msg);
                            $ok = false;
                            continue;
                        }
                    } else {
                        $filter_name = trim($this->getCell($data, $i, 3));
                        if ($filter_name == "") {
                            if (!$has_missing_filters) {
                                $msg = str_replace('%1', $worksheet_name, $this->language->get('error_missing_filter_name'));
                                $this->log->write($msg);
                                $has_missing_filters = true;
                            }
                            $ok = false;
                            continue;
                        }
                        if (!isset($filter_groups[$filter_group_id][$filter_name])) {
                            $msg = $this->language->get('error_invalid_filter_group_id_filter_name');
                            $msg = str_replace('%1', $worksheet_name, $msg);
                            $msg = str_replace('%2', $filter_group_id, $msg);
                            $msg = str_replace('%3', $filter_name, $msg);
                            $this->log->write($msg);
                            $ok = false;
                            continue;
                        }
                    }
                } else {
                    $filter_group_name = trim($this->getCell($data, $i, 2));
                    if ($filter_group_name == "") {
                        if (!$has_missing_filter_groups) {
                            $msg = str_replace('%1', $worksheet_name, $this->language->get('error_missing_filter_group_name'));
                            $this->log->write($msg);
                            $has_missing_filter_groups = true;
                        }
                        $ok = false;
                        continue;
                    }
                    if (!isset($filter_groups[$filter_group_name])) {
                        $msg = $this->language->get('error_invalid_filter_group_name');
                        $msg = str_replace('%1', $worksheet_name, $msg);
                        $msg = str_replace('%2', $filter_group_name, $msg);
                        $this->log->write($msg);
                        $ok = false;
                        continue;
                    }
                    if ($export_import_settings_use_filter_id) {
                        $filter_id = trim($this->getCell($data, $i, 3));
                        if ($filter_id == "") {
                            if (!$has_missing_filters) {
                                $msg = str_replace('%1', $worksheet_name, $this->language->get('error_missing_filter_id'));
                                $this->log->write($msg);
                                $has_missing_filters = true;
                            }
                            $ok = false;
                            continue;
                        }
                        if (!isset($filter_groups[$filter_group_name][$filter_id])) {
                            $msg = $this->language->get('error_invalid_filter_group_name_filter_id');
                            $msg = str_replace('%1', $worksheet_name, $msg);
                            $msg = str_replace('%2', $filter_group_name, $msg);
                            $msg = str_replace('%3', $filter_id, $msg);
                            $this->log->write($msg);
                            $ok = false;
                            continue;
                        }
                    } else {
                        $filter_name = trim($this->getCell($data, $i, 3));
                        if ($filter_name == "") {
                            if (!$has_missing_filters) {
                                $msg = str_replace('%1', $worksheet_name, $this->language->get('error_missing_filter_name'));
                                $this->log->write($msg);
                                $has_missing_filters = true;
                            }
                            $ok = false;
                            continue;
                        }
                        if (!isset($filter_groups[$filter_group_name][$filter_name])) {
                            $msg = $this->language->get('error_invalid_filter_group_name_filter_name');
                            $msg = str_replace('%1', $worksheet_name, $msg);
                            $msg = str_replace('%2', $filter_group_name, $msg);
                            $msg = str_replace('%3', $filter_name, $msg);
                            $this->log->write($msg);
                            $ok = false;
                            continue;
                        }
                    }
                }
            }
        }

        return $ok;
    }

    protected function isInteger($input) {
        return(ctype_digit(strval($input)));
    }

    protected function validateStoreIds(&$reader) {
        $worksheets = array('Customers', 'CategorySEOKeywords', 'ProductSEOKeywords');
        $ok = true;
        $store_ids = $this->getAvailableStoreIds();
        foreach ($worksheets as $worksheet) {
            $data = $reader->getSheetByName($worksheet);
            if ($data == null) {
                continue;
            }
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $j = ($worksheet == 'Customers') ? 3 : 2;
                $store_id = $this->getCell($data, $i, $j);
                if (!$this->isInteger($store_id)) {
                    // Invalid store_id='...' used in worksheet '...'
                    $msg = $this->language->get('error_invalid_store_id');
                    $msg = str_replace('%1', $store_id, $msg);
                    $msg = str_replace('%2', $worksheet, $msg);
                    $this->log->write($msg);
                    $ok = false;
                } else if (!in_array((int) $store_id, $store_ids)) {
                    // Invalid store_id='...' used in worksheet '...'
                    $msg = $this->language->get('error_invalid_store_id');
                    $msg = str_replace('%1', $store_id, $msg);
                    $msg = str_replace('%2', $worksheet, $msg);
                    $this->log->write($msg);
                    $ok = false;
                }
            }
        }
        return $ok;
    }

    protected function validateIncrementalOnly(&$reader, $incremental) {
        // certain worksheets can only be imported in incremental mode for the time being
        $ok = true;
        $worksheets = array('Customers', 'Addresses');
        foreach ($worksheets as $worksheet) {
            $data = $reader->getSheetByName($worksheet);
            if ($data) {
                if (!$incremental) {
                    $msg = $this->language->get('error_incremental_only');
                    $msg = str_replace('%1', $worksheet, $msg);
                    $this->log->write($msg);
                    $ok = false;
                }
            }
        }
        return $ok;
    }

    protected function validateCategorySEOUrls(&$reader, &$languages) {
        $ok = true;

        // all category_id/store_id combinations must be unique
        $data = $reader->getSheetByName('CategorySEOKeywords');
        if ($data == null) {
            return true;
        }
        $category_id_store_id = array();
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $j = 1;
            $category_id = $this->getCell($data, $i, $j);
            if (!$this->isInteger($category_id)) {
                continue;
            }
            $j += 1;
            $store_id = $this->getCell($data, $i, $j);
            if (!$this->isInteger($store_id)) {
                continue;
            }
            if (!isset($category_id_store_id[$category_id])) {
                $category_id_store_id[$category_id] = array();
            }
            if (!isset($category_id_store_id[$category_id][$store_id])) {
                $category_id_store_id[$category_id][$store_id] = 0;
            }
            $category_id_store_id[$category_id][$store_id] += 1;
        }
        foreach ($category_id_store_id as $category_id => $val1) {
            foreach ($val1 as $store_id => $val2) {
                if ($val2 > 1) {
                    $msg = $this->language->get('error_multiple_category_id_store_id');
                    $msg = str_replace('%1', $category_id, $msg);
                    $msg = str_replace('%2', $store_id, $msg);
                    $this->log->write($msg);
                    $ok = false;
                }
            }
        }
        if (!$ok) {
            return false;
        }

        // all keywords for each store_id must unique
        $store_ids = $this->getAvailableStoreIds();
        foreach ($store_ids as $next_store_id) {
            $keyword_counts = array();
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $j = 1;
                $category_id = $this->getCell($data, $i, $j);
                if (!$this->isInteger($category_id)) {
                    continue;
                }
                $j += 1;
                $store_id = $this->getCell($data, $i, $j);
                if (!$this->isInteger($store_id)) {
                    continue;
                }
                if ($store_id != $next_store_id) {
                    continue;
                }
                foreach ($languages as $language) {
                    $j += 1;
                    $keyword = trim($this->getCell($data, $i, $j, ''));
                    if ($keyword != '') {
                        if (!isset($keyword_counts[$keyword])) {
                            $keyword_counts[$keyword] = 0;
                        }
                        $keyword_counts[$keyword] += 1;
                    }
                }
            }
            foreach ($keyword_counts as $keyword => $count) {
                if ($count > 1) {
                    $msg = $this->language->get('error_unique_keyword');
                    $msg = str_replace('%1', $keyword, $msg);
                    $msg = str_replace('%2', $next_store_id, $msg);
                    $msg = str_replace('%3', 'CategorySEOKeywords', $msg);
                    $this->log->write($msg);
                    $ok = false;
                }
            }
        }

        return $ok;
    }

    protected function validateProductSEOUrls(&$reader, &$languages) {
        $ok = true;

        // all product_id/store_id combinations must be unique
        $data = $reader->getSheetByName('ProductSEOKeywords');
        if ($data == null) {
            return true;
        }
        $product_id_store_id = array();
        $k = $data->getHighestRow();
        for ($i = 1; $i < $k; $i += 1) {
            $j = 1;
            $product_id = $this->getCell($data, $i, $j);
            if (!$this->isInteger($product_id)) {
                continue;
            }
            $j += 1;
            $store_id = $this->getCell($data, $i, $j);
            if (!$this->isInteger($store_id)) {
                continue;
            }
            if (!isset($product_id_store_id[$product_id])) {
                $product_id_store_id[$product_id] = array();
            }
            if (!isset($product_id_store_id[$product_id][$store_id])) {
                $product_id_store_id[$product_id][$store_id] = 0;
            }
            $product_id_store_id[$product_id][$store_id] += 1;
        }
        foreach ($product_id_store_id as $product_id => $val1) {
            foreach ($val1 as $store_id => $val2) {
                if ($val2 > 1) {
                    $msg = $this->language->get('error_multiple_product_id_store_id');
                    $msg = str_replace('%1', $product_id, $msg);
                    $msg = str_replace('%2', $store_id, $msg);
                    $this->log->write($msg);
                    $ok = false;
                }
            }
        }
        if (!$ok) {
            return false;
        }

        // all keywords for each store_id must unique
        $store_ids = $this->getAvailableStoreIds();
        foreach ($store_ids as $next_store_id) {
            $keyword_counts = array();
            $k = $data->getHighestRow();
            for ($i = 1; $i < $k; $i += 1) {
                $j = 1;
                $product_id = $this->getCell($data, $i, $j);
                if (!$this->isInteger($product_id)) {
                    continue;
                }
                $j += 1;
                $store_id = $this->getCell($data, $i, $j);
                if (!$this->isInteger($store_id)) {
                    continue;
                }
                if ($store_id != $next_store_id) {
                    continue;
                }
                foreach ($languages as $language) {
                    $j += 1;
                    $keyword = trim($this->getCell($data, $i, $j, ''));
                    if ($keyword != '') {
                        if (!isset($keyword_counts[$keyword])) {
                            $keyword_counts[$keyword] = 0;
                        }
                        $keyword_counts[$keyword] += 1;
                    }
                }
            }
            foreach ($keyword_counts as $keyword => $count) {
                if ($count > 1) {
                    $msg = $this->language->get('error_unique_keyword');
                    $msg = str_replace('%1', $keyword, $msg);
                    $msg = str_replace('%2', $next_store_id, $msg);
                    $msg = str_replace('%3', 'ProductSEOKeywords', $msg);
                    $this->log->write($msg);
                    $ok = false;
                }
            }
        }

        return $ok;
    }

//    protected function validateWorksheetNames(&$reader) {
//        $allowed_worksheets = array(
//            'Categories',
//            'CategoryFilters',
//            'CategorySEOKeywords',
//            'Products',
//            'AdditionalImages',
//            'Specials',
//            'Discounts',
//            'Rewards',
//            'ProductOptions',
//            'ProductOptionValues',
//            'ProductAttributes',
//            'ProductFilters',
//            'ProductSEOKeywords',
//            'Options',
//            'OptionValues',
//            'AttributeGroups',
//            'Attributes',
//            'FilterGroups',
//            'Filters',
//            'Customers',
//            'Addresses'
//        );
//        $all_worksheets_ignored = true;
//        $worksheets = $reader->getSheetNames();
//        foreach ($worksheets as $worksheet) {
//            if (in_array($worksheet, $allowed_worksheets)) {
//                $all_worksheets_ignored = false;
//                break;
//            }
//        }
//        if ($all_worksheets_ignored) {
//            return false;
//        }
//        return true;
//    }
protected function validateWorksheetNames(&$reader) {
        $allowed_worksheets = array(
            'dsr'
        );
        $all_worksheets_ignored = true;
        $worksheets = $reader->getSheetNames();
        foreach ($worksheets as $worksheet) {
            if (in_array($worksheet, $allowed_worksheets)) {
                $all_worksheets_ignored = false;
                break;
            }
        }
        if ($all_worksheets_ignored) {
            return false;
        }
        return true;
    }
    protected function validateUpload(&$reader) {
        $ok = true;
        $languages = $this->getLanguages();

        // make sure at least one of worksheet names is valid
        if (!$this->validateWorksheetNames($reader)) {
            $this->log->write($this->language->get('error_worksheets'));
            $ok = false;
        }

        // worksheets must have correct heading rows
//        if (!$this->validateCategories($reader)) {
//            $this->log->write($this->language->get('error_categories_header'));
//            $ok = false;
//        }
//        if (!$this->validateCategoryFilters($reader)) {
//            $this->log->write($this->language->get('error_category_filters_header'));
//            $ok = false;
//        }
//        if (!$this->validateCategorySEOKeywords($reader)) {
//            $this->log->write($this->language->get('error_category_seo_keywords_header'));
//            $ok = false;
//        }
//        if (!$this->validateProducts($reader)) {
//            $this->log->write($this->language->get('error_products_header'));
//            $ok = false;
//        }
//        if (!$this->validateAdditionalImages($reader)) {
//            $this->log->write($this->language->get('error_additional_images_header'));
//            $ok = false;
//        }
//        if (!$this->validateSpecials($reader)) {
//            $this->log->write($this->language->get('error_specials_header'));
//            $ok = false;
//        }
//        if (!$this->validateDiscounts($reader)) {
//            $this->log->write($this->language->get('error_discounts_header'));
//            $ok = false;
//        }
//        if (!$this->validateRewards($reader)) {
//            $this->log->write($this->language->get('error_rewards_header'));
//            $ok = false;
//        }
//        if (!$this->validateProductOptions($reader)) {
//            $this->log->write($this->language->get('error_product_options_header'));
//            $ok = false;
//        }
//        if (!$this->validateProductOptionValues($reader)) {
//            $this->log->write($this->language->get('error_product_option_values_header'));
//            $ok = false;
//        }
//        if (!$this->validateProductAttributes($reader)) {
//            $this->log->write($this->language->get('error_product_attributes_header'));
//            $ok = false;
//        }
//        if (!$this->validateProductFilters($reader)) {
//            $this->log->write($this->language->get('error_product_filters_header'));
//            $ok = false;
//        }
//        if (!$this->validateProductSEOKeywords($reader)) {
//            $this->log->write($this->language->get('error_product_seo_keywords_header'));
//            $ok = false;
//        }
//        if (!$this->validateOptions($reader)) {
//            $this->log->write($this->language->get('error_options_header'));
//            $ok = false;
//        }
//        if (!$this->validateOptionValues($reader)) {
//            $this->log->write($this->language->get('error_option_values_header'));
//            $ok = false;
//        }
//        if (!$this->validateAttributeGroups($reader)) {
//            $this->log->write($this->language->get('error_attribute_groups_header'));
//            $ok = false;
//        }
        if (!$this->validateDsrInfo($reader)) {
            $this->log->write($this->language->get('error_dsr_info_header'));
            $ok = false;
        }
//        if (!$this->validateAttributes($reader)) {
//            $this->log->write($this->language->get('error_attributes_header'));
//            $ok = false;
//        }
//        if (!$this->validateFilterGroups($reader)) {
//            $this->log->write($this->language->get('error_filter_groups_header'));
//            $ok = false;
//        }
//        if (!$this->validateFilters($reader)) {
//            $this->log->write($this->language->get('error_filters_header'));
//            $ok = false;
//        }
//        if (!$this->validateCustomers($reader)) {
//            $this->log->write($this->language->get('error_customers_header'));
//            $ok = false;
//        }
//        if (!$this->validateAddresses($reader)) {
//            $this->log->write($this->language->get('error_addresses_header'));
//            $ok = false;
//        }

        // certain worksheets rely on the existence of other worksheets
//        $names = $reader->getSheetNames();
//        $exist_categories = false;
//        $exist_category_filters = false;
//        $exist_category_seo_keywords = false;
//        $exist_product_options = false;
//        $exist_product_option_values = false;
//        $exist_products = false;
//        $exist_additional_images = false;
//        $exist_specials = false;
//        $exist_discounts = false;
//        $exist_rewards = false;
//        $exist_product_attributes = false;
//        $exist_product_filters = false;
//        $exist_product_seo_keywords = false;
//        $exist_attribute_groups = false;
//        $exist_filters = false;
//        $exist_filter_groups = false;
//        $exist_attributes = false;
//        $exist_options = false;
//        $exist_option_values = false;
//        $exist_customers = false;
//        $exist_addresses = false;
//        foreach ($names as $name) {
//            if ($name == 'Categories') {
//                $exist_categories = true;
//                continue;
//            }
//            if ($name == 'CategoryFilters') {
//                if (!$exist_categories) {
//                    // Missing Categories worksheet, or Categories worksheet not listed before CategoryFilters
//                    $this->log->write($this->language->get('error_category_filters'));
//                    $ok = false;
//                }
//                $exist_category_filters = true;
//                continue;
//            }
//            if ($name == 'CategorySEOKeywords') {
//                if (!$exist_categories) {
//                    // Missing Categories worksheet, or Categories worksheet not listed before CategorySEOKeywords
//                    $this->log->write($this->language->get('error_category_seo_keywords'));
//                    $ok = false;
//                }
//                $exist_category_seo_keywords = true;
//                continue;
//            }
//            if ($name == 'Products') {
//                $exist_products = true;
//                continue;
//            }
//            if ($name == 'ProductOptions') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before ProductOptions
//                    $this->log->write($this->language->get('error_product_options'));
//                    $ok = false;
//                }
//                $exist_product_options = true;
//                continue;
//            }
//            if ($name == 'ProductOptionValues') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before ProductOptionValues
//                    $this->log->write($this->language->get('error_product_option_values'));
//                    $ok = false;
//                }
//                if (!$exist_product_options) {
//                    // Missing ProductOptions worksheet, or ProductOptions worksheet not listed before ProductOptionValues
//                    $this->log->write($this->language->get('error_product_option_values_2'));
//                    $ok = false;
//                }
//                $exist_product_option_values = true;
//                continue;
//            }
//            if ($name == 'AdditionalImages') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before AdditionalImages
//                    $this->log->write($this->language->get('error_additional_images'));
//                    $ok = false;
//                }
//                $exist_additional_images = true;
//                continue;
//            }
//            if ($name == 'Specials') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before Specials
//                    $this->log->write($this->language->get('error_specials'));
//                    $ok = false;
//                }
//                $exist_specials = true;
//                continue;
//            }
//            if ($name == 'Discounts') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before Discounts
//                    $this->log->write($this->language->get('error_discounts'));
//                    $ok = false;
//                }
//                $exist_discounts = true;
//                continue;
//            }
//            if ($name == 'Rewards') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before Rewards
//                    $this->log->write($this->language->get('error_rewards'));
//                    $ok = false;
//                }
//                $exist_rewards = true;
//                continue;
//            }
//            if ($name == 'ProductAttributes') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before ProductAttributes
//                    $this->log->write($this->language->get('error_product_attributes'));
//                    $ok = false;
//                }
//                $exist_product_attributes = true;
//                continue;
//            }
//            if ($name == 'AttributeGroups') {
//                $exist_attribute_groups = true;
//                continue;
//            }
//            if ($name == 'Attributes') {
//                if (!$exist_attribute_groups) {
//                    // Missing AttributeGroups worksheet, or AttributeGroups worksheet not listed before Attributes
//                    $this->log->write($this->language->get('error_attributes'));
//                    $ok = false;
//                }
//                $exist_attributes = true;
//                continue;
//            }
//            if ($name == 'ProductFilters') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before ProductFilters
//                    $this->log->write($this->language->get('error_product_filters'));
//                    $ok = false;
//                }
//                $exist_product_filters = true;
//                continue;
//            }
//            if ($name == 'ProductSEOKeywords') {
//                if (!$exist_products) {
//                    // Missing Products worksheet, or Products worksheet not listed before ProductSEOKeywords
//                    $this->log->write($this->language->get('error_product_seo_keywords'));
//                    $ok = false;
//                }
//                $exist_product_seo_keywords = true;
//                continue;
//            }
//            if ($name == 'FilterGroups') {
//                $exist_filter_groups = true;
//                continue;
//            }
//            if ($name == 'Filters') {
//                if (!$exist_filter_groups) {
//                    // Missing FilterGroups worksheet, or FilterGroups worksheet not listed before Filters
//                    $this->log->write($this->language->get('error_filters'));
//                    $ok = false;
//                }
//                $exist_filters = true;
//                continue;
//            }
//            if ($name == 'Options') {
//                $exist_options = true;
//                continue;
//            }
//            if ($name == 'OptionValues') {
//                if (!$exist_options) {
//                    // Missing Options worksheet, or Options worksheet not listed before OptionValues
//                    $this->log->write($this->language->get('error_option_values'));
//                    $ok = false;
//                }
//                $exist_option_values = true;
//                continue;
//            }
//            if ($name == 'Customers') {
//                $exist_customers = true;
//                continue;
//            }
//            if ($name == 'Addresses') {
//                if (!$exist_customers) {
//                    // Missing Cutomers worksheet, or Customers worksheet not listed before Addresses
//                    $this->log->write($this->language->get('error_addresses'));
//                    $ok = false;
//                }
//                $exist_addresses = true;
//                continue;
//            }
//        }
//        if ($exist_product_options) {
//            if (!$exist_product_option_values) {
//                // ProductOptionValues worksheet also expected after a ProductOptions worksheet
//                $this->log->write($this->language->get('error_product_option_values_3'));
//                $ok = false;
//            }
//        }
//        if ($exist_attribute_groups) {
//            if (!$exist_attributes) {
//                // Attributes worksheet also expected after an AttributeGroups worksheet
//                $this->log->write($this->language->get('error_attributes_2'));
//                $ok = false;
//            }
//        }
//        if ($exist_filter_groups) {
//            if (!$exist_filters) {
//                // Filters worksheet also expected after an FilterGroups worksheet
//                $this->log->write($this->language->get('error_filters_2'));
//                $ok = false;
//            }
//        }
//        if ($exist_options) {
//            if (!$exist_option_values) {
//                // OptionValues worksheet also expected after an Options worksheet
//                $this->log->write($this->language->get('error_option_values_2'));
//                $ok = false;
//            }
//        }
//        if ($exist_customers) {
//            if (!$exist_addresses) {
//                // Addresses worksheet also expected after Customers worksheet
//                $this->log->write($this->language->get('error_addresses_2'));
//                $ok = false;
//            }
//        }
//
//        if (!$ok) {
//            return false;
//        }
//
//        if (!$this->validateProductIdColumns($reader)) {
//            $ok = false;
//        }
//
//        if (!$this->validateCategoryIdColumns($reader)) {
//            $ok = false;
//        }
//
//        if (!$this->validateCustomerIdColumns($reader)) {
//            $ok = false;
//        }
//
//        if (!$this->validateCustomerGroupColumns($reader)) {
//            $ok = false;
//        }
//
//        if (!$this->validateOptionColumns($reader)) {
//            $ok = false;
//        }
//
//        if (!$this->validateAttributeColumns($reader)) {
//            $ok = false;
//        }
//
//        if ($this->existFilter()) {
//            if (!$this->validateFilterColumns($reader)) {
//                $ok = false;
//            }
//        }
//
//        if (!$this->validateStoreIds($reader)) {
//            $ok = false;
//        }
//
//        if (!$this->validateAddressCountriesAndZones($reader)) {
//            $ok = false;
//        }
//
//        if ($this->use_table_seo_url) {
//            if (!$this->validateCategorySEOUrls($reader, $languages)) {
//                $ok = false;
//            }
//
//            if (!$this->validateProductSEOUrls($reader, $languages)) {
//                $ok = false;
//            }
//        }

        return $ok;
    }

    protected function clearCache() {
        $this->cache->delete('*');
    }

    public function upload($filename, $incremental = false) {
        // we use our own error handler
        global $registry;
        $registry = $this->registry;
//		set_error_handler('error_handler_for_export_import',E_ALL);
//		register_shutdown_function('fatal_error_shutdown_handler_for_export_import');

        try {
            $this->session->data['export_import_nochange'] = 1;

            // we use the PHPExcel package from https://github.com/PHPOffice/PHPExcel
            $cwd = getcwd();
            $dir = version_compare(VERSION, '3.0', '>=') ? 'library/export_import' : 'PHPExcel';
            chdir(DIR_SYSTEM . $dir);
            require_once( 'E:/xampp/htdocs/ShippingAndLogistics/system/library/export_import/Classes/PHPExcel.php' );
            chdir($cwd);

            // Memory Optimization
            if ($this->config->get('export_import_settings_use_import_cache')) {
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array(' memoryCacheSize ' => '16MB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            }

            // parse uploaded spreadsheet file
            $inputFileType = PHPExcel_IOFactory::identify($filename);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $reader = $objReader->load($filename);

            // read the various worksheets and load them to the database
            if (!$this->validateIncrementalOnly($reader, $incremental)) {
                return false;
            }
            if (!$this->validateUpload( $reader )) {
                    return false;
            }
            $this->clearCache();
            $this->session->data['export_import_nochange'] = 0;

            $this->uploadDsrInfo($reader, $incremental);

            return true;
        } catch (Exception $e) {
            $errstr = $e->getMessage();
            $errline = $e->getLine();
            $errfile = $e->getFile();
            $errno = $e->getCode();
            $this->session->data['export_import_error'] = array('errstr' => $errstr, 'errno' => $errno, 'errfile' => $errfile, 'errline' => $errline);
            if ($this->config->get('config_error_log')) {
                $this->log->write('PHP ' . get_class($e) . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
            }
            return false;
        }
    }

    protected function getStoreIdsForCategories() {
        $sql = "SELECT category_id, store_id FROM `" . DB_PREFIX . "category_to_store` cs;";
        $store_ids = array();
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            $categoryId = $row['category_id'];
            $store_id = $row['store_id'];
            if (!isset($store_ids[$categoryId])) {
                $store_ids[$categoryId] = array();
            }
            if (!in_array($store_id, $store_ids[$categoryId])) {
                $store_ids[$categoryId][] = $store_id;
            }
        }
        return $store_ids;
    }

    protected function getLayoutsForCategories() {
        $sql = "SELECT cl.*, l.name FROM `" . DB_PREFIX . "category_to_layout` cl ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "layout` l ON cl.layout_id = l.layout_id ";
        $sql .= "ORDER BY cl.category_id, cl.store_id;";
        $result = $this->db->query($sql);
        $layouts = array();
        foreach ($result->rows as $row) {
            $categoryId = $row['category_id'];
            $store_id = $row['store_id'];
            $name = $row['name'];
            if (!isset($layouts[$categoryId])) {
                $layouts[$categoryId] = array();
            }
            $layouts[$categoryId][$store_id] = $name;
        }
        return $layouts;
    }

    protected function setColumnStyles(&$worksheet, &$styles, $min_row, $max_row) {
        if ($max_row < $min_row) {
            return;
        }
        foreach ($styles as $col => $style) {
            $from = PHPExcel_Cell::stringFromColumnIndex($col) . $min_row;
            $to = PHPExcel_Cell::stringFromColumnIndex($col) . $max_row;
            $range = $from . ':' . $to;
            $worksheet->getStyle($range)->applyFromArray($style, false);
        }
    }

    protected function setCellRow($worksheet, $row/* 1-based */, $data, &$default_style = null, &$styles = null) {
        if (!empty($default_style)) {
            $worksheet->getStyle("$row:$row")->applyFromArray($default_style, false);
        }
        if (!empty($styles)) {
            foreach ($styles as $col => $style) {
                $worksheet->getStyleByColumnAndRow($col, $row)->applyFromArray($style, false);
            }
        }
        $worksheet->fromArray($data, null, 'A' . $row, true);
//		foreach ($data as $col=>$val) {
//			$worksheet->setCellValueExplicitByColumnAndRow( $col, $row-1, $val );
//		}
//		foreach ($data as $col=>$val) {
//			$worksheet->setCellValueByColumnAndRow( $col, $row, $val );
//		}
    }

    protected function setCell(&$worksheet, $row/* 1-based */, $col/* 0-based */, $val, &$style = null) {
        $worksheet->setCellValueByColumnAndRow($col, $row, $val);
        if (!empty($style)) {
            $worksheet->getStyleByColumnAndRow($col, $row)->applyFromArray($style, false);
        }
    }

    protected function getCategoryDescriptions(&$languages, $offset = null, $rows = null, $min_id = null, $max_id = null) {
        // query the category_description table for each language
        $category_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT c.category_id, cd.* ";
            $sql .= "FROM `" . DB_PREFIX . "category` c ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "category_description` cd ON cd.category_id=c.category_id AND cd.language_id='" . (int) $language_id . "' ";
            if (isset($min_id) && isset($max_id)) {
                $sql .= "WHERE c.category_id BETWEEN $min_id AND $max_id ";
            }
            $sql .= "GROUP BY c.`category_id` ";
            $sql .= "ORDER BY c.`category_id` ASC ";
            if (isset($offset) && isset($rows)) {
                $sql .= "LIMIT $offset,$rows; ";
            } else {
                $sql .= "; ";
            }
            $query = $this->db->query($sql);
            $category_descriptions[$language_code] = $query->rows;
        }
        return $category_descriptions;
    }

    protected function getCategories(&$languages, $exist_meta_title, $exist_seo_url_table, $offset = null, $rows = null, $min_id = null, $max_id = null) {
        if ($exist_seo_url_table) {
            $sql = "SELECT c.* FROM `" . DB_PREFIX . "category` c ";
        } else {
            $sql = "SELECT c.*, ua.keyword FROM `" . DB_PREFIX . "category` c ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "url_alias` ua ON ua.query=CONCAT('category_id=',c.category_id) ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE c.category_id BETWEEN $min_id AND $max_id ";
        }
        $sql .= "GROUP BY c.`category_id` ";
        $sql .= "ORDER BY c.`category_id` ASC ";
        if (isset($offset) && isset($rows)) {
            $sql .= "LIMIT $offset,$rows; ";
        } else {
            $sql .= "; ";
        }
        $results = $this->db->query($sql);
        $category_descriptions = $this->getCategoryDescriptions($languages, $offset, $rows, $min_id, $max_id);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($category_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $category_descriptions[$language_code][$key]['name'];
                    $results->rows[$key]['description'][$language_code] = $category_descriptions[$language_code][$key]['description'];
                    if ($exist_meta_title) {
                        $results->rows[$key]['meta_title'][$language_code] = $category_descriptions[$language_code][$key]['meta_title'];
                    }
                    $results->rows[$key]['meta_description'][$language_code] = $category_descriptions[$language_code][$key]['meta_description'];
                    $results->rows[$key]['meta_keyword'][$language_code] = $category_descriptions[$language_code][$key]['meta_keyword'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                    $results->rows[$key]['description'][$language_code] = '';
                    if ($exist_meta_title) {
                        $results->rows[$key]['meta_title'][$language_code] = '';
                    }
                    $results->rows[$key]['meta_description'][$language_code] = '';
                    $results->rows[$key]['meta_keyword'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateCategoriesWorksheet(&$worksheet, &$languages, &$box_format, &$text_format, $offset = null, $rows = null, &$min_id = null, &$max_id = null) {
        // Opencart versions from 2.0 onwards also have category_description.meta_title
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "category_description` LIKE 'meta_title'";
        $query = $this->db->query($sql);
        $exist_meta_title = ($query->num_rows > 0) ? true : false;

        // Opencart versions from 3.0 onwards use the seo_url DB table
        $exist_seo_url_table = $this->use_table_seo_url;

        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('category_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('parent_id') + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('top'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('columns') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('sort_order') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_name'), 12) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_added'), 19) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_modified'), 19) + 1);
        if (!$exist_seo_url_table) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('seo_keyword'), 16) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('description'), 32) + 1);
        }
        if ($exist_meta_title) {
            foreach ($languages as $language) {
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_title'), 20) + 1);
            }
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_description'), 32) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_keywords'), 32) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('store_ids'), 16) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('layout'), 16) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'), 5) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'category_id';
        $data[$j++] = 'parent_id';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $data[$j++] = 'top';
        $data[$j++] = 'columns';
        $data[$j++] = 'sort_order';
        $styles[$j] = &$text_format;
        $data[$j++] = 'image_name';
        $styles[$j] = &$text_format;
        $data[$j++] = 'date_added';
        $styles[$j] = &$text_format;
        $data[$j++] = 'date_modified';
        if (!$exist_seo_url_table) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'seo_keyword';
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'description(' . $language['code'] . ')';
        }
        if ($exist_meta_title) {
            foreach ($languages as $language) {
                $styles[$j] = &$text_format;
                $data[$j++] = 'meta_title(' . $language['code'] . ')';
            }
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'meta_description(' . $language['code'] . ')';
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'meta_keywords(' . $language['code'] . ')';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'store_ids';
        $styles[$j] = &$text_format;
        $data[$j++] = 'layout';
        $data[$j++] = 'status';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual categories data
        $i += 1;
        $j = 0;
        $store_ids = $this->getStoreIdsForCategories();
        $layouts = $this->getLayoutsForCategories();
        $categories = $this->getCategories($languages, $exist_meta_title, $exist_seo_url_table, $offset, $rows, $min_id, $max_id);
        $len = count($categories);
        $min_id = ($len > 0) ? $categories[0]['category_id'] : 0;
        $max_id = ($len > 0) ? $categories[$len - 1]['category_id'] : 0;
        foreach ($categories as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(26);
            $data = array();
            $data[$j++] = $row['category_id'];
            $data[$j++] = $row['parent_id'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $data[$j++] = ($row['top'] == 0) ? "false" : "true";
            $data[$j++] = $row['column'];
            $data[$j++] = $row['sort_order'];
            $data[$j++] = $row['image'];
            $data[$j++] = $row['date_added'];
            $data[$j++] = $row['date_modified'];
            if (!$exist_seo_url_table) {
                $data[$j++] = $row['keyword'];
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['description'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            if ($exist_meta_title) {
                foreach ($languages as $language) {
                    $data[$j++] = html_entity_decode($row['meta_title'][$language['code']], ENT_QUOTES, 'UTF-8');
                }
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_description'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_keyword'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $store_id_list = '';
            $category_id = $row['category_id'];
            if (isset($store_ids[$category_id])) {
                foreach ($store_ids[$category_id] as $store_id) {
                    $store_id_list .= ($store_id_list == '') ? $store_id : ',' . $store_id;
                }
            }
            $data[$j++] = $store_id_list;
            $layout_list = '';
            if (isset($layouts[$category_id])) {
                foreach ($layouts[$category_id] as $store_id => $name) {
                    $layout_list .= ($layout_list == '') ? $store_id . ':' . $name : ',' . $store_id . ':' . $name;
                }
            }
            $data[$j++] = $layout_list;
            $data[$j++] = ($row['status'] == 0) ? 'false' : 'true';
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getFilterGroupNames($language_id) {
        $sql = "SELECT filter_group_id, name ";
        $sql .= "FROM `" . DB_PREFIX . "filter_group_description` ";
        $sql .= "WHERE language_id='" . (int) $language_id . "' ";
        $sql .= "ORDER BY filter_group_id ASC";
        $query = $this->db->query($sql);
        $filter_group_names = array();
        foreach ($query->rows as $row) {
            $filter_group_id = $row['filter_group_id'];
            $name = $row['name'];
            $filter_group_names[$filter_group_id] = $name;
        }
        return $filter_group_names;
    }

    protected function getFilterNames($language_id) {
        $sql = "SELECT filter_id, name ";
        $sql .= "FROM `" . DB_PREFIX . "filter_description` ";
        $sql .= "WHERE language_id='" . (int) $language_id . "' ";
        $sql .= "ORDER BY filter_id ASC";
        $query = $this->db->query($sql);
        $filter_names = array();
        foreach ($query->rows as $row) {
            $filter_id = $row['filter_id'];
            $filter_name = $row['name'];
            $filter_names[$filter_id] = $filter_name;
        }
        return $filter_names;
    }

    protected function getCategoryFilters($min_id, $max_id) {
        $sql = "SELECT cf.category_id, fg.filter_group_id, cf.filter_id ";
        $sql .= "FROM `" . DB_PREFIX . "category_filter` cf ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "filter` f ON f.filter_id=cf.filter_id ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "filter_group` fg ON fg.filter_group_id=f.filter_group_id ";
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE category_id BETWEEN $min_id AND $max_id ";
        }
        $sql .= "ORDER BY cf.category_id ASC, fg.filter_group_id ASC, cf.filter_id ASC";
        $query = $this->db->query($sql);
        $category_filters = array();
        foreach ($query->rows as $row) {
            $category_filter = array();
            $category_filter['category_id'] = $row['category_id'];
            $category_filter['filter_group_id'] = $row['filter_group_id'];
            $category_filter['filter_id'] = $row['filter_id'];
            $category_filters[] = $category_filter;
        }
        return $category_filters;
    }

    protected function populateCategoryFiltersWorksheet(&$worksheet, &$languages, $default_language_id, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('category_id') + 1);
        if ($this->config->get('export_import_settings_use_filter_group_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('filter_group_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter_group'), 30) + 1);
        }
        if ($this->config->get('export_import_settings_use_filter_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('filter_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter'), 30) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('text') + 4, 30) + 1);
        }

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'category_id';
        if ($this->config->get('export_import_settings_use_filter_group_id')) {
            $data[$j++] = 'filter_group_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'filter_group';
        }
        if ($this->config->get('export_import_settings_use_filter_id')) {
            $data[$j++] = 'filter_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'filter';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual category filters data
        if (!$this->config->get('export_import_settings_use_filter_group_id')) {
            $filter_group_names = $this->getFilterGroupNames($default_language_id);
        }
        if (!$this->config->get('export_import_settings_use_filter_id')) {
            $filter_names = $this->getFilterNames($default_language_id);
        }
        $i += 1;
        $j = 0;
        $category_filters = $this->getCategoryFilters($min_id, $max_id);
        foreach ($category_filters as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['category_id'];
            if ($this->config->get('export_import_settings_use_filter_group_id')) {
                $data[$j++] = $row['filter_group_id'];
            } else {
                $data[$j++] = html_entity_decode($filter_group_names[$row['filter_group_id']], ENT_QUOTES, 'UTF-8');
            }
            if ($this->config->get('export_import_settings_use_filter_id')) {
                $data[$j++] = $row['filter_id'];
            } else {
                $data[$j++] = html_entity_decode($filter_names[$row['filter_id']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getCategorySEOKeywords(&$languages, $min_id, $max_id) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "seo_url` ";
        $sql .= "WHERE query LIKE 'category_id=%' AND ";
        $sql .= "CAST(SUBSTRING(query FROM 13) AS UNSIGNED INTEGER) >= '" . (int) $min_id . "' AND ";
        $sql .= "CAST(SUBSTRING(query FROM 13) AS UNSIGNED INTEGER) <= '" . (int) $max_id . "' ";
        $sql .= "ORDER BY CAST(SUBSTRING(query FROM 13) AS UNSIGNED INTEGER), store_id, language_id";
        $query = $this->db->query($sql);
        $seo_keywords = array();
        foreach ($query->rows as $row) {
            $category_id = (int) substr($row['query'], 12);
            $store_id = (int) $row['store_id'];
            $language_id = (int) $row['language_id'];
            if (!isset($seo_keywords[$category_id])) {
                $seo_keywords[$category_id] = array();
            }
            if (!isset($seo_keywords[$category_id][$store_id])) {
                $seo_keywords[$category_id][$store_id] = array();
            }
            $seo_keywords[$category_id][$store_id][$language_id] = $row['keyword'];
        }
        $results = array();
        foreach ($seo_keywords as $category_id => $val1) {
            foreach ($val1 as $store_id => $val2) {
                $keyword = array();
                foreach ($languages as $language) {
                    $language_id = $language['language_id'];
                    $language_code = $language['code'];
                    $keyword[$language_code] = isset($val2[$language_id]) ? $val2[$language_id] : '';
                }
                $results[] = array(
                    'category_id' => $category_id,
                    'store_id' => $store_id,
                    'keyword' => $keyword
                );
            }
        }
        return $results;
    }

    protected function populateCategorySEOKeywordsWorksheet(&$worksheet, &$languages, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('category_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('store_id') + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('keyword') + 4, 30) + 1);
        }

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'category_id';
        $data[$j++] = 'store_id';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'keyword(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual category SEO keywords data
        $i += 1;
        $j = 0;
        $category_seo_keywords = $this->getCategorySEOKeywords($languages, $min_id, $max_id);
        foreach ($category_seo_keywords as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(26);
            $data = array();
            $data[$j++] = $row['category_id'];
            $data[$j++] = $row['store_id'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['keyword'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getStoreIdsForProducts() {
        $sql = "SELECT product_id, store_id FROM `" . DB_PREFIX . "product_to_store` ps;";
        $store_ids = array();
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            $productId = $row['product_id'];
            $store_id = $row['store_id'];
            if (!isset($store_ids[$productId])) {
                $store_ids[$productId] = array();
            }
            if (!in_array($store_id, $store_ids[$productId])) {
                $store_ids[$productId][] = $store_id;
            }
        }
        return $store_ids;
    }

    protected function getLayoutsForProducts() {
        $sql = "SELECT pl.*, l.name FROM `" . DB_PREFIX . "product_to_layout` pl ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "layout` l ON pl.layout_id = l.layout_id ";
        $sql .= "ORDER BY pl.product_id, pl.store_id;";
        $result = $this->db->query($sql);
        $layouts = array();
        foreach ($result->rows as $row) {
            $productId = $row['product_id'];
            $store_id = $row['store_id'];
            $name = $row['name'];
            if (!isset($layouts[$productId])) {
                $layouts[$productId] = array();
            }
            $layouts[$productId][$store_id] = $name;
        }
        return $layouts;
    }

    protected function getProductDescriptions(&$languages, $offset = null, $rows = null, $min_id = null, $max_id = null) {
        // some older versions of OpenCart use the 'product_tag' table
        $exist_table_product_tag = false;
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_tag'");
        $exist_table_product_tag = ($query->num_rows > 0);

        // query the product_description table for each language
        $product_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT p.product_id, " . (($exist_table_product_tag) ? "GROUP_CONCAT(pt.tag SEPARATOR \",\") AS tag, " : "") . "pd.* ";
            $sql .= "FROM `" . DB_PREFIX . "product` p ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_description` pd ON pd.product_id=p.product_id AND pd.language_id='" . (int) $language_id . "' ";
            if ($exist_table_product_tag) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "product_tag` pt ON pt.product_id=p.product_id AND pt.language_id='" . (int) $language_id . "' ";
            }
            if ($this->posted_categories) {
                $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=p.product_id ";
            }
            if (isset($min_id) && isset($max_id)) {
                $sql .= "WHERE p.product_id BETWEEN $min_id AND $max_id ";
                if ($this->posted_categories) {
                    $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
                }
            } else if ($this->posted_categories) {
                $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
            }
            $sql .= "GROUP BY p.product_id ";
            $sql .= "ORDER BY p.product_id ";
            if (isset($offset) && isset($rows)) {
                $sql .= "LIMIT $offset,$rows; ";
            } else {
                $sql .= "; ";
            }
            $query = $this->db->query($sql);
            $product_descriptions[$language_code] = $query->rows;
        }
        return $product_descriptions;
    }

    protected function getProducts(&$languages, $default_language_id, $product_fields, $exist_meta_title, $exist_seo_url_table, $offset = null, $rows = null, $min_id = null, $max_id = null) {
        $sql = "SELECT ";
        $sql .= "  p.product_id,";
        $sql .= "  GROUP_CONCAT( DISTINCT CAST(pc.category_id AS CHAR(11)) SEPARATOR \",\" ) AS categories,";
        $sql .= "  p.sku,";
        $sql .= "  p.upc,";
        if (in_array('ean', $product_fields)) {
            $sql .= "  p.ean,";
        }
        if (in_array('jan', $product_fields)) {
            $sql .= "  p.jan,";
        }
        if (in_array('isbn', $product_fields)) {
            $sql .= "  p.isbn,";
        }
        if (in_array('mpn', $product_fields)) {
            $sql .= "  p.mpn,";
        }
        $sql .= "  p.location,";
        $sql .= "  p.quantity,";
        $sql .= "  p.model,";
        $sql .= "  m.name AS manufacturer,";
        $sql .= "  p.image AS image_name,";
        $sql .= "  p.shipping,";
        $sql .= "  p.price,";
        $sql .= "  p.points,";
        $sql .= "  p.date_added,";
        $sql .= "  p.date_modified,";
        $sql .= "  p.date_available,";
        $sql .= "  p.weight,";
        $sql .= "  wc.unit AS weight_unit,";
        $sql .= "  p.length,";
        $sql .= "  p.width,";
        $sql .= "  p.height,";
        $sql .= "  p.status,";
        $sql .= "  p.tax_class_id,";
        $sql .= "  p.sort_order,";
        if (!$exist_seo_url_table) {
            $sql .= "  ua.keyword,";
        }
        $sql .= "  p.stock_status_id, ";
        $sql .= "  mc.unit AS length_unit, ";
        $sql .= "  p.subtract, ";
        $sql .= "  p.minimum, ";
        $sql .= "  GROUP_CONCAT( DISTINCT CAST(pr.related_id AS CHAR(11)) SEPARATOR \",\" ) AS related ";
        $sql .= "FROM `" . DB_PREFIX . "product` p ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON p.product_id=pc.product_id ";
        if ($this->posted_categories) {
            $sql .= " LEFT JOIN `" . DB_PREFIX . "product_to_category` pc2 ON p.product_id=pc2.product_id ";
        }
        if (!$exist_seo_url_table) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "url_alias` ua ON ua.query=CONCAT('product_id=',p.product_id) ";
        }
        $sql .= "LEFT JOIN `" . DB_PREFIX . "manufacturer` m ON m.manufacturer_id = p.manufacturer_id ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "weight_class_description` wc ON wc.weight_class_id = p.weight_class_id ";
        $sql .= "  AND wc.language_id=$default_language_id ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "length_class_description` mc ON mc.length_class_id=p.length_class_id ";
        $sql .= "  AND mc.language_id=$default_language_id ";
        $sql .= "LEFT JOIN `" . DB_PREFIX . "product_related` pr ON pr.product_id=p.product_id ";
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE p.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc2.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc2.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "GROUP BY p.product_id ";
        $sql .= "ORDER BY p.product_id ";
        if (isset($offset) && isset($rows)) {
            $sql .= "LIMIT $offset,$rows; ";
        } else {
            $sql .= "; ";
        }
        $results = $this->db->query($sql);
        $product_descriptions = $this->getProductDescriptions($languages, $offset, $rows, $min_id, $max_id);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($product_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $product_descriptions[$language_code][$key]['name'];
                    $results->rows[$key]['description'][$language_code] = $product_descriptions[$language_code][$key]['description'];
                    if ($exist_meta_title) {
                        $results->rows[$key]['meta_title'][$language_code] = $product_descriptions[$language_code][$key]['meta_title'];
                    }
                    $results->rows[$key]['meta_description'][$language_code] = $product_descriptions[$language_code][$key]['meta_description'];
                    $results->rows[$key]['meta_keyword'][$language_code] = $product_descriptions[$language_code][$key]['meta_keyword'];
                    $results->rows[$key]['tag'][$language_code] = $product_descriptions[$language_code][$key]['tag'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                    $results->rows[$key]['description'][$language_code] = '';
                    if ($exist_meta_title) {
                        $results->rows[$key]['meta_title'][$language_code] = '';
                    }
                    $results->rows[$key]['meta_description'][$language_code] = '';
                    $results->rows[$key]['meta_keyword'][$language_code] = '';
                    $results->rows[$key]['tag'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateProductsWorksheet(&$worksheet, &$languages, $default_language_id, &$price_format, &$box_format, &$weight_format, &$text_format, $offset = null, $rows = null, &$min_id = null, &$max_id = null) {
        // get list of the field names, some are only available for certain OpenCart versions
        $query = $this->db->query("DESCRIBE `" . DB_PREFIX . "product`");
        $product_fields = array();
        foreach ($query->rows as $row) {
            $product_fields[] = $row['Field'];
        }

        // Opencart versions from 2.0 onwards also have product_description.meta_title
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "product_description` LIKE 'meta_title'";
        $query = $this->db->query($sql);
        $exist_meta_title = ($query->num_rows > 0) ? true : false;

        // Opencart versions from 3.0 onwards use the seo_url DB table
        $exist_seo_url_table = $this->use_table_seo_url;

        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'), 4) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('categories'), 12) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sku'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('upc'), 12) + 1);
        if (in_array('ean', $product_fields)) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('ean'), 14) + 1);
        }
        if (in_array('jan', $product_fields)) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('jan'), 13) + 1);
        }
        if (in_array('isbn', $product_fields)) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('isbn'), 13) + 1);
        }
        if (in_array('mpn', $product_fields)) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('mpn'), 15) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('location'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('quantity'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('model'), 8) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('manufacturer'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image_name'), 12) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('shipping'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_added'), 19) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_modified'), 19) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_available'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight'), 6) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight_unit'), 3) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length'), 8) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('width'), 8) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('height'), 8) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('length_unit'), 3) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('tax_class_id'), 2) + 1);
        if (!$exist_seo_url_table) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('seo_keyword'), 16) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('description') + 4, 32) + 1);
        }
        if ($exist_meta_title) {
            foreach ($languages as $language) {
                $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_title') + 4, 20) + 1);
            }
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_description') + 4, 32) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('meta_keywords') + 4, 32) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('stock_status_id'), 3) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('store_ids'), 16) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('layout'), 16) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('related_ids'), 16) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('tags') + 4, 32) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 8) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('subtract'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('minimum'), 8) + 1);

        // The product headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'categories';
        $styles[$j] = &$text_format;
        $data[$j++] = 'sku';
        $styles[$j] = &$text_format;
        $data[$j++] = 'upc';
        if (in_array('ean', $product_fields)) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'ean';
        }
        if (in_array('jan', $product_fields)) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'jan';
        }
        if (in_array('isbn', $product_fields)) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'isbn';
        }
        if (in_array('mpn', $product_fields)) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'mpn';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'location';
        $data[$j++] = 'quantity';
        $styles[$j] = &$text_format;
        $data[$j++] = 'model';
        $styles[$j] = &$text_format;
        $data[$j++] = 'manufacturer';
        $styles[$j] = &$text_format;
        $data[$j++] = 'image_name';
        $data[$j++] = 'shipping';
        $styles[$j] = &$price_format;
        $data[$j++] = 'price';
        $data[$j++] = 'points';
        $data[$j++] = 'date_added';
        $data[$j++] = 'date_modified';
        $data[$j++] = 'date_available';
        $styles[$j] = &$weight_format;
        $data[$j++] = 'weight';
        $data[$j++] = 'weight_unit';
        $data[$j++] = 'length';
        $data[$j++] = 'width';
        $data[$j++] = 'height';
        $data[$j++] = 'length_unit';
        $data[$j++] = 'status';
        $data[$j++] = 'tax_class_id';
        if (!$exist_seo_url_table) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'seo_keyword';
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'description(' . $language['code'] . ')';
        }
        if ($exist_meta_title) {
            foreach ($languages as $language) {
                $styles[$j] = &$text_format;
                $data[$j++] = 'meta_title(' . $language['code'] . ')';
            }
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'meta_description(' . $language['code'] . ')';
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'meta_keywords(' . $language['code'] . ')';
        }
        $data[$j++] = 'stock_status_id';
        $data[$j++] = 'store_ids';
        $styles[$j] = &$text_format;
        $data[$j++] = 'layout';
        $data[$j++] = 'related_ids';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'tags(' . $language['code'] . ')';
        }
        $data[$j++] = 'sort_order';
        $data[$j++] = 'subtract';
        $data[$j++] = 'minimum';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual products data
        $i += 1;
        $j = 0;
        $store_ids = $this->getStoreIdsForProducts();
        $layouts = $this->getLayoutsForProducts();
        $products = $this->getProducts($languages, $default_language_id, $product_fields, $exist_meta_title, $exist_seo_url_table, $offset, $rows, $min_id, $max_id);
        $len = count($products);
        $min_id = ($len > 0) ? $products[0]['product_id'] : 0;
        $max_id = ($len > 0) ? $products[$len - 1]['product_id'] : 0;
        foreach ($products as $row) {
            $data = array();
            $worksheet->getRowDimension($i)->setRowHeight(26);
            $product_id = $row['product_id'];
            $data[$j++] = $product_id;
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $data[$j++] = $row['categories'];
            $data[$j++] = $row['sku'];
            $data[$j++] = $row['upc'];
            if (in_array('ean', $product_fields)) {
                $data[$j++] = $row['ean'];
            }
            if (in_array('jan', $product_fields)) {
                $data[$j++] = $row['jan'];
            }
            if (in_array('isbn', $product_fields)) {
                $data[$j++] = $row['isbn'];
            }
            if (in_array('mpn', $product_fields)) {
                $data[$j++] = $row['mpn'];
            }
            $data[$j++] = $row['location'];
            $data[$j++] = $row['quantity'];
            $data[$j++] = $row['model'];
            $data[$j++] = $row['manufacturer'];
            $data[$j++] = $row['image_name'];
            $data[$j++] = ($row['shipping'] == 0) ? 'no' : 'yes';
            $data[$j++] = $row['price'];
            $data[$j++] = $row['points'];
            $data[$j++] = $row['date_added'];
            $data[$j++] = $row['date_modified'];
            $data[$j++] = $row['date_available'];
            $data[$j++] = $row['weight'];
            $data[$j++] = $row['weight_unit'];
            $data[$j++] = $row['length'];
            $data[$j++] = $row['width'];
            $data[$j++] = $row['height'];
            $data[$j++] = $row['length_unit'];
            $data[$j++] = ($row['status'] == 0) ? 'false' : 'true';
            $data[$j++] = $row['tax_class_id'];
            if (!$exist_seo_url_table) {
                $data[$j++] = ($row['keyword']) ? $row['keyword'] : '';
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['description'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            if ($exist_meta_title) {
                foreach ($languages as $language) {
                    $data[$j++] = html_entity_decode($row['meta_title'][$language['code']], ENT_QUOTES, 'UTF-8');
                }
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_description'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['meta_keyword'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $data[$j++] = $row['stock_status_id'];
            $store_id_list = '';
            if (isset($store_ids[$product_id])) {
                foreach ($store_ids[$product_id] as $store_id) {
                    $store_id_list .= ($store_id_list == '') ? $store_id : ',' . $store_id;
                }
            }
            $data[$j++] = $store_id_list;
            $layout_list = '';
            if (isset($layouts[$product_id])) {
                foreach ($layouts[$product_id] as $store_id => $name) {
                    $layout_list .= ($layout_list == '') ? $store_id . ':' . $name : ',' . $store_id . ':' . $name;
                }
            }
            $data[$j++] = $layout_list;
            $data[$j++] = $row['related'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['tag'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $data[$j++] = $row['sort_order'];
            $data[$j++] = ($row['subtract'] == 0) ? 'false' : 'true';
            $data[$j++] = $row['minimum'];
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getAdditionalImages($min_id = null, $max_id = null, $exist_sort_order = true) {
        if ($exist_sort_order) {
            $sql = "SELECT DISTINCT pi.product_id, pi.image, pi.sort_order ";
        } else {
            $sql = "SELECT DISTINCT pi.product_id, pi.image ";
        }
        $sql .= "FROM `" . DB_PREFIX . "product_image` pi ";
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=pi.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE pi.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        if ($exist_sort_order) {
            $sql .= "ORDER BY product_id, sort_order, image;";
        } else {
            $sql .= "ORDER BY product_id, image;";
        }
        $result = $this->db->query($sql);
        return $result->rows;
    }

    protected function populateAdditionalImagesWorksheet(&$worksheet, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // check for the existence of product_image.sort_order field
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "product_image` LIKE 'sort_order'";
        $query = $this->db->query($sql);
        $exist_sort_order = ($query->num_rows > 0) ? true : false;

        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('product_id'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image'), 30) + 1);
        if ($exist_sort_order) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        }

        // The additional images headings row and colum styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'image';
        if ($exist_sort_order) {
            $data[$j++] = 'sort_order';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual additional images data
        $styles = array();
        $i += 1;
        $j = 0;
        $additional_images = $this->getAdditionalImages($min_id, $max_id, $exist_sort_order);
        foreach ($additional_images as $row) {
            $data = array();
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data[$j++] = $row['product_id'];
            $data[$j++] = $row['image'];
            if ($exist_sort_order) {
                $data[$j++] = $row['sort_order'];
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getSpecials($language_id, $min_id = null, $max_id = null) {
        // Newer OC versions use the 'customer_group_description' instead of 'customer_group' table for the 'name' field
        $exist_table_customer_group_description = false;
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "customer_group_description'");
        $exist_table_customer_group_description = ($query->num_rows > 0);

        // get the product specials
        $sql = "SELECT DISTINCT ps.*, ";
        $sql .= ($exist_table_customer_group_description) ? "cgd.name " : "cg.name ";
        $sql .= "FROM `" . DB_PREFIX . "product_special` ps ";
        if ($exist_table_customer_group_description) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON cgd.customer_group_id=ps.customer_group_id ";
            $sql .= "  AND cgd.language_id=$language_id ";
        } else {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group` cg ON cg.customer_group_id=ps.customer_group_id ";
        }
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=ps.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE ps.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "ORDER BY ps.product_id, name, ps.priority";
        $result = $this->db->query($sql);
        return $result->rows;
    }

    protected function populateSpecialsWorksheet(&$worksheet, $language_id, &$price_format, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('priority') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_start'), 19) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_end'), 19) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'customer_group';
        $data[$j++] = 'priority';
        $styles[$j] = &$price_format;
        $data[$j++] = 'price';
        $data[$j++] = 'date_start';
        $data[$j++] = 'date_end';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product specials data
        $i += 1;
        $j = 0;
        $specials = $this->getSpecials($language_id, $min_id, $max_id);
        foreach ($specials as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            $data[$j++] = $row['name'];
            $data[$j++] = $row['priority'];
            $data[$j++] = $row['price'];
            $data[$j++] = $row['date_start'];
            $data[$j++] = $row['date_end'];
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getDiscounts($language_id, $min_id = null, $max_id = null) {
        // Newer OC versions use the 'customer_group_description' instead of 'customer_group' table for the 'name' field
        $exist_table_customer_group_description = false;
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "customer_group_description'");
        $exist_table_customer_group_description = ($query->num_rows > 0);

        // get the product discounts
        $sql = "SELECT pd.*, ";
        $sql .= ($exist_table_customer_group_description) ? "cgd.name " : "cg.name ";
        $sql .= "FROM `" . DB_PREFIX . "product_discount` pd ";
        if ($exist_table_customer_group_description) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON cgd.customer_group_id=pd.customer_group_id ";
            $sql .= "  AND cgd.language_id=$language_id ";
        } else {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group` cg ON cg.customer_group_id=pd.customer_group_id ";
        }
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=pd.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE pd.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "ORDER BY pd.product_id ASC, name ASC, pd.quantity ASC";
        $result = $this->db->query($sql);
        return $result->rows;
    }

    protected function populateDiscountsWorksheet(&$worksheet, $language_id, &$price_format, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('quantity') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('priority') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_start'), 19) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_end'), 19) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'customer_group';
        $data[$j++] = 'quantity';
        $data[$j++] = 'priority';
        $styles[$j] = &$price_format;
        $data[$j++] = 'price';
        $data[$j++] = 'date_start';
        $data[$j++] = 'date_end';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product discounts data
        $i += 1;
        $j = 0;
        $discounts = $this->getDiscounts($language_id, $min_id, $max_id);
        foreach ($discounts as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            $data[$j++] = $row['name'];
            $data[$j++] = $row['quantity'];
            $data[$j++] = $row['priority'];
            $data[$j++] = $row['price'];
            $data[$j++] = $row['date_start'];
            $data[$j++] = $row['date_end'];
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getRewards($language_id, $min_id = null, $max_id = null) {
        // Newer OC versions use the 'customer_group_description' instead of 'customer_group' table for the 'name' field
        $exist_table_customer_group_description = false;
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "customer_group_description'");
        $exist_table_customer_group_description = ($query->num_rows > 0);

        // get the product rewards
        $sql = "SELECT pr.*, ";
        $sql .= ($exist_table_customer_group_description) ? "cgd.name " : "cg.name ";
        $sql .= "FROM `" . DB_PREFIX . "product_reward` pr ";
        if ($exist_table_customer_group_description) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON cgd.customer_group_id=pr.customer_group_id ";
            $sql .= "  AND cgd.language_id=$language_id ";
        } else {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "customer_group` cg ON cg.customer_group_id=pr.customer_group_id ";
        }
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=pr.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE pr.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "ORDER BY pr.product_id, name";
        $result = $this->db->query($sql);
        return $result->rows;
    }

    protected function populateRewardsWorksheet(&$worksheet, $language_id, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('points') + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'customer_group';
        $data[$j++] = 'points';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product rewards data
        $i += 1;
        $j = 0;
        $rewards = $this->getRewards($language_id, $min_id, $max_id);
        foreach ($rewards as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            $data[$j++] = $row['name'];
            $data[$j++] = $row['points'];
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getProductOptions($min_id, $max_id) {
        // get default language id
        $language_id = $this->getDefaultLanguageId();

        // Opencart versions from 2.0 onwards use product_option.value instead of the older product_option.option_value
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "product_option` LIKE 'value'";
        $query = $this->db->query($sql);
        $exist_po_value = ($query->num_rows > 0) ? true : false;

        // DB query for getting the product options
        if ($exist_po_value) {
            $sql = "SELECT p.product_id, po.option_id, po.value AS option_value, po.required, od.name AS `option` FROM ";
        } else {
            $sql = "SELECT p.product_id, po.option_id, po.option_value, po.required, od.name AS `option` FROM ";
        }
        $sql .= "( SELECT p1.product_id ";
        $sql .= "  FROM `" . DB_PREFIX . "product` p1 ";
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=p1.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "  WHERE p1.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "  ORDER BY p1.product_id ASC ";
        $sql .= ") AS p ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "product_option` po ON po.product_id=p.product_id ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "option_description` od ON od.option_id=po.option_id AND od.language_id='" . (int) $language_id . "' ";
        $sql .= "ORDER BY p.product_id ASC, po.option_id ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    protected function populateProductOptionsWorksheet(&$worksheet, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        if ($this->config->get('export_import_settings_use_option_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('option_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option'), 30) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('default_option_value') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('required'), 5) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        if ($this->config->get('export_import_settings_use_option_id')) {
            $data[$j++] = 'option_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'option';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'default_option_value';
        $data[$j++] = 'required';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product options data
        $i += 1;
        $j = 0;
        $product_options = $this->getProductOptions($min_id, $max_id);
        foreach ($product_options as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            if ($this->config->get('export_import_settings_use_option_id')) {
                $data[$j++] = $row['option_id'];
            } else {
                $data[$j++] = html_entity_decode($row['option'], ENT_QUOTES, 'UTF-8');
            }
            $data[$j++] = html_entity_decode($row['option_value'], ENT_QUOTES, 'UTF-8');
            $data[$j++] = ($row['required'] == 0) ? 'false' : 'true';
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getProductOptionValues($min_id, $max_id) {
        $language_id = $this->getDefaultLanguageId();
        $sql = "SELECT ";
        $sql .= "  p.product_id, pov.option_id, pov.option_value_id, pov.quantity, pov.subtract, od.name AS `option`, ovd.name AS option_value, ";
        $sql .= "  pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix ";
        $sql .= "FROM ";
        $sql .= "( SELECT p1.product_id ";
        $sql .= "  FROM `" . DB_PREFIX . "product` p1 ";
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=p1.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "  WHERE p1.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "  ORDER BY product_id ASC ";
        $sql .= ") AS p ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "product_option_value` pov ON pov.product_id=p.product_id ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "option_value_description` ovd ON ovd.option_value_id=pov.option_value_id AND ovd.language_id='" . (int) $language_id . "' ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "option_description` od ON od.option_id=ovd.option_id AND od.language_id='" . (int) $language_id . "' ";
        $sql .= "ORDER BY p.product_id ASC, pov.option_id ASC, pov.option_value_id";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    protected function populateProductOptionValuesWorksheet(&$worksheet, &$price_format, &$box_format, &$weight_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        if ($this->config->get('export_import_settings_use_option_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('option_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option'), 30) + 1);
        }
        if ($this->config->get('export_import_settings_use_option_value_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('option_value_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_value'), 30) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('quantity'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('subtract'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('price_prefix'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('points_prefix'), 5) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('weight_prefix'), 5) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        if ($this->config->get('export_import_settings_use_option_id')) {
            $data[$j++] = 'option_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'option';
        }
        if ($this->config->get('export_import_settings_use_option_value_id')) {
            $data[$j++] = 'option_value_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'option_value';
        }
        $data[$j++] = 'quantity';
        $data[$j++] = 'subtract';
        $styles[$j] = &$price_format;
        $data[$j++] = 'price';
        $data[$j++] = "price_prefix";
        $data[$j++] = 'points';
        $data[$j++] = "points_prefix";
        $styles[$j] = &$weight_format;
        $data[$j++] = 'weight';
        $data[$j++] = 'weight_prefix';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product option values data
        $i += 1;
        $j = 0;
        $product_option_values = $this->getProductOptionValues($min_id, $max_id);
        foreach ($product_option_values as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            if ($this->config->get('export_import_settings_use_option_id')) {
                $data[$j++] = $row['option_id'];
            } else {
                $data[$j++] = html_entity_decode($row['option'], ENT_QUOTES, 'UTF-8');
            }
            if ($this->config->get('export_import_settings_use_option_value_id')) {
                $data[$j++] = $row['option_value_id'];
            } else {
                $data[$j++] = html_entity_decode($row['option_value'], ENT_QUOTES, 'UTF-8');
            }
            $data[$j++] = $row['quantity'];
            $data[$j++] = ($row['subtract'] == 0) ? 'false' : 'true';
            $data[$j++] = $row['price'];
            $data[$j++] = $row['price_prefix'];
            $data[$j++] = $row['points'];
            $data[$j++] = $row['points_prefix'];
            $data[$j++] = $row['weight'];
            $data[$j++] = $row['weight_prefix'];
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getProductSEOKeywords(&$languages, $min_id, $max_id) {
        $sql = "SELECT s.* FROM `" . DB_PREFIX . "seo_url` s ";
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=CAST(SUBSTRING(s.query FROM 12) AS UNSIGNED INTEGER) ";
        }
        $sql .= "WHERE s.query LIKE 'product_id=%' AND ";
        if ($this->posted_categories) {
            $sql .= "pc.category_id IN " . $this->posted_categories . " AND ";
        }
        $sql .= "CAST(SUBSTRING(s.query FROM 12) AS UNSIGNED INTEGER) >= '" . (int) $min_id . "' AND ";
        $sql .= "CAST(SUBSTRING(s.query FROM 12) AS UNSIGNED INTEGER) <= '" . (int) $max_id . "' ";
        $sql .= "ORDER BY CAST(SUBSTRING(s.query FROM 12) AS UNSIGNED INTEGER), s.store_id, s.language_id";
        $query = $this->db->query($sql);
        $seo_keywords = array();
        foreach ($query->rows as $row) {
            $product_id = (int) substr($row['query'], 11);
            $store_id = (int) $row['store_id'];
            $language_id = (int) $row['language_id'];
            if (!isset($seo_keywords[$product_id])) {
                $seo_keywords[$product_id] = array();
            }
            if (!isset($seo_keywords[$product_id][$store_id])) {
                $seo_keywords[$product_id][$store_id] = array();
            }
            $seo_keywords[$product_id][$store_id][$language_id] = $row['keyword'];
        }
        $results = array();
        foreach ($seo_keywords as $product_id => $val1) {
            foreach ($val1 as $store_id => $val2) {
                $keyword = array();
                foreach ($languages as $language) {
                    $language_id = $language['language_id'];
                    $language_code = $language['code'];
                    $keyword[$language_code] = isset($val2[$language_id]) ? $val2[$language_id] : '';
                }
                $results[] = array(
                    'product_id' => $product_id,
                    'store_id' => $store_id,
                    'keyword' => $keyword
                );
            }
        }
        return $results;
    }

    protected function populateProductSEOKeywordsWorksheet(&$worksheet, &$languages, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('store_id') + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('keyword') + 4, 30) + 1);
        }

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        $data[$j++] = 'store_id';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'keyword(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product SEO keywords data
        $i += 1;
        $j = 0;
        $product_seo_keywords = $this->getProductSEOKeywords($languages, $min_id, $max_id);
        foreach ($product_seo_keywords as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(26);
            $data = array();
            $data[$j++] = $row['product_id'];
            $data[$j++] = $row['store_id'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['keyword'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getAttributeGroupNames($language_id) {
        $sql = "SELECT attribute_group_id, name ";
        $sql .= "FROM `" . DB_PREFIX . "attribute_group_description` ";
        $sql .= "WHERE language_id='" . (int) $language_id . "' ";
        $sql .= "ORDER BY attribute_group_id ASC";
        $query = $this->db->query($sql);
        $attribute_group_names = array();
        foreach ($query->rows as $row) {
            $attribute_group_id = $row['attribute_group_id'];
            $name = $row['name'];
            $attribute_group_names[$attribute_group_id] = $name;
        }
        return $attribute_group_names;
    }

    protected function getAttributeNames($language_id) {
        $sql = "SELECT attribute_id, name ";
        $sql .= "FROM `" . DB_PREFIX . "attribute_description` ";
        $sql .= "WHERE language_id='" . (int) $language_id . "' ";
        $sql .= "ORDER BY attribute_id ASC";
        $query = $this->db->query($sql);
        $attribute_names = array();
        foreach ($query->rows as $row) {
            $attribute_id = $row['attribute_id'];
            $attribute_name = $row['name'];
            $attribute_names[$attribute_id] = $attribute_name;
        }
        return $attribute_names;
    }

    protected function getProductAttributes(&$languages, $min_id, $max_id) {
        $sql = "SELECT pa.product_id, ag.attribute_group_id, pa.attribute_id, pa.language_id, pa.text ";
        $sql .= "FROM `" . DB_PREFIX . "product_attribute` pa ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "attribute` a ON a.attribute_id=pa.attribute_id ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "attribute_group` ag ON ag.attribute_group_id=a.attribute_group_id ";
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=pa.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE pa.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "ORDER BY pa.product_id ASC, ag.attribute_group_id ASC, pa.attribute_id ASC";
        $query = $this->db->query($sql);
        $texts = array();
        foreach ($query->rows as $row) {
            $product_id = $row['product_id'];
            $attribute_group_id = $row['attribute_group_id'];
            $attribute_id = $row['attribute_id'];
            $language_id = $row['language_id'];
            $text = $row['text'];
            $texts[$product_id][$attribute_group_id][$attribute_id][$language_id] = $text;
        }
        $product_attributes = array();
        foreach ($texts as $product_id => $level1) {
            foreach ($level1 as $attribute_group_id => $level2) {
                foreach ($level2 as $attribute_id => $text) {
                    $product_attribute = array();
                    $product_attribute['product_id'] = $product_id;
                    $product_attribute['attribute_group_id'] = $attribute_group_id;
                    $product_attribute['attribute_id'] = $attribute_id;
                    $product_attribute['text'] = array();
                    foreach ($languages as $language) {
                        $language_id = $language['language_id'];
                        $code = $language['code'];
                        if (isset($text[$language_id])) {
                            $product_attribute['text'][$code] = $text[$language_id];
                        } else {
                            $product_attribute['text'][$code] = '';
                        }
                    }
                    $product_attributes[] = $product_attribute;
                }
            }
        }
        return $product_attributes;
    }

    protected function populateProductAttributesWorksheet(&$worksheet, &$languages, $default_language_id, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        if ($this->config->get('export_import_settings_use_attribute_group_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('attribute_group_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute_group'), 30) + 1);
        }
        if ($this->config->get('export_import_settings_use_attribute_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('attribute_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute'), 30) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('text') + 4, 30) + 1);
        }

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        if ($this->config->get('export_import_settings_use_attribute_group_id')) {
            $data[$j++] = 'attribute_group_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'attribute_group';
        }
        if ($this->config->get('export_import_settings_use_attribute_id')) {
            $data[$j++] = 'attribute_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'attribute';
        }
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'text(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product attributes data
        if (!$this->config->get('export_import_settings_use_attribute_group_id')) {
            $attribute_group_names = $this->getAttributeGroupNames($default_language_id);
        }
        if (!$this->config->get('export_import_settings_use_attribute_id')) {
            $attribute_names = $this->getAttributeNames($default_language_id);
        }
        $i += 1;
        $j = 0;
        $product_attributes = $this->getProductAttributes($languages, $min_id, $max_id);
        foreach ($product_attributes as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            if ($this->config->get('export_import_settings_use_attribute_group_id')) {
                $data[$j++] = $row['attribute_group_id'];
            } else {
                $data[$j++] = html_entity_decode($attribute_group_names[$row['attribute_group_id']], ENT_QUOTES, 'UTF-8');
            }
            if ($this->config->get('export_import_settings_use_attribute_id')) {
                $data[$j++] = $row['attribute_id'];
            } else {
                $data[$j++] = html_entity_decode($attribute_names[$row['attribute_id']], ENT_QUOTES, 'UTF-8');
            }
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['text'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getProductFilters($min_id, $max_id) {
        $sql = "SELECT pf.product_id, fg.filter_group_id, pf.filter_id ";
        $sql .= "FROM `" . DB_PREFIX . "product_filter` pf ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "filter` f ON f.filter_id=pf.filter_id ";
        $sql .= "INNER JOIN `" . DB_PREFIX . "filter_group` fg ON fg.filter_group_id=f.filter_group_id ";
        if ($this->posted_categories) {
            $sql .= "LEFT JOIN `" . DB_PREFIX . "product_to_category` pc ON pc.product_id=pf.product_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE pf.product_id BETWEEN $min_id AND $max_id ";
            if ($this->posted_categories) {
                $sql .= "AND pc.category_id IN " . $this->posted_categories . " ";
            }
        } else if ($this->posted_categories) {
            $sql .= "WHERE pc.category_id IN " . $this->posted_categories . " ";
        }
        $sql .= "ORDER BY pf.product_id ASC, fg.filter_group_id ASC, pf.filter_id ASC";
        $query = $this->db->query($sql);
        $product_filters = array();
        foreach ($query->rows as $row) {
            $product_filter = array();
            $product_filter['product_id'] = $row['product_id'];
            $product_filter['filter_group_id'] = $row['filter_group_id'];
            $product_filter['filter_id'] = $row['filter_id'];
            $product_filters[] = $product_filter;
        }
        return $product_filters;
    }

    protected function populateProductFiltersWorksheet(&$worksheet, &$languages, $default_language_id, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('product_id') + 1);
        if ($this->config->get('export_import_settings_use_filter_group_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('filter_group_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter_group'), 30) + 1);
        }
        if ($this->config->get('export_import_settings_use_filter_id')) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('filter_id') + 1);
        } else {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter'), 30) + 1);
        }
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('text') + 4, 30) + 1);
        }

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'product_id';
        if ($this->config->get('export_import_settings_use_filter_group_id')) {
            $data[$j++] = 'filter_group_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'filter_group';
        }
        if ($this->config->get('export_import_settings_use_filter_id')) {
            $data[$j++] = 'filter_id';
        } else {
            $styles[$j] = &$text_format;
            $data[$j++] = 'filter';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual product filters data
        if (!$this->config->get('export_import_settings_use_filter_group_id')) {
            $filter_group_names = $this->getFilterGroupNames($default_language_id);
        }
        if (!$this->config->get('export_import_settings_use_filter_id')) {
            $filter_names = $this->getFilterNames($default_language_id);
        }
        $i += 1;
        $j = 0;
        $product_filters = $this->getProductFilters($min_id, $max_id);
        foreach ($product_filters as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['product_id'];
            if ($this->config->get('export_import_settings_use_filter_group_id')) {
                $data[$j++] = $row['filter_group_id'];
            } else {
                $data[$j++] = html_entity_decode($filter_group_names[$row['filter_group_id']], ENT_QUOTES, 'UTF-8');
            }
            if ($this->config->get('export_import_settings_use_filter_id')) {
                $data[$j++] = $row['filter_id'];
            } else {
                $data[$j++] = html_entity_decode($filter_names[$row['filter_id']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getOptionDescriptions(&$languages) {
        // query the option_description table for each language
        $option_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT o.option_id, od.* ";
            $sql .= "FROM `" . DB_PREFIX . "option` o ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "option_description` od ON od.option_id=o.option_id AND od.language_id='" . (int) $language_id . "' ";
            $sql .= "GROUP BY o.option_id ";
            $sql .= "ORDER BY o.option_id ASC ";
            $query = $this->db->query($sql);
            $option_descriptions[$language_code] = $query->rows;
        }
        return $option_descriptions;
    }

    protected function getOptions(&$languages) {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` ORDER BY option_id ASC");
        $option_descriptions = $this->getOptionDescriptions($languages);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($option_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $option_descriptions[$language_code][$key]['name'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateOptionsWorksheet(&$worksheet, &$languages, &$box_format, &$text_format) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_id'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('type'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }

        // The options headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'option_id';
        $data[$j++] = 'type';
        $data[$j++] = 'sort_order';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual options data
        $i += 1;
        $j = 0;
        $options = $this->getOptions($languages);
        foreach ($options as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['option_id'];
            $data[$j++] = $row['type'];
            $data[$j++] = $row['sort_order'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getOptionValueDescriptions(&$languages) {
        // query the option_description table for each language
        $option_value_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT ov.option_id, ov.option_value_id, ovd.* ";
            $sql .= "FROM `" . DB_PREFIX . "option_value` ov ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "option_value_description` ovd ON ovd.option_value_id=ov.option_value_id AND ovd.language_id='" . (int) $language_id . "' ";
            $sql .= "GROUP BY ov.option_id, ov.option_value_id ";
            $sql .= "ORDER BY ov.option_id ASC, ov.option_value_id ASC ";
            $query = $this->db->query($sql);
            $option_value_descriptions[$language_code] = $query->rows;
        }
        return $option_value_descriptions;
    }

    protected function getOptionValues(&$languages) {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option_value` ORDER BY option_id ASC, option_value_id ASC");
        $option_value_descriptions = $this->getOptionValueDescriptions($languages);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($option_value_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $option_value_descriptions[$language_code][$key]['name'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateOptionValuesWorksheet(&$worksheet, $languages, &$box_format, &$text_format) {
        // check for the existence of option_value.image field
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "option_value` LIKE 'image'";
        $query = $this->db->query($sql);
        $exist_image = ($query->num_rows > 0) ? true : false;

        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_value_id'), 2) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_id'), 4) + 1);
        if ($exist_image) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('image'), 12) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }

        // The option values headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'option_value_id';
        $data[$j++] = 'option_id';
        if ($exist_image) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'image';
        }
        $data[$j++] = 'sort_order';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual option values data
        $i += 1;
        $j = 0;
        $options = $this->getOptionValues($languages);
        foreach ($options as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['option_value_id'];
            $data[$j++] = $row['option_id'];
            if ($exist_image) {
                $data[$j++] = $row['image'];
            }
            $data[$j++] = $row['sort_order'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getAttributeGroupDescriptions(&$languages) {
        // query the attribute_group_description table for each language
        $attribute_group_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT ag.attribute_group_id, agd.* ";
            $sql .= "FROM `" . DB_PREFIX . "attribute_group` ag ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "attribute_group_description` agd ON agd.attribute_group_id=ag.attribute_group_id AND agd.language_id='" . (int) $language_id . "' ";
            $sql .= "GROUP BY ag.attribute_group_id ";
            $sql .= "ORDER BY ag.attribute_group_id ASC ";
            $query = $this->db->query($sql);
            $attribute_group_descriptions[$language_code] = $query->rows;
        }
        return $attribute_group_descriptions;
    }

    protected function getAttributeGroups(&$languages) {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "attribute_group` ORDER BY attribute_group_id ASC");
        $attribute_group_descriptions = $this->getAttributeGroupDescriptions($languages);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($attribute_group_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $attribute_group_descriptions[$language_code][$key]['name'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateAttributeGroupsWorksheet(&$worksheet, $languages, &$box_format, &$text_format) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute_group_id'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }

        // The attribute groups headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'attribute_group_id';
        $data[$j++] = 'sort_order';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual attribute groups data
        $i += 1;
        $j = 0;
        $attributes = $this->getAttributeGroups($languages);
        foreach ($attributes as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['attribute_group_id'];
            $data[$j++] = $row['sort_order'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getAttributeDescriptions(&$languages) {
        // query the attribute_description table for each language
        $attribute_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT a.attribute_group_id, a.attribute_id, ad.* ";
            $sql .= "FROM `" . DB_PREFIX . "attribute` a ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "attribute_description` ad ON ad.attribute_id=a.attribute_id AND ad.language_id='" . (int) $language_id . "' ";
            $sql .= "GROUP BY a.attribute_group_id, a.attribute_id ";
            $sql .= "ORDER BY a.attribute_group_id ASC, a.attribute_id ASC ";
            $query = $this->db->query($sql);
            $attribute_descriptions[$language_code] = $query->rows;
        }
        return $attribute_descriptions;
    }

    protected function getAttributes(&$languages) {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "attribute` ORDER BY attribute_group_id ASC, attribute_id ASC");
        $attribute_descriptions = $this->getAttributeDescriptions($languages);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($attribute_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $attribute_descriptions[$language_code][$key]['name'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateAttributesWorksheet(&$worksheet, $languages, &$box_format, &$text_format) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute_id'), 2) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('attribute_group_id'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }

        // The attributes headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'attribute_id';
        $data[$j++] = 'attribute_group_id';
        $data[$j++] = 'sort_order';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual attributes values data
        $i += 1;
        $j = 0;
        $options = $this->getAttributes($languages);
        foreach ($options as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['attribute_id'];
            $data[$j++] = $row['attribute_group_id'];
            $data[$j++] = $row['sort_order'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getFilterGroupDescriptions(&$languages) {
        // query the filter_group_description table for each language
        $filter_group_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT ag.filter_group_id, agd.* ";
            $sql .= "FROM `" . DB_PREFIX . "filter_group` ag ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "filter_group_description` agd ON agd.filter_group_id=ag.filter_group_id AND agd.language_id='" . (int) $language_id . "' ";
            $sql .= "GROUP BY ag.filter_group_id ";
            $sql .= "ORDER BY ag.filter_group_id ASC ";
            $query = $this->db->query($sql);
            $filter_group_descriptions[$language_code] = $query->rows;
        }
        return $filter_group_descriptions;
    }

    protected function getFilterGroups(&$languages) {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "filter_group` ORDER BY filter_group_id ASC");
        $filter_group_descriptions = $this->getFilterGroupDescriptions($languages);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($filter_group_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $filter_group_descriptions[$language_code][$key]['name'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateFilterGroupsWorksheet(&$worksheet, $languages, &$box_format, &$text_format) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter_group_id'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }

        // The filter groups headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'filter_group_id';
        $data[$j++] = 'sort_order';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual filter groups data
        $i += 1;
        $j = 0;
        $filters = $this->getFilterGroups($languages);
        foreach ($filters as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['filter_group_id'];
            $data[$j++] = $row['sort_order'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getFilterDescriptions(&$languages) {
        // query the filter_description table for each language
        $filter_descriptions = array();
        foreach ($languages as $language) {
            $language_id = $language['language_id'];
            $language_code = $language['code'];
            $sql = "SELECT a.filter_group_id, a.filter_id, ad.* ";
            $sql .= "FROM `" . DB_PREFIX . "filter` a ";
            $sql .= "LEFT JOIN `" . DB_PREFIX . "filter_description` ad ON ad.filter_id=a.filter_id AND ad.language_id='" . (int) $language_id . "' ";
            $sql .= "GROUP BY a.filter_group_id, a.filter_id ";
            $sql .= "ORDER BY a.filter_group_id ASC, a.filter_id ASC ";
            $query = $this->db->query($sql);
            $filter_descriptions[$language_code] = $query->rows;
        }
        return $filter_descriptions;
    }

    protected function getFilters(&$languages) {
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "filter` ORDER BY filter_group_id ASC, filter_id ASC");
        $filter_descriptions = $this->getFilterDescriptions($languages);
        foreach ($languages as $language) {
            $language_code = $language['code'];
            foreach ($results->rows as $key => $row) {
                if (isset($filter_descriptions[$language_code][$key])) {
                    $results->rows[$key]['name'][$language_code] = $filter_descriptions[$language_code][$key]['name'];
                } else {
                    $results->rows[$key]['name'][$language_code] = '';
                }
            }
        }
        return $results->rows;
    }

    protected function populateFiltersWorksheet(&$worksheet, $languages, &$box_format, &$text_format) {
        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter_id'), 2) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('filter_group_id'), 4) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('sort_order'), 5) + 1);
        foreach ($languages as $language) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('name') + 4, 30) + 1);
        }

        // The filters headings row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'filter_id';
        $data[$j++] = 'filter_group_id';
        $data[$j++] = 'sort_order';
        foreach ($languages as $language) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'name(' . $language['code'] . ')';
        }
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual filters values data
        $i += 1;
        $j = 0;
        $options = $this->getFilters($languages);
        foreach ($options as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['filter_id'];
            $data[$j++] = $row['filter_group_id'];
            $data[$j++] = $row['sort_order'];
            foreach ($languages as $language) {
                $data[$j++] = html_entity_decode($row['name'][$language['code']], ENT_QUOTES, 'UTF-8');
            }
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function getCustomers($exist_custom_field, $exist_salt, $exist_safe, $exist_token, $exist_code, $offset = null, $rows = null, $min_id = null, $max_id = null) {
        // Newer OC versions use the 'customer_group_description' instead of 'customer_group' table for the 'name' field
        $exist_table_customer_group_description = false;
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "customer_group_description'");
        $exist_table_customer_group_description = ($query->num_rows > 0);

        $language_id = $this->getDefaultLanguageId();

        if ($exist_table_customer_group_description) {
            $sql = "SELECT c.*, cgd.name AS customer_group FROM `" . DB_PREFIX . "customer` c ";
            $sql .= "INNER JOIN `" . DB_PREFIX . "customer_group_description` cgd ON cgd.customer_group_id=c.customer_group_id AND cgd.language_id='" . (int) $language_id . "' ";
        } else {
            $sql = "SELECT c.*, cg.name AS customer_group FROM `" . DB_PREFIX . "customer` c ";
            $sql .= "INNER JOIN `" . DB_PREFIX . "customer_group` cg ON cg.customer_group_id=c.customer_group_id ";
        }
        if (isset($min_id) && isset($max_id)) {
            $sql .= "WHERE c.customer_id BETWEEN $min_id AND $max_id ";
        }
        $sql .= "GROUP BY c.`customer_id` ";
        $sql .= "ORDER BY c.`customer_id` ASC ";
        if (isset($offset) && isset($rows)) {
            $sql .= "LIMIT $offset,$rows; ";
        } else {
            $sql .= "; ";
        }
        $results = $this->db->query($sql);
        return $results->rows;
    }

    protected function populateCustomersWorksheet(&$worksheet, &$box_format, &$text_format, $offset = null, $rows = null, &$min_id = null, &$max_id = null) {
        // Some fields are only available in certain Opencart versions
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'custom_field'";
        $query = $this->db->query($sql);
        $exist_custom_field = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'salt'";
        $query = $this->db->query($sql);
        $exist_salt = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'safe'";
        $query = $this->db->query($sql);
        $exist_safe = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'token'";
        $query = $this->db->query($sql);
        $exist_token = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'code'";
        $query = $this->db->query($sql);
        $exist_code = ($query->num_rows > 0) ? true : false;
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'approved'";
        $query = $this->db->query($sql);
        $exist_approved = ($query->num_rows > 0) ? true : false;

        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_group') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('store_id'), 2) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('firstname'), 20) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('lastname'), 20) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('email'), 30) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('telephone'), 14) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('fax'), 14) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('password'), 40) + 1);
        if ($exist_salt) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('salt'), 12) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('cart'), 14) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('wishlist'), 20) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('newsletter'), 5) + 1);
        if ($exist_custom_field) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('custom_field'), 20) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('ip'), 15) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('status'), 5) + 1);
        if ($exist_approved) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('approved'), 5) + 1);
        }
        if ($exist_safe) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('safe'), 5) + 1);
        }
        if ($exist_token) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('token'), 20) + 1);
        }
        if ($exist_code) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('code'), 20) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('date_added'), 19) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'customer_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'customer_group';
        $data[$j++] = 'store_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'firstname';
        $styles[$j] = &$text_format;
        $data[$j++] = 'lastname';
        $styles[$j] = &$text_format;
        $data[$j++] = 'email';
        $styles[$j] = &$text_format;
        $data[$j++] = 'telephone';
        $styles[$j] = &$text_format;
        $data[$j++] = 'fax';
        $styles[$j] = &$text_format;
        $data[$j++] = 'password';
        if ($exist_salt) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'salt';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'cart';
        $styles[$j] = &$text_format;
        $data[$j++] = 'wishlist';
        $data[$j++] = 'newsletter';
        if ($exist_custom_field) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'custom_field';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'ip';
        $data[$j++] = 'status';
        if ($exist_approved) {
            $data[$j++] = 'approved';
        }
        if ($exist_safe) {
            $data[$j++] = 'safe';
        }
        if ($exist_token) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'token';
        }
        if ($exist_code) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'code';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'date_added';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual customers data
        $i += 1;
        $j = 0;
        $customers = $this->getCustomers($exist_custom_field, $exist_salt, $exist_safe, $exist_token, $exist_code, $offset, $rows, $min_id, $max_id);
        $len = count($customers);
        $min_id = ($len > 0) ? $customers[0]['customer_id'] : 0;
        $max_id = ($len > 0) ? $customers[$len - 1]['customer_id'] : 0;
        foreach ($customers as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(26);
            $data = array();
            $data[$j++] = $row['customer_id'];
            $data[$j++] = $row['customer_group'];
            $data[$j++] = $row['store_id'];
            $data[$j++] = $row['firstname'];
            $data[$j++] = $row['lastname'];
            $data[$j++] = $row['email'];
            $data[$j++] = $row['telephone'];
            $data[$j++] = $row['fax'];
            $data[$j++] = $row['password'];
            if ($exist_salt) {
                $data[$j++] = $row['salt'];
            }
            $data[$j++] = $row['cart'];
            $data[$j++] = $row['wishlist'];
            $data[$j++] = ($row['newsletter'] == 0) ? 'no' : 'yes';
            if ($exist_custom_field) {
                $data[$j++] = $row['custom_field'];
            }
            $data[$j++] = $row['ip'];
            $data[$j++] = ($row['status'] == 0) ? 'false' : 'true';
            if ($exist_approved) {
                $data[$j++] = ($row['approved'] == 0) ? 'false' : 'true';
            }
            if ($exist_safe) {
                $data[$j++] = ($row['safe'] == 0) ? 'false' : 'true';
            }
            if ($exist_token) {
                $data[$j++] = $row['token'];
            }
            if ($exist_code) {
                $data[$j++] = $row['code'];
            }
            $data[$j++] = $row['date_added'];
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function populateAddressesWorksheet(&$worksheet, &$box_format, &$text_format, $min_id = null, $max_id = null) {
        // Some Opencart 1.5.x versions also have company_id
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "address` LIKE 'company_id'";
        $query = $this->db->query($sql);
        $exist_company_id = ($query->num_rows > 0) ? true : false;

        // Some Opencart 1.5.x versions also have tax_id
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "address` LIKE 'tax_id'";
        $query = $this->db->query($sql);
        $exist_tax_id = ($query->num_rows > 0) ? true : false;

        // Opencart 2.x versions have custom_field
        $sql = "SHOW COLUMNS FROM `" . DB_PREFIX . "address` LIKE 'custom_field'";
        $query = $this->db->query($sql);
        $exist_custom_field = ($query->num_rows > 0) ? true : false;

        // Set the column widths
        $j = 0;
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('customer_id') + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('firstname'), 20) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('lastname'), 20) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('company'), 30) + 1);
        if ($exist_company_id) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('company_id'), 10) + 1);
        }
        if ($exist_tax_id) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('tax_id'), 15) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('address_1'), 30) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('address_2'), 30) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('city'), 30) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('postcode'), 10) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('zone'), 20) + 1);
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('country'), 20) + 1);
        if ($exist_custom_field) {
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('custom_field'), 20) + 1);
        }
        $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('default'), 5) + 1);

        // The heading row and column styles
        $styles = array();
        $data = array();
        $i = 1;
        $j = 0;
        $data[$j++] = 'customer_id';
        $styles[$j] = &$text_format;
        $data[$j++] = 'firstname';
        $styles[$j] = &$text_format;
        $data[$j++] = 'lastname';
        $styles[$j] = &$text_format;
        $data[$j++] = 'company';
        if ($exist_company_id) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'company_id';
        }
        if ($exist_tax_id) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'tax_id';
        }
        $styles[$j] = &$text_format;
        $data[$j++] = 'address_1';
        $styles[$j] = &$text_format;
        $data[$j++] = 'address_2';
        $styles[$j] = &$text_format;
        $data[$j++] = 'city';
        $styles[$j] = &$text_format;
        $data[$j++] = 'postcode';
        $styles[$j] = &$text_format;
        $data[$j++] = 'zone';
        $styles[$j] = &$text_format;
        $data[$j++] = 'country';
        if ($exist_custom_field) {
            $styles[$j] = &$text_format;
            $data[$j++] = 'custom_field';
        }
        $data[$j++] = 'default';
        $worksheet->getRowDimension($i)->setRowHeight(30);
        $this->setCellRow($worksheet, $i, $data, $box_format);

        // The actual addresses data
        $i += 1;
        $j = 0;
        $addresses = $this->getAddresses($min_id, $max_id);
        foreach ($addresses as $row) {
            $worksheet->getRowDimension($i)->setRowHeight(13);
            $data = array();
            $data[$j++] = $row['customer_id'];
            $data[$j++] = $row['firstname'];
            $data[$j++] = $row['lastname'];
            $data[$j++] = $row['company'];
            if ($exist_company_id) {
                $data[$j++] = $row['company_id'];
            }
            if ($exist_tax_id) {
                $data[$j++] = $row['tax_id'];
            }
            $data[$j++] = $row['address_1'];
            $data[$j++] = $row['address_2'];
            $data[$j++] = $row['city'];
            $data[$j++] = $row['postcode'];
            $data[$j++] = html_entity_decode($row['zone'], ENT_QUOTES, 'UTF-8');
            $data[$j++] = $row['country'];
            if ($exist_custom_field) {
                $data[$j++] = $row['custom_field'];
            }
            $data[$j++] = ($row['default'] == 0) ? 'no' : 'yes';
            $this->setCellRow($worksheet, $i, $data, $this->null_array, $styles);
            $i += 1;
            $j = 0;
        }
    }

    protected function clearSpreadsheetCache() {
        $files = glob(DIR_CACHE . 'Spreadsheet_Excel_Writer' . '*');

        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                    clearstatcache();
                }
            }
        }
    }



    public function getImportReports() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "temp_dsr`");
        return $query->rows;
    }

    public function addImportReport($data) {

        foreach ($data['dsr'] as $job_nos) {

            $query = $this->db->query("INSERT INTO " . DB_PREFIX . "dsr SET job_no = '" . $this->db->escape($job_nos['job_no']) . "',igm_no ='" . $this->db->escape($job_nos['igm_no']) . "',igm_date ='" . $this->db->escape($job_nos['igm_date']) . "'");
        }


        $this->db->query("DELETE FROM " . DB_PREFIX . "temp_dsr");
        return $query;
    }

    public function deleteImportReport() {
        $this->db->query("DELETE FROM " . DB_PREFIX . "temp_dsr");
    }

}
