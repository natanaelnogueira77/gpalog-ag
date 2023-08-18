<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\PDFRender;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Conference;
use Src\Models\ConferenceInput;
use Src\Models\ConferenceInputForm;
use Src\Models\ConferenceOutputForm;
use Src\Models\ConferenceProduct;
use Src\Models\ConferenceProductForm;
use Src\Models\Config;
use Src\Models\Operation;
use Src\Models\Pallet;
use Src\Models\Provider;

class ConferenceController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/conference/index');
    }

    public function input(array $data): void 
    {
        $this->addData();

        $tnConference = Conference::tableName();
        $tnOperation = Operation::tableName();
        $tnProvider = Provider::tableName();

        $dbConferences = (new Conference())->join("{$tnOperation} t2", [
            'raw' => "t2.id = {$tnConference}.ope_id"
        ])->join("{$tnProvider} t3", [
            'raw' => "t3.id = t2.for_id"
        ])->get([
            'in' => ["{$tnConference}.c_status" => [Conference::CS_WAITING, Conference::CS_STARTED]]
        ], "{$tnConference}.*, t2.plate AS plate, t3.name AS provider_name")->fetch(true);

        if($dbConferences) {
            foreach($dbConferences as $dbConference) {
                $dbConference->created_at = $this->getDateTime($dbConference->created_at)->format('d/m/Y');
            }
        }

        $this->render('user/conference/input', [
            'dbConferences' => $dbConferences
        ]);
    }

    public function singleInput(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        $this->addData();

        if(!$dbConference = (new Conference())->findById(intval($data['conference_id']))) {
            $this->session->setFlash('error', _('A conferência não foi encontrada!'));
            $this->redirect('user.conference.index');
        } elseif($dbConference->isFinished()) {
            $this->session->setFlash('error', _('Esta conferência já foi finalizada!'));
            $this->redirect('user.conference.index');
        }

        if($dbConference->isWaiting()) {
            $dbConference->loadData([
                'start_usu_id' => $this->session->getAuth()->id,
                'date_start' => date('Y-m-d H:i:s')
            ])->setAsStarted()->save();
        }

        if($dbOperation = $dbConference->operation()) {
            $dbOperation->provider();
        }

        if($dbConferenceInputs = $dbConference->conferenceInputs()) {
            $dbConferenceInputs = ConferenceInput::withProduct($dbConferenceInputs);
        }

        $conferenceInputForm = new ConferenceInputForm();
        if(isset($data['include_product']) || isset($data['search_product']) || isset($data['is_completed']) || $this->request->isPost()) {
            $conferenceInputForm->has_started = true;
        }

        if(!isset($data['finish_conference'])) {
            if(isset($data['search_product']) || $this->request->isPost()) {
                if(!$dbProduct = $conferenceInputForm->loadData(['barcode' => $data['barcode']])->getProduct()) {
                    $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
                }
            }
        }

        if($this->request->isPost()) {
            if(!isset($data['finish_conference'])) {
                if(!$conferenceInputForm->loadData(['package' => $dbProduct->emb_fb] + $data)->complete()) {
                    $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
                }
    
                if(isset($data['is_completed'])) {
                    $dbConferenceInput = (new ConferenceInput())->loadData([
                        'con_id' => $dbConference->id,
                        'usu_id' => $this->session->getAuth()->id,
                        'barcode' => $conferenceInputForm->barcode,
                        'pro_id' => $dbProduct->id, 
                        'package' => $conferenceInputForm->package, 
                        'physic_boxes_amount' => $conferenceInputForm->physic_boxes_amount, 
                        'closed_plts_amount' => $conferenceInputForm->closed_plts_amount, 
                        'units_amount' => $conferenceInputForm->physic_boxes_amount * $conferenceInputForm->package, 
                        'service_type' => $conferenceInputForm->service_type, 
                        'pallet_height' => $conferenceInputForm->pallet_height
                    ]);
    
                    if(!$dbConferenceInput->save()) {
                        $this->session->setFlash('error', _('Lamentamos, mas ocorreu um erro na requisição!'));
                        $this->redirect('user.conference.singleInput', ['conference_id' => $dbConference->id]);
                    } else {
                        $this->session->setFlash(
                            'success', 
                            sprintf(
                                _('A entrada do produto "%s" na conferência de ID %s foi feita com sucesso!'), 
                                $dbProduct->name, $dbConference->id
                            )
                        );
                        $this->redirect('user.conference.singleInput', ['conference_id' => $dbConference->id]);
                    }
                }
            } else {
                $dbConference->loadData([
                    'end_usu_id' => $this->session->getAuth()->id, 
                    'date_end' => date('Y-m-d H:i:s')
                ]);

                if(!$dbPallets = $dbConference->generatePallets()) {
                    $this->session->setFlash('error', _('Não há pallets para serem armazenados!'));
                    $this->redirect('user.conference.singleInput', ['conference_id' => $dbConference->id]);
                }

                if(!Pallet::hasAllocationForAll($dbPallets)) {
                    $this->session->setFlash('error', _('Não há posições suficientes para alocar todos os pallets!'));
                    $this->redirect('user.conference.singleInput', ['conference_id' => $dbConference->id]);
                }

                if(!Pallet::allocateMany($dbPallets) || !$dbConference->setAsFinished()->save()) {
                    $this->session->setFlash('error', _('Lamentamos, mas ocorreu um erro na requisição!'));
                    $this->redirect('user.conference.singleInput', ['conference_id' => $dbConference->id]);
                } else {
                    $this->session->setFlash('success', sprintf(_('A conferência de ID %s foi finalizada com sucesso!'), $dbConference->id));
                    $this->redirect('user.conference.getInputPDF', ['conference_id' => $dbConference->id]);
                }
            }
        }

        $dbConference->created_at = $this->getDateTime($dbConference->created_at)->format('d/m/Y');

        $this->render('user/conference/single-input', [
            'dbConference' => $dbConference,
            'dbOperation' => $dbOperation,
            'dbProduct' => $dbProduct,
            'dbConferenceInputs' => $dbConferenceInputs,
            'conferenceInputForm' => $conferenceInputForm,
            'serviceTypes' => ConferenceInput::getServiceTypes()
        ]);
    }

    public function output(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        $this->addData();

        $nextStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
        $previousStep = 0;

        $conferenceOutputForm = (new ConferenceOutputForm())->loadData($data);
        if($conferenceOutputForm->isOnServiceOrder() || $conferenceOutputForm->isOnPallet() 
            || $conferenceOutputForm->isOnCompletion() || $conferenceOutputForm->isOnExpedition()) {
            if(!$dbOperation = $conferenceOutputForm->getOperation()) {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
                $nextStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
                $previousStep = 0;
            } else {
                $nextStep = ConferenceOutputForm::STEP_PALLET;
                $previousStep = 0;
            }
        }

        if($conferenceOutputForm->isOnPallet() || $conferenceOutputForm->isOnCompletion() 
            || $conferenceOutputForm->isOnExpedition()) {
            if(!$dbPallet = $conferenceOutputForm->getPallet()) {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
                $nextStep = ConferenceOutputForm::STEP_PALLET;
                $previousStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
            } else {
                $nextStep = ConferenceOutputForm::STEP_COMPLETION;
                $previousStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
            }
        }

        if($conferenceOutputForm->isOnCompletion() || $conferenceOutputForm->isOnExpedition()) {
            if(!$conferenceOutputForm->validateCompletion()) {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
                $nextStep = ConferenceOutputForm::STEP_COMPLETION;
                $previousStep = ConferenceOutputForm::STEP_PALLET;
            } else {
                $nextStep = ConferenceOutputForm::STEP_EXPEDITION;
                $previousStep = ConferenceOutputForm::STEP_PALLET;
            }
        }

        if($this->request->isPost() && $conferenceOutputForm->isOnExpedition()) {
            $dbPallet->loadData([
                'release_usu_id' => $this->session->getAuth()->id,
                'release_date' => date('Y-m-d H:i:s'),
                'load_plate' => $conferenceOutputForm->load_plate,
                'dock' => $conferenceOutputForm->dock
            ]);
            if(!$dbPallet->setAsReleased()->save()) {
                $this->session->setFlash('error', _('Lamentamos, mas ocorreu um erro na requisição!'));
            } else {
                $this->session->setFlash('success', _('O pallet foi expedido com sucesso!'));
                $this->redirect('user.conference.getOutputPDF', ['pallet_id' => $dbPallet->id]);
            }
        }

        $this->render('user/conference/output', [
            'dbOperation' => $dbOperation,
            'dbPallet' => $dbPallet,
            'dbProduct' => $dbPallet ? $dbPallet->product() : null,
            'conferenceOutputForm' => $conferenceOutputForm,
            'nextStep' => $nextStep,
            'previousStep' => $previousStep
        ]);
    }

    public function getInputPDF(array $data): void 
    {
        $this->addData();

        if(!$dbConference = (new Conference())->findById(intval($data['conference_id']))) {
            $this->session->setFlash('error', _('A conferência não foi encontrada!'));
            $this->redirect('user.conference.index');
        } elseif(!$dbConference->isFinished()) {
            $this->session->setFlash('error', _('Esta conferência ainda não foi finalizada!'));
            $this->redirect('user.conference.index');
        }

        if($dbPallets = $dbConference->pallets()) {
            $dbPallets = Pallet::withProduct($dbPallets);
        }

        $filename = sprintf(_('etiqueta-de-entrada-%s'), $dbConference->id) . '.pdf';

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment');
        header("filename: {$filename}");

        $html = $this->getView('user/conference/components/input-pdf', [
            'dbPallets' => $dbPallets,
            'dbOperation' => $dbConference->operation(),
            'logo' => url((new Config())->getMeta('logo'))
        ]);

        $PDFRender = new PDFRender();
        if(!$PDFRender->loadHtml($html)->setPaper('A4', 'portrait')->render()) {
            $this->session->setFlash('error', _('Lamentamos, mas o PDF não pôde ser gerado!'));
            $this->redirect('user.conference.index');
        }

        $dompdf = $PDFRender->getDompdf();
        $dompdf->stream($filename, ['Attachment' => false]);
    }

    public function getOutputPDF(array $data): void 
    {
        $this->addData();

        if(!$dbPallet = (new Pallet())->findById(intval($data['pallet_id']))) {
            $this->session->setFlash('error', _('O pallet não foi encontrado!'));
            $this->redirect('user.conference.index');
        } elseif(!$dbPallet->isReleased()) {
            $this->session->setFlash('error', _('Este pallet ainda não foi liberado!'));
            $this->redirect('user.conference.index');
        }

        $filename = sprintf(_('etiqueta-%s'), $dbPallet->code) . '.pdf';

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment');
        header("filename: {$filename}");

        $html = $this->getView('user/conference/components/output-pdf', [
            'dbPallet' => $dbPallet,
            'dbProduct' => $dbPallet->product(),
            'dbOperation' => $dbPallet->conference()?->operation(),
            'logo' => url((new Config())->getMeta('logo'))
        ]);

        $PDFRender = new PDFRender();
        if(!$PDFRender->loadHtml($html)->setPaper('A4', 'portrait')->render()) {
            $this->session->setFlash('error', _('Lamentamos, mas o PDF não pôde ser gerado!'));
            $this->redirect('user.conference.index');
        }

        $dompdf = $PDFRender->getDompdf();
        $dompdf->stream($filename, ['Attachment' => false]);
    }
}