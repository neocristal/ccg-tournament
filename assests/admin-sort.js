document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("#ccg-stats-table thead th").forEach((th, colIndex) => {
        th.style.cursor = "pointer";
        th.addEventListener("click", () => {
            const table = th.closest("table");
            const rows = Array.from(table.querySelectorAll("tbody tr"));
            const asc = !th.classList.contains("asc");

            rows.sort((a, b) => {
                const aText = a.children[colIndex].innerText;
                const bText = b.children[colIndex].innerText;
                return asc
                    ? aText.localeCompare(bText, undefined, { numeric: true })
                    : bText.localeCompare(aText, undefined, { numeric: true });
            });

            rows.forEach(row => table.querySelector("tbody").appendChild(row));
            table.querySelectorAll("th").forEach(th => th.classList.remove("asc", "desc"));
            th.classList.add(asc ? "asc" : "desc");
        });
    });
});