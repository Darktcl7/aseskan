# Fix: Double Save Issue - Continuation Project

## 🐛 Problem
Saat save continuation project, data tersimpan **2 kali** (double):
- Session 4 dibuat saat click "Continue" ✓
- Session 5 dibuat lagi saat click "Save" ✗ (DUPLICATE!)

**Result:** File list menampilkan 2 session dengan nama sama tapi ID berbeda.

---

## 🔍 Root Cause

### Original Flow (SALAH):
```
1. Admin/files → Click "Continue" button
   └─> continue_project() → Create Session 4 (ID: 163)
   └─> Set session: continuing_project_id = 163

2. Dashboard → Click "Ready to Start"
   └─> Set preview name
   └─> Start survey

3. Dashboard → Click "Save" button
   └─> save_report() → INSERT Session 5 (ID: 164) ✗ WRONG!
   └─> Result: Double records!
```

**Problem:** Method `save_report()` selalu **INSERT** record baru untuk continuing project, padahal session sudah dibuat di step 1.

---

## ✅ Solution

### Corrected Flow:
```
1. Admin/files → Click "Continue" button
   └─> continue_project() → Create Session 4 (ID: 163) ✓
   └─> Set session: continuing_project_id = 163

2. Dashboard → Click "Ready to Start"
   └─> Set preview name
   └─> Start survey (data mulai dari 0)

3. Dashboard → Click "Save" button
   └─> save_report() → UPDATE Session 4 (ID: 163) ✓ CORRECT!
   └─> Result: Only 1 record!
```

**Fix:** Method `save_report()` sekarang **UPDATE** session yang sudah ada (bukan insert baru).

---

## 🔧 Code Changes

### File: `application/controllers/Admin.php`

#### **Before (WRONG):**
```php
if ($is_continuing) {
    // ❌ SALAH: INSERT session baru lagi
    $last_session = $this->Project_continuation_model->get_last_session($continuing_project_id);
    $next_session = $last_session + 1;
    
    $this->db->insert('stopwatch_stats', [
        'project_session' => $next_session,
        'is_continuation' => 1,
        // ... data lainnya
    ]);
    $updated_video_id = $this->db->insert_id(); // New ID!
}
```

#### **After (CORRECT):**
```php
if ($is_continuing) {
    // ✅ BENAR: UPDATE session yang sudah ada
    $current_project = $this->Stopwatch_model->get_stats_by_id($continuing_project_id);
    
    $this->db->where('id', $continuing_project_id);
    $this->db->update('stopwatch_stats', [
        'love_count' => (int)$love_count,
        'share_count' => (int)$share_count,
        'session_end_date' => date('Y-m-d H:i:s'),
        // ... data lainnya
    ]);
    $updated_video_id = $continuing_project_id; // Same ID!
}
```

---

## 📊 Database Impact

### Before Fix:
```sql
-- Session 4 dibuat saat "Continue"
INSERT INTO stopwatch_stats (nama_video, project_session, ...) 
VALUES ('coba1233243 - Session 4', 4, ...);
-- ID: 163

-- Session 5 dibuat lagi saat "Save" ❌ DUPLICATE!
INSERT INTO stopwatch_stats (nama_video, project_session, ...) 
VALUES ('coba1233243 - Session 4', 5, ...);
-- ID: 164
```

### After Fix:
```sql
-- Session 4 dibuat saat "Continue"
INSERT INTO stopwatch_stats (nama_video, project_session, ...) 
VALUES ('coba1233243 - Session 4', 4, ...);
-- ID: 163

-- Session 4 di-update saat "Save" ✓ CORRECT!
UPDATE stopwatch_stats 
SET love_count = 22, share_count = 11, session_end_date = '2025-11-13 17:52:00', ...
WHERE id = 163;
-- Same ID: 163
```

---

## 🧪 Testing Steps

### Test 1: Single Session Save
```
1. Go to /admin/files
2. Click "Continue" on existing project
   ✅ Verify: Modal shows existing sessions
   ✅ Verify: "Session N" will be created
   
3. Confirm creation
   ✅ Verify: Success message
   ✅ Verify: Redirect to dashboard
   ✅ Verify: "Continuing Project" card shows correct name

4. Click "Ready to Start"
   ✅ Verify: No errors
   ✅ Verify: Start button enabled

5. Click "Start" → Run survey → Click "Save"
   ✅ Verify: Success message: "Session data saved successfully!"
   ✅ Verify: Only 1 new record in database
```

