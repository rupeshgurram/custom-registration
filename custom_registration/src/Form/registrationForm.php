<?php

namespace Drupal\custom_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Component\Utility\EmailValidatorInterface;
use Symfony\Component\HttpFoundation;

/**
 * Provides the form for adding registraion.
 */
class registrationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_registration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    
    $form['fname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' =>  '',
    ];
	 $form['lname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' =>  '',
    ];
	$form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' => '',
    ];
	 $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' => '',
    ];
	$form['company'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Company name'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' => '',
    ];
	 $form['contact'] = [
      '#type' => 'textfield',
      '#title' => $this->t('contact message'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#default_value' => '',
    ];
	$form['address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Address'),
      '#required' => TRUE,
      '#maxlength' => 250,
      '#default_value' => '',
    ];
	 $form['conditions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Terms and Conditions'),
      '#required' => TRUE,
      '#default_value' => 'True',
    ];
	
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#default_value' => $this->t('Save') ,
    ];
	
    return $form;

  }
  
   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {
       $fname = $form_state->getValue('fname');
	   
		if (!preg_match('/^[A-Za-z]+$/', $fname)) {
			$form_state->setErrorByName('title', $this->t('Please enter alphabets only for Name'));
        }
       $lname = $form_state->getValue('lname');	
	   
		if (!preg_match('/^[A-Za-z]+$/', $lname)) {
			$form_state->setErrorByName('title', $this->t('Please enter alphabets only for Name'));
        }
	   $email = $form_state->getValue('email');
	   
	    if (!\Drupal::service('email.validator')->isValid($form_state->getValues()['email'])) {
           $form_state->setErrorByName('Email', $this->t('Email address is not a valid one.'));
        }
	   $phone = $form_state->getValue('phone');
	   
	    if (strlen($phone) < 10) {
           $form_state->setErrorByName('phone', $this->t('Please enter a valid Contact Number'));
        }
	   $address = $form_state->getValue('address');
	   
	    if (strlen($address) < 250) {
           $form_state->setErrorByName('phone', $this->t('Address should not exceed 250 characters'));
        }
		
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array & $form, FormStateInterface $form_state) {
	\Drupal::request()->getClientIp()
	try{
		$conn = Database::getConnection();
		
		$field = $form_state->getValues();
	   
		$fields["fname"] = $field['fname'];
		$fields["lname"] = $field['lname'];
		$fields["email"] = $field['email'];
		$fields["phone"] = $field['phone'];
		$fields["company"] = $field['company'];
		$fields["contact"] = $field['contact'];
		$fields["address"] = $field['address'];
		
		  $conn->insert('registration')
			   ->fields($fields)->execute();
		  \Drupal::messenger()->addMessage($this->t('The Registration data has been succesfully saved'));
		 
	} catch(Exception $ex){
		\Drupal::logger('custom_registration')->error($ex->getMessage());
	}
    $site_mail = \Drupal::config('system.site')->get('mail');
	
	$uri = $file->getFileUri();
    $headers = array(
    'Content-Type'     => $file->getMimeType(),
    'Content-Disposition' => 'attachment;filename="'.$file->getFilename().'"'
  );
  $form_state->setResponse(new \Symfony\Component\HttpFoundation\BinaryFileResponse($uri, 200, $headers, true));
  }

}