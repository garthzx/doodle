<?php
/**
 * A Dom parser for use with crawling html elements from a website.
 */
class DomDocumentParser {

  private $doc; 
  
  public function __construct($url){
    // For use when requesting a webpage
    $options = array(
      'http' => array('method' => "GET", 'header' => "User-Agent: doodleBot/0.1\n")
    );

    $context = stream_context_create($options);
    
    $this->doc = new DOMDocument();
    // suppress
    @$this->doc->loadHTML('<?xml encoding="UTF-8">'.file_get_contents($url, false, $context));
  }

  public function getLinks() {
    return $this->doc->getElementsByTagName("a");
  }

  public function getTitleTags() {
    return $this->doc->getElementsByTagName("title");
  } 
  
  public function getMetaTags() {
    return $this->doc->getElementsByTagName("meta");
  }

  public function getImages() {
    return $this->doc->getElementsByTagName("img");
  }
}