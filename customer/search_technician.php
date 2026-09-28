<?php
session_start();


if (!isset($_SESSION["id"]) && !isset($_SESSION["username"])) {
    // header("Location: login.php");
    // exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Technician - FixLink</title>
</head>
<body bgcolor="#f4f7f9" style="font-family: Arial, sans-serif;">

<h1 align="center" style="color: #1e3a8a;">
    Search Technician
</h1>

<hr>

<b style="color: #1e3a8a;">Search Technician:</b>
<br>
<input
    type="text"
    id="search"
    onkeyup="searchTechnician()"
    placeholder="Enter Name, Skill, or Location"
    style="padding: 8px 12px; width: 60%; border: 1px solid #cbd5e1; border-radius: 4px;">
<br><br>

<a href="customerdashboard.php">
   <input type="button" value="Back" style="background-color: #475569; color: #ffffff; border: none; padding: 8px 18px; border-radius: 5px; cursor: pointer;">
</a>

<br><br>

<table border="1" cellpadding="10" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Specialization / Skill</th>
            <th>Location</th>
            <th>Experience</th>
            <th>Rating</th>
        </tr>
    </thead>
    <tbody id="result">
        
    </tbody>
</table>

<hr>

<p align="center">
    Copyright &copy; <?php echo date("Y"); ?> FixLink
</p>

<script>
function searchTechnician()
{
    var value = document.getElementById("search").value;

    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function()
    {
        if(xhr.readyState == 4 && xhr.status == 200)
        {
            document.getElementById("result").innerHTML = xhr.responseText;
        }
    };

    xhr.open("POST", "search_technician_ajax.php", true);

    xhr.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );

    xhr.send("value=" + encodeURIComponent(value));
}


window.onload = function() {
    searchTechnician();
};
</script>

</body>
</html>