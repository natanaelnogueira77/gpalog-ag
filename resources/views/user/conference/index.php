<?php 
    $this->layout("themes/black-screen/_theme", [
        'title' => sprintf(_('Operação | %s'), $appData['app_name'])
    ]);
?>

<p><?= _('Operação') ?></p>
<div>
    <input form="input" type="submit" value="<?= _('Entrada') ?>">
    <input form="output" type="submit" value="<?= _('Saída') ?>">
    <input form="logout" type="submit" value="<?= _('Sair') ?>">
</div>

<form id="input" action="<?= $router->route('user.conference.input') ?>" method="get"></form>
<form id="output" action="<?= $router->route('user.conference.output') ?>" method="get"></form>
<form id="logout" action="<?= $router->route('auth.logout') ?>" method="get"></form>