<?php /* Smarty version Smarty-3.1.16, created on 2017-05-31 10:58:38
         compiled from "/home/ematos/public_html/maestro3/apps/guia/public/themes/guia/index.html" */ ?>
<?php /*%%SmartyHeaderCode:2143342972592ecc0ecb5a51-81076488%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7d8307a8d778dec78effdca9cf4f81d7f364242d' => 
    array (
      0 => '/home/ematos/public_html/maestro3/apps/guia/public/themes/guia/index.html',
      1 => 1461196682,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2143342972592ecc0ecb5a51-81076488',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'manager' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.16',
  'unifunc' => 'content_592ecc0ece5929_63478657',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_592ecc0ece5929_63478657')) {function content_592ecc0ece5929_63478657($_smarty_tpl) {?><?php echo $_smarty_tpl->tpl_vars['page']->value->fetch('header');?>

<!-- basic preloader: -->
<div id="loader"><div id="loaderInner" style="direction:ltr;">Carregando.... </div></div>
<div id="appLayout" data-dojo-type="dijit/layout/BorderContainer" data-dojo-props="design: 'headline'">
<div id="topPane" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region: 'top'">
    <div id="elementLogo">
    </div>
</div>
    <div id="leftPane" data-dojo-type="manager/ElementPane" data-dojo-props="region: 'left', splitter: true, href:'<?php echo $_smarty_tpl->tpl_vars['manager']->value->getURL('mainmenu');?>
'">
    </div>
    <div id="centerPane" data-dojo-type="manager/ElementPane" data-dojo-props="region: 'center', tabPosition: 'bottom'">
        <?php echo $_smarty_tpl->tpl_vars['page']->value->generate('content');?>

    </div>
</div>

<?php echo $_smarty_tpl->tpl_vars['page']->value->fetch('footer');?>

<?php }} ?>
