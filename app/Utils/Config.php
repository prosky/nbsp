<?php


namespace App\Utils;


use Nette\Application\Application;
use Nette\Application\IPresenter;
use Nette\Application\UI\Presenter;
use Nette\Utils\ArrayHash;

class Config extends ArrayHash
{

    public function onPresenter(Application $application, IPresenter $presenter): void
    {
        $this->module = null;
        if ($presenter instanceof Presenter) {
            unset($presenter->onStartup['initModule']);
            $presenter->onStartup['initModule'] = function (Presenter $presenter) {
                $this->module = preg_replace('~^([^:/]+)[:/].+~', '$1', $presenter->getName());
            };
        }
    }
}