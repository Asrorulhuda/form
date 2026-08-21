# PRD — ASR-FORM

### Platform Form Builder & Document Generator berbasis PHP Native

**Versi:** 1.0
**Platform:** Web Application
**Backend:** PHP Native 8.2+
**Database:** MySQL 8.x
**Frontend:** HTML5, CSS3, JavaScript ES6+, Bootstrap 5 / Tailwind CSS
**UI:** Modern, responsive, mobile-friendly
**PDF:** Dompdf
**Editor:** Rich Text Editor
**Authentication:** Session-based authentication

---

## 1. Gambaran Umum

FORMORA adalah aplikasi web mandiri untuk membuat **formulir digital custom seperti Google Forms** sekaligus membuat **surat/dokumen otomatis berdasarkan template**.

Aplikasi tidak bergantung pada SIPANDA dan memiliki database, user management, konfigurasi, serta sistem penyimpanan sendiri.

Dua fitur utama:

1. **Form Builder**
2. **Document / Letter Generator**

Keduanya menggunakan sistem **Dynamic Field** sehingga administrator tidak perlu membuat tabel database baru setiap kali membuat form atau template surat baru.

---

# 2. Tujuan Aplikasi

Aplikasi harus memungkinkan pengguna:

* membuat form tanpa coding
* menambahkan field secara bebas
* mengatur properti setiap field
* mengatur urutan field dengan drag & drop
* membuat form publik
* menerima respons
* melihat respons
* export respons
* membuat template surat
* menggunakan variable dinamis dalam surat
* mengisi surat berdasarkan data form
* menghasilkan PDF
* mencetak surat
* membuat nomor surat otomatis
* menyimpan arsip dokumen
* melakukan approval dokumen
* melakukan verifikasi dokumen menggunakan QR Code

---

# 3. Konsep Utama

Sistem memiliki dua engine:

```text
FORM ENGINE
     │
     ├── Form
     ├── Fields
     ├── Responses
     └── Dynamic Data
             │
             ▼
DOCUMENT ENGINE
     │
     ├── Template
     ├── Variables
     ├── Document
     ├── Approval
     └── PDF
```

Contoh:

```text
Form:
Pengajuan Surat Keterangan

Fields:
nama
nik
tempat_lahir
tanggal_lahir
alamat
keperluan

        ↓

Document Template:

{{nama}}
{{nik}}
{{tempat_lahir}}
{{tanggal_lahir}}
{{alamat}}
{{keperluan}}

        ↓

PDF Surat
```

---

# 4. Role User

Minimal terdapat:

### Super Admin

Hak akses:

* semua fitur
* user management
* system settings
* backup
* template
* form
* dokumen
* approval
* audit log

### Admin

* membuat form
* membuat template
* melihat respons
* membuat dokumen
* mengelola arsip

### Editor

* membuat/edit form
* membuat/edit template
* tidak dapat mengubah system settings

### Operator

* mengisi form internal
* membuat dokumen
* mencetak dokumen
* melihat dokumen yang diizinkan

### Approver

* melihat dokumen yang menunggu persetujuan
* approve
* reject
* memberikan catatan

### User

* mengisi form publik/internal
* melihat dokumen miliknya jika diizinkan

---

# 5. Dashboard

Dashboard modern menggunakan layout:

```text
┌─────────────────────────────────────────────────────┐
│ FORMORA                         🔔  User ▼           │
├──────────────┬──────────────────────────────────────┤
│              │                                      │
│ Dashboard    │  Selamat datang, Ahmad              │
│              │                                      │
│ Forms        │  ┌──────┐ ┌──────┐ ┌──────┐         │
│ Documents    │  │ 128  │ │  42  │ │  17  │         │
│ Templates    │  │ Forms│ │ Docs │ │Pending│        │
│ Responses    │  └──────┘ └──────┘ └──────┘         │
│ Users        │                                      │
│ Settings     │  Aktivitas Terbaru                  │
│              │  ─────────────────────────           │
└──────────────┴──────────────────────────────────────┘
```

Dashboard menampilkan:

* jumlah form
* jumlah respons
* jumlah dokumen
* dokumen pending
* dokumen selesai
* aktivitas terakhir
* grafik respons
* grafik dokumen

---

# 6. FORM BUILDER

Menu:

```text
Forms
├── All Forms
├── Create Form
├── Templates
└── Trash
```

## Create Form

Editor menggunakan drag & drop.

Sidebar kiri:

