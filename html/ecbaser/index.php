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
require_once realpath(dirname(__FILE__)) . '/../require.php';
require_once PLUGIN_UPLOAD_REALDIR . 'ECbaser/plg_ECbaser_Index_LC_Page.php';

// }}}
// {{{ generate page
$objPage = new LC_Page_ECbaser_Index();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>