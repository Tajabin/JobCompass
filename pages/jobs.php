<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
require_once "../db.php";

$email = $_SESSION['user'];

// fetch user's skills to highlight matches (if present)
$user_skills = [];
$stmt = $conn->prepare("SELECT skills, fullName FROM users WHERE email = ?");
if ($stmt) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
        $user_skills = json_decode($row['skills'] ?? "[]", true);
        $user_fullname = $row['fullName'] ?? '';
    }
    $stmt->close();
}

// GET filters
$track = $_GET['track'] ?? '';
$location = $_GET['location'] ?? '';
$type = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

// Build base query safely (escaping values)
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM jobs WHERE 1=1";

if ($track) $sql .= " AND track = '" . $conn->real_escape_string($track) . "'";
if ($location) $sql .= " AND location LIKE '%" . $conn->real_escape_string($location) . "%'";
if ($type) $sql .= " AND type = '" . $conn->real_escape_string($type) . "'";
if ($search) {
    $s = $conn->real_escape_string($search);
    $sql .= " AND (title LIKE '%$s%' OR description LIKE '%$s%')";
}

// Sorting
if ($sort == 'newest') $sql .= " ORDER BY id DESC";
else if ($sort == 'oldest') $sql .= " ORDER BY id ASC";
else if ($sort == 'az') $sql .= " ORDER BY title ASC";
else if ($sort == 'za') $sql .= " ORDER BY title DESC";
else $sql .= " ORDER BY id DESC"; // default

// Pagination
$sql .= " LIMIT $limit OFFSET $offset";

$res = $conn->query($sql);
$jobs = [];
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $r['requiredSkills'] = json_decode($r['requiredSkills'] ?? "[]", true);
        $jobs[] = $r;
    }
}

// total rows for pagination
$total_res = $conn->query("SELECT FOUND_ROWS() AS total");
$total = 0;
if ($total_res) {
    $tr = $total_res->fetch_assoc();
    $total = intval($tr['total'] ?? 0);
}
$total_pages = max(1, ceil($total / $limit));

