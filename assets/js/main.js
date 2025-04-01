$(document).ready(function () {
    $(".viewHistory").click(function () {
        let problemName = $(this).data("problem");
        let tableBody = $("#historyTable");

        $.ajax({
            url: "history.php",
            type: "GET",
            data: { problem_name: problemName },
            success: function (data) {
                //let data = JSON.parse(response);
                if (data.error) {
                    alert(data.error);
                    return;
                }

                tableBody.empty();

                data.forEach(item => {
                    let row = $("<tr>");
                    row.append(`<td>${item.submitted_at}</td>`);
                    row.append(`<td>${item.score}</td>`);
                    row.append(`<td>${item.status}</td>`);
                    row.append(`<td>${item.language}</td>`);

                    let langClass = {
                        "C": "language-c",
                        "CPP": "language-cpp",
                        "PY": "language-python",
                        "PAS": "language-pascal"
                    }[item.language] || "language-plaintext";

                    let codeCell = $(`<td><pre><code class="${langClass}"></code></pre></td>`);
                    codeCell.find("code").text(item.backup_code);
                    row.append(codeCell);

                    let logCell = $("<td><pre><code class='language-log'></code></pre></td>");
                    logCell.find("code").text(item.backup_logs);
                    row.append(logCell);

                    tableBody.append(row);
                });

                Prism.highlightAll();

                $("#historyModalLabel").text(`Lịch sử nộp bài - ${problemName}`);
                $("#historyModal").modal("show");
            }
        });
    });
});


function showSubmitForm() {
    let problemName = $("#problemTitle").text();
    $("#submitProblemName").val(problemName);
    $("#submitModal").modal("show");
}

const md = markdownit({
    html: true,
    breaks: true
});

function viewProblem(problemName) {
    $.ajax({
        url: "get_problem.php",
        type: "GET",
        data: { name: problemName },
        dataType: "json",
        success: function (response) {
            $("#problemTitle").text(response.name);
            $("#problemScore").text(response.total_score);
            $("#problemTime").text(response.time_limit);
            $("#problemMemory").text(response.memory_limit);

            let renderedMarkdown = md.render(response.description);
            $("#problemDescription").html(renderedMarkdown);

            renderMathInElement(document.getElementById("problemDescription"), {
                delimiters: [
                    { left: "$$", right: "$$", display: true },
                    { left: "$", right: "$", display: false }
                ],
                throwOnError: false 
            });

			Prism.highlightAll();

            $("#problemModal").modal("show");
        },
        error: function () {
            alert("Lỗi khi tải đề bài!");
        }
    });
}


function updateCountdown() {
    let startTime = parseInt(document.getElementById("countdown").dataset.startTime) * 1000;
    let endTime = parseInt(document.getElementById("countdown").dataset.endTime) * 1000;
    let now = new Date().getTime();

    let countdownElement = document.getElementById("countdown");

    if (now < startTime) {
        let diff = startTime - now;
        countdownElement.innerHTML = "<i class=\"bi bi-alarm\"></i> Bắt đầu trong: " + formatTime(diff);
    } else if (now >= startTime && now < endTime) {
        let diff = endTime - now;
        countdownElement.innerHTML = "<i class=\"bi bi-hourglass-split\"></i> Kết thúc trong: " + formatTime(diff);
    } else {
        countdownElement.innerHTML = "<i class=\"bi bi-hourglass-bottom\"></i> Kỳ thi đã kết thúc!";
    }
}

function formatTime(ms) {
    let totalSeconds = Math.floor(ms / 1000);
    let days = Math.floor(totalSeconds / 86400);
    let hours = Math.floor((totalSeconds % 86400) / 3600);
    let minutes = Math.floor((totalSeconds % 3600) / 60);
    let seconds = totalSeconds % 60;

    let result = "";
    if (days > 0) result += `${days}d `;
    if (hours > 0 || days > 0) result += `${hours}h `;
    result += `${minutes}m ${seconds}s`;
    
    return result;
}

function formatTime_(seconds) {
    let h = Math.floor(seconds / 3600);
    let m = Math.floor((seconds % 3600) / 60);
    let s = seconds % 60;
    return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
}

function fetchRanking() {
    $.ajax({
        url: "fetch_ranking.php",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let rankingHTML = "";
            data.forEach((row, index) => {
                rankingHTML += `
                    <tr>
                        <td class="text-center align-middle">${index + 1}</td>
                        <td>
                            <strong class="text-info">${row.full_name}</strong><br>
                            <span>${row.username}</span><br /><b>${row.class}</b> - <b>${row.school}</b>
                        </td>
                        <td class="text-center align-middle"><strong class="score">${row.total_score}</strong><br /><small class="time">${formatTime_(row.total_time)}</small></td>
                    </tr>`;
            });
            $("#rankingTable tbody").html(rankingHTML);
        },
        error: function () {
            console.error("Lỗi khi tải bảng xếp hạng!");
        }
    });
}


setInterval(fetchRanking, 2500);
document.addEventListener("DOMContentLoaded", function() {
    updateCountdown();
    setInterval(updateCountdown, 1000);
});