<?php
/**
 *
 * PHP versions 5
 *
 * ECbaser : plugin for EC-CUBE
 * Copyright (C) 2012 ITManagement Co., Ltd. All Rights Reserved.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (C) 2012 ITManagement Co., Ltd. All Rights Reserved. (http://www.itm.ne.jp)
 * @link          http://www.itm.ne.jp
 * @package       ECbaser
 * @since         ECbaser v 0.1
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * EC-CUBE × baserCMS の設定クラス
 *
 * @package ECbaser
 * @author ITManagement Co., Ltd.
 * @version $Id: $
 */
class LC_Page_Plugin_ECbaser_Config extends LC_Page_Admin_Ex {

    var $arrForm = array();
    var $default_html_dir = 'ecbaser';

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = PLUGIN_UPLOAD_REALDIR ."ECbaser/templates/plg_ECbaser_config.tpl";
        $this->tpl_subtitle = "EC-CUBE × baserCMS";
    }

    /**
     * プロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        
        $css_file_path = PLUGIN_HTML_REALDIR . "ECbaser/media/plg_ECbaser_common.css";
        $arrForm = array();
        

        switch ($this->getMode()) {
        case 'edit':
            $arrForm = $objFormParam->getHashArray();
            $this->arrErr = $objFormParam->checkError();
            // エラーなしの場合にはデータを更新
            if (count($this->arrErr) == 0) {
                // データ更新
                $this->arrErr = $this->updateData($arrForm, $css_file_path);
                $this->tpl_onload = "alert('登録が完了しました。');";
                $this->tpl_onload .= 'window.close();';
            }
            break;
        default:
            //baserCMSの情報を取得
            // install済みか？
            //プリフィクス

            // プラグイン情報を取得.
            $plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("ECbaser");
            // baserCMSインストールディレクトリ
            $arrForm['baser_install_dir'] = $plugin['free_field1'];

            //DB情報(baserの)(ec-cubeを共有できるよん)
            $cake_db_conf = $_SERVER['DOCUMENT_ROOT'] . $arrForm['baser_install_dir'] . '/app/Config/database.php';
            if( file_exists($cake_db_conf) ){
                require_once($cake_db_conf);
                $database_config = new DATABASE_CONFIG();
                $this->tpl_database = "(可) " . $this->tpl_database = $database_config->baser['database'];
            } else {
                $this->tpl_database = "(不可) 設定ファイルがみつかりません path = {$cake_db_conf}";
            }

            //webrootのファイルがあるか
            $webroot_path = $_SERVER['DOCUMENT_ROOT'] . $arrForm['baser_install_dir'] . '/index.php';
            if( file_exists($webroot_path) ){
                $this->tpl_basercms = "(可) webrootが $webroot_path にあります。";
            } else {
                $this->tpl_basercms = "(不可) ファイルがみつかりません path = {$webroot_path}";
            }

            $this->tpl_index = HTTP_URL . "{$this->default_html_dir}/index.php?name=index";

            /*
            // CSSファイル.
            $arrForm['css_data'] = $this->getTplMainpage($css_file_path);
            */
            break;
        }
        $this->arrForm = $arrForm;
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
    
    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // ポスト
        $objFormParam->addParam('baserCMSインストールディレクトリ', 'baser_install_dir', array('EXIST_CHECK','NUM_CHECK'));
//        $objFormParam->addParam('スマートURLの利用', 'baser_smarturl', array('EXIST_CHECK','NUM_CHECK'));
//        $objFormParam->addParam('baserCMSのメールフォームで表示するディレクトリ', 'contact_dir', LLTEXT_LEN, '', array('EXIST_CHECK','MAX_LENGTH_CHECK'));
//        $objFormParam->addParam('baserCMSの固定フォームで表示するディレクトリ', 'page_dir', LLTEXT_LEN, '', array('EXIST_CHECK','MAX_LENGTH_CHECK'));
    }
    
    /**
     * ファイルパラメーター初期化.
     *
     * @param SC_UploadFile_Ex $objUpFile SC_UploadFileのインスタンス.
     * @param string $key 登録するキー.
     * @return void
     */
    function initUploadFile(&$objUpFile, $key) {
        $objUpFile->addFile('ECbaser', $key, explode(',', "jpg"), FILE_SIZE, true, 0, 0, false);
    }


    /**
     * ページデータを取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param integer $page_id ページID
     * @param SC_Helper_PageLayout $objLayout SC_Helper_PageLayout インスタンス
     * @return array ページデータの配列
     */
    function getTplMainpage($file_path) {

        if (file_exists($file_path)) {
            $arrfileData = file_get_contents($file_path);
        }
        return $arrfileData;
    }
    
    /**
     * 管理画面から、プラグインの設定をUPDATE
     * @param type $arrData
     * @return type 
     */
    function updateData($arrData, $css_file_path) {
        $arrErr = array();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        // UPDATEする値を作成する。
        $sqlval = array();
        $sqlval['free_field1'] = $arrData['baser_install_dir'];
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = 'plugin_code = ?';
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where, array('ECbaser'));

        //blogのテーブルを取得してなければ、取得する


        /*
        //ファイル更新
        if (!SC_Helper_FileManager_Ex::sfWriteFile($css_file_path, $arrData['view_dir'])) {
            $arrErr['plugin_code'] = '※ CSSファイルの書き込みに失敗しました<br />';
            $objQuery->rollback();
            return $arrErr;
        }
        */

        /* 他のテーブルをUPDATEする必要がある場合はここでUPDATE
        $sqlval_ECbaser_hogehoge = array();
        $sqlval_ECbaser_hogehoge['column1'] = $arrData['column1'];
        $sqlval_ECbaser_hogehoge['column2'] = $arrData['column2'];

        $where_ECbaser = 'id = ?';
        // UPDATEの実行
        $objQuery->update('plg_ECbaser_hogehoge', $sqlval_ECbaser_hogehoge, array(1));
        */

        $objQuery->commit();
        return $arrErr;
    }

}