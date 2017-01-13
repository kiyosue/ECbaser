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
 * プラグイン の情報クラス.
 *
 * @package ECbaser
 * @author ITManagement Co., Ltd.
 * @version $Id: $
 */
class plugin_info {
    /** プラグインコード(必須)：プラグインを識別する為キーで、他のプラグインと重複しない一意な値である必要がありま. */
    static $PLUGIN_CODE = "ECbaser";
    /** プラグイン名(必須)：EC-CUBE上で表示されるプラグイン名. */
    static $PLUGIN_NAME = "EC-CUBEとbaserCMSの連携プラグイン";
    /** クラス名(必須)：プラグインのクラス（拡張子は含まない） */
    static $CLASS_NAME        = "ECbaser";
    /** プラグインバージョン(必須)：プラグインのバージョン. */
    static $PLUGIN_VERSION    = "0.1";
    /** 対応バージョン(必須)：対応するEC-CUBEバージョン. */
    static $COMPLIANT_VERSION = " 2.12.2";
    /** 作者(必須)：プラグイン作者. */
    static $AUTHOR = "株式会社アイティマネジメント";
    /** 説明(必須)：プラグインの説明. */
    static $DESCRIPTION = "EC-CUBEとbaserCMSの連携プラグインです。baserCMSの固定ページやフォームをEC-CUBEのテンプレートで表示します。";
    /** プラグインURL：プラグイン毎に設定出来るURL（説明ページなど） */
    static $PLUGIN_SITE_URL = "http://www.itm.ne.jp/service/ecbaser";
    /** 作者URL */
    static $AUTHOR_SITE_URL = "http://www.itm.ne.jp/";

    //ローカルフックポイントの指定
    static $HOOK_POINTS = array(
        /* 
        array('LC_Page_Admin_System_Input_action_after',
                'baserAdminUserInsert'
            ),
        array('LC_Page_Admin_System_Delete_action_after',
             'baserAdminUserDelete'
            ),
        array('outputfilter_transform',
             'outputfilter_transform'
            )
        */
        );

    /** ライセンス */
    static $LICENSE = "MIT";


}