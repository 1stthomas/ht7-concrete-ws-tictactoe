<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\SinglePage\Dashboard\Ht7\Games;

use \Concrete\Core\Http\ResponseFactory;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\User\User;
use \Concrete\Core\User\Group\Group;
use \Concrete\Package\Ht7C5WsServer\Application\Provider;
use \Concrete\Package\Ht7C5WsServer\Definitions\ApplicationRunType;
use \Concrete\Package\Ht7C5WsServer\Definitions\ApplicationStatus;
use \Concrete\Package\Ht7C5WsServer\Entity\WsApplication as WsApplicationEntity;
use \Concrete\Package\Ht7C5WsServer\Messages\Error as ErrorMessage;
use \Concrete\Package\Ht7C5WsServer\Messages\Messagable;
use \Concrete\Package\Ht7C5WsServer\Messages\Success as SuccessMessage;

defined('C5_EXECUTE') or die('Access Denied.');

class TicTacToe extends DashboardPageController
{
    public function view()
    {
//        $this->requireAsset('ht7-ws/admin');
//        $this->requireAsset('css', 'ht7-widgets/status-toggle');
//
//        $pkgH = $this->app->make('helper/ht7/package/base');
//        $pkgFileConfig = $pkgH->getPackageFileConfig($this);
//
//        $u = $this->app->make(User::class);
//
//        $this->set('applications', $this->getApps());
//        $this->set(
//            'isActiveBsTooltips',
//            $pkgFileConfig->get('defaults.view.bs_tooltips_active', false)
//        );
//        $this->set('pkgHandle', $pkgH->getPackageHandle($this));
//        $this->set('pkgHandleBase', $pkgH->getPackageHandle());
//        $this->set('startUrl', $pkgFileConfig->get('defaults.view.start_url'));
//        $this->set('stopUrl', $pkgFileConfig->get('defaults.view.stop_url'));
//        $this->set('userId', $u->getUserID());
//        $this->set('userName', $u->getUserName());
//        $this->set('userToken', $u->getUserInfoObject()->getUserPassword());
//        $this->set('wsServerUrl', (new Provider())->getWsServerUrl());
    }
    protected function composeResponse(Messagable $msg)
    {
        $responseFactory = $this->app->make(ResponseFactory::class);

        $body = [
            'header' => $msg->getHeader(),
            'title' => $msg->getTitle()
        ];

        if ($msg instanceof SuccessMessage) {
            $body['success'] = $msg->getMessages();
        } elseif ($msg instanceof ErrorMessage) {
            $body['error'] = $msg->getMessages();
        }

        return $responseFactory->json($body, $msg->getHeader());
    }
}