```text
ADD FIELD

Text
Textarea
Number
Email
Phone
Date
Time
Date & Time
Dropdown
Radio
Checkbox
File
Image
Rating
Scale
Signature
Heading
Description
Section
```

Canvas:

```text
┌─────────────────────────────────────────┐
│ Form Title                              │
│ Description                             │
├─────────────────────────────────────────┤
│ Nama Lengkap                       ⋮⋮   │
│ [____________________________]           │
│                                         │
├─────────────────────────────────────────┤
│ Jenis Kelamin                       ⋮⋮  │
│ ○ Laki-laki                             │
│ ○ Perempuan                             │
└─────────────────────────────────────────┘
```

Panel kanan:

```text
FIELD SETTINGS

Label
[ Nama Lengkap ]

Field Name
[ nama_lengkap ]

Placeholder
[ Masukkan nama ]

Required
[ ON ]

Validation
Min Length
Max Length

Help Text
[ Masukkan nama lengkap ]

Visibility
[ Always visible ]
```

---

# 7. FIELD TYPES

Sistem minimal mendukung:

### Text

```text
label
name
placeholder
required
min_length
max_length
default_value
```

### Number

```text
min
max
step
required
```

### Email

Validasi email otomatis.

### Phone

Validasi nomor telepon.

### Textarea

Untuk teks panjang.

### Date

Date picker.

### Time

Time picker.

### DateTime

Tanggal + waktu.

### Dropdown

Contoh:

```text
Kelas
- 1A
- 1B
- 2A
- 2B
```

### Radio

Single choice.

### Checkbox

Multiple choice.

### File Upload

Konfigurasi:

* max size
* allowed extension
* jumlah file

### Image Upload

Untuk foto.

### Rating

```text
☆ ☆ ☆ ☆ ☆
```

### Scale

```text
1 ───────── 10
```

### Signature

Canvas tanda tangan digital.

### Heading

Tidak membutuhkan input.

### Description

Informasi tambahan.

### Section

Membagi form menjadi beberapa bagian.

---

# 8. CONDITIONAL LOGIC

Form harus mendukung kondisi.

Contoh:

```text
Apakah memiliki KIP?
       │
       ├── Ya
       │    ↓
       │  Nomor KIP muncul
       │
       └── Tidak
            ↓
          Nomor KIP disembunyikan
```

Operator:

```text
equals
not equals
contains
not contains
greater than
less than
is empty
is not empty
```

Action:

```text
show field
hide field
require field
unrequire field
```

---

# 9. FORM SETTINGS

Pengaturan:

### General

* title
* description
* logo
* cover image
* theme

### Submission

* one response per user
* allow edit response
* confirmation message
* redirect URL

### Security

* public/private
* login required
* password
* captcha
* submission limit

### Notification

* email notification
* admin notification
* webhook

---

# 10. PUBLIC FORM

Setiap form memiliki URL:

```text
/form/{slug}
```

Contoh:

```text
/form/pendaftaran-ekstrakurikuler
```

Halaman publik harus:

* cepat
* responsive
* mobile friendly
* tidak menampilkan sidebar admin
* memiliki progress bar jika banyak section

---

# 11. RESPONSE SYSTEM

Menu:

```text
Responses
```

Tampilan:

```text
Total Responses: 1.245

Nama           Kelas       Tanggal
--------------------------------------
Ahmad          6A          20/08/2026
Budi           6B          20/08/2026
Citra          6A          20/08/2026
```

Fitur:

* search
* filter
* sort
* pagination
* detail response
* edit response
* delete
* export Excel
* export CSV
* export PDF

---

# 12. DOCUMENT GENERATOR

Menu:

```text
Documents
├── All Documents
├── Create Document
├── Templates
├── Pending Approval
└── Archive
```

---

# 13. DOCUMENT TEMPLATE BUILDER

Template menggunakan rich text editor.

Contoh:

```text
                    SURAT KETERANGAN

Nomor: {{nomor_surat}}

Yang bertanda tangan di bawah ini:

Nama       : {{nama_pejabat}}
Jabatan    : {{jabatan}}

Menerangkan bahwa:

Nama       : {{nama}}
NIK        : {{nik}}
Tempat/Tgl : {{tempat_lahir}}, {{tanggal_lahir}}
Alamat     : {{alamat}}

Adalah benar ....................................

Demikian surat ini dibuat untuk digunakan
sebagaimana mestinya.

{{kota}}, {{tanggal}}

{{jabatan}}

{{nama_pejabat}}
NIP. {{nip}}
```

---

# 14. VARIABLE SYSTEM

Variable menggunakan:

