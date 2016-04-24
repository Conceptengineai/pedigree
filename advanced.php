<?php
// -------------------------------------------------------------------------

require_once dirname(dirname(__DIR__)) . '/mainfile.php';

/*
if (file_exists(XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/language/" . $xoopsConfig['language'] . "/main.php")) {
    require_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/language/" . $xoopsConfig['language'] . "/main.php";
} else {
    include_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/language/english/main.php";
}
*/

xoops_loadLanguage('main', basename(dirname(__DIR__)));

//needed for generation of pie charts
ob_start();
//include(XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/include/class_eq_pie.php");
require_once(XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/include/class_field.php");

$xoopsOption['template_main'] = "pedigree_advanced.tpl";

include XOOPS_ROOT_PATH . '/header.php';
// Include any common code for this module.
require_once(XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/include/functions.php");
$xoTheme->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript(PEDIGREE_URL . '/assets/js/jquery.canvasjs.min.js');

global $xoopsTpl, $xoopsDB;
$totpl = array();
$books = array();
//get module configuration
$module_handler = xoops_getHandler('module');
$module         = $module_handler->getByDirname("pedigree");
$config_handler = xoops_getHandler('config');
$moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

//colour variables
$colors  = explode(";", $moduleConfig['colourscheme']);
$actlink = $colors[0];
$even    = $colors[1];
$odd     = $colors[2];
$text    = $colors[3];
$hovlink = $colors[4];
$head    = $colors[5];
$body    = $colors[6];
$title   = $colors[7];

//query to count male dogs
$result = $xoopsDB->query("select count(id) from " . $xoopsDB->prefix("pedigree_tree") . " where roft='0'");
list($countmales) = $xoopsDB->fetchRow($result);

//query to count female dogs
$result = $xoopsDB->query("select count(id) from " . $xoopsDB->prefix("pedigree_tree") . " where roft='1'");
list($countfemales) = $xoopsDB->fetchRow($result);

$totaldogs = $countmales + $countfemales;
$perc_mdogs = round(100/$totaldogs*$countmales, 1);
$perc_fdogs = round(100/$totaldogs*$countfemales, 1);

//strtr(_MA_PEDIGREE_FLD_MALE, array( '[male]' => $moduleConfig['male'] ))
//strtr(_MA_PEDIGREE_ADV_ORPMUM, array( '[mother]' => $moduleConfig['mother'], '[animalTypes]' => $moduleConfig['animalTypes'] ))
if ($moduleConfig['proversion'] == '1') {
    $xoopsTpl->assign("pro", true);
}
$xoopsTpl->assign("title", strtr(_MA_PEDIGREE_ADV_VTMF, array('[male]' => $moduleConfig['male'], '[female]' => $moduleConfig['female'])));
$xoopsTpl->assign("topmales", "<a href=\"topstud.php?com=father\">" . strtr(_MA_PEDIGREE_ADV_STUD, array('[male]' => $moduleConfig['male'], '[children]' => $moduleConfig['children'])) . "</a>");
$xoopsTpl->assign("topfemales", "<a href=\"topstud.php?com=mother\">" . strtr(_MA_PEDIGREE_ADV_BITC, array('[female]' => $moduleConfig['female'], '[children]' => $moduleConfig['children'])) . "</a>");
$xoopsTpl->assign("tnmftitle", strtr(_MA_PEDIGREE_ADV_TNMFTIT, array('[male]' => $moduleConfig['male'], '[female]' => $moduleConfig['female'])));
$xoopsTpl->assign(
    "countmales",
    "<img src=\"assets/images/male.gif\"> " . strtr(_MA_PEDIGREE_ADV_TCMA, array('[male]' => $moduleConfig['male'], '[female]' => $moduleConfig['female']))
    . " : <a href=\"result.php?f=roft&w=zero&o=NAAM\">" . $countmales . "</a>"
);
$xoopsTpl->assign(
    "countfemales",
    "<img src=\"assets/images/female.gif\"> " . strtr(_MA_PEDIGREE_ADV_TCFE, array('[male]' => $moduleConfig['male'], '[female]' => $moduleConfig['female']))
    . " : <a href=\"result.php?f=roft&w=1&o=NAAM\">" . $countfemales
) . "</a>";
$xoopsTpl->assign("pienumber", "<img src=\"assets/images/numbers.png\">");
$xoopsTpl->assign("totpl", $totpl);
$xoopsTpl->assign("books", $books);

$xoopsTpl->assign("orptitle", _MA_PEDIGREE_ADV_ORPTIT);
$xoopsTpl->assign("orpall", "<a href=\"result.php?f=father=0 and mother&w=zero&o=NAAM\">" . strtr(_MA_PEDIGREE_ADV_ORPALL, array('[animalTypes]' => $moduleConfig['animalTypes'])) . "</a>");
$xoopsTpl->assign(
    "orpdad",
    "<a href=\"result.php?f=mother!=0 and father&w=zero&o=NAAM\">" . strtr(_MA_PEDIGREE_ADV_ORPDAD, array('[father]' => $moduleConfig['father'], '[animalTypes]' => $moduleConfig['animalTypes']))
    . "</a>"
);
$xoopsTpl->assign(
    "orpmum",
    "<a href=\"result.php?f=father!=0 and mother&w=zero&o=NAAM\">" . strtr(_MA_PEDIGREE_ADV_ORPMUM, array('[mother]' => $moduleConfig['mother'], '[animalTypes]' => $moduleConfig['animalTypes']))
    . "</a>"
);
$xoopsTpl->assign("position", _MA_PEDIGREE_M50_POS);
$xoopsTpl->assign("numdogs", _MA_PEDIGREE_M50_NUMD);
$xoopsTpl->assign("maledogs", $perc_mdogs);
$xoopsTpl->assign("femaledogs", $perc_fdogs);
//comments and footer
include XOOPS_ROOT_PATH . "/footer.php";
