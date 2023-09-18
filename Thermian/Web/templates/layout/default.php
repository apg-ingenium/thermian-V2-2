<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var Web\View\AppView $this
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RA4PV</title>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700|Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap"
          rel="stylesheet">

    <?= $this->Html->css("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css", ['block' => 'css']); ?>
    <?= $this->Html->css(['normalize.min', 'milligram.min', 'menu', 'thermian']) ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body>
<input class="show-menu-button" type="checkbox" value="show-menu" id="show-menu">
<aside class="side-bar">
    <label class="show-menu-label" for="show-menu"><span>RA4PV</span><i class="bi bi-chevron-double-down"></i></label>
    <nav class="menu">
        <a class="menu-item" href="/home">Home</a>
        <a class="menu-item" href="/hotspots/datasets"="">Datasets</a>
        <a class="menu-item" href="/hotspots/analysis/all"="">Analysis</a>
        <div class="sub-menu">
            <input type="checkbox" value="expand" id="account"/>
            <label class="menu-item" for="account">Account</label>
            <div class="menu-options">
                <a class="menu-item" href="/users/create">Register</a>
                <a class="menu-item" href="/users/login">Login</a>
                <a class="menu-item" href="/users/logout">Logout</a>
            </div>
        </div>
    </nav>
</aside>
<main>
    <div class="container">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </div>
</main>
<?= $this->fetch('script') ?>
</body>
</html>