```text
{{nama}}
{{nik}}
{{alamat}}
{{kelas}}
{{tanggal}}
```

Variable dapat berasal dari:

### System

```text
{{tanggal}}
{{bulan}}
{{tahun}}
{{nomor_surat}}
```

### User

```text
{{user_name}}
{{user_email}}
```

### Form

```text
{{nama_siswa}}
{{kelas}}
{{nisn}}
```

### Custom

Admin dapat membuat variable sendiri.

---

# 15. VARIABLE PICKER

Jangan mengharuskan user mengetik variable manual.

Editor menyediakan tombol:

```text
[ + Insert Variable ]
```

Kemudian:

```text
System
├── Tanggal
├── Bulan
├── Tahun
└── Nomor Surat

Form Data
├── Nama
├── NISN
├── Kelas
└── Alamat
```

Klik variable → otomatis masuk ke dokumen.

---

# 16. NOMOR SURAT

Sistem nomor surat otomatis.

Contoh format:

```text
{{sequence}}/{{code}}/{{month}}/{{year}}
```

Hasil:

```text
087/SK/08/2026
```

Bisa menggunakan format:

```text
{{sequence}}
{{roman_month}}
{{month}}
{{year}}
{{code}}
```

Admin dapat membuat beberapa konfigurasi nomor surat.

---

# 17. DOCUMENT WORKFLOW

Status:

```text
DRAFT
   ↓
SUBMITTED
   ↓
PENDING APPROVAL
   ↓
APPROVED
   ↓
GENERATED
   ↓
ARCHIVED
```

Jika ditolak:

```text
PENDING
   ↓
REJECTED
   ↓
DRAFT
```

Approver dapat memberikan:

```text
Catatan:
"Perbaiki nomor surat."
```

---

# 18. PDF GENERATOR

Gunakan:

**Dompdf**

Fitur:

* generate PDF
* preview PDF
* download
* print
* archive

PDF harus mempertahankan:

* kop surat
* margin
* font
* tabel
* gambar
* tanda tangan
* QR Code

---

# 19. QR CODE VERIFICATION

Setiap dokumen resmi mendapatkan:

```text
Document ID
Verification Token
QR Code
```

QR mengarah ke:

```text
/verify/{token}
```

Halaman:

```text
✓ DOKUMEN VALID

Nomor Dokumen
SK/087/VIII/2026

Jenis Dokumen
Surat Keterangan

Tanggal
20 Agustus 2026

Status
VALID
```

Tidak menampilkan data sensitif secara berlebihan.

---

# 20. SIGNATURE

Sistem mendukung:

### Signature Image

Upload tanda tangan PNG transparan.

### Digital Signature

User menandatangani menggunakan mouse/touchscreen.

Contoh:

```text
┌──────────────────────────────┐
│                              │
│       Area Tanda Tangan      │
│                              │
└──────────────────────────────┘

[ Clear ]       [ Save ]
```

---

# 21. TEMPLATE SYSTEM

Template dapat dikategorikan:

```text
Surat
├── Surat Keterangan
├── Surat Pengantar
├── Surat Tugas
├── Surat Pernyataan
├── Surat Undangan
└── Surat Izin

Form
├── Pendaftaran
├── Survey
├── Pendataan
└── Evaluasi
```

Template dapat:

* duplicate
* edit
* archive
* delete
* set as default

---

# 22. DATABASE

Gunakan struktur relational yang fleksibel.

### users

```text
id
name
email
password
role_id
status
created_at
updated_at
```

### roles

```text
id
name
permissions
```

### forms

```text
id
user_id
title
slug
description
status
settings_json
created_at
updated_at
```

### form_fields

```text
id
form_id
field_type
field_name
label
description
placeholder
options_json
validation_json
conditional_json
settings_json
sort_order
is_required
created_at
updated_at
```

### form_responses

```text
id
form_id
respondent_id
submitted_at
ip_address
user_agent
```

### form_response_values

```text
id
response_id
field_id
value_text
value_json
```

### document_templates

```text
id
user_id
name
category
content
settings_json
status
created_at
updated_at
```

### documents

```text
id
template_id
form_response_id
document_number
title
content
status
verification_token
created_by
approved_by
approved_at
created_at
updated_at
```

### document_variables

```text
id
template_id
variable_name
label
source_type
source_key
default_value
```

### approvals

```text
id
document_id
approver_id
status
notes
approved_at
```

### file_uploads

```text
id
user_id
filename
stored_filename
mime_type
size
path
created_at
```

### audit_logs

