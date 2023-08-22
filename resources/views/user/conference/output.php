<?php 
    $this->layout("themes/black-screen/_theme", [
        'title' => sprintf(_('Saída | %s'), $appData['app_name'])
    ]);
?>

<p><?= _('Saída') ?></p>
<div>
    <input form="save-output" type="hidden" name="step" value="<?= $nextStep ?>">
    
    <?php if(!$conferenceOutputForm->hasServiceOrder()): ?>
    <input form="save-output" type="submit" value="<?= _('Buscar') ?>">
    <?php elseif(!$conferenceOutputForm->hasPallet()): ?>
    <input form="save-output" type="submit" value="<?= _('Inserir') ?>">
    <?php elseif(!$conferenceOutputForm->hasCompletion()): ?>
    <input form="save-output" type="submit" value="<?= _('Inserir Placa') ?>">
    <?php else: ?>
    <input form="save-output" type="submit" value="<?= _('Baixar Etiqueta') ?>">
    <?php endif; ?>

    <input form="return" type="submit" value="<?= _('Voltar') ?>">
</div>

<form id="return" action="<?= !$conferenceOutputForm->hasServiceOrder() 
    ? $router->route('user.conference.index') 
    : $router->route('user.conference.output') ?>" method="get">
    <input type="hidden" name="step" value="<?= $previousStep ?>">

    <?php if($conferenceOutputForm->hasPallet()): ?>
    <input type="hidden" name="service_order" value="<?= $conferenceOutputForm->service_order ?>">
    <?php endif; ?>
    
    <?php if($conferenceOutputForm->hasCompletion()): ?>
    <input type="hidden" name="pallet_number" value="<?= $conferenceOutputForm->pallet_number ?>">
    <?php endif; ?>
</form>
<br>

<form id="save-output" action="<?= $router->route('user.conference.output') ?>" 
    method="<?= $conferenceOutputForm->hasCompletion() ? 'post' : 'get' ?>">
    <table>
        <tbody>
            <tr>
                <td><?= _('Ordem de Serviço') ?></td>
                <td>
                    <?php if(!$conferenceOutputForm->hasServiceOrder()): ?>
                    <input type="text" name="service_order" value="<?= $conferenceOutputForm->service_order ?>" style="max-width: 100px;">
                    <br>
                    <small style="color: red;">
                        <?= $conferenceOutputForm->hasError('service_order') ? $conferenceOutputForm->getFirstError('service_order') : '' ?>
                    </small>
                    <?php else: ?>
                    <input type="hidden" name="service_order" value="<?= $conferenceOutputForm->service_order ?>">
                    <?= $conferenceOutputForm->service_order ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if($conferenceOutputForm->hasServiceOrder()): ?>
            <tr>
                <td><?= _('Número do Pallet') ?></td>
                <td>
                    <?php if(!$conferenceOutputForm->hasPallet()): ?>
                    <input type="text" name="pallet_number" value="<?= $conferenceOutputForm->pallet_number ?>" style="max-width: 100px;">
                    <br>
                    <small style="color: red;">
                        <?= $conferenceOutputForm->hasError('pallet_number') ? $conferenceOutputForm->getFirstError('pallet_number') : '' ?>
                    </small>
                    <?php else: ?>
                    <input type="hidden" name="pallet_number" value="<?= $conferenceOutputForm->pallet_number ?>">
                    <?= $conferenceOutputForm->pallet_number ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if($conferenceOutputForm->hasPallet()): ?>
            <tr>
                <td><?= _('Código de Barras') ?></td>
                <td><?= $dbProduct->ean ?></td>
            </tr>
            <tr>
                <td><?= _('Nome do Produto') ?></td>
                <td><?= $dbProduct->name ?></td>
            </tr>
            <tr>
                <td><?= _('Placa de Carregamento') ?></td>
                <td>
                    <?php if(!$conferenceOutputForm->hasCompletion()): ?>
                    <input type="text" name="load_plate" value="<?= $conferenceOutputForm->load_plate ?>" style="max-width: 100px;">
                    <br>
                    <small style="color: red;">
                        <?= $conferenceOutputForm->hasError('load_plate') ? $conferenceOutputForm->getFirstError('load_plate') : '' ?>
                    </small>
                    <?php else: ?>
                    <input type="hidden" name="load_plate" value="<?= $conferenceOutputForm->load_plate ?>">
                    <?= $conferenceOutputForm->load_plate ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><?= _('Doca') ?></td>
                <td>
                    <?php if(!$conferenceOutputForm->hasCompletion()): ?>
                    <input type="text" name="dock" value="<?= $conferenceOutputForm->dock ?>" style="max-width: 100px;">
                    <br>
                    <small style="color: red;">
                        <?= $conferenceOutputForm->hasError('dock') ? $conferenceOutputForm->getFirstError('dock') : '' ?>
                    </small>
                    <?php else: ?>
                    <input type="hidden" name="dock" value="<?= $conferenceOutputForm->dock ?>">
                    <?= $conferenceOutputForm->dock ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php endif; ?>
        </tbody>
    </table>
</form>