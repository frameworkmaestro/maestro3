<?php

/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou 
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada 
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, 
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL 
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a 
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

$baseDir = dirname(__FILE__);

return array(
/* . */ 
    'mui' => $baseDir . '/mui.php',
    'mxmlcontrols' => $baseDir . '/mxmlcontrols.php',

/* controls/action/button */ 
    'mbutton' => $baseDir . '/controls/action/button/mbutton.php',
    'mbuttondropdown' => $baseDir . '/controls/action/button/mbuttondropdown.php',
    'mbuttonfind' => $baseDir . '/controls/action/button/mbuttonfind.php',
    'mbuttonicon' => $baseDir . '/controls/action/button/mbuttonicon.php',
    'minputbutton' => $baseDir . '/controls/action/button/minputbutton.php',
    'mtoolbutton' => $baseDir . '/controls/action/button/mtoolbutton.php',

/* controls/action/dnd */ 
    'mdragdrop' => $baseDir . '/controls/action/dnd/mdragdrop.php',

/* controls/action/link */ 
    'mclose' => $baseDir . '/controls/action/link/mclose.php',
    'mlink' => $baseDir . '/controls/action/link/mlink.php',
    'mlinkbutton' => $baseDir . '/controls/action/link/mlinkbutton.php',
    'mopenwindow' => $baseDir . '/controls/action/link/mopenwindow.php',

/* controls/action */ 
    'maction' => $baseDir . '/controls/action/maction.php',
    'mactioncontrol' => $baseDir . '/controls/action/mactioncontrol.php',

/* controls/action/menu */ 
    'mbreadcrumb' => $baseDir . '/controls/action/menu/mbreadcrumb.php',
    'mdropdownmenu' => $baseDir . '/controls/action/menu/mdropdownmenu.php',
    'mmenu' => $baseDir . '/controls/action/menu/mmenu.php',
    'mmenubar' => $baseDir . '/controls/action/menu/mmenubar.php',
    'mmenubaritem' => $baseDir . '/controls/action/menu/mmenubaritem.php',
    'mmenubarlist' => $baseDir . '/controls/action/menu/mmenubarlist.php',
    'mmenuitem' => $baseDir . '/controls/action/menu/mmenuitem.php',
    'mmenulist' => $baseDir . '/controls/action/menu/mmenulist.php',
    'mmenuseparator' => $baseDir . '/controls/action/menu/mmenuseparator.php',
    'mpopupmenubaritem' => $baseDir . '/controls/action/menu/mpopupmenubaritem.php',
    'mpopupmenuitem' => $baseDir . '/controls/action/menu/mpopupmenuitem.php',
    'msimpletoolbar' => $baseDir . '/controls/action/menu/msimpletoolbar.php',
    'mtoolbar' => $baseDir . '/controls/action/menu/mtoolbar.php',
    'mtoolbaritem' => $baseDir . '/controls/action/menu/mtoolbaritem.php',
    'mtoolbarlist' => $baseDir . '/controls/action/menu/mtoolbarlist.php',
    'mtree' => $baseDir . '/controls/action/menu/mtree.php',

/* controls/base */ 
    'icontrol' => $baseDir . '/controls/base/icontrol.php',
    'mcontrol' => $baseDir . '/controls/base/mcontrol.php',

/* controls/container/dialog */ 
    'mhelp' => $baseDir . '/controls/container/dialog/mhelp.php',
    'mprompt' => $baseDir . '/controls/container/dialog/mprompt.php',
    'mwindow' => $baseDir . '/controls/container/dialog/mwindow.php',

/* controls/container/form */ 
    'mbaseform' => $baseDir . '/controls/container/form/mbaseform.php',
    'mconcreteform' => $baseDir . '/controls/container/form/mconcreteform.php',
    'mdivform' => $baseDir . '/controls/container/form/mdivform.php',
    'mform' => $baseDir . '/controls/container/form/mform.php',
    'mformaction' => $baseDir . '/controls/container/form/mformaction.php',
    'mformbase' => $baseDir . '/controls/container/form/mformbase.php',
    'mformcontrol' => $baseDir . '/controls/container/form/mformcontrol.php',
    'mformflow' => $baseDir . '/controls/container/form/mformflow.php',
    'mformobject' => $baseDir . '/controls/container/form/mformobject.php',
    'msimpleform' => $baseDir . '/controls/container/form/msimpleform.php',

/* controls/container/grid/action */ 
    'mgridaction' => $baseDir . '/controls/container/grid/action/mgridaction.php',
    'mgridactioncontrol' => $baseDir . '/controls/container/grid/action/mgridactioncontrol.php',
    'mgridactiondetail' => $baseDir . '/controls/container/grid/action/mgridactiondetail.php',
    'mgridactionicon' => $baseDir . '/controls/container/grid/action/mgridactionicon.php',
    'mgridactionselect' => $baseDir . '/controls/container/grid/action/mgridactionselect.php',
    'mgridactiontext' => $baseDir . '/controls/container/grid/action/mgridactiontext.php',

/* controls/container/grid/column */ 
    'mgridcolumn' => $baseDir . '/controls/container/grid/column/mgridcolumn.php',
    'mgridcolumncontrol' => $baseDir . '/controls/container/grid/column/mgridcolumncontrol.php',
    'mgridcolumnrender' => $baseDir . '/controls/container/grid/column/mgridcolumnrender.php',
    'mgridcontrol' => $baseDir . '/controls/container/grid/column/mgridcontrol.php',
    'mgridhyperlink' => $baseDir . '/controls/container/grid/column/mgridhyperlink.php',

/* controls/container/grid */ 
    'mbasegrid' => $baseDir . '/controls/container/grid/mbasegrid.php',
    'mcriteriagrid' => $baseDir . '/controls/container/grid/mcriteriagrid.php',
    'mdatagrid' => $baseDir . '/controls/container/grid/mdatagrid.php',
    'mdgrid' => $baseDir . '/controls/container/grid/mdgrid.php',
    'mgrid' => $baseDir . '/controls/container/grid/mgrid.php',
    'mgridnavigator' => $baseDir . '/controls/container/grid/mgridnavigator.php',
    'mlookupgrid' => $baseDir . '/controls/container/grid/mlookupgrid.php',
    'mobjectgrid' => $baseDir . '/controls/container/grid/mobjectgrid.php',
    'msqlgrid' => $baseDir . '/controls/container/grid/msqlgrid.php',

/* controls/container/group */ 
    'mbasegroup' => $baseDir . '/controls/container/group/mbasegroup.php',
    'mcheckboxgroup' => $baseDir . '/controls/container/group/mcheckboxgroup.php',
    'mlinkbuttongroup' => $baseDir . '/controls/container/group/mlinkbuttongroup.php',
    'moptiongroup' => $baseDir . '/controls/container/group/moptiongroup.php',
    'mradiobuttongroup' => $baseDir . '/controls/container/group/mradiobuttongroup.php',

/* controls/container */ 
    'icontainer' => $baseDir . '/controls/container/icontainer.php',

/* controls/container/layout */ 
    'maccordion' => $baseDir . '/controls/container/layout/maccordion.php',
    'mareacontainer' => $baseDir . '/controls/container/layout/mareacontainer.php',
    'mcontainer' => $baseDir . '/controls/container/layout/mcontainer.php',
    'mhcontainer' => $baseDir . '/controls/container/layout/mhcontainer.php',
    'mstackcontainer' => $baseDir . '/controls/container/layout/mstackcontainer.php',
    'mtabcontainer' => $baseDir . '/controls/container/layout/mtabcontainer.php',
    'mtcontainer' => $baseDir . '/controls/container/layout/mtcontainer.php',
    'mvcontainer' => $baseDir . '/controls/container/layout/mvcontainer.php',

/* controls/container */ 
    'mcontainercontrol' => $baseDir . '/controls/container/mcontainercontrol.php',

/* controls/container/pane */ 
    'mbasediv' => $baseDir . '/controls/container/pane/mbasediv.php',
    'mbox' => $baseDir . '/controls/container/pane/mbox.php',
    'mcontent' => $baseDir . '/controls/container/pane/mcontent.php',
    'mcontentpane' => $baseDir . '/controls/container/pane/mcontentpane.php',
    'mcustombox' => $baseDir . '/controls/container/pane/mcustombox.php',
    'mdiv' => $baseDir . '/controls/container/pane/mdiv.php',
    'miframe' => $baseDir . '/controls/container/pane/miframe.php',

/* controls/container/panel */ 
    'mactionpanel' => $baseDir . '/controls/container/panel/mactionpanel.php',
    'mbasepanel' => $baseDir . '/controls/container/panel/mbasepanel.php',
    'mpanel' => $baseDir . '/controls/container/panel/mpanel.php',

/* controls/container/table/elements */ 
    'mtablecolgroup' => $baseDir . '/controls/container/table/elements/mtablecolgroup.php',
    'mtbody' => $baseDir . '/controls/container/table/elements/mtbody.php',
    'mtd' => $baseDir . '/controls/container/table/elements/mtd.php',
    'mtfoot' => $baseDir . '/controls/container/table/elements/mtfoot.php',
    'mth' => $baseDir . '/controls/container/table/elements/mth.php',
    'mthead' => $baseDir . '/controls/container/table/elements/mthead.php',
    'mtr' => $baseDir . '/controls/container/table/elements/mtr.php',

/* controls/container/table */ 
    'mbasetable' => $baseDir . '/controls/container/table/mbasetable.php',
    'msimpletable' => $baseDir . '/controls/container/table/msimpletable.php',
    'mtable' => $baseDir . '/controls/container/table/mtable.php',
    'mtableraw' => $baseDir . '/controls/container/table/mtableraw.php',
    'mtablexml' => $baseDir . '/controls/container/table/mtablexml.php',
    'mtexttable' => $baseDir . '/controls/container/table/mtexttable.php',

/* controls/input/choice */ 
    'mcheckbox' => $baseDir . '/controls/input/choice/mcheckbox.php',
    'mcheckcontrol' => $baseDir . '/controls/input/choice/mcheckcontrol.php',
    'moption' => $baseDir . '/controls/input/choice/moption.php',
    'mradiobutton' => $baseDir . '/controls/input/choice/mradiobutton.php',
    'mselection' => $baseDir . '/controls/input/choice/mselection.php',
    'mtransferbox' => $baseDir . '/controls/input/choice/mtransferbox.php',

/* controls/input/grid */ 
    'mgridfield' => $baseDir . '/controls/input/grid/mgridfield.php',
    'mgridinput' => $baseDir . '/controls/input/grid/mgridinput.php',
    'mindexedcontrol' => $baseDir . '/controls/input/grid/mindexedcontrol.php',

/* controls/input/lookup */ 
    'mformpopup' => $baseDir . '/controls/input/lookup/mformpopup.php',
    'mlookupfield' => $baseDir . '/controls/input/lookup/mlookupfield.php',
    'mlookupfieldvalue' => $baseDir . '/controls/input/lookup/mlookupfieldvalue.php',
    'mlookupobject' => $baseDir . '/controls/input/lookup/mlookupobject.php',
    'mlookuptextfield' => $baseDir . '/controls/input/lookup/mlookuptextfield.php',

/* controls/input */ 
    'mcheckbox' => $baseDir . '/controls/input/mcheckbox.php',
    'minputcontrol' => $baseDir . '/controls/input/minputcontrol.php',

/* controls/input/text */ 
    'mbooleanfield' => $baseDir . '/controls/input/text/mbooleanfield.php',
    'mcalendarfield' => $baseDir . '/controls/input/text/mcalendarfield.php',
    'mcepfield' => $baseDir . '/controls/input/text/mcepfield.php',
    'mcnpjfield' => $baseDir . '/controls/input/text/mcnpjfield.php',
    'mcpffield' => $baseDir . '/controls/input/text/mcpffield.php',
    'mcurrencyfield' => $baseDir . '/controls/input/text/mcurrencyfield.php',
    'meditor' => $baseDir . '/controls/input/text/meditor.php',
    'memailfield' => $baseDir . '/controls/input/text/memailfield.php',
    'mfilefield' => $baseDir . '/controls/input/text/mfilefield.php',
    'mhiddenfield' => $baseDir . '/controls/input/text/mhiddenfield.php',
    'minputfield' => $baseDir . '/controls/input/text/minputfield.php',
    'minputgrid' => $baseDir . '/controls/input/text/minputgrid.php',
    'mmultilinefield' => $baseDir . '/controls/input/text/mmultilinefield.php',
    'mnitfield' => $baseDir . '/controls/input/text/mnitfield.php',
    'mnumberfield' => $baseDir . '/controls/input/text/mnumberfield.php',
    'mnumberspinner' => $baseDir . '/controls/input/text/mnumberspinner.php',
    'mpasswordfield' => $baseDir . '/controls/input/text/mpasswordfield.php',
    'mphonefield' => $baseDir . '/controls/input/text/mphonefield.php',
    'mcellphonefield' => $baseDir . '/controls/input/text/mcellphonefield.php',
    'msiapefield' => $baseDir . '/controls/input/text/msiapefield.php',
    'mtextfield' => $baseDir . '/controls/input/text/mtextfield.php',
    'mtimefield' => $baseDir . '/controls/input/text/mtimefield.php',
    'mtimestampfield' => $baseDir . '/controls/input/text/mtimestampfield.php',

/* controls/input/validator */ 
    'mcnpjvalidator' => $baseDir . '/controls/input/validator/mcnpjvalidator.php',
    'mcpfvalidator' => $baseDir . '/controls/input/validator/mcpfvalidator.php',
    'mdatedmyvalidator' => $baseDir . '/controls/input/validator/mdatedmyvalidator.php',
    'mdateymdvalidator' => $baseDir . '/controls/input/validator/mdateymdvalidator.php',
    'memailvalidator' => $baseDir . '/controls/input/validator/memailvalidator.php',
    'mnitvalidator' => $baseDir . '/controls/input/validator/mnitvalidator.php',
    'mrangevalidator' => $baseDir . '/controls/input/validator/mrangevalidator.php',
    'mregexpvalidator' => $baseDir . '/controls/input/validator/mregexpvalidator.php',
    'mrequiredvalidator' => $baseDir . '/controls/input/validator/mrequiredvalidator.php',
    'mvalidator' => $baseDir . '/controls/input/validator/mvalidator.php',

/* controls/output/element */ 
    'mhr' => $baseDir . '/controls/output/element/mhr.php',
    'mpagecomment' => $baseDir . '/controls/output/element/mpagecomment.php',
    'mrawcontrol' => $baseDir . '/controls/output/element/mrawcontrol.php',
    'mseparator' => $baseDir . '/controls/output/element/mseparator.php',
    'mspacer' => $baseDir . '/controls/output/element/mspacer.php',

/* controls/output/header */ 
    'mcontentheader' => $baseDir . '/controls/output/header/mcontentheader.php',
    'mmoduleheader' => $baseDir . '/controls/output/header/mmoduleheader.php',
    'mtextheader' => $baseDir . '/controls/output/header/mtextheader.php',

/* controls/output/image */ 
    'mgdtext' => $baseDir . '/controls/output/image/mgdtext.php',
    'mimage' => $baseDir . '/controls/output/image/mimage.php',
    'mimagebutton' => $baseDir . '/controls/output/image/mimagebutton.php',
    'mimageformlabel' => $baseDir . '/controls/output/image/mimageformlabel.php',
    'mimagelink' => $baseDir . '/controls/output/image/mimagelink.php',
    'mimagelinklabel' => $baseDir . '/controls/output/image/mimagelinklabel.php',
    'mimagelinklabelaction' => $baseDir . '/controls/output/image/mimagelinklabelaction.php',
    'mprogressbar' => $baseDir . '/controls/output/image/mprogressbar.php',
    'mtoolicon' => $baseDir . '/controls/output/image/mtoolicon.php',

/* controls/output/list */ 
    'mlistcontrol' => $baseDir . '/controls/output/list/mlistcontrol.php',
    'morderedlist' => $baseDir . '/controls/output/list/morderedlist.php',
    'munorderedlist' => $baseDir . '/controls/output/list/munorderedlist.php',

/* controls/output */ 
    'moutputcontrol' => $baseDir . '/controls/output/moutputcontrol.php',

/* controls/output/reports */ 
    'mexporter' => $baseDir . '/controls/output/reports/mexporter.php',
    'mezpdfreport' => $baseDir . '/controls/output/reports/mezpdfreport.php',
    'mjasperreport' => $baseDir . '/controls/output/reports/mjasperreport.php',
    'mjavajasperreport' => $baseDir . '/controls/output/reports/mjavajasperreport.php',
    'mpdfreport' => $baseDir . '/controls/output/reports/mpdfreport.php',
    'mreport' => $baseDir . '/controls/output/reports/mreport.php',

/* controls/output/text */ 
    'mbaselabel' => $baseDir . '/controls/output/text/mbaselabel.php',
    'mfieldlabel' => $baseDir . '/controls/output/text/mfieldlabel.php',
    'mfilecontent' => $baseDir . '/controls/output/text/mfilecontent.php',
    'mhint' => $baseDir . '/controls/output/text/mhint.php',
    'mlabel' => $baseDir . '/controls/output/text/mlabel.php',
    'mrawtext' => $baseDir . '/controls/output/text/mrawtext.php',
    'mspan' => $baseDir . '/controls/output/text/mspan.php',
    'msyntax' => $baseDir . '/controls/output/text/msyntax.php',
    'mtext' => $baseDir . '/controls/output/text/mtext.php',
    'mtextlabel' => $baseDir . '/controls/output/text/mtextlabel.php',

/* controls/structure */ 
    'mattributes' => $baseDir . '/controls/structure/mattributes.php',
    'mcss' => $baseDir . '/controls/structure/mcss.php',
    'mdojoprops' => $baseDir . '/controls/structure/mdojoprops.php',
    'mjavascriptcode' => $baseDir . '/controls/structure/mjavascriptcode.php',
    'mstyle' => $baseDir . '/controls/structure/mstyle.php',
    'mxmlflow' => $baseDir . '/controls/structure/mxmlflow.php',

/* painter */ 
    'mbasepainter' => $baseDir . '/painter/mbasepainter.php',
    'mbootstrappainter' => $baseDir . '/painter/mbootstrappainter.php',
    'mbtemplate' => $baseDir . '/painter/mbtemplate.php',
    'mdojopainter' => $baseDir . '/painter/mdojopainter.php',
    'mhtmlpainter' => $baseDir . '/painter/mhtmlpainter.php',
    'mjquerymobilepainter' => $baseDir . '/painter/mjquerymobilepainter.php',

);