```text
id
user_id
action
module
record_id
description
ip_address
created_at
```

---

# 23. JSON UNTUK FIELD

Konfigurasi field disimpan sebagai JSON.

Contoh:

```json
{
  "placeholder": "Masukkan nama lengkap",
  "min_length": 3,
  "max_length": 100
}
```

Options:

```json
[
  {
    "label": "Laki-laki",
    "value": "L"
  },
  {
    "label": "Perempuan",
    "value": "P"
  }
]
```

Dengan pendekatan ini, aplikasi tidak perlu mengubah struktur database setiap kali admin membuat form baru.

---

# 24. DESIGN SYSTEM

Desain harus modern, bersih dan profesional.

Gaya:

* dashboard SaaS modern
* rounded card
* subtle shadow
* whitespace cukup
* sidebar collapsible
* responsive
* dark mode optional
* toast notification
* modal modern
* skeleton loading
* empty state
* confirmation dialog

Warna utama dapat menggunakan:

```text
Primary: #4F46E5
Success: #10B981
Warning: #F59E0B
Danger: #EF4444
```

Namun warna harus disimpan dalam CSS variable agar mudah diganti.

---

# 25. RESPONSIVE

Wajib optimal untuk:

```text
Desktop
Laptop
Tablet
Mobile
```

Form publik harus diprioritaskan untuk mobile.

Admin dashboard:

```text
Desktop → Sidebar + Content

Tablet → Collapsible Sidebar

Mobile → Bottom/Drawer Navigation
```

---

# 26. SECURITY

Implementasi wajib:

### Password

Gunakan:

```php
password_hash()
password_verify()
```

### SQL

Gunakan:

```php
PDO + Prepared Statement
```

### Session

* secure session
* session regeneration
* timeout
* logout

### CSRF

Semua POST/PUT/DELETE menggunakan CSRF token.

### XSS

Sanitize output.

### Upload

Validasi:

* MIME
* extension
* file size
* random filename
* jangan menyimpan file upload executable

### Authorization

Setiap endpoint harus melakukan pengecekan permission.

---

# 27. STRUKTUR PROJECT

Gunakan struktur PHP Native yang terorganisasi:

```text
formora/
│
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Helpers/
│   ├── Middleware/
│   └── Core/
│
├── config/
│   ├── app.php
│   └── database.php
│
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── uploads/
│
├── routes/
│   └── web.php
│
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── dashboard/
│   ├── forms/
│   ├── responses/
│   ├── documents/
│   ├── templates/
│   └── settings/
│
├── storage/
│   ├── logs/
│   ├── cache/
│   └── generated/
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── vendor/
│
├── .env
├── composer.json
└── README.md
```

---

# 28. ARSITEKTUR

Walaupun PHP Native, jangan membuat semua kode dalam satu file.

Gunakan pola:

```text
Route
  ↓
Controller
  ↓
Service
  ↓
Model
  ↓
Database
```

Contoh:

```text
FormController
      ↓
FormService
      ↓
FormModel
      ↓
MySQL
```

Untuk generate PDF:

```text
DocumentController
        ↓
DocumentService
        ↓
TemplateEngine
        ↓
VariableResolver
        ↓
PDFService
        ↓
Dompdf
```

---

# 29. API INTERNAL

Gunakan endpoint JSON untuk fitur dinamis.

Contoh:

```text
/api/forms
/api/forms/{id}
/api/forms/{id}/fields
/api/forms/{id}/responses
/api/documents
/api/templates
/api/variables
```

Response standar:

```json
{
  "success": true,
  "message": "Data berhasil disimpan",
  "data": {}
}
```

---

# 30. AJAX / FETCH

Gunakan JavaScript:

```javascript
fetch()
```

untuk:

* tambah field
* edit field
* drag & drop
* autosave
* preview
* conditional logic
* delete
* update settings

Tidak perlu reload halaman untuk setiap perubahan kecil.

---

# 31. AUTOSAVE

Form Builder harus memiliki autosave.

Contoh:

```text
Saving...
    ↓
Saved ✓
```

Jika browser tertutup, draft tetap tersimpan.

---

# 32. PREVIEW

Form Builder memiliki:

```text
[ Edit ] [ Preview ] [ Publish ]
```

Preview dapat menampilkan:

```text
Desktop
Tablet
Mobile
```

---

# 33. DUPLICATE

Semua form dan template dapat:

```text
Duplicate
```

Misalnya:

```text
Form Pendaftaran Siswa
       ↓ Duplicate
Form Pendaftaran Siswa 2027
```

