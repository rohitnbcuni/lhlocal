<?php

class WorkorderForm extends Zend_Form
{
    public function __construct($options = null)
    {
        $this->addElementPrefixPath('App', 'App/');
        parent::__construct($options);
        $this->setName('workorder');
        $this->setAttrib('enctype', 'multipart/form-data');

        
        $id = new Zend_Form_Element_Hidden(array('name'=>'id'));      
        $bcid = new Zend_Form_Element_Hidden(array('name'=>'bcid'));
        $category_id = new Zend_Form_Element_Hidden(array('name'=>'category_id'));
        $cclist = new Zend_Form_Element_Hidden(array('name'=>'cclist'));

        $requested_by = new Zend_Form_Element_Select('requested_by');
        $requested_by->setLabel('Requested By')
        ->addMultiOptions(array());

        $project_id = new Zend_Form_Element_Select('project_id');
        $project_id->setLabel('Project')
        ->addMultiOptions(array());
        
        $type = new Zend_Form_Element_Select('type');
        $type->setLabel('Work Order Type')
        ->addMultiOptions(array());

        $priority = new Zend_Form_Element_Select('priority');
        $priority->setLabel('Priority')
        ->addMultiOptions(array());

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
        ->setRequired(true)
        ->setAttrib('size','50')
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');
        
        $body = new Zend_Form_Element_Textarea('body');
        $body->setLabel('Work Order Description')
        ->setRequired(false)
        ->setAttribs(array('rows'=>'6','cols'=>'40'))
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        
               
        $new_comment = new Zend_Form_Element_Textarea('new_comment');
        $new_comment->setLabel('New Comment')
        ->setRequired(false)
        ->setAttribs(array('rows'=>'4','cols'=>'40'))
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        
        $post = new Zend_Form_Element_Button('post');
        $post->setAttrib('id', 'post')
        ->setLabel("Post New Comment");
        
        $close = new Zend_Form_Element_Button('close');
        $close->setAttrib('id', 'close')
        ->setLabel("Closing Comment");
        
        
        $assigned_to = new Zend_Form_Element_Select('assigned_to');
        $assigned_to->setLabel('Assigned To')
        ->addMultiOptions(array());

        $status = new Zend_Form_Element_Select('status');
        $status->setLabel('Workorder Status')
        ->addMultiOptions(array());

        $extra_resources = new Zend_Form_Element_Select('extra_resources');
        $extra_resources->setLabel('Extra Resources')
        ->addMultiOptions(array());
        
        
        $start_date = new Zend_Form_Element_Text('start_date');
        $start_date->setLabel('Start Date (yyyy-mm-dd)')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        
        $closed_date = new Zend_Form_Element_Text('closed_date');
        $closed_date->setLabel('Close Date (yyyy-mm-dd)')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim');

        
        $est_due_date = new Zend_Form_Element_Text('est_due_date');
        $est_due_date->setLabel('Estimated Due Date (yyyy-mm-dd)')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim');
        
        $file = new App_Form_Element_File('file');
        $file->setLabel('Attachment')
                 ->setRequired(false)
                 ->addValidator('NotEmpty');

        
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton')
        ->setLabel("Save");
        
        $reset = new Zend_Form_Element_Reset('reset');
        $reset->setAttrib('id', 'resetbutton')
        ->setLabel('Reset');
        
        
        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setAttrib('id', 'cancelbutton')
        ->setLabel('Cancel');


        $this->addElements(array(
        $id,
        $bcid, 
        $project_id,
        $cclist, 
        $type,  
        $priority, 
        $title, 
        $body,
        $new_comment,
        $post,
        $close,
        $file,
        $category_id, 
        $requested_by,
        $assigned_to, 
        $extra_resources, 
        $status, 
        $start_date, 
        $est_due_date, 
        $closed_date,
        $submit,
        $cancel,
        $reset
        ));
        
        /*
        
         $this->addDisplayGroup(array('id',
        'bcid', 
        'project_id', 
        'type',  
        'priority', 
        'title', 
        'body', 
        'file',
        'category_id'),'client',array('legend'=>'New Workorder Information'));
        
        $this->addDisplayGroup(array('requested_by','phone','email'),'requester',array('legend'=>'Requester Information'));

        $this->addDisplayGroup(array(
        'assigned_to', 
        'extra_resources', 
        'status', 
        'start_date', 
        'est_due_date', 
        'closed_date'),'mgr',array('legend'=>'Project Management'));
        
         //$this->addDisplayGroup(array('submit','reset','cancel'),'buttons');
       
       if(App_Auth_Adapter_Basecamp::isClient())
       {
        	$this->mgr->setAttrib("style","display:none");
        	
        	$this->requester->setAttrib("style","display:none");
        	
        }
        
        */

    }
}
