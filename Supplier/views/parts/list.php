<!DOCTYPE html>

<html>

<head>

<title>My Parts</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = "My Parts";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<!-- ADDED: live search box -->
<div class="search-bar">

<input type="text" id="partSearchInput" placeholder="Search by name, category, or description...">

</div>

<table border="1" cellpadding="10" align="center">

<thead>

<tr>

<th>ID</th>

<th>Part Name</th>

<th>Category</th>

<th>Description</th>

<th>Price</th>

<th>Deposit</th>

<th>Duration</th>

<th>Total</th>

<th>Stock</th>

<th>Action</th>

</tr>

</thead>

<!-- CHANGED: tbody starts empty -- JavaScript fills it in, both on page
     load (with an empty search term, i.e. "show everything") and again
     every time the search box changes. -->
<tbody id="partsTableBody">

<tr><td colspan="10" style="text-align:center;">Loading...</td></tr>

</tbody>

</table>

<script>
// Whether this page is running inside the dashboard's iframe -- needed so
// the Edit/Delete links we build in JS keep working the same way the
// server-rendered ones used to.
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

function renderPartsRows(parts) {
    var tbody = document.getElementById("partsTableBody");

    if (parts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;">No parts found.</td></tr>';
        return;
    }

    var embedSuffix = EMBEDDED ? "&embed=1" : "";
    var html = "";

    parts.forEach(function (row) {
        var total = (parseFloat(row.price) * parseInt(row.rental_duration_days, 10)) + parseFloat(row.security_deposit);

        html += "<tr>"
            + "<td>" + row.id + "</td>"
            + "<td>" + escapeHtml(row.part_name) + "</td>"
            + "<td>" + escapeHtml(row.category) + "</td>"
            + "<td>" + escapeHtml(row.description) + "</td>"
            + "<td>" + row.price + "</td>"
            + "<td>" + row.security_deposit + "</td>"
            + "<td>" + row.rental_duration_days + " day(s)</td>"
            + "<td>" + total.toFixed(2) + "</td>"
            + "<td>" + row.stock + "</td>"
            + "<td>"
            +   "<a href=\"index.php?page=editPart&id=" + row.id + embedSuffix + "\">Edit</a>"
            +   " | "
            +   "<a href=\"index.php?page=deletePart&id=" + row.id + embedSuffix + "\">Delete</a>"
            + "</td>"
            + "</tr>";
    });

    tbody.innerHTML = html;
}

function runSearch(term) {
    fetch("index.php?page=searchParts&term=" + encodeURIComponent(term))
        .then(function (response) { return response.json(); })
        .then(function (data) { renderPartsRows(data); })
        .catch(function (error) {
            console.error("Search failed:", error);
            document.getElementById("partsTableBody").innerHTML =
                '<tr><td colspan="10" style="text-align:center;">Something went wrong loading parts.</td></tr>';
        });
}

// Debounced input handler: waits 250ms after typing stops before firing
// the request, so it doesn't spam the server on every single keystroke.
var searchDebounce;
document.getElementById("partSearchInput").addEventListener("input", function () {
    clearTimeout(searchDebounce);
    var term = this.value;
    searchDebounce = setTimeout(function () {
        runSearch(term);
    }, 250);
});

// Initial load: run a search with an empty term, which returns everything.
runSearch("");
</script>

<?php if (!$embedded): ?>

</main>

</div>

<hr>

<p align="center">

Copyright &copy;

<?php echo date("Y"); ?>

</p>

<?php endif; ?>

</body>

</html>
