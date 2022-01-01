<?php

namespace Concrete\Package\Ht7C5WsTictactoe;

use Concrete\Core\Application\Application;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\View\PageView;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Route;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\User\Group\Group;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Concrete\Package\Ht7C5WsServer\Application\Registry as WsApplicationRegistry;
use Concrete\Package\Ht7C5WsTictactoe\Controller\Dialog\GameFinish;
use Concrete\Package\Ht7C5WsTictactoe\ServiceProvider;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends Package
{
    protected $appVersionRequired = '8.5.4';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::$packageDependencies
     */
    protected $packageDependencies = [
        'ht7_c5_ws_chat_simple' => '0.1.0'
    ];
    protected $pkgAutoloaderMapCoreExtensions = true;
    protected $pkgHandle = 'ht7_c5_ws_tictactoe';
    protected $pkgVersion = '0.1.2';

    /**
     * @var \Concrete\Core\Entity\Package
     */
    private $pkg;

    public function __construct(Application $app)
    {
        parent::__construct($app);

        // On installation, this one will be null, but parent::install() will
        // return the current package entity.
        $this->pkg = $app->make(PackageService::class)
            ->getByHandle($this->pkgHandle);
    }
    public function getPackageDescription()
    {
        return tc(
            'ht7_c5_ws_tictactoe',
            'A WebSocket Server which can serve several apps.'
        );
    }
    public function getPackageName()
    {
        return tc('ht7_c5_ws_tictactoe', 'Ht7 Ws TicTacToe');
    }
    public function install()
    {
        // Install the current package and create the defined db entities.
        $this->pkg = parent::install();

        $this->registerServices();
        // Create all pages through the content XML.
        $this->installContentFile('install.xml');
        // Activate user registration.
        $this->fixConfigs();
        // If the path uses "-" to separate words and the filename
        // uses "_", the cFilename on the Pages table is empty.
        // Therefor we need to update this field.
        $this->fixFilenames();
//        $this->fixFolderPermissions();
//        $this->installUserGroups();
        $this->addUsers();
        $this->restrictPagePermissions();
        $this->grantSiteAccess();

//        $this->setupAutoloader();
        $this->registerWsGameApp();
    }
    public function on_start()
    {
        $this->registerAssets();
        $this->registerServices();
        $this->registerRoutes();
        $this->registerEvents();
    }
    public function upgrade()
    {
        parent::upgrade();

        // Create all missing pages through the content XML.
        $this->installContentFile('install.xml');
    }
    private function addUsers()
    {
        $u1a = $this->app->make(UserInfoRepository::class)->getByName('heinrich');

        /* @var $u1 User */
        if (is_object($u1a)) {
            $u1 = $u1a;
//            $u1 = $u1a->getUserInfoObject()->getUserObject();
        } else {
            /* @var $ui1 UserInfo */
            $ui1 = $this->app->make('user/registration')->create([
                'uName' => 'heinrich',
                'uEmail' => 'heinrich.zimmermann@ffhs.ch',
                'uPassword' => '12345',
            ]);
            $u1 = $ui1->getUserObject();
        }
        $gAdmin = Group::getByName('Ws Admin');
        $u1->enterGroup($gAdmin);

        /* @var $u2 User */
        $u2a = $this->app->make(UserInfoRepository::class)->getByName('thomas');

        if (is_object($u2a)) {
            $u2 = $u2a;
//            $u2 = $u2a->getUserInfoObject()->getUserObject();
        } else {
            $ui2 = $this->app->make('user/registration')->create([
                'uName' => 'thomas',
                'uEmail' => 'thomas.pluess@students.ffhs.ch',
                'uPassword' => '12345',
            ]);
            $u2 = $ui2->getUserObject();
        }
        $gUser = Group::getByName('Ws Chat Admin');
        $u2->enterGroup($gUser);
    }
    private function fixConfigs()
    {
        $config = $this->getApplication()->make('config');
//        $config = $this->getFileConfig();

        $config->save('concrete.misc.login_redirect', 'HOMEPAGE');
        $config->save('concrete.user.registration.enabled', true);
        $config->save('concrete.user.registration.type', 'enabled');
    }
    /**
     * Make sure the c5 knows the the filenames belonging to the paths defined
     * by this package.
     */
    private function fixFilenames()
    {
//        $this->app->make('helper/ht7/file/namefixer')
//            ->fixFilenames('/dashboard/ht7/ws-tictactoe', $this->pkg);
    }
    private function fixFolderPermissions()
    {
        chmod('/app/application/files/cache/expensive/', 0775);
    }
    /**
     * Get the content of the admin app definitions found in the package config
     * folder.
     *
     * While installation the file config of the affected package is not accessable.
     * Therefor the package config data needs to be gathered by our own.
     *
     * @return  array                       The content of the admin app definitions
     *                                      found at <code>pkgHandle/config/admin_app.php</code>
     */
    private function getAppConfig()
    {
        $path = DIR_PACKAGES . '/' . $this->pkgHandle . '/config/tictactoe_app.php';

        return include $path;
    }
    private function grantSiteAccess()
    {
        $home = Page::getByID(1, 'RECENT');
        $viewObj = GroupPermissionAccessEntity::getOrCreate(Group::getByID(GUEST_GROUP_ID));

        $pk = PermissionKey::getByHandle('view_page');
        $pk->setPermissionObject($home);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->clearPermissionAssignment();
        $pa = PermissionAccess::create($pk);
        $pa->addListItem($viewObj);
        $pt->assignPermissionAccess($pa);
    }
    private function registerAssets()
    {
        $al = AssetList::getInstance();

        $al->register(
            'css', 'ht7-ffhs/main', 'css/ht7.ffhs.main.css',
            ['version' => '0.0.1', 'minify' => true, 'combine' => true],
            $this
        );
        $al->register(
            'css', 'ht7-ws/tictactoe', 'css/ht7.ws.tictactoe.css',
            ['version' => '0.0.1', 'minify' => true, 'combine' => true],
            $this
        );
        $al->register(
            'javascript', 'ht7-ws/tictactoe', 'js/ht7.ws.games.tictactoe.js',
            ['version' => '0.0.1', 'minify' => true, 'combine' => true],
            $this
        );
        $al->registerGroup(
            'ht7-ws/tictactoe',
            [
                ['css', 'ht7-ffhs/main'],
                ['css', 'ht7-ws/tictactoe'],
                ['javascript', 'ht7-ws/tictactoe'],
//                ['javascript', 'ht7-widgets/tictactoe'],
            ]
        );
    }
    public function registerEvents()
    {
        // The login pages are on english, lets change it, if we find a language
        // in the session which is supported by the course management.
        Events::addListener('on_before_render', function($event) {
            $cView = $event->getArgument('view');

            if (is_object($cView) && $cView instanceof PageView) {
                $c = $cView->getCollectionObject();

                if ((int) $c->getCollectionID() === 1) {
                    header('Location: ' . Url::to('/tictactoe'));
                    exit();
                }
            }
        });
    }
    /**
     * Register all routes defined in config/paths.php.
     *
     * This method let the route helper compose an array which can be used by
     * calling <code>Route::registerMultiple($routesArray)</code>.
     *
     */
    private function registerRoutes()
    {
        Route::registerMultiple([
            '/ccm/ht7/ws/tictactoe/finish/{id}/{winner}/{timelimit}'
            => [GameFinish::class . '::view'],
        ]);
    }
    /**
     * Register the package services, register the package specific ErrorHandler.
     */
    private function registerServices()
    {
        $list = new ProviderList($this->app);
        $list->registerProvider(ServiceProvider::class);
    }
    private function registerWsGameApp()
    {
        $provider = new Provider();
        $registry = new WsApplicationRegistry();
        $game = $provider->getApplicationByHandle('tictactoe');

        if (!is_object($game)) {
            $data = $this->getAppConfig();

            $registry->register($data);
        }
//        $data = $this->getAppConfig();
//
//        \Log::addEntry(print_r($data, true));
//
//        if (!class_exists(Concrete\Package\Ht7C5WsTicTacToe\Application\Games::class)) {
//            \Log::addEntry('class controller existiert NICHT');
//        } else {
//            \Log::addEntry('class controller existiert!');
//        }
//
//        (new WsApplicationRegistry())->register($data);
    }
    private function restrictPagePermissions()
    {
        $pages = [
//            Page::getByPath('/tictactoe'),
            Page::getByPath('/tictactoe/lobby'),
            Page::getByPath('/tictactoe/game'),
        ];
        $gNames = [
            'Registered Users',
        ];

        foreach ($pages as $page) {
            $page->clearPagePermissions();
            // Remove also the guest group access permission...
            // src: https://documentation.concrete5.org/developers/permissions-access-security/advanced-programmatically-setting-permissions-on-an-object
            $pk = PermissionKey::getByHandle('view_page');
            $pk->setPermissionObject($page);
            $pa = $pk->getPermissionAccessObject();
            $pe = GroupPermissionAccessEntity::getOrCreate(Group::getByID(GUEST_GROUP_ID));
            $pa->removeListItem($pe);

            $allPagePermissionKeys = PermissionKey::getList('page');

            $allPagePermissionKeyHandles = array_map(function ($permissionKey) {
                return $permissionKey->getPermissionKeyHandle();
            }, $allPagePermissionKeys);

            foreach ($gNames as $gName) {
                $page->assignPermissions(Group::getByName($gName), $allPagePermissionKeyHandles);
            }
        }
    }
}
