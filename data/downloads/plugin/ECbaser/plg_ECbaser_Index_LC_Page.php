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
        if(! isset($this->tpl_page_class_name)){
            $this->tpl_page_class_name = '';
        }
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
        $this->baser_path = $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir . '/index.php';
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
        $result = str_replace("/index.php/index.php", "/index.php", $result);//2重になるので、変更
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
//        2.13.0にて実装がなくなったのでコメントアウト
//        parent::destroy();
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

        $fileName = $_SERVER['SCRIPT_FILENAME'];
        ini_set('date.timezone', 'Asia/Tokyo');
        @putenv("TZ=JST-9");
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        $fileName = str_replace('/', DS, $fileName);

        if (!defined('ROOT')) {
            define('ROOT', $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir );
        }
        if (!defined('APP_DIR')) {
            define('APP_DIR', 'app');
        }
        if (!defined('WEBROOT_DIR')) {
            define('WEBROOT_DIR', 'webroot');
        }
        if (!defined('WWW_ROOT')) {
            define('WWW_ROOT', ROOT . '/');
        }

        if (!defined('CAKE_CORE_INCLUDE_PATH')) {
            if (function_exists('ini_set')) {
                ini_set('include_path', ROOT . DS . 'lib' . PATH_SEPARATOR . ini_get('include_path'));
            }
            if (!include 'Cake' . DS . 'bootstrap.php') {
                $failed = true;
            }
        } else {
            if (!include CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'bootstrap.php') {
                $failed = true;
            }
        }
        if (!empty($failed)) {
            trigger_error("CakePHP core could not be found. Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php. It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
        }

        App::uses('Dispatcher', 'Routing');

        Configure::write('App.baseUrl', $_SERVER['SCRIPT_NAME']);//smart url を強制OFF
        $result = "";
        $contents = false ;

        if( $search ){
            //blogかcontactか、pageかで処理の振り分け
            //公開blogか？
            $cake_db_conf = $_SERVER['DOCUMENT_ROOT'] . $this->baser_dir . '/app/Config/database.php';
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

            if( isset($rst[0][0]['status']) ){
                if($rst[0][0]['status'] == true ){
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
                $query = "SELECT name,status FROM {$database_config->plugin['prefix']}mail_contents as t1 WHERE name = '{$this->name}' ";
                $rst = $model->query($query);
                if( isset($rst[0][0]['status']) ){
                    if($rst[0][0]['status'] == 1 ){
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
            //下記の２つをbaserCMS側に設定する必要がある。が制御できないので、 basercms/app/Controller AppController.php に追記した
//                $this->Security->validatePost = false;
//                $this->Security->csrfCheck = false;
            // ecbaserというflagを立てる これをkeyにして、AppControllerで制御を行う。
            $event = $dispatcher->getEventManager();
            $event->attach(function(){
                Configure::write('eccubeEcbaserFlag', true);
            },'ECCUBE.ECbaser.writeConfig');
            $event->dispatch('ECCUBE.ECbaser.writeConfig');

            $result = $dispatcher->dispatch(new CakeRequest($dispatch), new CakeResponse(), array('return'=>false,'bare'=>$bare));
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

