<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use GTG\MVC\Components\PDFRender;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Config;
use Src\Models\Operation;
use Src\Models\Output;
use Src\Models\OutputForm;
use Src\Models\Pallet;
use Src\Models\Product;
use Src\Models\Provider;
use Src\Models\User;
use Src\Utils\ErrorMessages;

class OutputsController extends TemplateController 
{
    public function index(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        $this->addData();

        $outputForm = new OutputForm();
        if(isset($data['order_number'])) {
            if(!$dbOperation = $outputForm->loadData(['order_number' => $data['order_number']])->getOperation()) {
                $this->session->setFlash('error', ErrorMessages::form());
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
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbOutput->getFirstErrors())->APIResponse([], 422);
            return;
        }

        if($dbPallets = (new Pallet())->get(['in' => ['id' => $data['pallets']]])->fetch(true)) {
            foreach($dbPallets as $dbPallet) {
                $dbPallet->sai_id = $dbOutput->id;
                $dbPallet->setAsSeparated();
            }

            if(!Pallet::saveMany($dbPallets)) {
                $this->setMessage('error', ErrorMessages::requisition())->APIResponse([], 422);
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

        $filename = sprintf(_('Saída de Pallets - ID %s'), $dbOutput->id) . '.pdf';

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment');
        header("filename: {$filename}");

        $html = $this->getView('user/outputs/_components/pdf', [
            'dbOperation' => $dbOperation,
            'dbOutput' => $dbOutput,
            'dbPallets' => $dbPallets,
            'logo' => url((new Config())->getMeta(Config::KEY_LOGO))
        ]);

        $PDFRender = new PDFRender();
        if(!$PDFRender->loadHtml($html)->setPaper('A4', 'portrait')->render()) {
            $this->session->setFlash('error', ErrorMessages::pdf());
            $this->redirect('user.outputs.index');
        }

        $dompdf = $PDFRender->getDompdf();
        $dompdf->stream($filename, ['Attachment' => false]);
    }

    public function export(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $excelData = [];

        $tnOutput = Output::tableName();
        $tnOperation = Operation::tableName();
        $tnPallet = Pallet::tableName();
        $tnProduct = Product::tableName();
        $tnProvider = Provider::tableName();
        $tnUser = User::tableName();

        $dbOutputs = (new Output())->join("{$tnOperation} t2", [
            'raw' => "t2.id = {$tnOutput}.ope_id"
        ])->join("{$tnUser} t3", [
            'raw' => "t3.id = {$tnOutput}.usu_id"
        ])->leftJoin("{$tnPallet} t4", [
            'raw' => "t4.sai_id = {$tnOutput}.id"
        ])->leftJoin("{$tnUser} t5", [
            'raw' => "t5.id = t4.release_usu_id"
        ])->leftJoin("{$tnProduct} t6", [
            'raw' => "t6.id = t4.pro_id"
        ])->get([], "
            {$tnOutput}.*,
            t2.id AS operation_id,
            t2.plate AS operation_plate,
            t3.name AS adm_name,
            t4.created_at AS p_created_at,
            t4.code AS p_code,
            t4.package AS p_package,
            t4.physic_boxes_amount AS p_physic_boxes_amount,
            t4.units_amount AS p_units_amount,
            t4.service_type AS p_service_type,
            t4.pallet_height AS p_pallet_height,
            t4.street_number AS p_street_number,
            t4.position AS p_position,
            t4.height AS p_height,
            t4.sai_id AS p_sai_id,
            t4.release_date AS p_release_date,
            t4.load_plate AS p_load_plate,
            t4.dock AS p_dock,
            t4.p_status AS p_p_status,
            t5.name AS release_user_name,
            t6.name AS product_name,
            t6.prov_name AS product_prov_name,
            t6.ean AS product_ean
        ")->fetch(true);

        if($dbOutputs) {
            foreach($dbOutputs as $dbOutput) {
                $excelData[] = [
                    _('ID de Separação') => $dbOutput->operation_plate,
                    _('ADM') => $dbOutput->adm_name,
                    _('ID de Operação') => $dbOutput->operation_id,
                    _('Placa') => $dbOutput->operation_plate,
                    _('Número do Pallet') => $dbOutput->p_code ?? '---',
                    _('Data de Entrada') => $dbOutput->p_created_at 
                        ? $this->getDateTime($dbOutput->p_created_at)->format('d/m/Y') 
                        : '--/--/----',
                    _('Hora de Entrada') => $dbOutput->p_created_at 
                        ? $this->getDateTime($dbOutput->p_created_at)->format('H:i:s') 
                        : '--:--:--',
                    _('Embalagem') => $dbOutput->p_package ?? '---',
                    _('Produto') => $dbOutput->product_name ?? '---',
                    _('Código EAN') => $dbOutput->product_ean ?? '---',
                    _('Fornecedor') => $dbOutput->product_prov_name ?? '---',
                    _('Quantidade de Caixas Físicas') => $dbOutput->p_physic_boxes_amount ?? '---',
                    _('Quantidade de Unidades') => $dbOutput->p_units_amount ?? '---',
                    _('Tipo de Serviço') => Pallet::getServiceTypes()[$dbOutput->p_service_type],
                    _('Altura do Pallet') => $dbOutput->p_pallet_height ?? '---',
                    _('Número da Rua') => $dbOutput->p_street_number ?? '---',
                    _('Posição') => $dbOutput->p_position ?? '---',
                    _('Altura') => $dbOutput->p_height ?? '---',
                    _('ID de Separação') => $dbOutput->p_sai_id ?? '---',
                    _('Operador que fez Saída') => $dbOutput->release_user_name ?? '---',
                    _('Data de Saída') => $dbOutput->p_release_date 
                        ? $this->getDateTime($dbOutput->p_release_date)->format('d/m/Y') 
                        : '--/--/----',
                    _('Hora de Saída') => $dbOutput->p_release_date 
                        ? $this->getDateTime($dbOutput->p_release_date)->format('H:i:s') 
                        : '--:--:--',
                    _('Placa de Carregamento') => $dbOutput->p_load_plate ?? '---',
                    _('Doca') => $dbOutput->p_dock ?? '---',
                    _('Status') => Pallet::getStates()[$dbOutput->p_p_status]
                ];
            }
        }

        $excel = (new ExcelGenerator($excelData, _('Saídas')));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.outputs.index');
        }

        $excel->stream();
    }
}