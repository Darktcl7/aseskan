# Dokumentasi Fitur Continue/Session Project

## 📋 Overview
Fitur Continue/Session memungkinkan Anda untuk melanjutkan project survey yang sudah ada ke session berikutnya (Session 2, 3, dst). Data dari setiap session akan **digabungkan secara otomatis** untuk analisis komprehensif.

---

## 🎯 Fitur Utama

### 1. **Create Continuation Project**
- Membuat session baru dari project yang sudah ada
- Session baru dimulai dengan data kosong (reset counters)
- Nama otomatis: `[Nama Project] - Session [N]`
- Semua session terhubung melalui `original_project_id`

### 2. **Multi-Session Tracking**
- Setiap session memiliki nomor urut (1, 2, 3, ...)
- Badge "Session N" ditampilkan di list project
- Info "Continued from project #X" untuk session lanjutan

### 3. **Combined Statistics**
- Agregasi otomatis data dari semua session
- Total responden dari semua session
- Rata-rata usia tertimbang (weighted average)
- Total love, share, responses dari semua session

---

## 🗄️ Struktur Database

### Table: `stopwatch_stats`
```sql
- id (PK)
- parent_project_id (FK ke id project sebelumnya)
- original_project_id (FK ke id project pertama)
- project_session (nomor session: 1, 2, 3, ...)
- is_continuation (0 = original, 1 = continuation)
- session_start_date (tanggal mulai session)
- session_end_date (tanggal selesai session - nullable)
- nama_video
- love_count, share_count, dll (data per session)
```

### Contoh Data:
```
ID  | parent_project_id | original_project_id | project_session | nama_video
----|-------------------|---------------------|-----------------|------------------
130 | NULL              | 130                 | 1               | "Video ABC"
157 | 130               | 130                 | 2               | "Video ABC - Session 2"
158 | 157               | 130                 | 3               | "Video ABC - Session 3"
```

---

## 💻 Implementasi Backend (PHP/CodeIgniter)

### Model: `Project_continuation_model.php`

#### **Method: `create_continuation($parent_project_id)`**
Membuat session baru dengan data reset.
```php
// Usage:
$new_project_id = $this->Project_continuation_model->create_continuation(130);
```

#### **Method: `get_all_sessions($project_id)`**
Mendapatkan semua session dari satu project group.
```php
// Returns array of all sessions (sorted by project_session ASC)
$sessions = $this->Project_continuation_model->get_all_sessions(130);
```

#### **Method: `get_combined_stats($project_id)`**
Menggabungkan data statistik dari semua session.
```php
$combined = $this->Project_continuation_model->get_combined_stats(130);
// Returns:
// [
//   'total_sessions' => 3,
//   'total_love_count' => 150,
//   'total_share_count' => 80,
//   'total_respondents' => 45,
//   'average_age' => 32.5,
//   'sessions' => [...], // array of individual sessions
//   ...
// ]
```

### Controller: `Admin.php`

#### **Method: `continue_project()`**
AJAX endpoint untuk continue project.

**Request:**
```javascript
POST /admin/continue_project
{
  project_id: 130,
  confirm: 0  // 0 = get info, 1 = create continuation
}
```

**Response (confirm = 0):**
```json
{
  "status": "confirm",
  "project": {...},
  "sessions": [{...}, {...}],
  "next_session": 2
}
```

**Response (confirm = 1):**
```json
{
  "status": "success",
  "message": "New session created: Video ABC - Session 2",
  "new_project_id": 157
}
```

#### **Method: `get_report_details($id)`**
Mendapatkan detail report termasuk combined stats.

**Response:**
```json
{
  "report": {...},
  "comments": [...],
  "comment_stats": {...},
  "all_sessions": [{...}, {...}],  // jika ada multi-session
  "combined_stats": {...}          // agregasi data
}
```

---

## 🎨 Implementasi Frontend (JavaScript)

### File: `assets/js/admin/files.js`

