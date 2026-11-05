<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\TagAction;
use iutnc\touiteur\repository\TouiteRepository;

class Dispatcher
{
    public function run(): string
    {
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
        } else {
            $action = 'home';
        }

        switch ($action) {

            case 'tag':
                $repository = new TouiteRepository();
                $action = new TagAction($repository);
                break;

            default:
                return "<h1>Page en construction</h1>";
        }

        return $action->execute();
    }
}
