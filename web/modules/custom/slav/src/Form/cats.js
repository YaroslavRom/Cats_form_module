$(document).ready(function () {
  $("form").submit(function (event) {
    var formData = {
      name: $("#title").val(),
    };

    $.ajax({
      type: "POST",
      url: "cats.php",
      data: formData,
      dataType: "json",
      encode: true,
    }).done(function (data) {
      console.log(data);
    });

    event.preventDefault();
  });
});
