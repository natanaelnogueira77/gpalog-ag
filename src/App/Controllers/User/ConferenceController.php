<?php

namespace Src\App\Controllers\User;

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
use Src\Utils\ErrorMessages;

class ConferenceController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/conference/index', [
            'message' => $this->getFeedbackMessage()
        ]);
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
            $dbConferences = Conference::withOperation($dbConferences);
            foreach($dbConferences as $dbConference) {
                $dbConference->created_at = $dbConference->getCreatedAtDateTime()->format('d/m/Y');
            }
        }

        $this->render('user/conference/input', [
            'dbConferences' => $dbConferences,
            'message' => $this->getFeedbackMessage()
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
                    $this->session->setFlash('error', ErrorMessages::form());
                }
            }
        }

        if($this->request->isPost()) {
            if(!isset($data['finish_conference'])) {
                $conferenceInputForm->loadData([
                    'package' => $dbProduct->emb_fb,
                    'barcode' => $data['barcode'] ? $data['barcode'] : null,
                    'physic_boxes_amount' => $data['physic_boxes_amount'] ? $data['physic_boxes_amount'] : null,
                    'closed_plts_amount' => $data['closed_plts_amount'] ? $data['closed_plts_amount'] : null,
                    'service_type' => $data['service_type'] ? $data['service_type'] : null,
                    'pallet_height' => $data['pallet_height'] ? floatval($data['pallet_height']) : null
                ]);
                if(!$conferenceInputForm->complete()) {
                    $this->session->setFlash('error', ErrorMessages::form());
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
                        $this->session->setFlash('error', ErrorMessages::requisition());
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

                if(!Pallet::allocateMany($dbPallets) || !$dbConference->setAsFinished()->save()) {
                    $this->session->setFlash('error', ErrorMessages::requisition());
                    $this->redirect('user.conference.singleInput', ['conference_id' => $dbConference->id]);
                } else {
                    $this->session->setFlash('success', sprintf(_('A conferência de ID %s foi finalizada com sucesso!'), $dbConference->id));
                    $this->redirect('user.conference.input');
                }
            }
        }

        if(!$conferenceInputForm->hasStarted()) {
            $return = $this->getRoute('user.conference.input');
        } elseif(!$conferenceInputForm->hasProduct()) {
            $return = $this->getRoute('user.conference.singleInput', ['conference_id' => $dbConference->id]);
        } elseif(!$conferenceInputForm->isCompleted()) {
            $return = $this->getRoute('user.conference.singleInput', [
                'conference_id' => $dbConference->id,
                'include_product' => true
            ]);
        } else {
            $return = $this->getRoute('user.conference.singleInput', [
                'conference_id' => $dbConference->id,
                'search_product' => true,
                'barcode' => $conferenceInputForm->barcode
            ]);
        }

        $dbConference->created_at = $dbConference->getCreatedAtDateTime()->format('d/m/Y');

        $this->render('user/conference/single-input', [
            'dbConference' => $dbConference,
            'dbOperation' => $dbOperation,
            'dbProduct' => $dbProduct,
            'dbConferenceInputs' => $dbConferenceInputs,
            'conferenceInputForm' => $conferenceInputForm,
            'serviceTypes' => ConferenceInput::getServiceTypes(),
            'return' => $return,
            'message' => $this->getFeedbackMessage()
        ]);
    }

    public function inputProducts(array $data): void 
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

        if($dbOperation = $dbConference->operation()) {
            $dbOperation->provider();
        }

        if($dbConferenceInputs = $dbConference->conferenceInputs()) {
            $dbConferenceInputs = ConferenceInput::withProduct($dbConferenceInputs);
        }

        $dbConference->created_at = $dbConference->getCreatedAtDateTime()->format('d/m/Y');

        $this->render('user/conference/input-products', [
            'dbConference' => $dbConference,
            'dbOperation' => $dbOperation,
            'dbConferenceInputs' => $dbConferenceInputs,
            'message' => $this->getFeedbackMessage()
        ]);
    }

    public function output(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));
        $this->addData();

        $nextStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
        $previousStep = 0;

        $conferenceOutputForm = (new ConferenceOutputForm())->loadData([
            'service_order' => $data['service_order'] ? $data['service_order'] : null,
            'pallet_number' => $data['pallet_number'] ? $data['pallet_number'] : null,
            'load_plate' => $data['load_plate'] ? $data['load_plate'] : null,
            'dock' => $data['dock'] ? $data['dock'] : null,
            'step' => intval($data['step']),
            'has_service_order' => $data['has_service_order'] ? true : false,
            'has_pallet' => $data['has_pallet'] ? true : false,
            'has_completion' => $data['has_completion'] ? true : false
        ]);
        if($conferenceOutputForm->isOnServiceOrder() || $conferenceOutputForm->isOnPallet() 
            || $conferenceOutputForm->isOnCompletion() || $conferenceOutputForm->isOnExpedition()) {
            if(!$dbOperation = $conferenceOutputForm->getOperation()) {
                $this->session->setFlash('error', ErrorMessages::form());
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
                $this->session->setFlash('error', ErrorMessages::form());
                $nextStep = ConferenceOutputForm::STEP_PALLET;
                $previousStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
            } else {
                $nextStep = ConferenceOutputForm::STEP_COMPLETION;
                $previousStep = ConferenceOutputForm::STEP_SERVICE_ORDER;
            }
        }

        if($conferenceOutputForm->isOnCompletion() || $conferenceOutputForm->isOnExpedition()) {
            if(!$conferenceOutputForm->validateCompletion()) {
                $this->session->setFlash('error', ErrorMessages::form());
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
                $this->session->setFlash('error', ErrorMessages::requisition());
            } else {
                $this->session->setFlash('success', _('O pallet foi expedido com sucesso!'));
                $this->redirect('user.conference.output', [
                    'step' => ConferenceOutputForm::STEP_SERVICE_ORDER, 
                    'service_order' => $conferenceOutputForm->service_order
                ]);
            }
        }

        $this->render('user/conference/output', [
            'dbOperation' => $dbOperation,
            'dbPallet' => $dbPallet,
            'dbProduct' => $dbPallet ? $dbPallet->product() : null,
            'conferenceOutputForm' => $conferenceOutputForm,
            'nextStep' => $nextStep,
            'previousStep' => $previousStep,
            'message' => $this->getFeedbackMessage()
        ]);
    }

    private function getFeedbackMessage(): ?array 
    {
        if($message = $this->session->getFlash('error')) {
            return [
                'type' => 'error',
                'message' => $message
            ];
        } elseif($message = $this->session->getFlash('success')) {
            return [
                'type' => 'success',
                'message' => $message
            ];
        }

        return null;
    }
}