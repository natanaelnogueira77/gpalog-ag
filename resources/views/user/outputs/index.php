<?php 
    $theme->title = sprintf(_('Saídas | %s'), $appData['app_name']);
    $this->layout("themes/architect-ui/_theme", ['theme' => $theme]);

    $this->insert('themes/architect-ui/_components/title', [
        'title' => _('Saídas de Pallet'),
        'subtitle' => _('Selecione uma ordem de serviço para fazer separação de pallets para saída'),
        'icon' => 'pe-7s-next-2',
        'icon_color' => 'bg-malibu-beach'
    ]);
?>

<div class="card shadow mb-4 br-15">
    <div class="card-header-tab card-header-tab-animation card-header brt-15">    
        <div class="card-header-title">
            <i class="header-icon icofont-logout icon-gradient bg-info"> </i>
            <?= _('Saídas') ?>
        </div>

        <div class="btn-actions-pane-right">
            <div role="group" class="btn-group-sm btn-group">
                <a class="btn btn-lg btn-outline-success" href="<?= $router->route('user.outputs.export') ?>"
                    target="_blank">
                    <i class="icofont-file-excel"></i>
                    <?= _('Exportar Excel') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="<?= $router->route('user.outputs.index') ?>" method="get">
            <div class="form-group">
                <label>
                    <?= _('Ordem de Serviço') ?> 
                    <span data-toggle="tooltip" data-placement="top" 
                        title='<?= _('Digite o número da ordem de serviço para ter a relação dos pallets, e clique em "Buscar".') ?>'>
                        <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                    </span>
                </label>
                <div class="input-group">
                    <input type="text" id="order_number" name="order_number" value="<?= $outputForm->order_number ?>" 
                        class="form-control <?= $outputForm->hasError('order_number') ? 'is-invalid' : '' ?>" 
                        placeholder="<?= _('Digite a ordem de serviço...') ?>">
                    <div class="input-group-append">
                        <input type="submit" class="btn btn-sm btn-success" value="<?= _('Buscar') ?>">
                    </div>
                    <div class="invalid-feedback">
                        <?= $outputForm->hasError('order_number') ? $outputForm->getFirstError('order_number') : '' ?>
                    </div>
                </div>
            </div>
        </form>

        <?php if($dbPallets): ?>
        <form id="pallets-list" action="<?= $router->route('user.outputs.store', ['operation_id' => $dbOperation->id]) ?>" method="post">
            <div class="table-responsive-lg">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <th class="align-middle">
                            <input type="checkbox" id="include-all" class="form-control">
                        </th>
                        <th class="align-middle"><?= _('Nº do Pallet') ?></th>
                        <th class="align-middle"><?= _('Produto') ?></th>
                        <th class="align-middle"><?= _('Código EAN') ?></th>
                        <th class="align-middle"><?= _('Rua') ?></th>
                        <th class="align-middle"><?= _('Posição') ?></th>
                        <th class="align-middle"><?= _('Altura') ?></th>
                        <th class="align-middle"><?= _('Serviço') ?></th>
                    </thead>
                    <tbody>
                        <?php foreach($dbPallets as $dbPallet): ?>
                        <tr>
                            <td class="text-center align-middle">
                                <input type="checkbox" name="pallets[]" class="form-control" value="<?= $dbPallet->id ?>">
                            </td>
                            <td class="align-middle"><?= $dbPallet->code ?></td>
                            <td class="align-middle"><?= $dbPallet->product->name ?></td>
                            <td class="align-middle"><?= $dbPallet->product->ean ?></td>
                            <td class="align-middle"><?= $dbPallet->street_number ?></td>
                            <td class="align-middle"><?= $dbPallet->position ?></td>
                            <td class="align-middle"><?= $dbPallet->height ?></td>
                            <td class="align-middle"><?= $dbPallet->getServiceType() ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <?php if($dbPallets): ?>
    <div class="card-footer d-block text-center brb-15">
        <input type="submit" form="pallets-list" class="btn btn-lg btn-success" value="<?= _('Gerar Lista de Separação') ?>">
    </div>
    <?php endif; ?>
</div>

<?php 
    $this->start('scripts'); 
    $this->insert('user/outputs/_scripts/index.js');
    $this->end(); 
?>