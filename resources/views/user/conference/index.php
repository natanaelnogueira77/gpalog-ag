<?php 
    $theme->title = sprintf(_('Operação | %s'), $appData['app_name']);
    $this->layout("themes/black-screen/_theme", [
        'theme' => $theme,
        'message' => $message
    ]);
?>

<p><?= _('Operação') ?></p>
<div>
    <input type="button" value="<?= _('Entrada') ?>" onclick="window.location.href='<?= $router->route('user.conference.input') ?>'">
    <input type="button" value="<?= _('Saída') ?>" onclick="window.location.href='<?= $router->route('user.conference.output') ?>'">
    <input type="button" value="<?= _('Sair') ?>" onclick="window.location.href='<?= $router->route('auth.logout') ?>'">
</div>