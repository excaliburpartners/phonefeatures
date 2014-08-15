<?php 
/* $Id */
if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }

function phonefeatures_hookGet_config($engine) {
	global $ext;
		
	$modulename = 'donotdisturb';
		
	$fcc = new featurecode($modulename, 'dnd_on');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-dnd-on";
		$ext->splice($id, $code, 'hook_1', new ext_setvar('FEATURE', 'yes'));
		$ext->splice($id, $code, 'hook_1', new ext_gosub('1', 'sfeature', 'sip-feature-events-dnd'));
	}
	
	$fcc = new featurecode($modulename, 'dnd_off');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-dnd-off";
		$ext->splice($id, $code, 'hook_1', new ext_setvar('FEATURE', 'no'));
		$ext->splice($id, $code, 'hook_1', new ext_gosub('1', 'sfeature', 'sip-feature-events-dnd'));
	}
	
	$fcc = new featurecode($modulename, 'dnd_toggle');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-dnd-toggle";
		$ext->splice($id, $code, 'hook_on', new ext_setvar('FEATURE', 'yes'));
		$ext->splice($id, $code, 'hook_on', new ext_gosub('1', 'sfeature', 'sip-feature-events-dnd'));
		$ext->splice($id, $code, 'hook_off', new ext_setvar('FEATURE', 'no'));
		$ext->splice($id, $code, 'hook_off', new ext_gosub('1', 'sfeature', 'sip-feature-events-dnd'));
	}
	
	$modulename = 'callforward';
	
	$fcc = new featurecode($modulename, 'cfon');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-cf-on";
		$ext->splice($id, $code, 'hook_1', new ext_setvar('FEATURE', '${toext}'));
		$ext->splice($id, $code, 'hook_1', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
		$ext->splice($id, "_$code.", 'hook_2', new ext_setvar('FEATURE', '${toext}'));
		$ext->splice($id, "_$code.", 'hook_2', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	}
	
	$fcc = new featurecode($modulename, 'cfpon');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-cf-prompting-on";
		$ext->splice($id, $code, 'hook_1', new ext_setvar('FEATURE', '${toext}'));
		$ext->splice($id, $code, 'hook_1', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
		$ext->splice($id, "_$code.", 'hook_2', new ext_setvar('FEATURE', '${toext}'));
		$ext->splice($id, "_$code.", 'hook_2', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	}
	
	$fcc = new featurecode($modulename, 'cfoff');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-cf-off";
		$ext->splice($id, $code, 'hook_1', new ext_setvar('FEATURE', ''));
		$ext->splice($id, $code, 'hook_1', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	}
	
	$fcc = new featurecode($modulename, 'cfoff_any');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-cf-off-any";
		$ext->splice($id, $code, 'hook_1', new ext_setvar('FEATURE', ''));
		$ext->splice($id, $code, 'hook_1', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	}
	
	$fcc = new featurecode($modulename, 'cf_toggle');
	$code = $fcc->getCodeActive();
	unset($fcc);

	if($code != '') {
		$id = "app-cf-toggle";
		$ext->splice($id, $code, 'hook_on', new ext_setvar('FEATURE', '${toext}'));
		$ext->splice($id, $code, 'hook_on', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
		$ext->splice($id, $code, 'hook_off', new ext_setvar('FEATURE', ''));
		$ext->splice($id, $code, 'hook_off', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	}
}

function phonefeatures_get_config($engine) {
	global $ext;
	global $amp_conf;

	$id = "sip-feature-events-dnd"; // The context to be included
	
	$c = "on";
	$ext->add($id, $c, '', new ext_macro('user-callerid')); // $cmd,n,Macro(user-callerid)
	$ext->add($id, $c, '', new ext_setvar('DB(DND/${AMPUSER})', 'YES')); // $cmd,n,Set(...=YES)
	if ($amp_conf['USEDEVSTATE']) {
		$ext->add($id, $c, '', new ext_setvar('STATE', 'BUSY'));
		$ext->add($id, $c, '', new ext_gosub('1', 'sstate', 'sip-feature-events-dnd'));
	}
	$ext->add($id, $c, '', new ext_setvar('FEATURE', 'yes'));
	$ext->add($id, $c, '', new ext_gosub('1', 'sfeature', 'sip-feature-events-dnd'));
	$ext->add($id, $c, '', new ext_macro('hangupcall')); // $cmd,n,Macro(user-callerid)

	$c = "off";
	$ext->add($id, $c, '', new ext_macro('user-callerid')); // $cmd,n,Macro(user-callerid)
	$ext->add($id, $c, '', new ext_dbdel('DND/${AMPUSER}')); // $cmd,n,DBdel(..)
	if ($amp_conf['USEDEVSTATE']) {
		$ext->add($id, $c, '', new ext_setvar('STATE', 'NOT_INUSE'));
		$ext->add($id, $c, '', new ext_gosub('1', 'sstate', 'sip-feature-events-dnd'));
	}
	$ext->add($id, $c, '', new ext_setvar('FEATURE', 'no'));
	$ext->add($id, $c, '', new ext_gosub('1', 'sfeature', 'sip-feature-events-dnd'));
	$ext->add($id, $c, '', new ext_macro('hangupcall')); // $cmd,n,Macro(user-callerid)
	
	if ($amp_conf['USEDEVSTATE']) {
		$c = 'sstate';
		$ext->add($id, $c, '', new ext_setvar($amp_conf['AST_FUNC_DEVICE_STATE'].'(Custom:DND${AMPUSER})', '${STATE}'));
		$ext->add($id, $c, '', new ext_dbget('DEVICES','AMPUSER/${AMPUSER}/device'));
		$ext->add($id, $c, '', new ext_gotoif('$["${DEVICES}" = "" ]', 'return'));
		$ext->add($id, $c, '', new ext_setvar('LOOPCNT', '${FIELDQTY(DEVICES,&)}'));
		$ext->add($id, $c, '', new ext_setvar('ITER', '1'));
		$ext->add($id, $c, 'begin', new ext_setvar($amp_conf['AST_FUNC_DEVICE_STATE'].'(Custom:DEVDND${CUT(DEVICES,&,${ITER})})','${STATE}'));
		$ext->add($id, $c, '', new ext_setvar('ITER', '$[${ITER} + 1]'));
		$ext->add($id, $c, '', new ext_gotoif('$[${ITER} <= ${LOOPCNT}]', 'begin'));
		$ext->add($id, $c, 'return', new ext_return());
	}	
	
	$c = 'sfeature';
	$ext->add($id, $c, '', new ext_dbget('DEVICES','AMPUSER/${AMPUSER}/device'));
	$ext->add($id, $c, '', new ext_gotoif('$["${DEVICES}" = "" ]', 'return'));
	$ext->add($id, $c, '', new ext_setvar('LOOPCNT', '${FIELDQTY(DEVICES,&)}'));
	$ext->add($id, $c, '', new ext_setvar('ITER', '1'));
	$ext->add($id, $c, 'begin', new ext_gotoif('$["${CHANNEL}" = "SIPFeatureTrigger" & "${CUT(DEVICES,&,${ITER})}" = "${REALCALLERIDNUM}"]', 'skip'));
	$ext->add($id, $c, '', new ext_setvar('SIPPEER(${CUT(DEVICES,&,${ITER})},donotdisturb)','${FEATURE}'));
	$ext->add($id, $c, 'skip', new ext_setvar('ITER', '$[${ITER} + 1]'));
	$ext->add($id, $c, '', new ext_gotoif('$[${ITER} <= ${LOOPCNT}]', 'begin'));
	$ext->add($id, $c, 'return', new ext_return());
	
	$id = "sip-feature-events-cf"; // The context to be included
	
	$c = '_[0-9+].';
	$ext->add($id, $c, '', new ext_macro('user-callerid')); // $cmd,n,Macro(user-callerid)
	$ext->add($id, $c, '', new ext_setvar('fromext', '${AMPUSER}'));
	$ext->add($id, $c, '', new ext_setvar('DB(CF/${fromext})', '${EXTEN}'));
	if ($amp_conf['USEDEVSTATE']) {
		$ext->add($id, $c, '', new ext_setvar('STATE', 'BUSY'));
		$ext->add($id, $c, '', new ext_gosub('1', 'sstate', 'sip-feature-events-cf'));
	}
	$ext->add($id, $c, '', new ext_setvar('FEATURE', '${EXTEN}'));
	$ext->add($id, $c, '', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	$ext->add($id, $c, '', new ext_macro('hangupcall')); // $cmd,n,Macro(user-callerid)
	
	$c = 'off';
	$ext->add($id, $c, '', new ext_macro('user-callerid')); // $cmd,n,Macro(user-callerid)
	$ext->add($id, $c, '', new ext_setvar('fromext', '${AMPUSER}'));
	$ext->add($id, $c, '', new ext_dbdel('CF/${fromext}'));
	if ($amp_conf['USEDEVSTATE']) {
		$ext->add($id, $c, '', new ext_setvar('STATE', 'NOT_INUSE'));
		$ext->add($id, $c, '', new ext_gosub('1', 'sstate', 'sip-feature-events-cf'));
	}
	$ext->add($id, $c, '', new ext_setvar('FEATURE', ''));
	$ext->add($id, $c, '', new ext_gosub('1', 'sfeature', 'sip-feature-events-cf'));
	$ext->add($id, $c, '', new ext_macro('hangupcall')); // $cmd,n,Macro(user-callerid)

	if ($amp_conf['USEDEVSTATE']) {
		$c = 'sstate';
		$ext->add($id, $c, '', new ext_setvar($amp_conf['AST_FUNC_DEVICE_STATE'].'(Custom:CF${fromext})', '${STATE}'));
		$ext->add($id, $c, '', new ext_dbget('DEVICES','AMPUSER/${fromext}/device'));
		$ext->add($id, $c, '', new ext_gotoif('$["${DEVICES}" = "" ]', 'return'));
		$ext->add($id, $c, '', new ext_setvar('LOOPCNT', '${FIELDQTY(DEVICES,&)}'));
		$ext->add($id, $c, '', new ext_setvar('ITER', '1'));
		$ext->add($id, $c, 'begin', new ext_setvar($amp_conf['AST_FUNC_DEVICE_STATE'].'(Custom:DEVCF${CUT(DEVICES,&,${ITER})})','${STATE}'));
		$ext->add($id, $c, '', new ext_setvar('ITER', '$[${ITER} + 1]'));
		$ext->add($id, $c, '', new ext_gotoif('$[${ITER} <= ${LOOPCNT}]', 'begin'));
		$ext->add($id, $c, 'return', new ext_return());
	}
	
	$c = 'sfeature';
	$ext->add($id, $c, '', new ext_dbget('DEVICES','AMPUSER/${fromext}/device'));
	$ext->add($id, $c, '', new ext_gotoif('$["${DEVICES}" = "" ]', 'return'));
	$ext->add($id, $c, '', new ext_setvar('LOOPCNT', '${FIELDQTY(DEVICES,&)}'));
	$ext->add($id, $c, '', new ext_setvar('ITER', '1'));
	$ext->add($id, $c, 'begin', new ext_gotoif('$["${CHANNEL}" = "SIPFeatureTrigger" & "${CUT(DEVICES,&,${ITER})}" = "${REALCALLERIDNUM}"]', 'skip'));
	$ext->add($id, $c, '', new ext_setvar('SIPPEER(${CUT(DEVICES,&,${ITER})},callforward)','${FEATURE}'));
	$ext->add($id, $c, 'skip', new ext_setvar('ITER', '$[${ITER} + 1]'));
	$ext->add($id, $c, '', new ext_gotoif('$[${ITER} <= ${LOOPCNT}]', 'begin'));
	$ext->add($id, $c, 'return', new ext_return());
}

?>
