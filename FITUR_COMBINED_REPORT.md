# Fitur Combined Report - Gabungan Data Semua Session

## 📊 Overview

Fitur ini memungkinkan Anda melihat **gabungan data dari semua session** dalam satu project group. Data dari Session 1, Session 2, Session 3, dst akan diagregasi dan ditampilkan dalam satu dashboard komprehensif.

---

## 🎯 Features

### 1. **Automatic Data Aggregation**
- ✅ Total respondents dari semua session
- ✅ Total loves/shares dari semua session
- ✅ Total responses (senang, biasa, sedih)
- ✅ Weighted average age (consider jumlah responden per session)
- ✅ Demographics breakdown (male/female)
- ✅ Preferences aggregation

### 2. **Sessions Breakdown Table**
- ✅ List semua session dengan detail
- ✅ Tanggal & waktu setiap session
- ✅ Jumlah responden per session
- ✅ Loves/shares per session

### 3. **Report Details (Love/Share Timeline)**
- ✅ Love timeline dari semua session
- ✅ Share timeline dari semua session
- ✅ Badge session identifier (S1, S2, S3, dst)
- ✅ Sorted by second (descending)

### 4. **All Comments from All Sessions**
- ✅ Gabungan semua komentar
- ✅ Badge menunjukkan dari session mana
- ✅ Like/dislike indicators

### 5. **PDF Export**
- ✅ Download Combined Report dalam PDF
- ✅ Include semua sections (stats, demographics, sessions, timeline, comments)
- ✅ Smart pagination (limit 20 likes/shares, 30 comments untuk PDF)
- ✅ Professional layout dengan proper styling

---

## 🔧 Implementation

### **1. Backend - Controller**

**File:** `application/controllers/Admin.php`

**Method:** `view_combined_report($project_id)`

```php
public function view_combined_report($project_id)
{
    // Get combined stats from all sessions
    $combined = $this->Project_continuation_model->get_combined_stats($project_id);
    
    // Get all comments from all sessions
    $all_comments = [];
    foreach ($combined['sessions'] as $session) {
        $session_comments = $this->Stopwatch_model->get_comments_by_video_id($session['id']);
        foreach ($session_comments as $comment) {
            $comment['session_name'] = $session['nama_video'];
            $comment['session_number'] = $session['project_session'];
            $all_comments[] = $comment;
        }
    }
    
    // Load view
    $this->load->view('admin/combined_report', $data);
}
```

---

### **2. Backend - Model**

**File:** `application/models/Project_continuation_model.php`

**Method:** `get_combined_stats($project_id)`

**Data yang diagregasi:**
```php
[
    'total_sessions' => 3,
    'total_respondents' => 45,
    'total_love_count' => 150,
    'total_share_count' => 80,
    'total_response_senang' => 30,
    'total_response_biasa' => 10,
    'total_response_sedih' => 5,
    'total_male_count' => 25,
    'total_female_count' => 20,
    'average_age' => 32.5, // Weighted average!
    'total_pref_senang' => 35,
    'total_pref_biasa' => 8,
    'total_pref_marah' => 2,
    'sessions' => [...] // Array of individual sessions
]
```

---

### **3. Frontend - View**

**File:** `application/views/admin/combined_report.php`

**Sections:**

1. **Header Card**
   - Title: "Combined Report - All Sessions"
   - Project name
   - Download PDF button (red)
   - Back button

2. **Summary Stats Cards (4 cards)**
   - Total Sessions
   - Total Respondents
   - Total Loves
   - Total Shares

3. **Demographics Card**
   - Male/Female count
   - Average age (weighted)

4. **Responses Card**
   - Progress bars untuk Senang, Biasa, Sedih
   - Percentage calculation

5. **Preferences Card**
   - Senang, Biasa, Marah dengan icons

6. **Sessions Breakdown Table**
   - Detail setiap session
   - Sortable/filterable

7. **Report Details Section** ⭐ NEW
   - Love Timeline (Combined from all sessions)
   - Share Timeline (Combined from all sessions)
   - Session badges (S1, S2, S3, dst)
   - Scrollable tables (max-height: 300px)

8. **All Comments Section**
   - Semua komentar dari semua session
   - Badge session number

---

### **4. Frontend - Button**

**File:** `application/views/admin/admin_files.php`

**Button "Combined"** muncul hanya jika project memiliki **multiple sessions**:

```php
<?php if ($hasMultipleSessions): ?>
    <a href="<?= base_url('admin/view_combined_report/' . $file['id']); ?>"
       class="btn btn-link text-primary">
        <i class="material-symbols-rounded">analytics</i> Combined
    </a>
<?php endif; ?>
```

