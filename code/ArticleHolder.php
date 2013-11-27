<?php

class ArticleHolder extends Page {
	
	public static $icon = 'plump_news/images/icons/news.png';
	
	public static $allowed_children = array('ArticlePage');
	
	//getters
	
	public function Articles($year = NULL) {
		
		$filter = 'ParentID = ' . $this->ID;
		
		if ($year != NULL) {
			$filter .= " AND YEAR(\"ArticlePage\".\"Date\") = '$year'";
		}
		
		return ArticlePage::get()->where($filter)->sort('Date DESC');
		
	}
	
}

class ArticleHolder_Controller extends Page_Controller {
	
	public static $allowed_actions = array(
		'index',
		'archive',
		'category',
		'SearchForm'
	);
	
	public function NewsArticles() {
		
		$articles = NULL;

		$perPage = $this->dataRecord->config()->get('articles_per_page');
			
		if ($this->IsArchive()) {
			$articles = $this->Articles();
		} else if ($this->IsCategory()) {
			$articles = $this->CurrentCategory()->SortedArticles();
		} else {
			$articles = $this->Articles($this->CurrentYear());
		}
		
		$paginatedList = new PaginatedList($articles, $this->request);
		$paginatedList->setPageLength($perPage);
		
		return $paginatedList;
	}
	
	public function CurrentYear() {
		return date('Y');
	}
	
	public function IsArchive() {
		return ($this->request->latestParam('Action') == 'archive');
	}
	
	public function IsCategory() {
		return ($this->request->latestParam('Action') == 'category');
	}

	public function CurrentCategory() {
		$segment = $this->request->latestParam('ID');
		return NewsCategory::get()->filter('URLSegment', $segment)->First();
	}
	
	public function Categories() {
		//TODO remove categories that have no articles
		return NewsCategory::get();
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