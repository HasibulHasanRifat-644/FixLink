<!DOCTYPE html>
<html>
<head>
<title>My Equipment</title>
<link rel="stylesheet" href="design.css">
</head>
<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = "My Equipment";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<fieldset>

<!-- ADDED: live search box -->
<div class="search-bar">

<input type="text" id="equipmentSearchInput" placeholder="Search by name, category, brand, or model...">

</div>

<table border="1" cellpadding="10" align="center">

<thead>

<tr>

<th>ID</th>

<th>Equipment Name</th>

<th>Category</th>

<th>Brand</th>

<th>Model</th>

<th>Rental Price</th>

<th>Availability</th>

<th>Action</th>

</tr>

</thead>

<!-- CHANGED: tbody starts empty -- JavaScript fills it in, both on page
     load (empty search term = show everything) and on every keystroke. -->
<tbody id="equipmentTableBody">

<tr><td colspan="8" style="text-align:center;">Loading...</td></tr>

</tbody>

</table>

</fieldset>

<script>
var EMBEDDED = <?php echo $embedded ? "true" : "false"; ?>;

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return "";
    }
    return value.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function renderEquipmentRows(items) {
    var tbody = document.getElementById("equipmentTableBody");

    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No equipment found.</td></tr>';
        return;
    }

    var embedSuffix = EMBEDDED ? "&embed=1" : "";
    var html = "";

    items.forEach(function (row) {
        var statusClass = row.computed_availability === "Rented" ? "status-rented" : "status-available";

        html += "<tr>"
            + "<td>" + row.id + "</td>"
            + "<td>" + escapeHtml(row.equipment_name) + "</td>"
            + "<td>" + escapeHtml(row.category) + "</td>"
            + "<td>" + escapeHtml(row.brand) + "</td>"
            + "<td>" + escapeHtml(row.model) + "</td>"
            + "<td>" + row.rental_price + "</td>"
            + "<td><span class=\"status-badge " + statusClass + "\">" + row.computed_availability + "</span></td>"
            + "<td>"
            +   "<a href=\"index.php?page=editEquipment&id=" + row.id + embedSuffix + "\">Edit</a>"
            +   " | "
            +   "<a href=\"index.php?page=deleteEquipment&id=" + row.id + embedSuffix + "\">Delete</a>"
            + "</td>"
            + "</tr>";
    });

    tbody.innerHTML = html;
}

function runSearch(term) {
    fetch("index.php?page=searchEquipment&term=" + encodeURIComponent(term))
        .then(function (response) { return response.json(); })
        .then(function (data) { renderEquipmentRows(data); })
        .catch(function (error) {
            console.error("Search failed:", error);
            document.getElementById("equipmentTableBody").innerHTML =
                '<tr><td colspan="8" style="text-align:center;">Something went wrong loading equipment.</td></tr>';
        });
}

var searchDebounce;
document.getElementById("equipmentSearchInput").addEventListener("input", function () {
    clearTimeout(searchDebounce);
    var term = this.value;
    searchDebounce = setTimeout(function () {
        runSearch(term);
    }, 250);
});

runSearch("");
</script>

<?php if (!$embedded): ?>

</main>

</div>

<hr>

<?php endif; ?>

</body>
</html>
