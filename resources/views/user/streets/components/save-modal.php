<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="save-street-modal" 
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" modal-info="title"></h5>
                <span data-toggle="tooltip" data-placement="top" 
                    title="<?= _('Complete os campos abaixo para criar/editar uma rua.') ?>">
                    <i class="icofont-question-circle" style="font-size: 1.7rem;"></i>
                </span>
            </div>

            <div class="modal-body">
                <form id="save-street">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="street_number">
                                <?= _('Número da Rua') ?> 
                                <span data-toggle="tooltip" data-placement="top" 
                                    title='<?= _('Digite o número da rua.') ?>'>
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="street_number" placeholder="<?= _('Informe o número da rua...') ?>" 
                                class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="start_position">
                                <?= _('Posição Inicial') ?>
                                <span data-toggle="tooltip" data-placement="top" 
                                    title='<?= _('Digite a primeira posição da rua.') ?>'>
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="start_position" placeholder="<?= _('Informe a posição inicial...') ?>" 
                                class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="end_position">
                                <?= _('Posição Final') ?> 
                                <span data-toggle="tooltip" data-placement="top" 
                                    title='<?= _('Digite a última posição da rua.') ?>'>
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="end_position" placeholder="<?= _('Informe a posição final...') ?>" 
                                class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="max_height">
                                <?= _('Altura Máxima') ?> 
                                <span data-toggle="tooltip" data-placement="top" 
                                    title='<?= _('Digite a altura máxima da rua.') ?>'>
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="max_height" placeholder="<?= _('Informe a altura máxima...') ?>" 
                                class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="profile">
                                <?= _('Perfil') ?> 
                                <span data-toggle="tooltip" data-placement="top" 
                                    title='<?= _('Digite o perfil da rua, em metros.') ?>'>
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="profile" placeholder="<?= _('Informe o perfil da rua...') ?>" 
                                class="form-control" step="0.01" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label for="max_plts">
                                <?= _('Capacidade Máxima') ?> 
                                <span data-toggle="tooltip" data-placement="top" 
                                    title='<?= _('Digite a capacidade máxima de pallets para essa rua.') ?>'>
                                    <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                                </span>
                            </label>
                            <input type="number" name="max_plts" placeholder="<?= _('Informe a capacidade máxima da rua...') ?>" 
                                class="form-control" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="obs">
                            <?= _('Observações') ?>
                            <span data-toggle="tooltip" data-placement="top" 
                                title='<?= _('É opcional, uma observação da rua.') ?>'>
                                <i class="icofont-question-circle" style="font-size: 1.1rem;"></i>
                            </span>
                        </label>
                        <textarea name="obs" rows="5" class="form-control" max="500" 
                            placeholder="<?= _('Descreva as observações da rua...') ?>"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer d-block text-center">
                <input form="save-street" type="submit" class="btn btn-success btn-lg" value="<?= _('Salvar') ?>">
                <button type="button" class="btn btn-danger btn-lg" data-bs-dismiss="modal"><?= _('Voltar') ?></button>
            </div>
        </div>
    </div>
</div>