---

# 34. SEARCH & FILTER

Semua data harus memiliki:

* search
* filter
* sorting
* pagination

Untuk data besar gunakan server-side pagination.

---

# 35. NOTIFICATION

Gunakan toast:

```text
✓ Form berhasil disimpan
✓ Template berhasil dibuat
✓ Dokumen berhasil diterbitkan
✕ Gagal menyimpan data
```

---

# 36. SETTINGS

### Application

* nama aplikasi
* logo
* favicon
* timezone
* date format

### Email

* SMTP host
* SMTP port
* username
* password

### Storage

* upload limit
* allowed extensions

### Document

* default paper
* default margin
* default font

### Numbering

* format nomor surat
* reset sequence

---

# 37. AUDIT LOG

Catat aktivitas penting:

```text
20/08/2026 12:05
Ahmad membuat form "Pendaftaran Siswa"

20/08/2026 12:15
Ahmad mengubah template "Surat Keterangan"

20/08/2026 12:20
Budi menyetujui dokumen #DOC-001
```

---

# 38. BACKUP

Super Admin dapat:

```text
Backup Database
Backup Files
Download Backup
```

Sistem menghasilkan:

```text
backup_2026-08-20.zip
```

---

# 39. REQUIREMENT NON-FUNCTIONAL

Aplikasi harus:

* PHP Native
* tidak menggunakan Laravel
* menggunakan PDO
* MySQL 8+
* PHP 8.2+
* responsive
* aman
* modular
* mudah dipelihara
* mudah di-deploy ke hosting/cPanel
* tidak bergantung pada SIPANDA
* memiliki konfigurasi `.env`
* menggunakan Composer untuk dependency eksternal

---

# 40. PHASE DEVELOPMENT

### Phase 1 — Core

* authentication
* dashboard
* user
* roles
* database
* settings

### Phase 2 — Form Builder

* create form
* field builder
* drag & drop
* field settings
* validation
* public form
* response

### Phase 3 — Advanced Form

* conditional logic
* file upload
* signature
* autosave
* templates
* export

### Phase 4 — Document Generator

* template
* rich text editor
* variable engine
* document generation
* numbering

### Phase 5 — Workflow

* approval
* rejection
* archive
* audit log

### Phase 6 — PDF

* Dompdf
* QR Code
* signature
* print
* verification

### Phase 7 — Polish

* responsive
* dark mode
* performance
* security audit
* UX improvement

---

# 41. ACCEPTANCE CRITERIA

Aplikasi dianggap berhasil apabila:

* Admin dapat membuat form tanpa coding.
* Admin dapat menambahkan minimal 15 tipe field.
* Field dapat diurutkan dengan drag & drop.
* Setiap field memiliki konfigurasi sendiri.
* Form dapat dipublikasikan.
* User dapat mengisi form melalui HP.
* Respons tersimpan dengan benar.
* Respons dapat diekspor.
* Admin dapat membuat template surat.
* Template mendukung variable.
* Variable dapat berasal dari response form.
* Dokumen dapat dibuat otomatis dari response.
* Nomor surat dapat dibuat otomatis.
* Dokumen dapat melalui approval.
* Dokumen dapat menjadi PDF.
* PDF memiliki QR Code verifikasi.
* Dokumen dapat diverifikasi melalui URL.
* Semua data memiliki permission.
* Semua input terlindungi dari SQL Injection, XSS dan CSRF.
* Aplikasi dapat berjalan pada hosting PHP + MySQL standar.

---

# 42. HASIL AKHIR YANG DIHARAPKAN

Target akhirnya bukan sekadar aplikasi form.

FORMORA harus menjadi:

```text
                 FORMORA
                    │
       ┌────────────┴────────────┐
       │                         │
  FORM BUILDER             DOCUMENT BUILDER
       │                         │
       ├─ Dynamic Fields         ├─ Templates
       ├─ Logic                  ├─ Variables
       ├─ Responses              ├─ Numbering
       ├─ Upload                 ├─ Approval
       └─ Export                 ├─ PDF
                                 ├─ Signature
                                 └─ QR Verify
```

**Prinsip utama development:** jangan membuat fitur form dan surat sebagai dua sistem terpisah. Bangun **Dynamic Field + Variable Engine** sebagai fondasi utama. Dengan begitu, nantinya aplikasi bisa dikembangkan lagi menjadi generator **surat, sertifikat, kartu, laporan, invoice, surat tugas, formulir pendaftaran, dan dokumen lainnya** tanpa mengubah struktur database inti.
