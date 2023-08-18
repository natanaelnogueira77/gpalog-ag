<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-product-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" modal-info="title"></h5>
                <span data-toggle="tooltip" data-placement="top" 
                    title="<?= _('Complete os campos abaixo para criar/editar um produto.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.7rem;"></i>
                </span>
            </div>

            <div class="modal-body">
                <form id="save-product">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="prod_id"><?= _('ID do Produto') ?></label>
                            <input type="number" name="prod_id" placeholder="<?= _('Informe o ID do produto...') ?>" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="name"><?= _('Nome do Produto') ?></label>
                            <input type="text" name="name" placeholder="<?= _('Informe o nome do produto...') ?>" 
                                class="form-control" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="prov_id"><?= _('ID do Fornecedor') ?></label>
                            <input type="number" name="prov_id" placeholder="<?= _('Informe o ID do fornecedor...') ?>" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="prov_name"><?= _('Nome do Fornecedor') ?></label>
                            <input type="text" name="prov_name" placeholder="<?= _('Informe o nome do fornecedor...') ?>"
                                class="form-control" maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="ean"><?= _('EAN') ?></label>
                            <input type="text" name="ean" placeholder="<?= _('Digite o código EAN...') ?>" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="dun14"><?= _('Dun14') ?></label>
                            <input type="text" name="dun14" placeholder="<?= _('Digite o código Dun14...') ?>" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="plu"><?= _('PLU') ?></label>
                            <input type="text" name="plu" placeholder="<?= _('Digite o código PLU...') ?>" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <h5 class="card-title"><?= _('Medidas') ?></h5>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="emb_fb"><?= _('Emb Fb') ?></label>
                            <input type="number" name="emb_fb" placeholder="<?= _('Digite o Emb Fb...') ?>" class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="p_length"><?= _('Comprimento') ?></label>
                            <input type="number" name="p_length" placeholder="<?= _('Digite o comprimento...') ?>" class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="p_width"><?= _('Largura') ?></label>
                            <input type="number" name="p_width" placeholder="<?= _('Digite a largura...') ?>" class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="p_height"><?= _('Altura') ?></label>
                            <input type="number" name="p_height" placeholder="<?= _('Digite a altura...') ?>" class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="p_base"><?= _('Base') ?></label>
                            <input type="number" name="p_base" placeholder="<?= _('Digite a base...') ?>" class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="p_weight"><?= _('Peso') ?></label>
                            <div class="input-group">
                                <input type="number" name="p_weight" placeholder="<?= _('Digite o peso...') ?>" class="form-control" 
                                    step="0.01" min="0">
                                <div class="input-group-append">
                                    <span class="input-group-text">Kg</span>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-product" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal"><?= _('Voltar') ?></button>
            </div>
        </div>
    </div>
</div>
