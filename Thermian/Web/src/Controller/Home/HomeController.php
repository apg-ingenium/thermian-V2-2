<?php
declare(strict_types=1);

namespace Web\Controller\Home;

use Cake\Http\Response;
use Web\Controller\AppController;

class HomeController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['execute']);
    }

    public function execute(): Response
    {
        return $this->render('/Home/home');
    }
}
