<?php

require('../vendor/autoload.php');

use Silex\Application;

class HomeController {

    public function envAction(Application $app) {
        $fb_id = getenv('FB_ID');
        $fb_secret = getenv('FB_SECRET');
        $gplus_id = getenv('GPLUS_ID');
        $gplus_secret = getenv('GPLUS_SECRET');
        echo getenv('MODE_PROD');
        echo getenv('MONGO_URI');
        echo getenv('MONGO_DB');
        echo getenv('APP_URL');
        echo $fb_id;
        echo $fb_secret;
        echo $gplus_id;
        echo $gplus_secret;
        return 'Hello';
    }

    public function indexAction(Application $app) {
        $app['monolog']->addDebug('logging output.');
        $user = $app['session']->get('user');
        if (NULL == $user) {
            return $app->redirect('/login');
        }
        return $app['twig']->render('home.twig', array('user' => $user));
    }

    public function authAction($provider, Application $app) {

        if (in_array(ucfirst($provider), array("Facebook", "Google"))) {
            if (!local_configs('MODE_PROD')) {
                $user = array('access_token' => 'DEccdXX223', 'displayName' => 'Klus Klax Klan');
            } else {
                $user = array();
                $hybridauth = new Hybrid_Auth(auth_configs());
                $adapter = $hybridauth->authenticate(ucfirst($provider));
                $access_token_array = $adapter->getAccessToken();
                $user_profile = $adapter->getUserProfile();
                $user['access_token'] = $access_token_array['access_token'];
                $user['email'] = $user_profile->email;
                $user['profileURL'] = $user_profile->profileURL;
                $user['displayName'] = $user_profile->displayName;
                $user['firstName'] = $user_profile->firstName;
                $user['lastName'] = $user_profile->lastName;
            }
            $app['session']->set('user', $user);
            return $app->redirect('/');
        }
        return $app->redirect('/login');
    }

    public function loginAction(Application $app) {
        return $app['twig']->render('login.twig', array());
    }

    public function logoutAction(Application $app) {
        $app['session']->clear();
        return $app->redirect('/login');
    }

    public function userDetailsAction(Application $app) {
        $user = $app['session']->get('user');
        if (NULL == $user) {
            $app->abort(401, "User not logged in.");
        } else {
            return $app->json(array('displayName' => $user['displayName'], 'api_key' => $user['access_token']), 200);
        }
    }

}
