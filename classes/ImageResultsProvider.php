<?php
class ImageResultsProvider
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
                                  FROM `images` 
                                  WHERE (title LIKE :term
                                  OR alt LIKE :term)
                                  AND broken=0");

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
    
    $query = $this->conn->prepare("SELECT * 
                                  FROM `images` 
                                  WHERE (title LIKE :term
                                  OR alt LIKE :term)
                                  AND broken=0
                                  ORDER BY clicks DESC
                                  LIMIT :fromLimit, :pageSize");

    $searchTerm = "%" . $term . "%";
    $query->bindParam(":term", $searchTerm);
    $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
    $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
    $query->execute();

    $resultsHtml = "<div class='imageResults'>";

    $count = 0;
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $count++;
      
      $id = $row["id"];
      $imageUrl = $row["imageUrl"];
      $siteUrl = $row["siteUrl"];
      $title = $row["title"];
      $alt = $row["alt"];

      if ($title) {
        $displayText = $title;
      } else if ($alt) {
        $displayText = $alt;
      } else {
        $displayText = $imageUrl;
      }

      $resultsHtml .= "<div class='gridItem image$count'>
                        <a href='$imageUrl' data-fancybox data-caption='$displayText'
                          data-siteurl='$siteUrl'>
                          <script>
                            $(document).ready(function() {
                              loadImage(\"$imageUrl\", \"image$count\");
                            });
                          </script>
                          <span class='details'>$displayText</span>
                        </a>
                      </div>";
    }
    
    $resultsHtml .= "</div>";

    return $resultsHtml;
  }

}
