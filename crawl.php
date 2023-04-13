<?php 
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

/**
 * Performs a PDO insert operation of a link to the database.
 */
function insertLink($url, $title, $description, $keywords) {
  /** @var PDO $conn */
  global $conn;

  $query = $conn->prepare("INSERT INTO sites(url, title, description, keywords)
                          VALUES (:url, :title, :description, :keywords)");
  $query->bindParam(":url", $url);
  $query->bindParam(":title", $title);
  $query->bindParam(":description", $description);
  $query->bindParam(":keywords", $keywords);

  return $query->execute();
}

function insertImage($url, $src, $alt, $title) {
  /** @var PDO $conn */
  global $conn;

  $query = $conn->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
                          VALUES (:siteUrl, :imageUrl, :alt, :title)");
  $query->bindParam(":siteUrl", $url);
  $query->bindParam(":imageUrl", $src);
  $query->bindParam(":alt", $alt);
  $query->bindParam(":title", $title);

  return $query->execute();
}

/**
 * Checks if $url exists in the database.
 * @param string $url 
 */
function linkExists($url) {
  /** @var PDO $conn */
  global $conn;

  $query = $conn->prepare("SELECT * FROM sites WHERE url=:url");
  $query->bindParam(":url", $url);

  $query->execute();

  return $query->rowCount() != 0;
}

/**
 * Converts relative links to absolute links.
 * 
 * @param string $src the href
 * @param string $url webapge link
 */
function createLink($src, $url) {

  $scheme = parse_url($url)["scheme"]; // http 
  $host = parse_url($url)["host"]; // www.mywebsite.com

  // if src=/mac/    then $src = http://apple.com/mac/
  if (substr($src, 0, 2) == "//") {
    $src = $scheme . ":" . $src; 
  } else if (substr($src, 0, 1) == "/") {
    $src = $scheme . "://" . $host . $src;
  } else if (substr($src, 0, 2) == "./") {
    echo 'dirname(parse_url($url)["path"]) -------- ' . dirname(parse_url($url)["path"]);
    $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
  } else if (substr($src, 0, 3) == "../") {
    $src = $scheme . "://" . $host . "/" . $src;
  } else if (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
    $src = $scheme . "://" . $host . "/" . $src;
  }

  return $src;
}

function getDetails($url) {
  $parser = new DomDocumentParser($url);
  $titleArray = $parser->getTitleTags();

  if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
    return;
  }

  $title = $titleArray->item(0)->nodeValue;
  $title = str_replace("\n", "", $title); // replace newlines to empty

  // ignore empty titles
  if ($title == "") {
    return;
  }

  $description = "";
  $keywords = "";

  $metaAsArray = $parser->getMetaTags();
  
  foreach ($metaAsArray as $meta) {
    if ($meta->getAttribute("name") == "description") {
      $description = $meta->getAttribute("content");
    }
    if ($meta->getAttribute("name") == "keywords") {
      $keywords = $meta->getAttribute("content");
    }
  }

  $description = str_replace("\n", "", $description); // replace newlines to empty
  $keywords = str_replace("\n", "", $keywords);

  // echo "URL: $url TITLE: $title DESCRIPTION: $description KEYWORDS: $keywords <br/>";

  if (linkExists($url)) {
    echo "$url already exists <br/>";
  } else if (insertLink($url, $title, $description, $keywords)) {
    echo "SUCCESS: $url <br/>";
  } else { // no insertion occurred
    echo "ERROR: Failed to insert $url <br/>";
  }

  $imageArray = $parser->getImages();
  foreach ($imageArray as $image) {
    $src = $image->getAttribute("src");
    $alt = $image->getAttribute("alt");
    $title = $image->getAttribute("title");

    if (!$title && !$alt) continue;

    $src = createLink($src, $url);

    global $alreadyFoundImages;
    if (!in_array($src, $alreadyFoundImages)) {
      $alreadyFoundImages[] = $src;

      // Insert the image
      insertImage($url, $src, $alt, $title);
    }
  }
}

/**
 * Gets all the href attribute of all anchor tags from the supplied $url. 
 * The function ignores pound signs. 
 * 
 * @param string $url Link to the webpage to be crawled
 * @return void
 */ 
function followLinks($url) {
  global $alreadyCrawled;
  global $crawling;
  
  $parser = new DomDocumentParser($url);

  $linkList = $parser->getLinks();

  // Loop over the elements and retreive href
  foreach ($linkList as $link) {
    $href = $link->getAttribute("href");

    // ignore pound sign
    if (strpos($href, "#") !== false) {
      continue;
    } else if (substr($href, 0, 11) == "javascript:") {
      continue;
    }

    $href = createLink($href, $url);

    if (!in_array($href, $alreadyCrawled)) {
      $alreadyCrawled[] = $href;
      $crawling[] = $href;

      // Insert href
      getDetails($href);
    }
    else return;

    // echo "OUTPUT : $href <br/>";
  }

  array_shift($crawling);

  foreach ($crawling as $site) {
    followLinks($site);
  }

}

$startUrl = "https://www.google.com/search?q=football&client=firefox-b-d&sxsrf=APwXEdcNdnmykhLiTuW6v5eomb_NFsQvIg:1681283989885&ei=lVs2ZMjDNZKC-Abt1YXIAQ&start=10&sa=N&ved=2ahUKEwiI4u735qP-AhUSAd4KHe1qARkQ8tMDegQIBBAE&biw=1536&bih=731&dpr=1.25";
followLinks($startUrl);
