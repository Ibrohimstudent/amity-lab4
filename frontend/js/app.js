const API = "backend";

// Hide logo if image is missing
document.querySelectorAll(".logo").forEach(img => {
    img.onerror = () => img.style.display = "none";
});

// HOME PAGE: Check backend connection
const status = document.getElementById("server-status");

if (status) {
    fetch(API + "/get_students.php")
        .then(response => response.json())
        .then(data => {
            status.textContent =
                "Backend connected ✔ (" + data.count + " students saved)";
            status.className = "ok";
        })
        .catch(() => {
            status.textContent = "Backend not reachable. Start the PHP server.";
            status.className = "bad";
        });
}

// FORM PAGE: Submit student details
const form = document.getElementById("studentForm");

if (form) {
    form.addEventListener("submit", event => {
        event.preventDefault();

        const msg = document.getElementById("msg");

        fetch(API + "/submit.php", {
            method: "POST",
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            msg.replaceChildren();

            if (data.success) {
                msg.className = "success";

                const p = document.createElement("p");
                p.textContent = data.message || "Student saved successfully!";
                msg.appendChild(p);

                const link = document.createElement("a");
                link.href = "table.html";
                link.textContent = "View table →";
                msg.appendChild(link);

                form.reset();
            } else {
                msg.className = "error";

                (data.errors || ["Please check your details."]).forEach(error => {
                    const p = document.createElement("p");
                    p.textContent = error;
                    msg.appendChild(p);
                });
            }
        })
        .catch(() => {
            msg.className = "error";
            msg.textContent = "Backend connection failed. Check your PHP server.";
        });
    });
}

// TABLE PAGE: Load student records
const tableBody = document.getElementById("tableBody");

if (tableBody) {
    fetch(API + "/get_students.php")
        .then(response => response.json())
        .then(data => {
            const students = data.students || [];

            if (students.length === 0) {
                tableBody.innerHTML =
                    "<tr><td colspan='7'>No students yet.</td></tr>";
                document.getElementById("average").textContent = "—";
                return;
            }

            tableBody.replaceChildren();

            students.forEach((student, index) => {
                const row = document.createElement("tr");

                [
                    index + 1,
                    student.enrollment,
                    student.name,
                    student.course,
                    student.gender,
                    student.marks,
                    student.grade
                ].forEach(value => {
                    const cell = document.createElement("td");
                    cell.textContent = value ?? "";
                    row.appendChild(cell);
                });

                tableBody.appendChild(row);
            });

            document.getElementById("average").textContent =
                data.average ?? "—";
        })
        .catch(() => {
            tableBody.innerHTML =
                "<tr><td colspan='7'>Could not load student records.</td></tr>";
        });
}