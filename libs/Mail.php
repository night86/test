<?php
namespace Signa\Libs;

use Phalcon\Mvc\View;
use Signa\Controllers\ControllerBase;
use Signa\Models\Organisations;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class Mail extends ControllerBase
{
    protected $transport;
    protected $config;

    public function onConstruct()
    {
        $di = \Phalcon\DI::getDefault();

        $newConfig = array();
        foreach ($di->getConfig()->mailer->toArray() as $key => $config)
        {
            preg_match('/[\S]+/', $key, $matches);
            if(count($matches)){
                $newConfig[$matches[0]] = str_replace('\'', '', $config);
            }else{
                $newConfig[$key] = str_replace('\'', '', $config);
            }
        }
        $this->config = $newConfig;
    }


    public function getTemplate($name, $params = null)
    {
        if(!is_null($params)){
            $parameters = array_merge(array('baseUrl' => baseUrl()), $params);
        }else{
            $parameters['baseUrl'] = baseUrl();
        }

        return $this->view->getRender('emailTemplates', $name, $parameters, function($view){
            $view->setRenderLevel(View::LEVEL_LAYOUT);
        });

        return $this->view->getContent();
    }

    public function send($to, $subject, $template, $params = null)
    {
        if ($this->config['smtp_host'] == 'smtp.mailtrap.io') {
            sleep(1);
        }
        if((filter_var($to, FILTER_VALIDATE_EMAIL)) !== FALSE){
            $template = $this->getTemplate($template, $params);
            // Create the message
            $message = new \Swift_Message();
            $message->setSubject($subject);
            $message->setTo($to);
            $message->setFrom(array(
                    $this->config['smtp_fromemail'] => $this->config['smtp_fromname']
                ));
            $message->setBody($template, 'text/html');

            if (!$this->transport) {
                $this->transport = (new \Swift_SmtpTransport(
                    $this->config['smtp_host'],
                    $this->config['smtp_port'],
                    $this->config['smtp_protocol']
                ))
                    ->setUsername($this->config['smtp_user'])
                    ->setPassword($this->config['smtp_pass'])
                ;
            }
            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($this->transport);
            return $mailer->send($message);
        }
        else {
            return false;
        }
    }

    public function sendOrganisationAdmins($organisation, $subject, $template, $params = null)
    {

        $admins = $organisation->getSupplierAdmins();
        $statusArr = array();

        foreach ($admins as $admin)
        {
            $statusArr[] = $this->send($admin->getEmail(), $subject, $template, $params);
        }
        return $statusArr;
    }
}

