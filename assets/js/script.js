var timer;

$(document).ready(function () {
  $(".result").on("click", function () {
    let id = $(this).attr("data-linkId");
    let url = $(this).attr("href"); // `this` is result element

    if (!id) {
      alert("data-linkId attribute not found"); // for debugging only
    }
    increaseLinkClicks(id, url);

    return false; // do not to default behavior (prevent link from going to page)
  });

  var grid = $(".imageResults");

  grid.on("layoutComplete", () => {
    $(".gridItem img").css("visibility", "visible");
  });

  grid.masonry({
    itemSelector: ".gridItem",
    columnWidth: 200,
    gutter: 5,
    transitionDuration: "0.4s",
    isInitLayout: false,
  });

  $("[data-fancybox]").fancybox({
    caption: function(instance, item) {
      var caption = $(this).data("caption") || "";
      var siteUrl = $(this).data("siteurl") || "";

      if (item.type === "image") {
        caption = (caption.length ? caption + "<br/>" : "")
          + '<a href="' + item.src + '">View image</a><br/>' 
          + '<a href="' + siteUrl + '">Visit page</a>'; 
      }

      return caption;
    },
    afterShow: function(instance, item) {
      increaseImageClicks(item.src);
    }
  });
});

/**
 * Updates the click value of the clicked link.
 * @param {number} linkId
 * @param {string} url
 */
function increaseLinkClicks(linkId, url) {
  $.post("ajax/updateLinkCount.php", { linkId: linkId }).done(function (
    result
  ) {
    if (result != "") {
      alert(result);
      return;
    }

    window.location.href = url;
  });
}

/**
 * Performs a post request to php to increase the click value of the clicked image.
 * @param {string} imageUrl
 */
function increaseImageClicks(imageUrl) {
  $.post("ajax/updateImageCount.php", { imageUrl: imageUrl })
    .done(function (result) {
      if (result != "") {
        alert(result);
        return;
      }
    });
}

/**
 * Generates an image element with the corresponding src and appends it 
 * to the className's anchor tags
 * @param {string} src 
 * @param {string} className
 */
function loadImage(src, className) {
  const image = $("<img>");
  image.on("load", function () {
    $("." + className + " a").append(image);

    clearTimeout(timer);

    timer = setTimeout(() => {
      $(".imageResults").masonry();
    }, 500);
  });

  image.on("error", function () {
    $("." + className).remove();

    $.post("ajax/setBroken.php", { src: src });
  });

  image.attr("src", src);

}
