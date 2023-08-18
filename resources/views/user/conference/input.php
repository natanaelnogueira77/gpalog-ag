<?php 
    $this->layout("themes/black-screen/_theme", [
        'title' => sprintf(_('Entrada | %s'), $appData['app_name'])
    ]);
?>

<p><?= _('Entrada') ?></p>

<div>
    <input form="return" type="submit" value="<?= _('Voltar') ?>">
    <input form="logout" type="submit" value="<?= _('Sair') ?>">
</div>

<form id="return" action="<?= $router->route('user.conference.index') ?>" method="get"></form>
<form id="logout" action="<?= $router->route('auth.logout') ?>" method="get"></form>

<table>
    <thead>
        <th><?= _('ID') ?></th>
        <th><?= _('Placa') ?></th>
        <th><?= _('Fornecedor') ?></th>
        <th><?= _('Data') ?></th>
        <th><?= _('Ação') ?></th>
    </thead>
    <tbody>
        <?php
        if($dbConferences):
            foreach($dbConferences as $dbConference): 
            ?>
            <form id="form_<?= $dbConference->id ?>" method="get" 
                action="<?= $router->route('user.conference.singleInput', ['conference_id' => $dbConference->id]) ?>"></form>
            <tr>
                <td><?= $dbConference->id ?></td>
                <td><?= $dbConference->plate ?></td>
                <td><?= $dbConference->provider_name ?></td>
                <td><?= $dbConference->created_at ?></td>
                <td>
                    <input form="form_<?= $dbConference->id ?>" type="submit" value="<?= _('Ir Conf.') ?>">
                </td>
            </tr>
            <?php 
            endforeach;
        endif;
        ?>
    </tbody>
</table>