<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\PDFRender;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Config;
use Src\Models\Operation;
use Src\Models\Output;
use Src\Models\OutputForm;
use Src\Models\Pallet;

class OutputsController extends TemplateController 
{
    public function index(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        $this->addData();

        $outputForm = new OutputForm();
        if(isset($data['order_number'])) {
            if(!$dbOperation = $outputForm->loadData(['order_number' => $data['order_number']])->getOperation()) {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
            } elseif(!$dbConference = $dbOperation->conference()) {
                $this->session->setFlash('error', _('A operação desta ordem de serviço não foi conferida ainda!'));
            } elseif(!$dbConference->isFinished()) {
                $this->session->setFlash('error', _('A conferência dessa operação ainda não foi finalizada!'));
            } elseif(!$dbPallets = $dbConference->pallets(['p_status' => Pallet::PS_STORED])) {
                $this->session->setFlash('error', _('Não foi encontrado nenhum pallet para separação!'));
            } else {
                $dbPallets = Pallet::withProduct($dbPallets);
            }
        }

        $this->render('user/outputs/index', [
            'outputForm' => $outputForm,
            'dbOperation' => $dbOperation,
            'dbConference' => $dbConference,
            'dbPallets' => $dbPallets
        ]);
    }

    public function store(array $data): void 
    {
        if(!$dbOperation = (new Operation())->findById(intval($data['operation_id']))) {
            $this->setMessage('error', _('A operação não foi encontrada!'))->APIResponse([], 400);
            return;
        }

        if(!isset($data['pallets']) || !is_array($data['pallets']) || count($data['pallets']) === 0) {
            $this->setMessage('error', _('Você precisa selecionar ao menos um pallet para separação!'))->APIResponse([], 422);
            return;
        }

        $dbOutput = new Output();
        if(!$dbOutput->loadData(['usu_id' => $this->session->getAuth()->id, 'ope_id' => $dbOperation->id])->save()) {
            $this->setMessage('error', _('Erros de validação! Verifique os campos.'))
                ->setErrors($dbOutput->getFirstErrors())->APIResponse([], 422);
            return;
        }

        if($dbPallets = (new Pallet())->get(['in' => ['id' => $data['pallets']]])->fetch(true)) {
            foreach($dbPallets as $dbPallet) {
                $dbPallet->sai_id = $dbOutput->id;
                $dbPallet->setAsSeparated();
            }

            if(!Pallet::saveMany($dbPallets)) {
                $this->setMessage('error', _('Lamentamos, mas ocorreu algum erro na requisição!'))->APIResponse([], 422);
                return;
            }
        }

        $this->setMessage('success', _('Os pallets selecionados foram separados para saída com sucesso!'));
        $this->APIResponse([
            'pdf' => $this->getRoute('user.outputs.getPDF', [
                'operation_id' => $dbOperation->id,
                'output_id' => $dbOutput->id
            ])
        ], 200);
    }

    public function getPDF(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        $this->addData();

        if(!$dbOperation = (new Operation())->findById(intval($data['operation_id']))) {
            $this->session->setFlash('error', _('A operação não foi encontrada!'));
            $this->redirect('user.outputs.index');
        } elseif(!$dbOutput = (new Output())->findById(intval($data['output_id']))) {
            $this->session->setFlash('error', _('A saída não foi encontrada!'));
            $this->redirect('user.outputs.index');
        }

        if($dbPallets = $dbOutput->pallets()) {
            $dbPallets = Pallet::withProduct($dbPallets);
        }

        $filename = sprintf(_('saida-de-pallets-%s'), $dbOutput->id) . '.pdf';

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment');
        header("filename: {$filename}");

        $html = $this->getView('user/outputs/components/pdf', [
            'dbOperation' => $dbOperation,
            'dbOutput' => $dbOutput,
            'dbPallets' => $dbPallets,
            'logo' => url((new Config())->getMeta('logo'))
        ]);

        $PDFRender = new PDFRender();
        if(!$PDFRender->loadHtml($html)->setPaper('A4', 'portrait')->render()) {
            $this->session->setFlash('error', _('Lamentamos, mas o PDF não pôde ser gerado!'));
            $this->redirect('user.outputs.index');
        }

        $dompdf = $PDFRender->getDompdf();
        $dompdf->stream($filename, ['Attachment' => false]);
    }
}