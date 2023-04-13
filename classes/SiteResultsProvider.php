<?php
class SiteResultsProvider
{

  private $conn;

  /**
   * 
   * @param PDO $conn
   */
  public function __construct($conn) {
    $this->conn = $conn;
  }

  /**
   * Returns the number of results that were found.
   */
  public function getNumResults($term) {
    $query = $this->conn->prepare("SELECT COUNT(*) as total 
                                  FROM sites WHERE title LIKE :term
                                  OR url LIKE :term
                                  OR keywords LIKE :term
                                  OR description LIKE :term");
    $searchTerm = "%" . $term . "%";
    $query->bindParam(":term", $searchTerm);
    $query->execute();

    // Store result in associative array
    $row = $query->fetch(PDO::FETCH_ASSOC);
    return $row["total"];
  }

  /**
   * Generates the HTML of related rows in the search term.
   * @param int $page
   * @param int $pageSize
   * @param string $term
   * @return string $resultsHtml
   */
  public function getResultHtml($page, $pageSize, $term) {
    // page 1: (1-1) * 20 = 0
    // page 2: (2-1) * 20 = 1
    // page 3: (3-1) * 20 = 40
    $fromLimit = ($page - 1) * $pageSize;
    
    $query = $this->conn->prepare("SELECT * FROM sites 
                                  WHERE title LIKE :term
                                  OR url LIKE :term
                                  OR keywords LIKE :term
                                  OR description LIKE :term
                                  ORDER BY clicks DESC
                                  LIMIT :fromLimit, :pageSize");

    $searchTerm = "%" . $term . "%";
    $query->bindParam(":term", $searchTerm);
    $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
    $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
    $query->execute();

    $resultsHtml = "<div class='siteResults'>";

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $id = $row["id"];
      $url = $row["url"];
      $title = $row["title"];
      $description = $row["description"];

      $title = $this->trimField($title, 55);
      $description = $this->trimField($description, 230);
      

      $resultsHtml .= "<div class='resultContainer'>
                        <h3 class='title'>
                          <a class='result' href='$url' data-linkId='$id'>$title</a>
                        </h3>
                        <span class='url'>$url</span>
                        <span class='description'>$description</span>
                      </div>";
    } 
    
    $resultsHtml .= "</div>";

    return $resultsHtml;
  }

  /**
   * Trims long text. 
   * 
   * @param string $string The text to trim.
   * @param int $characterLimit Number of characters to trim it to.
   */
  private function trimField($string, $characterLimit) {
    $dots = strlen($string) > $characterLimit ? "..." : "";
    return substr($string, 0, $characterLimit) . "$dots";
  }
}
