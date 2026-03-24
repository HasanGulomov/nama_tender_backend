Tushunarli, demak do'stingiz loyihani o'rnatayotganda qiynalmasligi uchun `README.md` fayliga aniq ko'rsatmalar yozib qo'yishimiz kerak. Bu professional yondashuv hisoblanadi.

Mana shu matnni nusxalab, `README.md` faylingizga joylashtiring (eski matnni o'chirib tashlasangiz ham bo'ladi):

---

```markdown
# Nama Tender Backend (Laravel)

Ushbu loyihani mahalliy kompyuterda ishga tushirish uchun quyidagi qadamlarni bajaring:

### 1. Loyihani yuklab olish
```bash
git clone [https://github.com/HasanGulomov/nama_tender_backend.git](https://github.com/HasanGulomov/nama_tender_backend.git)
cd nama_tender_backend
```

### 2. Kutubxonalarni o'rnatish
```bash
composer install
```

### 3. Muhit faylini sozlash (.env)
`.env.example` faylidan nusxa olib, yangi `.env` faylini yarating:
```bash
cp .env.example .env
```

### 4. Application Key generatsiya qilish
```bash
php artisan key:generate
```

### 5. Ma'lumotlar bazasini sozlash (MySQL)
Avval MySQL-da yangi baza oching (masalan: `napa_tender`). So'ng `.env` faylini ochib, quyidagi qatorlarni o'zingizning bazangizga moslang:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=napa_tender
DB_USERNAME=root
DB_PASSWORD=sizning_parolingiz
```

### 6. Migratsiyalarni amalga oshirish
Bazaga jadvallarni yuklash uchun:
```bash
php artisan migrate
```

### 7. Loyihani ishga tushirish
```bash
php artisan serve
```
```

---

### Buni qanday saqlash kerak?

1.  VS Code-da **README.md** faylini oching.
2.  Yuqoridagi kodni ichiga tashlang va saqlang (`Ctrl + S`).
3.  Terminalda quyidagi buyruqlarni yozib, GitHub-ga yuboring:
    ```bash
    git add README.md
    git commit -m "README fayli yangilandi: o'rnatish yo'riqnomasi qo'shildi"
    git push origin main
    ```

Endi do'stingiz GitHub sahifangizga kirsa, pastda tayyor qo'llanmani ko'radi va shunga qarab loyihani osonlikcha ishga tushiradi.

**Yana bir narsa:** Agar loyihangizda rasm yoki fayllar yuklangan bo'lsa, do'stingizga `php artisan storage:link` buyrug'ini ham yozishni maslahat berishimiz kerakmi?


Usmon gey 