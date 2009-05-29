<?php
/*----------------------------------------------------------------------
   GLPI - Gestionnaire Libre de Parc Informatique
   Copyright (C) 2003-2008 by the INDEPNET Development Team.

   http://indepnet.net/   http://glpi-project.org/
   ----------------------------------------------------------------------
   LICENSE

   This file is part of GLPI.

   GLPI is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   GLPI is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with GLPI; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
   ----------------------------------------------------------------------*/
/*----------------------------------------------------------------------
    Original Author of file: 
    Purpose of file:
    ----------------------------------------------------------------------*/

/*order dropdown selection */
function plugin_order_dropdownorder($myname,$entity_restrict='',$used=array()) {
	global $DB,$LANG,$CFG_GLPI;

	$rand=mt_rand();
	$where=" WHERE glpi_plugin_order.deleted='0' ";
	$where.=getEntitiesRestrictRequest("AND","glpi_plugin_order",'',$entity_restrict,true);
	if (count($used)) {
		$where .= " AND ID NOT IN (0";
		foreach ($used as $ID)
			$where .= ",$ID";
		$where .= ")";
	}
	$query="SELECT * 
			FROM glpi_dropdown_plugin_order_taxes 
			WHERE ID IN (
				SELECT DISTINCT taxes 
				FROM glpi_plugin_order 
				$where) 
			GROUP BY name ORDER BY name";
	$result=$DB->query($query);

	echo "<select name='_taxes' id='taxes_order'>\n";
	echo "<option value='0'>------</option>\n";
	while ($data=$DB->fetch_assoc($result)){
		echo "<option value='".$data['ID']."'>".$data['name']."</option>\n";
	}
	echo "</select>\n";

	$params=array('taxes_order'=>'__VALUE__',
			'entity_restrict'=>$entity_restrict,
			'rand'=>$rand,
			'myname'=>$myname,
			'used'=>$used
			);

	ajaxUpdateItemOnSelectEvent("taxes_order","show_$myname$rand",$CFG_GLPI["root_doc"]."/plugins/order/ajax/dropdownTypeorder.php",$params);

	echo "<span id='show_$myname$rand'>";
	$_POST["entity_restrict"]=$entity_restrict;
	$_POST["taxes_order"]=0;
	$_POST["myname"]=$myname;
	$_POST["rand"]=$rand;
	$_POST["used"]=$used;
	include (GLPI_ROOT."/plugins/order/ajax/dropdownTypeorder.php");
	echo "</span>\n";

	return $rand;
}

function plugin_order_dropdownAllItems($myname,$value_type=0,$value=0,$entity_restrict=-1,$types='') {
    global $LANG,$CFG_GLPI;
    if (!is_array($types)){
	$types=plugin_suppliertag_getTypes ();
    }
    $rand=mt_rand();
    $ci=new CommonItem();
    $options=array();
    
    foreach ($types as $type){
	$ci->setType($type);
	$options[$type]=$ci->getType();
    }
    asort($options);
    if (count($options)){
	echo "<select name='type' id='item_type$rand'>\n";
	    echo "<option value='0'>-----</option>\n";
	foreach ($options as $key => $val){
	    echo "<option value='".$key."' ".($key==$value?"selected": "").">".$val."</option>\n";
	}
	echo "</select>&nbsp;";
    }
}
?>