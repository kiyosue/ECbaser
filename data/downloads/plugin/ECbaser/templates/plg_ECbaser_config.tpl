<!--{*
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
 *}-->
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<script type="text/javascript">
</script>

<h2><!--{$tpl_subtitle}--></h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">
<p>EC-CUBE × baserCMSを表示する際の詳細な設定が行えます。<br/>
    <br/>
</p>

<table border="0" cellspacing="1" cellpadding="8" summary=" ">
    <tr>
        <td colspan="2" width="90" bgcolor="#f3f3f3">▼ECbaser詳細設定</td>
    </tr>
    <tr>
        <td colspan="2" bgcolor="#f3f3f3">利用確認<br />
        　全て可の場合にプラグインは動作します。不可がある場合は、共通設定を変更してください。</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">baserCMSが利用するdatabase</td>
        <td><!--{$tpl_database}-->
        </td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">baserCMSの呼び込み利用</td>
        <td><!--{$tpl_basercms}-->
        </td>
    </tr>
    <tr>
    	<td colspan="2" bgcolor="#f3f3f3">共通設定</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">baserCMSインストールディレクトリ</td>
        <td>
            Document Rootを基準としたbaserCMSのインストール場所<br />
            Document Root直下にbasercmsというディレクトリでインストールした場合 /basercms<br />
            <!--{assign var=key value="baser_install_dir"}-->
            <input type="text" class="box60" name="<!--{$key}-->" value="<!--{$arrForm[$key]}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
    </tr>
    <tr>
    	<td colspan="2" bgcolor="#f3f3f3">個別設定</td>
    </tr>
    <tr>
        <td bgcolor="#f3f3f3">表示URL(default)</td>
        <td> <a href="<!--{$tpl_index}-->" target="_blank"><!--{$tpl_index}--></a>
        </td>
    </tr>
</table>

<div class="btn-area">
    <ul>
        <li>
            <a class="btn-action" href="javascript:;" onclick="document.form1.submit();return false;"><span class="btn-next">この内容で登録する</span></a>
        </li>
    </ul>
</div>

</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