### Test 2: Verify Database
```sql
-- Check for duplicates
SELECT id, nama_video, project_session, is_continuation, 
       parent_project_id, session_start_date, session_end_date
FROM stopwatch_stats
WHERE nama_video LIKE '%Session%'
ORDER BY id DESC;

-- Expected result:
-- ✅ Each session has unique ID
-- ✅ No duplicate session numbers
-- ✅ session_end_date filled after save
```

### Test 3: Multiple Sessions
```
1. Create Session 2 from Session 1 → Save
2. Create Session 3 from Session 2 → Save
3. Create Session 4 from Session 3 → Save

✅ Expected:
   - 4 records total (Session 1, 2, 3, 4)
   - Each with unique ID
   - Each with correct parent_project_id
   - All share same original_project_id
```

---

## 🎯 Key Changes Summary

1. **`continue_project()` method:**
   - ✅ Creates NEW session (INSERT)
   - ✅ Sets `continuing_project_id` in session
   - ✅ Redirects to dashboard

2. **`save_report()` method:**
   - ✅ **Changed:** UPDATE existing session (was: INSERT new)
   - ✅ Uses `continuing_project_id` from session
   - ✅ Sets `session_end_date` to mark completion
   - ✅ Clears session variables after save

3. **Session variables cleared after save:**
   - `continuing_project_id`
   - `continuing_project_name`
   - `continuing_project_data`
   - `original_project_id`

---

## 📝 Updated Messages

### Before:
```
"Sesi baru untuk proyek lanjutan berhasil disimpan."
```

### After:
```
"Session data saved successfully! Data will be combined with previous sessions."
```

More clear and accurate messaging!

---

## 🔄 Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│ Step 1: Continue Project                                        │
├─────────────────────────────────────────────────────────────────┤
│ Admin/files → Click "Continue"                                  │
│   ↓                                                              │
│ continue_project() → INSERT new session                          │
│   ↓                                                              │
│ Session 4 created (ID: 163)                                     │
│   ↓                                                              │
│ Set continuing_project_id = 163                                 │
└─────────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│ Step 2: Start Survey                                            │
├─────────────────────────────────────────────────────────────────┤
│ Dashboard → Click "Ready to Start"                              │
│   ↓                                                              │
│ Set preview name for Session 4                                  │
│   ↓                                                              │
│ Click "Start" → Survey begins                                   │
│   ↓                                                              │
│ Collect data (starts from 0)                                    │
└─────────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────────┐
│ Step 3: Save Data                                               │
├─────────────────────────────────────────────────────────────────┤
│ Dashboard → Click "Save"                                        │
│   ↓                                                              │
│ save_report() → UPDATE Session 4 (ID: 163)                      │
│   ↓                                                              │
│ Set session_end_date                                            │
│   ↓                                                              │
│ Clear continuing_project_id                                     │
│   ↓                                                              │
│ Success! Only 1 record (ID: 163)                                │
└─────────────────────────────────────────────────────────────────┘
```

---

## ⚠️ Important Notes

1. **Session Creation:**
   - Session dibuat saat **"Continue"**, bukan saat **"Save"**
   - Counter dimulai dari 0 untuk session baru

2. **Data Update:**
   - **"Save"** hanya update data survey, tidak create session baru
   - `session_end_date` di-set untuk mark completion

3. **Session Variables:**
   - Di-set saat "Continue"
   - Di-clear setelah "Save" berhasil
   - Prevent conflict dengan project berikutnya

4. **Combined Stats:**
   - Tetap bekerja dengan benar
   - Aggregate data dari semua sessions
   - Weighted average age calculation

---

## 🚀 Deployment Checklist

Before deploying to production:

- [x] Fix `save_report()` method (UPDATE instead of INSERT)
- [x] Update success message
- [x] Clear all session variables after save
- [x] Test single session save
- [ ] Test multiple sessions (2-3 sessions)
- [ ] Verify no duplicate records in database
- [ ] Test combined stats accuracy
- [ ] Delete old duplicate records if any

---

## 🗑️ Cleanup Old Duplicates (Optional)

If you have duplicate records from testing:

```sql
-- Find duplicates
SELECT nama_video, project_session, COUNT(*) as count
FROM stopwatch_stats
GROUP BY nama_video, project_session
HAVING count > 1;

-- Delete duplicates (keep the earlier one)
-- CAREFUL! Test on staging first!
DELETE s1 FROM stopwatch_stats s1
INNER JOIN stopwatch_stats s2 
WHERE s1.id > s2.id 
  AND s1.nama_video = s2.nama_video 
  AND s1.project_session = s2.project_session;
```

---

**Fixed:** 2025-11-13  
**Version:** 1.1  
**Status:** ✅ RESOLVED
