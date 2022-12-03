<?php

/*
 * Copyright (C) 2017 Fabian Franz
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace OPNsense\Rspamd\Api;

use OPNsense\Base\ApiMutableModelControllerBase;
use OPNsense\Core\Config;
use OPNsense\Core\Backend;

class SettingsController extends ApiMutableModelControllerBase
{
    protected static $internalModelClass = '\OPNsense\Rspamd\RSpamd';
    protected static $internalModelName = 'rspamd';

    public function setAction()
    {
        $result = array("result" => "failed");

        if ($this->request->isPost() && $this->request->hasPost("rspamd")) {
            $mdlRspamd = $this->getModel();
            $mdlRspamd->setNodes($this->request->getPost("rspamd"));

            // perform validation
            $valMsgs = $mdlRspamd->performValidation();
            foreach ($valMsgs as $field => $msg) {
                if (!array_key_exists("validations", $result)) {
                    $result["validations"] = array();
                }
                $result["validations"]["rspamd." . $msg->getField()] = $msg->getMessage();
            }

            // serialize model to config and save
            if ($valMsgs->count() == 0) {
                // encrypt passwords if any
                $pass_fields = array('password', 'enable_password');
                $backend = new Backend();
                foreach ($pass_fields as $field) {
                    $phrase = $mdlRspamd->getNodeByReference('controller.' . $field) ? (string)$mdlRspamd->getNodeByReference('controller.' . $field) : "";
                    if (strlen($phrase) && (!str_starts_with((string)$mdlRspamd->controller->$field, "HASH: "))) {
                        $phrase = (string)$mdlRspamd->controller->$field;
                        $key = json_decode(trim($backend->configdRun("rspamd password encrypt " . $phrase)), true);
                        if ((string)$key['status'] == 'OK') {
                            $mdlRspamd->controller->$field = "HASH: " . (string)$key['password'];
                        } else {
                            if (!array_key_exists("validations", $result)) {
                                $result["validations"] = array();
                            }
                            $result["validations"]["rspamd.controller." . $field] = "Password encryption failed";
                        }
                    }
                }
                
                if (!array_key_exists("validations", $result)) {
                    $mdlRspamd->serializeToConfig();
                    Config::getInstance()->save();
                    $result["result"] = "saved";
                }
            }
        }

        return $result;
    }
}
