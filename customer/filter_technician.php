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
    <title>Filter Technicians - FixLink</title>
</head>
<body backgroundcolor="#f4f7f9" style="font-family:Arial,sans-serif;">

<h1 align="center" style="color:#1e3a8a;">
    Filter Technicians
</h1>

<hr>

<form onsubmit="return false;">
    <b style="color:#1e3a8a;">Filter Options:</b><br><br>
    
    Skill: 
    <input type="text" id="skill" onkeyup="filterTechnician()" placeholder="e.g. Laptop, Mobile">
    
    &nbsp;&nbsp;
    Location: 
    <input type="text" id="location" onkeyup="filterTechnician()" placeholder="e.g. Dhanmondi">
    
    <br><br>
    
    Experience: 
    <input type="text" id="experience" onkeyup="filterTechnician()" placeholder="e.g. 5 Years">
    
    <br><br>
    Minimum Rating: 
    <select id="rating" onchange="filterTechnician()">
        <option value="">All Ratings</option>
        <option value="4.5">4.5 & Above ★</option>
        <option value="4.0">4.0 & Above ★</option>
        <option value="3.5">3.5 & Above ★</option>
    </select>

    <br><br>
    <input type="button" value="Reset" onclick="resetFilters()" style="background-color: #64748b; color: #ffffff; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer;">
</form>

<br>

<a href="customerdashboard.php">
    <input type="button" value="Back" style="background-color: #475569; color: #ffffff; border: none; padding: 8px 18px; border-radius: 5px; cursor: pointer;">
</a>

<br><br>

<table border="1" cellpadding="10" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Skill / Specialization</th>
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
function filterTechnician()
{
    var skill = document.getElementById("skill").value;
    var location = document.getElementById("location").value;
    var experience = document.getElementById("experience").value;
    var rating = document.getElementById("rating").value;

    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function()
    {
        if(xhr.readyState == 4 && xhr.status == 200)
        {
            document.getElementById("result").innerHTML = xhr.responseText;
        }
    };

    xhr.open("POST", "filter_technician_ajax.php", true);

    xhr.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );

    var params = "skill=" + encodeURIComponent(skill) +
                 "&location=" + encodeURIComponent(location) +
                 "&experience=" + encodeURIComponent(experience) +
                 "&rating=" + encodeURIComponent(rating);

    xhr.send(params);
}

function resetFilters()
{
    document.getElementById("skill").value = "";
    document.getElementById("location").value = "";
    document.getElementById("experience").value = "";
    document.getElementById("rating").value = "";
    filterTechnician();
}


window.onload = function() {
    filterTechnician();
};
</script>

</body>
</html>