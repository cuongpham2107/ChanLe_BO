<?php

namespace App\Actions;


class PopupAction extends \TCG\Voyager\Actions\AbstractAction
{

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'players';
    }
    public function getTitle()
    {
        return 'Tăng/ trừ tiền';
    }

    public function getIcon()
    {
        return 'voyager-edit';
    }

    // public function getPolicy()
    // {
    //     return 'edit';
    // }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right mr-2 money' ,
            'data-id' => $this->data->{$this->data->getKeyName()},
            'id'      => 'money-'.$this->data->{$this->data->getKeyName()},
        ];
    }

    public function getDefaultRoute()
    {
        return 'javascript:;';
    }

}