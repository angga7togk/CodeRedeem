<?php

declare(strict_types = 1);

namespace angga7togk\coderedeem\libs\FormAPI;

use pocketmine\plugin\PluginBase;
use angga7togk\coderedeem\libs\FormAPI\ModalForm;
use angga7togk\coderedeem\libs\FormAPI\CustomForm;
use angga7togk\coderedeem\libs\FormAPI\SimpleForm;

class FormAPI extends PluginBase{

    /**
     * @deprecated
     *
     * @param callable|null $function
     * @return CustomForm
     */
    public function createCustomForm(?callable $function = null) : CustomForm {
        return new CustomForm($function);
    }

    /**
     * @deprecated
     *
     * @param callable|null $function
     * @return SimpleForm
     */
    public function createSimpleForm(?callable $function = null) : SimpleForm {
        return new SimpleForm($function);
    }

    /**
     * @deprecated
     *
     * @param callable|null $function
     * @return ModalForm
     */
    public function createModalForm(?callable $function = null) : ModalForm {
        return new ModalForm($function);
    }
}
