<?php

class NewsCategory extends DataObject {
	
	public static $db = array(
		'Title' => 'Varchar(100)',
		'URLSegment' => 'Varchar(100)'
	);
	
	public static $belongs_many_many = array(
		'Articles' => 'ArticlePage'
	);
	
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
		$articleHolder = ArticleHolder::get()->First();	
		return $articleHolder->Link('/category/' . $this->URLSegment);
	}
	
	public function SortedArticles() {
		return $this->Articles(NULL, 'Date DESC');
	}
		
}