---

## 📱 User Interface

### **Combined Report Dashboard:**

```
┌───────────────────────────────────────────────────────────────┐
│  Combined Report - All Sessions   [📥 Download PDF] [← Back] │
│  Project: coba1233243                                         │
└───────────────────────────────────────────────────────────────┘

┌──────────────┬──────────────┬──────────────┬──────────────┐
│ Total        │ Total        │ Total        │ Total        │
│ Sessions: 3  │ Respond: 45  │ Loves: 150   │ Shares: 80   │
└──────────────┴──────────────┴──────────────┴──────────────┘

┌─────────────────────────┬─────────────────────────────────┐
│   Demographics          │     Responses                   │
│                         │                                 │
│   👨 Male: 25          │   😊 Senang: 30 (67%)          │
│   👩 Female: 20        │   ██████████████░░░░░          │
│                         │                                 │
│   🎂 Avg Age: 32.5     │   😐 Biasa: 10 (22%)           │
│                         │   ████████░░░░░░░░░░          │
│                         │                                 │
│                         │   😢 Sedih: 5 (11%)            │
│                         │   ████░░░░░░░░░░░░░░          │
└─────────────────────────┴─────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Preferences                                            │
│                                                         │
│   😄 Senang: 35    😐 Biasa: 8    😠 Marah: 2        │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Sessions Breakdown                                     │
├─────────────┬──────────┬──────┬───────┬────────┬────────┤
│ Session     │ Date     │ Resp │ Loves │ Shares │ Respon │
├─────────────┼──────────┼──────┼───────┼────────┼────────┤
│ Session 1   │ 10 Nov   │  15  │  50   │   25   │   15   │
│ Session 2   │ 11 Nov   │  18  │  60   │   30   │   18   │
│ Session 3   │ 12 Nov   │  12  │  40   │   25   │   12   │
└─────────────┴──────────┴──────┴───────┴────────┴────────┘

┌─────────────────────────────────────────────────────────┐
│  Report Details                                         │
├────────────────────────┬────────────────────────────────┤
│  Love Timeline         │  Share Timeline                │
│  (Combined)            │  (Combined)                    │
├────────────────────────┼────────────────────────────────┤
│  Detik 120 | 5 [S1]   │  Detik 95 | 3 [S2]           │
│  Detik 95  | 3 [S2]   │  Detik 80 | 2 [S1]           │
│  Detik 80  | 2 [S3]   │  Detik 65 | 4 [S3]           │
│  ...                   │  ...                           │
└────────────────────────┴────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  All Comments (45)                                      │
│                                                         │
│  [Session 1] John Doe 👍                               │
│  "Great content! Very informative."                     │
│  10 Nov 2025, 14:30                                    │
│                                                         │
│  [Session 2] Jane Smith 👎                             │
│  "Could be better..."                                   │
│  11 Nov 2025, 15:45                                    │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 How It Works

### **Scenario: Project dengan 3 Sessions**

```
Project: "Video Tutorial ABC"

Session 1 (ID: 100)
- 15 respondents (10 male, 5 female)
- Avg age: 30
- 50 loves, 25 shares
- 15 responses (10 senang, 3 biasa, 2 sedih)

Session 2 (ID: 101)  
- 18 respondents (8 male, 10 female)
- Avg age: 35
- 60 loves, 30 shares
- 18 responses (12 senang, 4 biasa, 2 sedih)

Session 3 (ID: 102)
- 12 respondents (7 male, 5 female)  
- Avg age: 32
- 40 loves, 25 shares
- 12 responses (8 senang, 3 biasa, 1 sedih)
```

### **Combined Report akan menampilkan:**

```
Total Sessions: 3
Total Respondents: 45 (15 + 18 + 12)
Total Loves: 150 (50 + 60 + 40)
Total Shares: 80 (25 + 30 + 25)

Demographics:
- Male: 25 (10 + 8 + 7)
- Female: 20 (5 + 10 + 5)
- Average Age: 32.5 (weighted average!)
  Calculation: (30*15 + 35*18 + 32*12) / 45 = 32.5

Responses:
- Senang: 30 (10 + 12 + 8) = 67%
- Biasa: 10 (3 + 4 + 3) = 22%
- Sedih: 5 (2 + 2 + 1) = 11%
```

---

## 📊 Weighted Average Age

**Why weighted?**

Simple average: `(30 + 35 + 32) / 3 = 32.33` ❌ WRONG!

Weighted average: `(30*15 + 35*18 + 32*12) / 45 = 32.5` ✅ CORRECT!

**Code:**
```php
$total_age_sum = 0;
$total_respondents = 0;

