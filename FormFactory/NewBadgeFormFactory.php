<?php

namespace Ant\BadgeBundle\FormFactory;

/**
* Instanciates badge forms
*
* @author Pablo <pablo@antweb.es>
*/
class NewBadgeFormFactory extends AbstractBadgeFormFactory
{
    /**
	* Creates a new badge
	*
	* @return Form
	*/
    public function create()
    {
    	$badge = $this->createModelInstance();
    	
    	return $this->formFactory->createNamed($this->formName, $this->formType, $badge);

    }
}