<?php

namespace Src\App\Controllers\Web;

use Src\App\Controllers\Web\TemplateController;
use Src\Models\ContactForm;

class ContactController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();

        $contactForm = new ContactForm();
        if($this->request->isPost()) {
            if($contactForm->loadData($data)->send()) {
                $this->session->setFlash('success', _('Sua mensagem foi enviada com sucesso!'));
                $this->redirect('contact.index');
            } else {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
            }
        }
        
        $this->render('web/contact', [
            'contactForm' => $contactForm
        ]);
    }
}