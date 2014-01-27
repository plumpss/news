<?php

class ArticlePage extends Page {
	
	public static $icon = 'plump_news/images/icons/article.png';
	
	public static $can_be_root = FALSE;
	
	public static $use_summary = TRUE;
	
	public static $db = array(
		'Date' 		=> 'Date',
		'Summary' 	=> 'Text'
	);
	
	public static $has_one = array(
		'FeaturedImage' => 'Image'
	);
	
	public static $many_many = array(
		'Categories' => 'NewsCategory'
	);
	
	public static $defaults = array(
		'Date' => 'now'
	);
	
	 public function getCMSFields() {
	 		
	 	$fields = parent::getCMSFields();
	 	
	 	$dateField = new DateField('Date');
	 	$dateField->setConfig('showcalendar', true);
	 	$fields->addFieldToTab('Root.Main', $dateField, 'Content');
		
		$fields->addFieldToTab('Root.Main', new UploadField('FeaturedImage', 'Featured Image'), 'Content');
		
		if (self::$use_summary) {
			$fields->addFieldToTab('Root.Main', new TextareaField('Summary'), 'Content');
		}
		
		$this->addCategoriesTab($fields);
         
        return $fields;
    }
	 
	private function addCategoriesTab($fields) {
	 	
		$config = GridFieldConfig_RelationEditor::create();
		
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields(array('Title' => 'Title'));
		$config->getComponentByType('GridFieldAddExistingAutocompleter')->setPlaceholderText('Search for an existing category');
		
		$config->removeComponentsByType('GridFieldAddNewButton');
		$config->removeComponentsByType('GridFieldEditButton');
		
		$categoriesField = new GridField(
			'Categories', // Field name
			'Categories', // Field title
			$this->Categories(), // List of all related services
			$config
		);
		
        $fields->addFieldToTab('Root.Categories', $categoriesField);
		
	}
	
}

class ArticlePage_Controller extends Page_Controller {
	
	public function CurrentYear() {
		return date('Y');
	}
	
}

?>