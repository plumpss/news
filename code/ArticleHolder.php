<?php

class ArticleHolder extends Page {
	
	public static $icon = 'plump_news/images/icons/news.png';
	
	public static $allowed_children = array('ArticlePage');
	
	private static $has_many = array(
		'Categories' => 'NewsCategory'
	);
	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$this->addCategoriesTab($fields);
		
		return $fields;
	}
	
	private function addCategoriesTab($fields) {
		
		$config = GridFieldConfig_RecordEditor::create();
		$config->addComponent(new GridFieldSortableRows('SortOrder'));
		
        $categoriesField = new GridField(
            'Categories',
            'Categories',
            $this->Categories(),
            $config
        );
		
		$fields->addFieldToTab('Root.Categories', $categoriesField);
	}
	
	//getters
	
	public function Articles($year = NULL) {
		
		$filter = 'ParentID = ' . $this->ID;
		
		if ($year != NULL) {
			$filter .= " AND YEAR(\"ArticlePage\".\"Date\") = '$year'";
		}
		
		return ArticlePage::get()->where($filter)->sort('Date DESC');
		
	}

	public function RecentArticles($count = 5) {
		return ArticlePage::get()->filter('ParentID', $this->ID)->sort('Date DESC')->limit($count);
	}
	
}

class ArticleHolder_Controller extends Page_Controller {
	
	public static $allowed_actions = array(
		'index',
		'category',
		'SearchForm'
	);
	
	public function NewsArticles() {
		
		$articles = NULL;

		$perPage = $this->dataRecord->config()->get('articles_per_page');
			
		if ($this->IsCategory()) {
			$articles = $this->CurrentCategory()->SortedArticles();
		} else {
			$articles = $this->Articles();
		}
			
		$paginatedList = new PaginatedList($articles, $this->request);
		$paginatedList->setPageLength($perPage);
		
		return $paginatedList;
	}
	
	public function CurrentYear() {
		return date('Y');
	}
	
	public function IsCategory() {
		return ($this->request->latestParam('Action') == 'category');
	}

	public function CurrentCategory() {
		$segment = $this->request->latestParam('ID');
		return NewsCategory::get()->filter('URLSegment', $segment)->First();
	}
	
	public function SearchForm() {

		$searchText = '';

		if ($this->request && $this->request->getVar('Search')) {
			$searchText = $this->request->getVar('Search');
		}
		
		$queryField = new TextField('Search', FALSE, $searchText);
		$queryField->setAttribute('placeholder', 'Search news');

		$fields = new FieldList(
			$queryField
		);
		$actions = new FieldList(
			new FormAction('results', 'Search')
		);
		
		$form = new Form($this, 'SearchForm', $fields, $actions);
		$form->setFormMethod('get');
		$form->disableSecurityToken();
		return $form;
	}
	
	// actions
	
	public function results($data, $form, $request) {
		
		$query = $data['Search'];
		$filter = 'ParentID = ' . $this->ID . " AND (\"SiteTree\".\"Title\" LIKE '%{$query}%' OR \"SiteTree\".\"Content\" LIKE '%{$query}')";
		
		$articles = ArticlePage::get()->where($filter)->sort('Date DESC');
		
		$perPage = $this->dataRecord->config()->get('articles_per_page');
		$paginatedList = new PaginatedList($articles, $request);
		$paginatedList->setPageLength($perPage);
		
		return $this->customise(array(
            'NewsArticles' => $paginatedList
        ))->renderWith(array('ArticleHolder', 'Page'));
	}
	
}

?>