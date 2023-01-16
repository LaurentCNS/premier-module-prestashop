<?php

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'MyModule';
        $this->author = 'Laurent Cantos';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        $this->_directory = dirname(__FILE__);

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('mon module custom', [], 'Modules.MyModule.Admin');
        $this->description = $this->trans('affichage git', [], 'Modules.MyModule.Admin');

        $this->templateFile = 'module:mymodule/views/templates/hook/mymodule.tpl';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->unregisterHook('displayHome');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmitMyModule')) {
            Configuration::updateValue('GIT_ADRESS', Tools::getValue('GIT_ADRESS'));
        }
    }

    public function getContent(){
        return $this->postProcess().$this->renderForm();
    }
    public function hookDisplayHome()
    {
        $this->context->smarty->assign([
            'git_adress' => Configuration::get('GIT_ADRESS'),
        ]);
       return $this->fetch($this->templateFile);
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'input' => [
                    [
                        'type' => 'text',
                        'label' => 'Adresse de votre compte Git',
                        'name' => 'GIT_ADRESS',
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmitMyModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'GIT_ADRESS' => Tools::getValue('GIT_ADRESS', Configuration::get('GIT_ADRESS')),
        ];
    }

}
