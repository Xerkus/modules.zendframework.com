<?php

namespace User;

use ZfcBase\Module\AbstractModule;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;

class Module extends AbstractModule
{
    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        $em = $app->getEventManager()->getSharedManager();

        $em->attach('ScnSocialAuth\Authentication\Adapter\HybridAuth','githubToLocalUser', function($e) {
            $localUser = $e->getTarget();
            $userProfile = $e->getParam('userProfile');
            $localUser->setPhotoUrl($userProfile->photoURL);
        });
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'zfcuser_user_mapper' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $mapper = new Mapper\User();
                    $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $entityClass = $options->getUserEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setHydrator(new Mapper\UserHydrator());
                    return $mapper;
                },
            ),
        );
    }

    public function getDir()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }
}