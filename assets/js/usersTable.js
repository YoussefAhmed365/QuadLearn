function searchTable() {
    // استخدام قيمة البحث
    var input, filter, tables, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    tables = document.querySelectorAll('.table-container');

    // الاختبارات مع كل صف في كل جدول
    tables.forEach(function(table) {
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });
}
function showTable(tableId) {
    var tables = document.querySelectorAll('.table-container');
    tables.forEach(function(table) {
        table.style.display = 'none';
    });

    var selectedTable = document.getElementById(tableId);
    selectedTable.style.display = 'block';
}