#### **Event Listener: Continue Button**
```javascript
listGroup.addEventListener('click', async function (e) {
    const continueButton = e.target.closest('.continue-project-btn');
    if (continueButton) {
        e.preventDefault();
        await handleContinueProject(continueButton);
    }
});
```

#### **Function: `handleContinueProject(button)`**
1. Fetch project info dan existing sessions
2. Tampilkan modal konfirmasi dengan list sessions
3. Jika user confirm, panggil `executeContinueProject()`

#### **Function: `executeContinueProject(projectId, projectName, confirmed)`**
1. Kirim request dengan `confirm=1`
2. Tampilkan loading modal
3. Jika sukses, redirect ke dashboard
4. Session data disimpan untuk digunakan di dashboard

---

## 🖼️ Implementasi View

### File: `views/admin/admin_files.php`

#### **Continue Button**
```php
<a class="btn btn-link text-success px-3 mb-0 continue-project-btn"
   href="#"
   data-project-id="<?= $file['id']; ?>"
   data-project-name="<?= htmlspecialchars($file['nama_video']); ?>"
   title="Continue this project">
    <i class="material-symbols-rounded text-sm me-2">play_arrow</i>Continue
</a>
```

#### **Session Badge**
```php
<?php if ($isContinuation): ?>
    <span class="badge badge-sm bg-gradient-info ms-2">
        Session <?= $sessionNumber; ?>
    </span>
<?php endif; ?>
```

#### **Statistics Card** (Optional)
```php
<div class="card mt-4">
    <div class="card-body p-3">
        <h6>Statistics</h6>
        <div class="d-flex justify-content-between">
            <span>Total Projects:</span>
            <span class="font-weight-bold"><?= count($files); ?></span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Continued Projects:</span>
            <span class="font-weight-bold"><?= $continuationCount; ?></span>
        </div>
    </div>
</div>
```

---

## 🔄 Workflow: Continue Project

### Step 1: User Click "Continue" Button
```
User → Click "Continue" button di admin/files
```

### Step 2: Fetch Project Info
```
JavaScript → POST /admin/continue_project (confirm=0)
Backend → Return project info + existing sessions
```

### Step 3: Show Confirmation Modal
```
JavaScript → Display SweetAlert modal
Modal → Show:
  - Current project name
  - List of existing sessions
  - Next session number
  - Warning notes
```

### Step 4: User Confirms
```
User → Click "Yes, Create Session N"
JavaScript → POST /admin/continue_project (confirm=1)
```

### Step 5: Create New Session
```
Backend → Project_continuation_model->create_continuation()
Backend → Insert new record to stopwatch_stats
Backend → Return new_project_id
```

### Step 6: Set Session Data
```
Backend → Set CI session:
  - continuing_project_id
  - continuing_project_name
  - continuing_project_data
  - original_project_id
```

### Step 7: Redirect to Dashboard
```
JavaScript → Show success message
JavaScript → Redirect to /admin (dashboard)
Dashboard → Load dengan data continuation project
```

---

## 📊 Menampilkan Combined Stats

### Di Modal Detail Report:

```javascript
// Jika ada combined_stats
if (data.combined_stats) {
    html += `
        <div class="alert alert-info">
            <h6><i class="fas fa-layer-group"></i> Combined Stats (All Sessions)</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Total Sessions:</strong> ${data.combined_stats.total_sessions}<br>
                    <strong>Total Respondents:</strong> ${data.combined_stats.total_respondents}<br>
                    <strong>Average Age:</strong> ${data.combined_stats.average_age}
                </div>
                <div class="col-md-6">
                    <strong>Total Loves:</strong> ${data.combined_stats.total_love_count}<br>
                    <strong>Total Shares:</strong> ${data.combined_stats.total_share_count}<br>
                    <strong>Total Responses:</strong> ${data.combined_stats.total_response_senang + 
                                                      data.combined_stats.total_response_biasa + 
                                                      data.combined_stats.total_response_sedih}
                </div>
            </div>
        </div>
    `;
}
```

