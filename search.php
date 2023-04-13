<?php
include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/ImageResultsProvider.php");

if (isset($_GET["term"])) {
  $term = $_GET["term"];
} else {
  exit("You must enter a search term");
}

$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to Doodle</title>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- only put jquery at the head tag -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous">
  </script>
</head>

<body>
  <div class="wrapper">
    <div class="header">
      <div class="headerContent">
        <div class="logoContainer">
          <a href="index.php">
            <img src="assets/images/doodleLogo.png" alt="">
          </a>
        </div>
        <div class="searchContainer">
          <form action="search.php" method="GET">
            <div class="searchBarContainer">
              <input type="hidden" name="type" value="<?= $type ?>" />
              <input type="text" class="searchBox" name="term" value="<?= $_GET["term"] ?>">
              <button class="searchButton">
                <svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4285F4">
                  <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                </svg>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="tabsContainer">
        <ul class="tabList">
          <li class="<?php echo $type == 'sites' ? 'active' : ''; ?>">
            <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
          </li>
          <li class="<?php echo $type == 'images' ? 'active' : ''; ?>">
            <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="mainResultsSection">

      <?php
      if ($type == "sites") {
        $resultsProvider = new SiteResultsProvider($conn);
        $pageSize = 20;
      } else {
        $resultsProvider = new ImageResultsProvider($conn);
        $pageSize = 30;
      }

      $numResults = $resultsProvider->getNumResults($term);
      echo "<p class='resultCount'>$numResults results found</p>";


      echo $resultsProvider->getResultHtml($page, $pageSize, $term);
      ?>

    </div>

    <div class="paginationContainer">
      <div class="pageButtons">
        <div class="pageNumberContainer">
          <img src="assets/images/pageStart.png" alt="">
        </div>

        <?php

        $pagesToShow = 10;
        $numPages = ceil($numResults / $pageSize);
        $pagesLeft = min($pagesToShow, $numPages);

        $currentPage = $page - floor($pagesToShow / 2);
        if ($currentPage < 1) $currentPage = 1;

        if ($currentPage + $pagesLeft && $currentPage <= $numPages + 1) {
          $currentPage = $numPages + 1 - $pagesLeft;
        }

        while ($pagesLeft != 0 && $currentPage <= $numPages) {

          if ($currentPage == $page) {
            echo "<div class='pageNumberContainer'>
                    <img src='assets/images/pageSelected.png'/>
                    <span class='pageNumber'>$currentPage</span>
                  </div>";
          } else {
            echo "<div class='pageNumberContainer'>
                    <a href='search.php?term=$term&type=$type&page=$currentPage'>
                      <img src='assets/images/page.png'/>
                      <span class='pageNumber'>$currentPage</span>
                    </a>
                  </div>";
          }


          $currentPage++;
          $pagesLeft--;
        }

        ?>

        <div class="pageNumberContainer">
          <img src="assets/images/pageEnd.png" alt="">
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/script.js"></script>
  <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>
</body>

</html>