// Helper to preserve query params on pagination links (in template below)
function build_qs($overrides = []) {
    $qs = array_merge($_GET, $overrides);
    return http_build_query($qs);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Jobs | JobCompass</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<style>
/* Base */
:root{--primary:#0366d6;--muted:#6b7280;--card:#fff;--bg:#f4f7fb}
*{box-sizing:border-box}
body{font-family:Arial,Helvetica,sans-serif;background:var(--bg);color:#222;margin:0;padding:20px}
.container{max-width:1100px;margin:0 auto}

/* Nav */
.nav{background:var(--card);padding:12px 18px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:20px}
.nav a{margin-right:15px;color:var(--primary);text-decoration:none;font-weight:600}

/* Filter */
.filter-box{background:var(--card);padding:18px;border-radius:10px;margin-bottom:18px;box-shadow:0 4px 14px rgba(0,0,0,0.06)}
.filter-grid{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px}
.filter-grid .full{grid-column:1/-1}
.filter-grid input,.filter-grid select{width:100%;padding:10px;border:1px solid #e6e9ef;border-radius:8px}
.actions{display:flex;gap:8px;align-items:center}
button, .btn {padding:10px 12px;border-radius:8px;border:none;background:var(--primary);color:#fff;cursor:pointer}
.btn.secondary{background:#fff;color:var(--primary);border:1px solid #e6e9ef}
.small{padding:6px 8px;font-size:14px}

/* Cards & job list */
.job-list{display:grid;grid-template-columns:1fr;gap:14px}
@media(min-width:900px){ .job-list{grid-template-columns:1fr 1fr} }

.card{background:var(--card);padding:16px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.04)}
.card h3{margin:0 0 8px 0;font-size:18px}
.card p{margin:6px 0;color:var(--muted)}
.tags{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
.tag{background:#f1f5f9;padding:6px 8px;border-radius:999px;font-size:13px;color:#334155}
.tag.match{background:#e6ffef;border:1px solid #2ecc71;color:#064e3b;font-weight:600}

/* Buttons area */
.card .controls{margin-top:12px;display:flex;gap:8px;align-items:center}

/* Pagination */
.pagination{display:flex;gap:8px;flex-wrap:wrap;margin-top:18px}
.pagination a{display:inline-block;padding:8px 10px;border-radius:8px;background:#fff;text-decoration:none;color:var(--primary);box-shadow:0 2px 8px rgba(0,0,0,0.05)}
.pagination a.active{background:var(--primary);color:#fff}

/* Modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:none;align-items:center;justify-content:center;padding:20px;z-index:40}
.modal{background:var(--card);max-width:800px;width:100%;border-radius:12px;padding:18px;box-shadow:0 10px 30px rgba(0,0,0,0.3);max-height:90vh;overflow:auto}
.modal .close{float:right;background:transparent;border:none;font-size:20px;color:var(--muted);cursor:pointer}

/* Saved jobs badge */
.saved-list{margin-top:14px}
.saved-item{background:#fff;padding:10px;border-radius:8px;margin-bottom:8px;box-shadow:0 4px 12px rgba(0,0,0,0.04);display:flex;justify-content:space-between;align-items:center}
.small-muted{color:var(--muted);font-size:13px}
</style>
</head>
<body>
<nav class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="jobs.php">Jobs</a>
    <a href="resources.php">Resources</a>
    <a href="profile.php">Profile</a>
    <a href="skill_gap.php">Skill Gap Analysis</a>

    <a href="../server.php?action=logout">Logout</a>
</nav>

<div class="container">
    <h2>Jobs</h2>
    <p class="small-muted">Welcome <?= htmlspecialchars($user_fullname ?? '') ?> — find roles that match your skills. Matching skills are highlighted.</p>

    <!-- FILTER -->
    <div class="filter-box" role="region" aria-label="Job filters">
        <form method="GET" id="filterForm">
            <div class="filter-grid">
                <div><input type="text" name="search" placeholder="Search by title or description" value="<?= htmlspecialchars($search) ?>"></div>
                <div>
                    <select name="track">
                        <option value="">All Tracks</option>
                        <option value="web" <?= $track=='web'?'selected':'' ?>>Web</option>
                        <option value="ai" <?= $track=='ai'?'selected':'' ?>>AI</option>
                        <option value="design" <?= $track=='design'?'selected':'' ?>>Design</option>
                        <option value="network" <?= $track=='network'?'selected':'' ?>>Network</option>
                    </select>
                </div>
                <div><input type="text" name="location" placeholder="Location (e.g. Dhaka)" value="<?= htmlspecialchars($location) ?>"></div>
                <div>
                    <select name="type">
                        <option value="">All Types</option>
                        <option value="full-time" <?= $type=='full-time'?'selected':'' ?>>Full Time</option>
                        <option value="part-time" <?= $type=='part-time'?'selected':'' ?>>Part Time</option>
                        <option value="internship" <?= $type=='internship'?'selected':'' ?>>Internship</option>
                        <option value="remote" <?= $type=='remote'?'selected':'' ?>>Remote</option>
                    </select>
                </div>

                <div>
                    <select name="sort">
                        <option value="">Sort By</option>
                        <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest</option>
                        <option value="oldest" <?= $sort=='oldest'?'selected':'' ?>>Oldest</option>
                        <option value="az" <?= $sort=='az'?'selected':'' ?>>A → Z</option>
                        <option value="za" <?= $sort=='za'?'selected':'' ?>>Z → A</option>
                    </select>
                </div>

                <div class="actions">
                    <button type="submit" class="btn">Apply</button>
                    <button type="button" id="clearFilters" class="btn secondary small">Clear</button>
                </div>
            </div>
        </form>
    </div>

    <!-- JOB LIST -->
    <div class="job-list" id="jobList">
        <?php if (empty($jobs)): ?>
            <div class="card"><p>No jobs found for your filters.</p></div>
        <?php endif; ?>

        <?php foreach ($jobs as $job): 
            // prepare JSON for modal embedding
            $job_json = htmlspecialchars(json_encode($job), ENT_QUOTES, 'UTF-8');
            // compute matched skills
            $matches = [];
            foreach ($job['requiredSkills'] as $js) {
                if (in_array(strtolower(trim($js)), array_map('strtolower', $user_skills))) {
                    $matches[] = $js;
                }
            }
        ?>
            <div class="card" data-job='<?= $job_json ?>'>
                <h3><?= htmlspecialchars($job['title']) ?></h3>
                <p><strong>Track:</strong> <?= htmlspecialchars($job['track']) ?> &nbsp; <strong>Type:</strong> <?= htmlspecialchars($job['type']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>

                <div class="tags" aria-hidden="false">
                    <?php foreach ($job['requiredSkills'] as $sk): 
                        $is_match = in_array(strtolower(trim($sk)), array_map('strtolower', $user_skills));
                    ?>
                        <span class="tag <?= $is_match ? 'match' : '' ?>"><?= htmlspecialchars($sk) ?></span>
                    <?php endforeach; ?>
                </div>

                <p style="margin-top:10px;color:var(--muted);font-size:14px">
                    <?= htmlspecialchars(substr($job['description'],0,220)) ?><?= strlen($job['description'])>220 ? '...' : '' ?>
                </p>

                <div class="controls">
                    <button class="btn view-details" type="button">View Details</button>
                    <a href="apply.php?id=<?= $job['id'] ?>"><button class="btn secondary" type="button">Apply</button></a>
                    <button class="btn secondary save-job" data-id="<?= $job['id'] ?>" type="button">Save</button>
                    <div style="margin-left:auto;text-align:right">
                        <div class="small-muted">Job ID: <?= htmlspecialchars($job['id']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination (preserve filters) -->
    <div class="pagination" aria-label="Pagination">
        <?php
        // show up to 7 pages with current highlighting
        $start = max(1, $page - 3);
        $end = min($total_pages, $page + 3);
        for ($i = $start; $i <= $end; $i++): ?>
            <?php $qs = build_qs(['page' => $i]); ?>
            <a href="?<?= $qs ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <!-- Saved jobs quick list -->
    <div class="saved-list" aria-live="polite">
        <h4>Saved jobs</h4>
        <div id="savedContainer"></div>
    </div>
</div>

<!-- JOB DETAILS MODAL -->
<div id="modalBackdrop" class="modal-backdrop" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal" role="document">
        <button class="close" id="modalClose" aria-label="Close">&times;</button>
        <div id="modalContent">
            <!-- populated by JS -->
        </div>
    </div>
</div>

<script>
// Helper: parse saved jobs from localStorage
function getSavedJobs() {
    try {
        return JSON.parse(localStorage.getItem('savedJobs') || '[]');
    } catch(e) { return []; }
}
function saveSavedJobs(arr) {
    localStorage.setItem('savedJobs', JSON.stringify(arr));
    renderSavedJobs();
}
function renderSavedJobs() {
    const container = document.getElementById('savedContainer');
    const saved = getSavedJobs();
    if (!saved.length) {
        container.innerHTML = '<p class="small-muted">No saved jobs yet.</p>';
        return;
    }
    container.innerHTML = '';
    saved.forEach(s => {
        const el = document.createElement('div');
        el.className = 'saved-item';
        el.innerHTML = `<div><strong>${escapeHtml(s.title)}</strong><div class="small-muted">${escapeHtml(s.location)} • ${escapeHtml(s.type)}</div></div>
                        <div>
                          <button class="btn small" onclick="openJobFromSaved(${s.id})">View</button>
                          <button class="btn secondary small" onclick="removeSaved(${s.id})">Remove</button>
                        </div>`;
        container.appendChild(el);
    });
}
function removeSaved(id) {
    const arr = getSavedJobs().filter(x => x.id != id);
    saveSavedJobs(arr);
}
function openJobFromSaved(id) {
    // find job card on page by data-job id
    const cards = document.querySelectorAll('[data-job]');
    for (const c of cards) {
        const job = JSON.parse(c.getAttribute('data-job'));
        if (Number(job.id) === Number(id)) {
            showModalWithJob(job);
            return;
        }
    }
    alert('Saved job details not available on this page.');
}

/* Utility: escape HTML for safe insertion */
function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }

/* VIEW DETAILS: open modal with full job info */
function showModalWithJob(job) {
    const modalBackdrop = document.getElementById('modalBackdrop');
    const content = document.getElementById('modalContent');
    let skillsHtml = '';
    if (Array.isArray(job.requiredSkills)) {
        skillsHtml = job.requiredSkills.map(sk => `<span class="tag">${escapeHtml(sk)}</span>`).join(' ');
    }
    content.innerHTML = `
        <h2>${escapeHtml(job.title)}</h2>
        <p style="color:var(--muted)"><strong>Track:</strong> ${escapeHtml(job.track || '')} &nbsp; <strong>Type:</strong> ${escapeHtml(job.type || '')} &nbsp; <strong>Location:</strong> ${escapeHtml(job.location || '')}</p>
        <div class="tags">${skillsHtml}</div>
        <div style="margin-top:12px">${escapeHtml(job.description || '')}</div>
        <div style="margin-top:14px">
            <a href="apply.php?id=${encodeURIComponent(job.id)}"><button class="btn">Apply</button></a>
            <button class="btn secondary" onclick="saveJobInline(${job.id}, ${JSON.stringify(escapeHtml(job.title))}, ${JSON.stringify(escapeHtml(job.location || ''))}, ${JSON.stringify(escapeHtml(job.type || ''))})">Save</button>
        </div>
    `;
    modalBackdrop.style.display = 'flex';
    modalBackdrop.setAttribute('aria-hidden','false');
}

/* close modal */
document.getElementById('modalClose').addEventListener('click', () => {
    document.getElementById('modalBackdrop').style.display = 'none';
    document.getElementById('modalBackdrop').setAttribute('aria-hidden','true');
});
document.getElementById('modalBackdrop').addEventListener('click', function(e){
    if (e.target === this) {
        this.style.display = 'none';
        this.setAttribute('aria-hidden','true');
    }
});

/* attach view-details to each card */
document.querySelectorAll('.view-details').forEach(btn => {
    btn.addEventListener('click', function(){
        const card = this.closest('[data-job]');
        const job = JSON.parse(card.getAttribute('data-job'));
        showModalWithJob(job);
    });
});

/* Save job button (from card) */
document.querySelectorAll('.save-job').forEach(btn => {
    btn.addEventListener('click', function(){
        const id = this.getAttribute('data-id');
        const card = this.closest('[data-job]');
        const job = JSON.parse(card.getAttribute('data-job'));
        saveJobInline(job.id, job.title, job.location || '', job.type || '');
    });
});

/* Save job inline helper */
function saveJobInline(id, title, location, type) {
    const saved = getSavedJobs();
    if (saved.find(s => Number(s.id) === Number(id))) {
        alert('Already saved.');
        return;
    }
    saved.push({id: id, title: title, location: location, type: type});
    saveSavedJobs(saved);
    alert('Saved to local bookmarks.');
}

/* Render saved jobs on load */
renderSavedJobs();

/* Clear filters button */
document.getElementById('clearFilters').addEventListener('click', function(){
    document.querySelectorAll('#filterForm input, #filterForm select').forEach(el => {
        if (el.name !== 'page') el.value = '';
    });
    document.getElementById('filterForm').submit();
});

/* preserve query params when clicking pagination? links already have them via server-side build */

/* highlight matched skills (client-side double-check)*/
(function highlightMatches() {
    // user's skills are embedded from server side
    const userSkills = <?= json_encode(array_map('strtolower', $user_skills)) ?: '[]' ?>;
    document.querySelectorAll('.tag').forEach(t => {
        const text = t.textContent.trim().toLowerCase();
        if (userSkills.includes(text)) t.classList.add('match');
    });
})();
</script>
</body>
</html>
