<?php
/**
 * @author Gustavo Ulyssea - gustavo.ulyssea@gmail.com
 * @copyright Copyright (c) 2020 GumNet (https://gum.net.br)
 * @package GumNet AME
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY GUM Net (https://gum.net.br). AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE FOUNDATION OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace GumNet\AME\Helper;

class GumApi
{
    protected $_storeManager;
    protected $_scopeConfig;
    protected $url;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
                                )
    {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->url = "https://apiame.gum.net.br";
    }
    public function captureTransaction($result)
    {
        $this->gumRequest("capturetransaction",$result);
        return true;
    }
    public function createOrder($input,$result)
    {
        $this->gumRequest("createorder",$result,$input);
        return true;
    }
    public function gumRequest($action,$result,$input=""){
        $ch = curl_init();
        $environment = $this->getEnvironment();
        $post['environment'] = $environment;
        $post['siteurl'] = $this->_storeManager->getStore()->getBaseUrl();
        $post['input'] = $input;
        $post['result'] = $result;
        $post['action'] = $action;
        $post['hash'] = "E2F49DA5F963DAE26F07E778FB4B9301B051AEEA6E8E08D788163023876BC14E";

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $re = curl_exec($ch);
        curl_close($ch);
        return true;
    }
    public function getEnvironment(){
        $environment = "";
        if ($this->_scopeConfig->getValue('ame/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 0) {
            $environment = "dev";
        }
        if ($this->_scopeConfig->getValue('ame/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1) {
            $environment = "hml";
        }
        if ($this->_scopeConfig->getValue('ame/general/environment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 2) {
            $environment = "prod";
        }
        return $environment;
    }
}


