<?php

namespace App\Actions;


class ActiveCloseWithdrawAction extends \TCG\Voyager\Actions\AbstractAction
{

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'withdraws';
    }
    public function shouldActionDisplayOnRow($row)
    {
        return $row->status == 'waiting';
    }
    public function getTitle()
    {
        return 'Xác nhận/Từ chối';
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
            'class' => 'btn btn-sm btn-primary pull-right mr-2 active_close' ,
            'data-id' => $this->data->{$this->data->getKeyName()},
            'id'      => 'active_close-'.$this->data->{$this->data->getKeyName()},
        ];
    }

    public function getDefaultRoute()
    {
        return 'javascript:;';
    }

}