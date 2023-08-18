<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-provider-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" modal-info="title"></h5>
                <span data-toggle="tooltip" data-placement="top" 
                    title="<?= _('Complete os campos abaixo para criar/editar um fornecedor.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.7rem;"></i>
                </span>
            </div>

            <div class="modal-body">
                <form id="save-provider">
                    <div class="form-group">
                        <label>
                            <?= _('Nome') ?>
                            <span data-toggle="tooltip" data-placement="top" title='<?= _('Digite o nome do fornecedor.') ?>'>
                                <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                            </span>
                        </label>
                        <input type="text" name="name" class="form-control" maxlength="100" 
                            placeholder="<?= _('Digite o nome do fornecedor...') ?>">
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>

            <div class="modal-footer d-block text-center">
                <input form="save-provider" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal"><?= _('Voltar') ?></button>
            </div>
        </div>
    </div>
</div>