---

## ✅ Testing Checklist

### Backend Testing:
- [ ] Create continuation dari project ID yang valid
- [ ] Verify data session baru (counters = 0)
- [ ] Verify `original_project_id` dan `parent_project_id` benar
- [ ] Test `get_all_sessions()` mengembalikan semua session
- [ ] Test `get_combined_stats()` agregasi data dengan benar
- [ ] Test weighted average age calculation

### Frontend Testing:
- [ ] Tombol "Continue" tampil di admin/files
- [ ] Badge "Session N" tampil untuk continuation project
- [ ] Modal konfirmasi menampilkan existing sessions
- [ ] Setelah confirm, session baru berhasil dibuat
- [ ] Redirect ke dashboard dengan session data
- [ ] Combined stats tampil di modal detail (jika ada multiple sessions)

### Integration Testing:
- [ ] Full flow: Click Continue → Confirm → Create → Redirect
- [ ] Session 1 → Session 2 → Session 3 (multiple continuations)
- [ ] Combined stats menggabungkan data dengan benar
- [ ] Comments tidak tercampur antar session

---

## 🐛 Troubleshooting

### Error: "404 Not Found" saat POST continue_project
**Solution:** Pastikan:
- `.htaccess` ada dan berfungsi
- `$config['index_page'] = '';` di `config/config.php`
- URL menggunakan `AppConfig.actionBaseUrl` (bukan hardcode)

### Error: "Project not found"
**Solution:** Pastikan `project_id` yang dikirim valid dan ada di database.

### Combined stats tidak muncul
**Solution:** 
- Pastikan project memiliki `is_continuation = 1` atau `original_project_id != NULL`
- Pastikan ada lebih dari 1 session di group project tersebut

### Data tidak digabungkan dengan benar
**Solution:** Periksa method `get_combined_stats()`:
- Pastikan semua field di-aggregate dengan benar
- Cek weighted average age calculation
- Debug dengan `print_r($combined)` sebelum return

---

## 📝 Notes & Best Practices

1. **Session Naming**: Gunakan naming convention yang jelas
   - Session 1: "Project ABC"
   - Session 2: "Project ABC - Session 2"
   - Session 3: "Project ABC - Session 3"

2. **Data Reset**: Setiap session baru **HARUS** reset counters ke 0
   - Jangan copy data dari parent project
   - Hanya copy metadata penting (nama, dll)

3. **Combined Stats**: Hitung **weighted average** untuk age
   - Tidak bisa simple average karena tiap session beda jumlah responden
   - Formula: `(age1*count1 + age2*count2) / (count1 + count2)`

4. **Session Management**: 
   - Simpan `original_project_id` untuk grouping
   - Simpan `parent_project_id` untuk tracking hierarchy
   - Simpan `project_session` untuk ordering

5. **UI/UX**:
   - Tampilkan badge "Session N" agar user aware
   - Tampilkan combined stats di modal detail
   - Sediakan filter/search untuk mencari session tertentu

---

## 🚀 Future Enhancements

1. **Session Comparison**
   - Compare stats antar session (Session 1 vs Session 2)
   - Chart perbandingan performance

2. **Session Management Page**
   - View semua sessions dalam satu page
   - Edit/Delete individual sessions
   - Merge/Split sessions

3. **Advanced Analytics**
   - Trend analysis across sessions
   - Responden growth tracking
   - Session-wise performance metrics

4. **Export Features**
   - Export combined stats to PDF
   - Export all sessions to Excel
   - Include session breakdown in report

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek dokumentasi ini terlebih dahulu
2. Review code di:
   - `application/models/Project_continuation_model.php`
   - `application/controllers/Admin.php` (method `continue_project`)
   - `assets/js/admin/files.js` (search "handleContinueProject")
3. Debug dengan browser console (lihat log 🔄 dan 📥)
4. Cek database untuk verify data structure

---

**Last Updated:** 2025-11-13  
**Version:** 1.0  
**Author:** Droid AI Assistant
