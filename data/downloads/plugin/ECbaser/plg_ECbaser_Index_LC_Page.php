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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
/**
 * baserCMSのお問い合せ機能を呼び出すクラス
 *
 * @package ECbaser
 * @author ITManagement Co., Ltd.
 * @version $Id: $
 */
class LC_Page_ECbaser_Index extends LC_Page_Ex {

    private $plugin = null;
    private $baser_dir = null;
    private $baser_path = null;
    private $name = null;

    /**
     * 初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = '';
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

        $this->plugin = SC_Plugin_Util_Ex::getPluginByPluginCode("ECbaser");
        $this->baser_dir = $this->plugin['free_field1'];
        $this->baser_path = $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir . '/app/webroot/index.php';
        $this->tpl_mainpage =  TEMPLATE_REALDIR . strtolower($this->plugin['plugin_code']) . "/plg_ECbaser_index.tpl";
        $this->tpl_subtitle = 'baserCMS';
        $this->ecbaser_index = "";

        if( isset($_SERVER['PATH_INFO'])){
            $url = substr($_SERVER['PATH_INFO'],1);
        }else{
            if( isset($_GET['name']) ){
                $url = $_GET['name'] ;
            }else{
                $url = substr(basename($_SERVER['SCRIPT_FILENAME']), 0, strrpos(basename($_SERVER['SCRIPT_FILENAME']),'.'));
            }
        }
        list($this->name,) = explode("/",$url);

        //存在しなければ、利用不可
        if(! file_exists($this->baser_path)){
            return true ;
        }

        //プラグイン自体の有効か確認
        if($this->plugin['enable'] != 1){
            return true ;
        }

        //blog comment の場合そのままレスポンスが必要。
        if( strpos($url, 'blog/blog_comments/add', 0) === 0){
            $result = $this->getBaserContent($url, false, false);
            echo $result;
            exit;
        }

        //captcha の場合そのままレスポンスが必要。
        if( basename($url) == 'captcha' ){
            $result = $this->getBaserContent($url, false, false);
            echo $result;
            exit;
        }

        //feed の場合そのままレスポンスが必要。
        if( $this->name == 'feed' ){
            $result = $this->getBaserContent($url, false, false);
            echo $result;
            exit;
        }


        //スタイルシート の場合
        if( substr($url, strrpos($url, '.') + 1) == 'css'){
            $result = $this->getBaserContent($url, false, false);
            echo $result;
            exit;
        }

        // JSの場合
        if( substr($url, strrpos($url, '.') + 1) == 'js'){
            $result = $this->getBaserContent($url, false, false);
            echo $result;
            exit;
        }

        $result = $this->getBaserContent($url, true, true);

        $this->ecbaser_index = $result;
    }


    /**
     * blogの場合の変換処理
     */
    function getBlogReplace($result)
    {
        // ecbaser/index.php/ecbaser/index.phpとなる場合がある
        $result = str_replace("ecbaser/index.php/ecbaser/index.php", "ecbaser/index.php", $result);//2重になるので、変更
        $result = str_replace('/ecbaser/app/webroot', $this->baser_dir . "/app/webroot", $result);
        $result = str_replace('</fieldset></form>', "</fieldset><input type=\"hidden\" name=\"" .  TRANSACTION_ID_NAME  . "\" value=\"" . $this->transactionid . "\"></form>", $result);

        if( $result == 'Object' ){
            $result = "";
        }
        return $result ;
    }


    /**
     * 固定ページの場合の変換処理
     */
    function getPageReplace($result)
    {
        if( $result == 'Object' ){
            $result = "";
        }

        return $result ;
    }


    /**
     * お問い合せの場合の変換処理
     */
    function getContactReplace($result)
    {
        $result = str_replace('</form>', "<input type=\"hidden\" name=\"" .  TRANSACTION_ID_NAME  . "\" value=\"" . $this->transactionid . "\"></form>", $result);

        if( $result == 'Object' ){
            $result = "";
        }

        return $result ;
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
     * レスポンス
     * search true = コンテンツ捜査 false = dispatchのみ
     * params $bare true = レイアウトを含まない false = レイアウトを含む
     * 
     * return $string 結果のHTML
     */
    function getBaserContent($dispatch, $search = false, $bare = false)
    {
        define('ROOT', $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir );
        define('CAKE_CORE_INCLUDE_PATH', $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir );
        define('WWW_ROOT', ROOT . '/app/webroot/');
        define('WEBROOT_DIR', 'webroot');

        $_GET['url'] = 'favicon.ico';
        require_once $this->baser_path;
        Configure::write('App.baseUrl', $_SERVER['SCRIPT_NAME']);//smart url を強制OFF
        $result = "";
        $contents = false ;

        if( $search ){
            //blogかcontactか、pageかで処理の振り分け
            //公開blogか？
            $cake_db_conf = $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir . '/app/config/database.php';
            if( file_exists($cake_db_conf) ){
                require_once($cake_db_conf);
                $database_config = new DATABASE_CONFIG();
                $table_name = $database_config->plugin['prefix'] . 'blog_contents';
            } else {
                return $result ;//db情報が見つからない
            }

            $model = ClassRegistry::init('Content');
            $query = "SELECT status FROM {$table_name} as t1 WHERE name = '{$this->name}' ";
            $rst = $model->query($query);

            if( isset($rst[0]['t1']['status']) ){
                if($rst[0]['t1']['status'] == 1 ){
                    $contents = 'blog';
                }
            }else{
                // お問い合せフォームかどうか
                /*
                $params = array(
                    'conditions' => array(
                        "Content.model" => "MailContent",
                        "Content.url" => "/{$this->name}/index"
                    ),
                    'fields' => array('Content.url','Content.status')
                );
                $form = $model->find('first', $params);
                */
                $query = "SELECT name,status FROM bc_pg_mail_contents as t1 WHERE name = '{$this->name}' ";
                $rst = $model->query($query);
                if( isset($rst[0]['t1']['status']) ){
                    if($rst[0]['t1']['status'] == 1 ){
                        $contents = 'contact';
                        if( $this->name == $dispatch ){
                            $dispatch .= "/index";
                        }
                    }
                } else {
                    $contents = 'page';
                }
            }
        }else{
            $contents = true;
        }

        $result = "";
        if($contents){
            $dispatcher = new Dispatcher();
            $result = $dispatcher->dispatch($dispatch, array('return'=>false,'bare'=>$bare));

            switch($contents){
                case 'blog' :
                    $result = $this->getBlogReplace($result);
                    break ;
                case 'page' :
                    $result = $this->getPageReplace($result);
                    break ;
                case 'contact' :
                    $result = $this->getContactReplace($result);
                    break ;
                default :
            }
        }

        return $result;
    }
}

