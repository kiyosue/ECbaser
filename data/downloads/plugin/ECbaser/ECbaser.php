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


/**
 * プラグインのメインクラス
 *
 * @package ECbaser
 * @author ITManagement Co., Ltd.
 * @version $Id: $
 */
class ECbaser extends SC_Plugin_Base {


    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }
    

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        //default
        $sqlval_post = array();
        $sqlval_post['device_type_id'] = DEVICE_TYPE_PC;
        $sqlval_post['page_id'] = "";
        $sqlval_post['page_name'] = "";
        $sqlval_post['url'] = "";
        $sqlval_post['filename'] = "";
        $sqlval_post['header_chk'] = "1";
        $sqlval_post['footer_chk'] = "1";
        $sqlval_post['edit_flg'] = "2";
        $sqlval_post['create_date'] = "CURRENT_TIMESTAMP";
        $sqlval_post['update_date'] = "CURRENT_TIMESTAMP";

        // dtb_pagelayoutにページを追加する.
        $sqlval_post['page_id'] = $objQuery->max('page_id', "dtb_pagelayout", "device_type_id = " . DEVICE_TYPE_PC) + 1;
        $sqlval_post['page_name'] = "ECbaser";
        $sqlval_post['url'] = "ecbaser/index.php";
        $sqlval_post['filename'] = "ecbaser/plg_ECbaser_index";
        // INSERTの実行
        $objQuery->insert("dtb_pagelayout", $sqlval_post);


        ECbaser::insertFreeField();

        $objQuery->commit();

        // 必要なファイルをコピー
        copy(PLUGIN_UPLOAD_REALDIR . "ECbaser/config.php", PLUGIN_HTML_REALDIR . "ECbaser/config.php");
        copy(PLUGIN_UPLOAD_REALDIR . "ECbaser/logo.png", PLUGIN_HTML_REALDIR . "ECbaser/logo.png");
        mkdir(PLUGIN_HTML_REALDIR . "ECbaser/media");
        SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . "ECbaser/media/", PLUGIN_HTML_REALDIR . "ECbaser/media/");

        if(! mkdir(TEMPLATE_REALDIR . "ecbaser")) trigger_error("don't make directory . path = " . TEMPLATE_REALDIR . "ecbaser", E_USER_ERROR);
        if(copy(PLUGIN_UPLOAD_REALDIR . "ECbaser/templates/plg_ECbaser_index.tpl", TEMPLATE_REALDIR . "ecbaser/plg_ECbaser_index.tpl") === false) trigger_error("plg_ECbaser_page.tpl copy false.", E_USER_ERROR);

        // ディレクトリを作成
        mkdir(HTML_REALDIR . "ecbaser");
        //失敗した場合は、管理画面上で警告をだすので、無視する。
        copy(PLUGIN_UPLOAD_REALDIR . "ECbaser/html/ecbaser/index.php", HTML_REALDIR . "ecbaser/index.php");
    }


    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function uninstall($dtb_plugin){
        $objQuery = SC_Query_Ex::getSingletonInstance();

        //index用ページの削除
        $arrPageIdPost = $objQuery->getCol('page_id', "dtb_pagelayout", "device_type_id = ? AND filename = ?", array(DEVICE_TYPE_PC , "ecbaser/plg_ECbaser_index"));
        $page_id_post = (int) $arrPageIdPost[0];
        $where = "page_id = ?";
        $objQuery->delete("dtb_pagelayout", $where, array($page_id_post));


        // メディアディレクトリ削除.
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "ECbaser/media");
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . "ECbaser");

        //管理画面で設定している、利用フォルダを削除するのが筋だが、パスのミスで別のフォルダが削除される危険性があるので
        //初期設定以外のフォルダは各自で消して頂く。
        SC_Helper_FileManager_Ex::deleteFile(HTML_REALDIR . "ecbaser");

        //tempateの削除
        SC_Helper_FileManager_Ex::deleteFile(TEMPLATE_REALDIR . "ecbaser");
    }


    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
        // nop
    }


    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
        // nop
    }


    /**
     * preProcessはスーパーフックポイントを使って実行されます。各Pageクラスのinit処理で実行されます。
     * この関数をプラグイン内に定義するだけで実行されます。
     * param；呼び出し元のLC_Pageオブジェクト
     *
     */
    public function preProcess(LC_Page_EX $objPage){
        //開発用の設定
//        define("SMARTY_FORCE_COMPILE_MODD", true);
    
    }

    /**
     * preProcessはスーパーフックポイントを使って実行されます。各PageクラスのsendResponse処理で実行されます。
     * この関数をプラグイン内に定義するだけで実行されます。
     * param；LC_Pageオブジェクト
     *
     */
    public function process(LC_Page_EX $objPage){
    
    }


    /**
     * 処理の介入箇所とコールバック関数を設定
     * registerはプラグインインスタンス生成時に実行されます
     * 
     * @param SC_Helper_Plugin $objHelperPlugin 
     */
    function register(SC_Helper_Plugin $objHelperPlugin, $priority) {
    }


    /**
     *  プラグイン独自の設定データを追加
     *
     */
    function insertFreeField() {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $sqlval['free_field1'] = "/basercms";
        $sqlval['free_field2'] = "";
        $sqlval['free_field3'] = "";
        $sqlval['free_field4'] = "";
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        // UPDATEの実行
        $objQuery->update('dtb_plugin', $sqlval, $where, array('ECbaser'));
    }

}
