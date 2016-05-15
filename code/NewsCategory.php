<?php

class NewsCategory extends DataObject
{
    
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
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Remove fields that are automatically set.
        $fields->removeByName('SortOrder');
        $fields->removeByName('ArticleHolderID');
        $fields->removeByName('Articles');

        return $fields;
    }
    
    public function onBeforeWrite()
    {
        if (!$this->URLSegment) {
            $filter = URLSegmentFilter::create();
            $this->URLSegment = $filter->filter($this->Title);
        }
        
        parent::onBeforeWrite();
    }
    
    public function Link()
    {
        return $this->ArticleHolder()->Link('/category/' . $this->URLSegment);
    }
    
    public function SortedArticles()
    {
        return $this->Articles(null, 'Date DESC');
    }
    
    public function canCreate($member = null)
    {
        return true;
    }
    public function canEdit($member = null)
    {
        return true;
    }
    public function canDelete($member = null)
    {
        return true;
    }
    public function canView($member = null)
    {
        return true;
    }
}
