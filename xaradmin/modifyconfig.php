<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main configuration page for the cacher module
 *
 */

// Use this version of the modifyconfig file when the module is not a  utility module

function cacher_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurity::check('AdminCacher')) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:100', $phase, 'modify', xarVar::NOT_REQUIRED, xarVar::PREP_FOR_DISPLAY)) {
        return;
    }
    if (!xarVar::fetch('tab', 'str:1:100', $data['tab'], 'general', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['module_settings'] = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'cacher']);
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls, use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'general':
                    break;
                case 'tab2':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code. AJAX calls ignore this
            if (!xarSec::confirmAuthKey()) {
                return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'bad_author']);
            }
            switch ($data['tab']) {
                case 'general':
                    $isvalid = $data['module_settings']->checkInput();
                    if (!$isvalid) {
                        // If this is an AJAX call, send back a message (and end)
                        xarController::$request->msgAjax($data['module_settings']->getInvalids());
                        // No AJAX, just send the data to the template for display
                        return xarTpl::module('cacher', 'admin', 'modifyconfig', $data);
                    } else {
                        $itemid = $data['module_settings']->updateItem();
                    }

                    if (!xarVar::fetch('debugmode', 'checkbox', $debugmode, xarModVars::get('cacher', 'debugmode'), xarVar::NOT_REQUIRED)) {
                        return;
                    }

                    $modvars = [
                                    'debugmode',
                                    ];

                    foreach ($modvars as $var) {
                        if (isset($$var)) {
                            xarModVars::set('cacher', $var, $$var);
                        }
                    }
                    break;
                case 'tab2':
                    break;
                default:
                    break;
            }

            // If this is an AJAX call, end here
            xarController::$request->exitAjax();
            xarController::redirect(xarController::URL('cacher', 'admin', 'modifyconfig', ['tab' => $data['tab']]));
            return true;
            break;
    }
    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