foreach ($sessions as $session) {
    $session_respondents = $session['session_male_count'] + $session['session_female_count'];
    if ($session_respondents > 0 && $session['session_average_age'] > 0) {
        $total_age_sum += $session['session_average_age'] * $session_respondents;
        $total_respondents += $session_respondents;
    }
}

$combined['average_age'] = round($total_age_sum / $total_respondents, 1);
```

---

## 🧪 Testing Guide

### **Test 1: Create Multiple Sessions**

```
1. Create Session 1 → Run survey → Save
   ✅ Data saved in Session 1

2. Continue → Create Session 2 → Run survey → Save  
   ✅ Data saved in Session 2

3. Continue → Create Session 3 → Run survey → Save
   ✅ Data saved in Session 3

4. Go to /admin/files
   ✅ Button "Combined" muncul di salah satu session
```

### **Test 2: View Combined Report**

```
1. Click "Combined" button
   ✅ Redirect ke /admin/view_combined_report/{id}
   ✅ Page loads tanpa error

2. Verify Summary Cards:
   ✅ Total Sessions = 3
   ✅ Total Respondents = sum dari semua session
   ✅ Total Loves = sum
   ✅ Total Shares = sum

3. Verify Demographics:
   ✅ Male count = sum
   ✅ Female count = sum
   ✅ Average age = weighted average (reasonable number)

4. Verify Responses:
   ✅ Progress bars menampilkan percentage benar
   ✅ Total = 100%

5. Verify Sessions Table:
   ✅ Semua 3 sessions muncul
   ✅ Data setiap session benar

6. Verify Comments:
   ✅ Semua komentar dari semua session muncul
   ✅ Badge session number benar
   ✅ Like/dislike indicators tampil
```

### **Test 3: Edge Cases**

```
1. Single Session (no continuation)
   ✅ Button "Combined" TIDAK muncul

2. Project dengan 0 respondents
   ✅ Average age = 0
   ✅ No division by zero error

3. Project dengan 0 responses
   ✅ Percentage = 0%
   ✅ Progress bars empty

4. Project dengan banyak comments
   ✅ Scrollable
   ✅ Performance OK
```

---

## 🎯 Benefits

1. **Comprehensive Analysis**
   - Lihat performa keseluruhan project
   - Bandingkan antar sessions

2. **Accurate Statistics**
   - Weighted average untuk akurasi
   - Proper data aggregation

3. **Time Saving**
   - Tidak perlu manual gabungkan data
   - One-click access

4. **Better Insights**
   - Trend analysis across sessions
   - Growth tracking

---

## 📝 Future Enhancements (Optional)

1. **Session Comparison**
   - Side-by-side comparison
   - Growth percentage

2. **Charts & Graphs**
   - Line chart: Performance over sessions
   - Bar chart: Comparison per session

3. **Export Combined Report** ✅ DONE!
   - ✅ PDF with all sessions data
   - ✅ Love/Share timeline included
   - ✅ Smart pagination (20 likes/shares, 30 comments)
   - ⏳ Excel with breakdown (future)

4. **Filters**
   - Filter by date range
   - Filter specific sessions

---

## 🔗 Related Files

**Controllers:**
- `application/controllers/Admin.php`
  - `view_combined_report()` - Display combined report
  - `download_combined_pdf()` - Generate PDF

**Models:**
- `application/models/Project_continuation_model.php` - `get_combined_stats()`

**Views:**
- `application/views/admin/combined_report.php` - Main combined report page (with Report Details)
- `application/views/admin/combined_pdf_template.php` - PDF template for combined report
- `application/views/admin/admin_files.php` - Button "Combined"

---

## ✅ Checklist

**Implementation:**
- [x] Backend controller method (view_combined_report)
- [x] Backend PDF download method (download_combined_pdf)
- [x] Model aggregation logic
- [x] View combined report page
- [x] Report Details section (Love/Share Timeline)
- [x] PDF template for combined report
- [x] Button in files list
- [x] Weighted average calculation

**Testing:**
- [ ] Multiple sessions test
- [ ] View combined report with Report Details
- [ ] Download PDF combined report
- [ ] Data accuracy verification
- [ ] Edge cases handling
- [ ] PDF generation with large datasets

**Documentation:**
- [x] Feature documentation
- [x] Code comments
- [x] User guide
- [x] PDF export documentation

---

**Version:** 2.0  
**Created:** 2025-11-13  
**Updated:** 2025-11-13 (Added Report Details + PDF Export)  
**Status:** ✅ READY FOR TESTING
