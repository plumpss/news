<?php

class NewsCategory extends DataObject {
	
	private static $db = array(
		'Title'      => 'Varchar(100)',
		'URLSegment' => 'Varchar(100)',
		'SortOrder'  => 'Int'
	);
	
	private static $has_one = array(
		'ArticleHolder' => 'ArticleHolder'
	);
	
	private static $belongs_many_many = array(
		'Articles' => 'ArticlePage'
	);
	
	public static $default_sort = 'SortOrder';
	
	public function getCMSFields() {
		return new FieldList(
			new TextField('Title')
		);
	}
	
	public function onBeforeWrite() {
		
		if (!$this->URLSegment) {
			$filter = URLSegmentFilter::create();
			$this->URLSegment = $filter->filter($this->Title);
		}
		
		parent::onBeforeWrite();
	}
	
	public function Link() {
		return $this->ArticleHolder()->Link('/category/' . $this->URLSegment);
	}
	
	public function SortedArticles() {
		return $this->Articles(NULL, 'Date DESC');
	}
	
	public function canCreate($member = NULL) { return TRUE; } 
   	public function canEdit($member = NULL) { return TRUE; } 
	public function canDelete($member = NULL) { return TRUE; }
	public function canView($member = NULL) { return TRUE; }
		
}
