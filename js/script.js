$(function () {

    $("#submit").on("click", function (event) {
        //event.preventDefault();
        var str = $("#dtForm").serialize(); // serializes the form's elements.

        console.log(str);
        var url = "ajaxProcess.php"; // the script where you handle the form input.

        $.ajax({
            type: "POST",
            url: url,
            data: str,
            success: function (data)
            {
                $("#resultPanel").html(data); // show response from the php script.
                console.log(data);
            }
        });

    });
});